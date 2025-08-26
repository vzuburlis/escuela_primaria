<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class User
{
    private static $user = [];
    public static $invitationSent = 0;

    public static function create($email, $password, $name = '', $active = 0)
    {
        if (
            self::validatePassword($password) === true
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        ) {
            $pass = ($password === null) ? '' : Config::hash($password);
            if (DB::value("SELECT COUNT(*) FROM user WHERE email=?", [$email]) > 0) {
                trigger_error('This email exists', E_USER_WARNING);
                return false;
            }
            $userId = DB::create('user', [
            'email' => $email,'pass' => $pass,'username' => $name,'active' => $active,'language' => Config::lang()
            ]);
            Event::fire('User::create', ['userId' => $userId]);
            return $userId;
        } else {
            return false;
        }
    }

    public static function meta($id, $meta, $value = null, $multi = false)
    {
        if ($value === null) {
            $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? ORDER BY id DESC LIMIT 1;";
            return DB::value($ql, [$id, $meta]);
        }
        if ($multi == false) {
            if (DB::value("SELECT COUNT(*) FROM usermeta WHERE user_id=? AND vartype=?;", [$id, $meta])) {
                $ql = "UPDATE usermeta SET `value`=? WHERE user_id=? AND vartype=?;";
                return DB::query($ql, [$value, $id, $meta]);
            }
        }
        $ql = "INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,?,?);";
        return DB::query($ql, [$id, $meta, $value]);
    }

    public static function metaDelete($id, $meta, $value = null)
    {
        if ($value === null) {
            DB::query("DELETE FROM usermeta WHERE user_id=? AND vartype=?", [$id, $meta]);
        } else {
            DB::query("DELETE FROM usermeta WHERE user_id=? AND vartype=? AND `value`=?", [$id, $meta, $value]);
        }
    }

    public static function metaList($id, $meta, $values = null)
    {
        if ($values === null) {
            $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=?;";
            return DB::getList($ql, [$id, $meta]);
        }

        if (!is_array($values)) {
            return false;
        }

        self::metaDelete($id, $meta);
        foreach ($values as $value) {
            $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
            DB::query($ql, [$id, $meta, $value]);
        }
        return true;
    }

    public static function getGroupOptions($user_id)
    {
        $list = self::metaList($user_id, 'group');
        if (Config::get('user_group') == 1) {
            $list = $list + DB::getList("SELECT DISTINCT group_id FROM user_group WHERE user_id=? AND (expire_at IS NULL OR expire_at>?)", [$user_id, time()]);
        }
        $list = implode(',', $list);
        if ($list == '') {
            return [];
        }
        return DB::getOptions("SELECT id,usergroup FROM usergroup WHERE id IN($list)");
    }

    public static function pageFiles()
    {
        $ppp = 20;
        $offset = (($_GET['page'] ?? 1) - 1) * $ppp;
        $limit = 'LIMIT ' . $offset . ',' . $ppp;
        if (!empty($_GET['q'])) {
            $q = DB::res($_GET['q']);
            $_where = "WHERE tag LIKE '%{$q}%')";
            foreach (Config::getList('user-file-query') + ['post'] as $t) {
                $_where .= " OR path IN(SELECT image FROM $t WHERE title like '%{$q}%')";
            }
            $where = " WHERE id IN(SELECT file_id FROM file_tag $_where GROUP BY user_file.id";
        } else {
            $where = '';
        }
        if (Session::level() == 0) {
            $where .= empty($where) ? ' WHERE ' : ' AND ';
            $where .= '(user_id=' . Session::userId() . ')';
        }
        $files = DB::getAssoc("SELECT *,(SELECT GROUP_CONCAT(tag SEPARATOR ', ')
    FROM file_tag WHERE user_file.id=file_id) AS tags FROM user_file $where ORDER BY used_at DESC $limit;");
        $total = DB::value("SELECT COUNT(*) FROM user_file $where;");
        return [$files, $total];
    }

    public static function getIdsByMeta($vartype, $value)
    {
        return DB::getList("SELECT user_id FROM usermeta WHERE value=? AND vartype='$vartype';", [$value]);
    }

    public static function getIdsWithPermission($permission)
    {
        $roles = self::getRolesWithPermission($permission);
        if (empty($roles)) {
            return [];
        }
        $values = implode(',', $roles);
        return DB::getList("SELECT DISTINCT user_id FROM usermeta WHERE `value` IN({$values}) AND vartype='role';");
    }

    public static function getBySameGroup($user_id)
    {
        if (Config::get('user_group') == 1) {
            $list = DB::getList("SELECT DISTINCT group_id FROM user_group WHERE user_id=? AND (expire_at IS NULL OR expire_at>?)", [$user_id, time()]);
        } else {
            $list = self::metaList($user_id, 'group');
        }
        $ig = implode(',', $list);
        $users = DB::get("SELECT u.* FROM user u,usermeta um
    WHERE um.user_id=u.id AND um.vartype='group' AND um.value IN($ig)");
        return $users;
    }

    public static function getByGroup($group_id)
    {
        $users = DB::get("SELECT u.* FROM user u,usermeta um
    WHERE um.user_id=u.id AND um.vartype='group' AND um.value=?", [$group_id]);
        return $users;
    }

    public static function getByMeta($key, $value)
    {
        $res = DB::get("SELECT * FROM user WHERE id=(SELECT user_id FROM usermeta WHERE vartype=? AND value=? LIMIT 1)", [$key, $value]);
        if ($res) {
            return $res[0];
        }
        return false;
    }

    public static function getByEmail($email)
    {
        $user = DB::getOne("SELECT * FROM user WHERE email=?", $email);
        if (!$user) {
            return null;
        }
        $user['photo'] = self::meta($user['id'], 'photo');
        return $user;
    }

    public static function getById($id)
    {
        if (!$id) {
            return null;
        }
        if (!isset(self::$user[$id])) {
            self::$user[$id] = DB::getOne("SELECT * FROM user WHERE id=?", $id);
            self::$user[$id]['photo'] = self::meta($id, 'photo');
        }
        return self::$user[$id];
    }

    public static function getByResetCode($rp)
    {
        $user_id = DB::value("SELECT user_id FROM usermeta WHERE vartype='reset_code' AND value=?;", $rp);
        if (!$user_id) {
            return false;
        }
        return DB::get("SELECT * FROM user WHERE id='$user_id';")[0];
    }

    public static function updatePassword($id, $pass)
    {
        if (self::validatePassword($pass) === true) {
            DB::query("UPDATE user SET pass=? WHERE id=?;", [Config::hash($pass),$id]);
            return true;
        } else {
            return false;
        }
    }

    public static function validatePassword($password)
    {
        if (strlen($password) < 8) {
            View::alert('danger', __('Password must be at least 8 letters', [
            'es' => 'La contraseña debe tener al menos 8 letras'
            ]));
            return false;
        }
        if (count(array_unique(str_split($password))) < 4) {
            View::alert('danger', __('Password must include at least 4 different letters', [
            'es' => 'La contraseña debe incluir al menos 4 letras diferentes'
            ]));
            return false;
        }
        return Event::get('validateUserPassword', true, $password);
    }

    public static function updateName($id, $name)
    {
        if (Session::key('user_id') == $id) {
            Session::key('user_name', $name);
        }
        return DB::query("UPDATE user SET username=? WHERE id=?;", [$name,$id]);
    }

    public static function updateActive($id, $value)
    {
        return DB::query("UPDATE user SET active=? WHERE id=?;", [$value, $id]);
    }

    public static function permissions($id)
    {
        $response = [];
        $rp = Config::getArray('permissions') ?? [];
        if ($id === 0) {
            return [];
        }
        $roles = self::metaList($id, 'role');
        foreach ($roles as $role) {
            if (isset($rp[$role])) {
                foreach ($rp[$role] as $perm) {
                    if (!in_array($perm, $response)) {
                        $response[] = $perm;
                    }
                }
            }
        }
        if (isset($rp['member'])) {
            $response = array_merge($response, $rp['member']);
        }
        return $response;
    }

    public static function logoutFromDevice($n)
    {
        $sessions = Session::findByUserId(Session::userId());
        if (!isset($sessions[$n])) {
            return false;
        }
        Session::remove($sessions[$n]['gsessionid']);
        return true;
    }

    public static function level($id)
    {
        return DB::value("SELECT MAX(userrole.level) FROM userrole,usermeta
    WHERE userrole.id=usermeta.value AND usermeta.vartype='role' AND usermeta.user_id=?", $id) ?? 0;
    }

    public static function roleLevel($id)
    {
        return DB::value("SELECT userrole.level FROM userrole WHERE id=?", $id) ?? 0;
    }

    public static function sendInvitation($data)
    {
        if (self::$invitationSent > 0) {
            return;
        }
        if (Config::get('user_control') || empty($data['email'])) {
            return;
        }
        Config::addLang('core/lang/login/');
        $reset_code = substr(bin2hex(random_bytes(50)), 0, 50);
        $baseurl = Config::base('user/password_reset');
        $reset_url = $baseurl . '?rp=' . $reset_code;
        self::meta($data['id'], 'reset_code', $reset_code);
        if (
            Event::get(
                'user_invite.email',
                false,
                ['user' => $data, 'reset_code' => $reset_code, 'reset_url' => $reset_url]
            )
        ) {
            return;
        }

        $subject = strtr(__('invite_email_subject'), ['{sender}' => Session::key('user_name')]);
        $msg = __('invite_msg_ln2') . " {$data['username']}\n\n";
        $msg .= __('invite_msg_ln3') . ' ' . Config::get('title') . "\n\n";
        $msg .= __('invite_msg_ln4') . "\n";
        $msg .= $reset_url . "\n\n";
        $msg .= __('activate_msg_ln4');
        $headers = "From: " . Config::get('title') . " <noreply@{$_SERVER['HTTP_HOST']}>";
        Email::send(['email' => $data['email'], 'subject' => $subject, 'message' => $msg, 'headers' => $headers]);
        self::$invitationSent++;
    }

    public static function register($data)
    {
        if (Event::get('recaptcha', true) === false) {
            View::alert('error', __('_recaptcha_error'));
            return false;
        }
        if ($error = Event::get('register.error', null, $data)) {
            View::alert('error', $error);
            return false;
        }

        $email = $data['email'] ?? Request::key('email');
        $name = $data['name'] ?? Request::key('name');
        $password = $data['password'];
        Config::addLang('core/lang/myprofile/');

        if (strlen($name) < 6 || strpos($name, ' ') < 1) {
            View::alert('danger', __('Fullname should be real'));
            return false;
        }
        $chars = [',','.','@','&','%','^','|','{','}','<','>','[',']','(',')','?',':','\\',';','/','+','"','\'','=',"_"];
        foreach ($chars as $c) {
            if (strpos($name, $c)> -1) {
                View::alert('danger', __('Invalid character "'. $c. '"'));
                return false;
            }
        }

        if ($name != $data['name']) {
            View::alert('error', __('register_error2'));
        } elseif (self::getByEmail($email) || $email != $data['email']) {
            View::alert('error', __('register_error1'));
        } else {
          // register the user
            $active = Config::get('user_activation') == 'auto' ? 1 : 0;
            if ($userId = self::create($email, $password, $name, $active)) {
              // success
                Event::fire('User::register', ['userId' => $userId, 'data' => $data]);
                if (isset($data['phone']) && !empty($data['phone'])) {
                    self::meta($userId, 'phone', $data['phone']);
                }
                if (Config::get('user_activation') == 'byemail') {
                    $activate_code = substr(bin2hex(random_bytes(50)), 0, 50);
                    $baseactivate = Config::base('user/activate');
                    $activate_url = $baseactivate . '?email=' . $email . '&ap=' . $activate_code;
                    $data = [
                    'user' => ['id' => $userId, 'username' => $name, 'email' => $email],
                    'activate_code' => $activate_code, 'activate_url' => $activate_url
                    ];
                    self::meta($userId, 'activate_code', $activate_code);
                    self::sendActivationEmail($data);
                }
                return true;
            } else {
                View::alert('error', __('register_error2'));
            }
        }
        return false;
    }

    public static function sendActivationEmail($data)
    {
        if (!Event::get('user_activation.email', false, $data)) {
            $name = $data['user']['username'];
            $subject = __('activate_msg_ln1') . ' ' . $name;
            $msg = __('activate_msg_ln2') . " {$name}\n\n";
            $msg .= __('activate_msg_ln3') . ' ' . Config::get('title') . "\n\n";
            $msg .= $data['activate_url'] . "\n\n";
            $msg .= __('activate_msg_ln4');
            $headers = "From: " . Config::get('title') . " <noreply@{$_SERVER['HTTP_HOST']}>";
            new Sendmail(['email' => $data['user']['email'], 'subject' => $subject, 'message' => $msg, 'headers' => $headers]);
        }
        Event::log('user_activation.email', $userId);
    }

    public static function notify(int $user_id, $type, $details = '', $url = '')
    {
      // $type = DB::getOne("SELECT * FROM notification_type WHERE `type`=?", [$type]);
        UserNotification::send($user_id, $type, $details, $url);
    }

    public static function uploadImage($error, $tmp_file, $name)
    {
        if ($error > 0) {
            Response::error(FileManager::uploadError($error));
        }
        $path = Config::dir(Config::get('umedia_path') ?? 'assets/umedia/');
        $name = htmlentities($name);
        $ext = strtolower(pathinfo($name)['extension']);
        $allowed = ["mp4","webm"];
        if ($total = Config::get('media_uploads_limit')) {
            $size = Cache::remember('fsize', 8640, function () {
                return FileManager::getUploadsSize();
            });
            $total = $total * 1024 * 1024;
            if ($total > 0 && filesize($tmp_file) + $size > $total) {
                Response::error('No space available to upload!');
            }
        }
        if ($ext == 'gif' && filesize($tmp_file) > 4096 * 1024) {
            Response::error('GIF file limit is 4mb. Try using MP4 format');
        }
        if (Config::get('allow_svg') && Session::level() > 0) {
            $allowed[] = 'svg';
        }
        if ($ext == 'svg' && filesize($tmp_file) > 1024 * 1024) {
            Response::error('SVG file limit is 1mb.');
        }
        if (FileManager::imageExtension($ext) || in_array($ext, $allowed)) {
            $target = FileManager::move_uploaded($tmp_file, $path, $name);
            if (!$target) {
                Response::error('Could not upload the file');
            }
        } else {
            Response::error('Not a media file');
        }
        return $target;
    }

    public static function getRolesWithPermission($permission)
    {
        $rp = Config::getArray('permissions');
        $roles = [];
        foreach ($rp as $role => $row) {
            foreach ($row as $perm) {
                if ($permission == $perm && !in_array($role, $roles)) {
                    $roles[] = $role;
                }
            }
        }
        return $roles;
    }
}
