<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

global $xxd;
$xxd = 0;
class Session
{
    private static $started = false;
    private static $user_id;
    public static $data;
    public static $token = null;

    public static function start()
    {
        if (self::$started === true) {
            return;
        }
        self::$started = true;
        self::$user_id = 0;

      // token authentication
        if (!isset($_COOKIE['GSESSIONID'])) {
            self::$token = Request::post('token') ?? ($_SERVER['HTTP_TOKEN'] ?? null);
            if ($auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null) {
                self::$token = strtr($auth, ['Bearer ' => '']);
            }
            if (self::$token) {
                $usr = User::getByMeta('token', self::$token);
                if ($usr) {
                    self::$user_id = $usr['id'];
                    return;
                }
            }
        }

      // verify that session is in database
        if ($session = self::find($_COOKIE['GSESSIONID'] ?? self::$token)) {
          // refresh every minute
            self::$user_id = $session['user_id'];
            self::$data = json_decode($session['data'] ?? '[]', true);
            if (strtotime($session['updated']) + 60 < time()) {
                $usr = User::getById($session['user_id']);
                if ($usr['active'] === 1) {
                    self::user($usr);
                    if (isset($_COOKIE['GSESSIONID'])) {
                        self::updateCookie();
                    }
                    return;
                } else {
                    self::destroy();
                }
            }
        } elseif (isset($_COOKIE['GSESSIONID'])) {
            setcookie('GSESSIONID', $_COOKIE['GSESSIONID'], time() - 1, '/');
        }

        self::login();
    }

    public static function started()
    {
        return self::$started;
    }

    public static function login()
    {
        if (isset($_POST['username']) && isset($_POST['password']) && self::waitForLogin() < 1) {
            $email = trim($_POST['username']);
            @session_start();
            $usr = User::getByEmail($email);
            if ($usr && password_verify($_POST['password'], $usr['pass'])) {
                if ($usr['active'] === 1) {
                    unset($_SESSION['failed_attempts']);
                    self::setCookie($usr['id']);
                    self::user($usr, 'Log In');
                }
            } else {
                @$_SESSION['failed_attempts'][] = time();
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                $session_log = new Logger(LOG_PATH . '/login.failed.log');
                $session_log->log($_SERVER['REQUEST_URI'], htmlentities($email), ['ip' => $ip]);
            }
            session_commit();
        }
    }

    public static function user($user = [], $msg = null)
    {
        $id = $user['id'];
        self::$data['user_id'] = $id;
        self::$data['user_name'] = $user['username'] ?? '';
        self::$data['user_email'] = $user['email'] ?? '';
        self::$data['user_photo'] = $user['photo'] ?? null;
        self::$data['language'] = $user['language'] ?? Config::lang();
        self::$data['permissions'] = User::permissions($id);
        self::$data['level'] = User::level($id);
        self::commit();
        self::$user_id = $id;
        if ($msg !== null) {
            Event::log('user.login');
        }
    }

    public static function find($gsessionId)
    {
        if ($gsessionId === null) {
            return null;
        }
        $res = DB::get("SELECT * FROM sessions
    WHERE gsessionid=? LIMIT 1;", [$gsessionId]);
        return $res[0] ?? null;
    }

    public static function findByUserId($userId)
    {
        return DB::get("SELECT * FROM sessions WHERE user_id=?;", [$userId]);
    }

    public static function setCookie($userId)
    {
        $gsession = DB::uid('sessions', 'gsessionid', 60);

        $_COOKIE['GSESSIONID'] = $gsession;
        self::updateCookie();

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        self::create($userId, $gsession, $ip, $user_agent);
    }

    public static function updateCookie()
    {
        setcookie('GSESSIONID', $_COOKIE['GSESSIONID'], [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'secure' => Config::get('secure_cookie') ?? false,
        'httponly' => true,
        'samesite' => Config::get('samesite_cookie') ?? 'Lax'
        ]);
    }

    public static function create($userId, $gsessionId, $ip, $user_agent)
    {
        Event::fire('Session::create', [
        'user_id' => $userId, 'gsessionid' => $gsessionId,
        'ip_address' => $ip, 'user_agent' => $user_agent
        ]);
        self::$token = $gsessionId;
        DB::query("DELETE FROM `sessions` WHERE user_id=? AND updated<NOW()-INTERVAL 1 MONTH", [$userId]);
        $ql = "INSERT INTO `sessions` (user_id, gsessionid, ip_address, user_agent) VALUES(?,?,?,?);";
        if ($userId > 0) {
            DB::query($ql, [$userId, $gsessionId, $ip, $user_agent]);
        }
    }

    public static function remove($gsessionId)
    {
        $ql = "DELETE FROM sessions WHERE gsessionid=?;";
        DB::query($ql, [$gsessionId]);
    }

    public static function define($vars)
    {
        self::start();
        foreach ($vars as $key => $val) {
            if (!isset(self::$data[$key])) {
                self::$data[$key] = $val;
            }
        }
        self::commit();
    }

    public static function get($key)
    {
        self::start();
        return self::$data[$key] ?? null;
    }

    public static function key($key, $val = null, $t = 0)
    {
        self::start();
        if ($val === null) {
            return self::$data[$key] ?? null;
        }
        self::$data[$key] = $val;
        self::commit();

        if ($t !== 0) {
            if (is_object($val) || is_array($val)) {
                $val = json_encode($val);
            }
            setcookie($key, $val, (time() + $t));
        }
    }

    public static function commit()
    {
        $ql = "UPDATE `sessions` SET `data`=?, updated=NOW() WHERE gsessionid=?;";
        DB::query($ql, [json_encode(self::$data), $_COOKIE['GSESSIONID'] ?? self::$token]);
    }

    public static function unsetKey($key)
    {
        self::start();
        unset(self::$data[$key]);
        self::commit();
    }

  /**
  * Returns user id
  * @return int User's id. 0 if user is not logged in.
  */
    public static function userId(): int
    {
        if (!isset(self::$user_id)) {
            self::start();
        }
        return self::$user_id ?? 0;
    }

  /**
  * Destroys the session session
  */
    public static function destroy()
    {
        if (self::userId() > 0) {
            Event::log('user.logout');
        }
        self::remove($_COOKIE['GSESSIONID'] ?? self::$token);
    }

    public static function waitForLogin()
    {
        $wait = 0;
        $_SESSION['failed_attempts'] = $_SESSION['failed_attempts'] ?? [];
        if (@$_SESSION['failed_attempts']) {
            foreach ($_SESSION['failed_attempts'] as $key => $value) {
                if ($value + 120 < time()) {
                    array_splice($_SESSION['failed_attempts'], $key, 1);
                }
            }
            $attempts = count($_SESSION['failed_attempts']);
            if ($attempts < 5) {
                return 0;
            }
            $lastTime = $_SESSION['failed_attempts'][$attempts - 1];
            $wait = $lastTime - time() + 60;
            $wait = $wait < 0 ? 0 : $wait;
        }
        return $wait;
    }

    public static function hasPrivilege($pri)
    {
        if (!is_array($pri)) {
            $pri = explode(' ', $pri);
        }
        $user_privileges = self::permissions();
        if ($pri === '*' && !empty($user_privileges)) {
            return true;
        }

        foreach ($pri as $p) {
            if (@in_array($p, $user_privileges)) {
                return true;
            }
        }
        return false;
    }

    public static function permissions()
    {
        if (self::userId() === 0) {
            return User::permissions(0);
        }
        if (self::key('permissions') === null) {
            self::key('permissions', User::permissions(self::userId()));
            Log::debug(Session::key('user_email') . " reloaded permisions");
        }
        return self::key('permissions');
    }

    public static function level()
    {
        global $xxd;
        if (self::userId() === 0) {
            return 0;
        }
        if (!is_numeric(self::key('level'))) {
            $lv =  User::level(self::userId());
            Log::debug(Session::key('user_email') . " reloaded level");
            self::key('level', User::level(self::userId()));
        }
        return self::key('level');
    }

    public static function inGroup($group_id)
    {
        $list = User::metaList(Session::userId(), 'group');
        if (in_array($group_id, $list)) {
            return true;
        }
        if (0 < DB::value("SELECT COUNT(*) FROM user_group WHERE user_id=? AND group_id=? AND expire_at>?", [self::userId(), $group_id, time()])) {
            return true;
        }
        if (0 < DB::value("SELECT COUNT(*) FROM user_group a,usergroup ug WHERE user_id=? AND (a.expire_at IS NULL OR a.expire_at>?) AND group_id=ug.id AND ug.usergroup LIKE '*%'", [self::userId(), time()])) {
            return true; //TODO find another way to multigroup access
        }
        return false;
    }
}
