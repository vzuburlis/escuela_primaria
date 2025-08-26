<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Http
{
    private static $prefix = [];
    private $body;
    public $header = '';

    public static function post($url, $data = [], $args = [], $name = null)
    {
        $args['method'] = 'POST';
        return new HttpPost($url, $data, $args, $name);
    }

    public static function get($url, $args = [], $name = null)
    {
        $args['method'] = 'GET';
        return new HttpPost($url, [], $args, $name);
    }
}
