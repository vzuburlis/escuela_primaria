<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2024-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace academy\controllers;

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Event;
use Gila\Session;
use Gila\Email;
use Gila\Router;
use Gila\Response;
use Gila\Form;
use Gila\DB;
use Gila\Controller;

class AcademyController extends Controller
{
    public function __construct()
    {
        @header('X-Robots-Tag: noindex, nofollow');
        Config::addLang('core/lang/login/');
    }

    public function indexAction()
    {
    }
}
