<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2021-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace core\controllers;

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Event;
use Gila\Session;
use Gila\Email;
use Gila\Router;
use Gila\Response;
use Gila\Request;
use Gila\Form;
use Gila\DB;
use Gila\Profile;
use Gila\Logger;
use Gila\Controller;
use Gila\FileManager;
use Gila\Image;

class UserController extends Controller
{
    public function __construct()
    {
        @header('X-Robots-Tag: noindex, nofollow');
        Config::addLang('core/lang/login/');
    }

    public function indexAction()
    {
        Config::addLang('core/lang/login/');
        if (Session::userId() > 0) {
            if (isset($_COOKIE['user_redirect'])) {
                $url = $_COOKIE['user_redirect'];
                setcookie('user_redirect', '', time() - 1);
            } else {
                $url = Config::get('user_redirect') ?? '';
                if ($_rp = DB::value("SELECT redirect_url FROM userrole ur, usermeta um WHERE ur.id=um.value AND um.user_id=? AND um.vartype='role' ORDER BY ur.level DESC", [Session::userId()])) {
                    $url = $_rp;
                }
            }
            $base = Config::base($url);
            echo "<meta http-equiv='refresh' content='0;url=" . $base . "' />";
            exit;
        }
        if (isset($_GET['redirect'])) {
            setcookie('user_redirect', $_GET['redirect']);
        } else {
            setcookie("user_redirect", "", time() - 3600);
        }
        @header("X-Frame-Options: SAMEORIGIN");
        @header('X-Robots-Tag: noindex, nofollow');
        if (Session::waitForLogin() > 0) {
            View::alert('error', __('login_error_msg2'));
        } elseif (isset($_POST['username']) && isset($_POST['password'])) {
            $usr = User::getByEmail($_POST['username']);
            $error = __('login_error_msg');
            if ($usr && $usr['active'] == 0 && password_verify($_POST['password'], $usr['pass'])) {
                $error =  __('login_error_inactive', $error);
            }
            View::alert('error', $error);
        }
        View::set('page_title', __('Log In') . ' |' . Config::get('title'));
        View::includeFile('login.php');
    }

    public function callbackAction()
    {
        Event::fire('login.callback');
    }

    public function register_GET()
    {
        Config::addLang('core/lang/login/');
        if (Session::key('user_id') > 0 || Config::get('user_register') != 1) {
            echo "<meta http-equiv='refresh' content='0;url=" . Config::base('user') . "' />";
            return;
        }
        View::set('page_title', __('Register') . ' |' . Config::get('title'));
        Logger::stat();
        View::includeFile('register.php');
    }

    public function start_GET()
    {
        Config::addLang('core/lang/login/');
        if (Session::key('user_id') > 0 || Config::get('user_register') != 1) {
            echo "<meta http-equiv='refresh' content='0;url=" . Config::base('user') . "' />";
            return;
        }
        View::set('page_title', __('Register') . ' |' . Config::get('title'));
        Logger::stat();
        View::includeFile('register-alt.php');
    }

    public function register_POST()
    {
        Config::addLang('core/lang/login/');
        if (User::register($_POST)) {
            View::includeFile('user-register-success.php');
        } else {
            View::includeFile('register.php');
        }
    }

    public function activateAction()
    {
        if (Session::key('user_id') > 0) {
            echo "<meta http-equiv='refresh' content='0;url=" . Config::base('user') . "' />";
            return;
        }

        if (isset($_GET['ap'])) {
            $ids = User::getIdsByMeta('activate_code', $_GET['ap']);
            if (empty($ids)) {
                if (isset($_GET['ap'])) {
                    if (isset($_GET['email']) && $user = DB::getOne("SELECT * FROM user WHERE email=?", [$_GET['email']])) {
                        if ($user['active'] == 1) {
                              View::includeFile('user-activate-success.php');
                        } else {
                            View::includeFile('user-activate-error.php');
                        }
                    } else {
                  // email dont exit
                        View::includeFile('user-activate-error.php');
                    }
                } else {
                    View::includeFile('user-activate-invalid-code.php');
                }
            } else {
                $user = DB::getOne("SELECT * FROM user WHERE id=?", [$ids[0]]);
                User::updateActive($ids[0], 1);
                User::metaDelete($ids[0], 'activate_code');
                View::set('user', User::getById($ids[0]));
                View::set('login_link', User::level($ids[0]) > 0 ? 'admin' : 'user');
                Session::setCookie($ids[0]);
                Session::user($user);
                View::includeFile('user-activate-success.php');
                Event::log('user.activation');
            }
            return;
        }
        http_response_code(400);
    }

    public function password_resetAction()
    {
        if (Session::key('user_id') > 0) {
            View::includeFile('user-change-connected.php');
            return;
        }
        Config::addLang('core/lang/');
        Config::addLang('core/lang/login/');
        $rpa = 'reset-password-attempt';
        $rpt = 'reset-password-time';
        View::set('page_title', __('reset_pass'));

        if (isset($_GET['rp'])) {
            $r = User::getByResetCode($_GET['rp']);
            if (!$r) {
                echo "<meta http-equiv='refresh' content='0;url=" . Config::base('user') . "' />";
                return;
            }
            if (!isset($_POST['pass'])) {
                @session_start();
                $_SESSION['rpa'] = 0;
                @session_commit();
                View::includeFile('user-change-new.php');
                return;
            }
            $idUser = $r[0];
            if (User::updatePassword($idUser, $_POST['pass']) == false) {
                View::includeFile('user-change-new.php');
                return;
            }
            User::metaDelete($idUser, 'reset_code');
            View::set('login_link', User::level($idUser) > 0 ? 'admin' : 'user');
            if ($r['active'] == 1) {
                Session::setCookie($idUser);
                Session::user($r, 'Reset Pass');
            }
            View::includeFile('user-change-success.php');
            return;
        }

        if (!isset($_POST['email'])) {
            View::includeFile('user-change-password.php');
            return;
        }

        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            View::alert('error', __('reset_error2'));
            View::includeFile('user-change-password.php');
            return;
        }

        $r = User::getByEmail($email);
        @session_start();
        $_SESSION['rpa'] = $_SESSION['rpa'] ?? 0;
        $_SESSION['rpt'] = $_SESSION['rpt'] ?? time();

        if (
            $r && $r['active'] == 1
            && ($_SESSION['rpa'] < 200 || $_SESSION['rpt'] + 3600 < time())
        ) {
            $_SESSION['rpa']++;
            $_SESSION['rpt'] = time();
            $reset_code = substr(bin2hex(random_bytes(50)), 0, 50);
            User::meta($r['id'], 'reset_code', $reset_code);
            $basereset = Config::base('user/password_reset');
            $reset_url = $basereset . '?rp=' . $reset_code;

            if (
                !Event::get(
                    'user_password_reset.email',
                    false,
                    ['user' => $r, 'reset_code' => $reset_code, 'reset_url' => $reset_url]
                )
            ) {
                $subject = __('reset_msg_ln1') . ' ' . $r['username'];
                $msg = __('reset_msg_ln2') . " {$r['username']}\n\n";
                $msg .= __('reset_msg_ln3') . ' ' . Config::get('title') . "\n\n";
                $msg .= $reset_url . "\n\n";
                $msg .= __('reset_msg_ln4');
                $headers = "From: " . Config::get('title') . " <noreply@{$_SERVER['HTTP_HOST']}>";
                Email::send(['email' => $email, 'subject' => $subject, 'message' => $msg, 'headers' => $headers]);
            }
        }
        @session_commit();

        View::includeFile('user-change-emailed.php');
    }

    public function authAction()
    {
        header('Content-Type: application/json');
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            Response::error(__('login_error_msg'), 400);
        }
        $usr = User::getByEmail($_POST['email']);
        if ($usr && password_verify($_POST['password'], $usr['pass'])) {
            if ($usr['active'] != 1) {
                Response::error(__('login_error_inactive'));
            }
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $token = DB::uid('sessions', 'gsessionid', 60);
            Session::create($usr['id'], $token, $ip, $user_agent);
            Session::user($usr['id'], $usr['username'], $usr['email']);
            Response::success([
            'id' => $usr['id'],
            'username' => $usr['username'],
            'token' => $token
            ]);
        } else {
            Response::error(__('login_error_msg'));
        }
    }

    public function me_GET()
    {
        if ($user = DB::getOne("SELECT username,email,id FROM user WHERE id=?", [Session::userId()])) {
            Response::success($user);
        }
        Response::error();
    }

    public function logoutAction()
    {
        @header('X-Robots-Tag: noindex, nofollow');
        if (Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        Session::destroy();
        if (Session::$token) {
            Response::success();
        } else {
            echo "<meta http-equiv='refresh' content='0;url=" . Config::get('base') . "' />";
        }
    }

    public function uploadImages_POST()
    {
        if (!Config::get('uploadImage') && Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        if (isset($_POST['removefiles'])) {
            foreach ($_POST['removefiles'] as $f) {
                      Logger::stat('removeImage', $f);
            }
        }
        $file = $_FILES['uploadfiles'] ?? [];
        $images = [];

        for ($i = 0; $i < count($file); $i++) {
            if (isset($file['name'][$i])) {
                      $target = User::uploadImage($file["error"][$i], $file['tmp_name'][$i], $file['name'][$i]);
                      $images[] = htmlentities($target);
            }
        }
        Response::success(['images' => $images]);
    }

    public function uploadImage_POST()
    {
        if (!Config::get('uploadImage') && Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        if (isset($_POST['removefiles'])) {
            foreach ($_POST['removefiles'] as $f) {
                      Logger::stat('removeImage', $f);
            }
        }
        if (isset($_FILES['uploadfiles'])) {
            $target = User::uploadImage($_FILES['uploadfiles']["error"], $_FILES['uploadfiles']['tmp_name'], $_FILES['uploadfiles']['name']);
            Response::success(['image' => htmlentities($target)]);
        }
    }
    public function removeImage_POST()
    {
        if (Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        $src = Request::post('src');
        $tables = DB::getList("SELECT DISTINCT TABLE_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME IN ('image','media')
    AND TABLE_SCHEMA=?;", [$GLOBAL['config']['db']['name']]);
        foreach ($tables as $table) {
            if ($table != 'user_file') {
                if ($id = DB::value("SELECT id FROM $table WHERE image=?;", [$src])) {
                    Response::error('This image is occupied at ' . $table . ' with ID=' . $id);
                }
            }
        }
        if (Session::hasPrivilege('edit_assets admin')) {
            DB::query("DELETE FROM user_file WHERE path=?;", [$src]);
        } else {
            DB::query("DELETE FROM user_file WHERE path=? AND user_id=?;", [$src, Session::userId()]);
        }
        Log::debug(Session::key('user_email') . " deleted $src");
    }

    public function uploadFile_POST()
    {
        if (!Config::get('uploadFile') && Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        if (isset($_POST['removefiles'])) {
            foreach ($_POST['removefiles'] as $f) {
                      Logger::stat('removeImage', $f);
            }
        }
        if (isset($_FILES['uploadfiles'])) {
            if (isset($_FILES['uploadfiles']["error"])) {
                if ($_FILES['uploadfiles']["error"] > 0) {
                    echo '{"success":false,"msg":"' . $_FILES['uploadfiles']['error'] . '"}';
                }
            }

            $path = Config::dir(Config::get('umedia_path') ?? 'assets/umedia/');
            $tmp_file = $_FILES['uploadfiles']['tmp_name'];
            $name = htmlentities($_FILES['uploadfiles']['name']);
            $ext = strtolower(pathinfo($name)['extension']);

            if (in_array($ext, ["jpg","jpeg","png","gif","webp","txt","pdf","doc","docx","ppt"])) {
                $target = FileManager::move_uploaded($tmp_file, $path, $ext);
                if (!$target) {
                    Response::error('Could not upload the file');
                    return;
                }
                Response::success(['image' => htmlentities($target)]);
            } else {
                Response::error('File type ' . $ext . ' not supported');
            }
        }
    }
    public function removeFile_POST()
    {
        if (Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        $src = Request::post('src');
        if (Session::hasPrivilege('edit_assets admin')) {
            DB::query("DELETE FROM user_file WHERE path=?;", [$src]);
        } else {
            DB::query("DELETE FROM user_file WHERE path=? AND user_id=?;", [$src, Session::userId()]);
        }
        Logger::stat('removeFile', $src);
    }

    public function resizeImage_POST()
    {
        if (!Config::get('uploadImage') && Session::userId() === 0) {
            Response::error();
        }
        $target = Request::post('image');
        $maxWidth = Config::get('maxImgWidth') ?? 800;
        $maxHeight = Config::get('maxImgHeight') ?? 800;
        if (file_exists($target) && $maxWidth > 0 && $maxHeight > 0) {
            Image::makeThumb($target, $target, $maxWidth, $maxHeight);
            DB::query("UPDATE user_file SET size=? WHERE path=?", [
            filesize($target), $target
            ]);
        }
        Response::success(['image' => $target]);
    }

    public function useImage_POST()
    {
        $data = Request::post();
        if (!empty($data['path'])) {
            DB::query("UPDATE user_file SET used_at=NOW() WHERE path=?", [$data['path']]);
        }
    }

    public function getImage_POST()
    {
        if (Session::userId() === 0) {
            http_response_code(403);
            return;
        }
        Request::load();
        if (isset($_POST['removefiles'])) {
            foreach ($_POST['removefiles'] as $f) {
                      Logger::stat('removeImage', $f);
            }
        }
        if (isset($_POST['path']) && @is_array(getimagesize($_POST['path']))) {
            $path = Config::dir(Config::get('umedia_path') ?? 'assets/umedia/');
            $ext = $_POST['ext'] ?? 'jpg';
          //$ext = strtolower(pathinfo($name)['extension']??$ext);
            $target = FileManager::genName('x', $path) . '.' . $ext;
            if (file_put_contents($target, file_get_contents($_POST['path']))) {
                DB::query("INSERT INTO user_file(user_id,path,size) VALUES(?,?,?)", [
                Session::userId(), $target, filesize($target)
                ]);
                Response::success(['image' => htmlentities($target)]);
            }
            Response::error('Could not upload the file');
        }
        Response::error('Could not get media file');
    }

    public function profileAction()
    {
        $user_id = Session::key('user_id');
        Profile::postUpdate($user_id);
        Config::addLang('core/lang/myprofile/');
        View::set('page_title', __('My Profile'));
        View::set('token', User::meta($user_id, 'token'));
        View::set('user_photo', User::meta($user_id, 'photo'));
        View::set('user_id', $user_id);
        View::set('action_url', 'user/profile');
        View::render('admin/myprofile.php');
    }

    public function contentAction($type = null)
    {
        if ($type == null) {
            View::renderAdmin('404.php');
            return;
        }
        View::set('table', $type);
        View::set('tablesrc', $src);
        View::renderFile('user/content');
    }
}
