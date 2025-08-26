<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Controller
{
    public static function admin($minLvl = 1)
    {
        @header("X-Frame-Options: SAMEORIGIN");
        if (Session::userId() === 0) {
            Config::addLang('core/lang/login/');
            if (Session::waitForLogin() > 0) {
                View::alert('error', __('login_error_msg2'));
            } elseif (isset($_POST['username']) && isset($_POST['password'])) {
                View::alert('error', __('login_error_msg'));
            }
            if (!empty($_POST)) {
                Response::error(__('Log in to use this feature', ['es' => 'Inicia sesión para acceder a esta función']));
            } else {
                $url = Config::base('user') . '?redirect=' . ($_SERVER['REQUEST_URI'] ?? '');
                echo "<meta http-equiv='refresh' content='0;url=$url' />";
                exit;
            }
        }
        if (!empty(Session::key('language'))) {
            Config::lang(Session::key('language'));
        }
        self::accessLvl($minLvl);
    }

    public static function access($pri)
    {
        if (Session::hasPrivilege($pri) === false) {
            View::renderFile('403.php');
            Response::code(403);
        }
    }

    public static function accessLvl($minLvl)
    {
        if (Session::level() < $minLvl) {
            View::renderFile('403.php');
            Response::code(403);
        }
    }

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }
}
