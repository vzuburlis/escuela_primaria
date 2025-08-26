<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace core\controllers;

use Gila\Config;
use Gila\Router;
use Gila\Controller;

class WebhookController extends Controller
{
    public function __construct()
    {
        @header('X-Robots-Tag: noindex, nofollow');
        $folder = LOG_PATH . '/webhooks/';
        if ($id = Router::param('id', 1)) {
            $folder .= $id . '/';
        }
        Config::dir($folder);
        if ($_POST != [] || $_POST = json_decode(file_get_contents("php://input"), true)) {
            $ip = ($_SERVER['REMOTE_HOST'] ?? $_SERVER['REMOTE_ADDR']);
            $ip = str_replace(['/' . '\\','.'], '', $ip);
            file_put_contents(
                $folder . date("Y-m-d H:i:s ") . $ip . ".json",
                json_encode($_POST, JSON_PRETTY_PRINT)
            );
        }
    }

    public function indexAction()
    {
    }
}
