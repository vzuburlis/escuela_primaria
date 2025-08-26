<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class IPAddress
{
    public static function block()
    {
        file_put_contents('block_ips.txt', $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND | LOCK_EX);
    }

    public static function isSafe()
    {
        if (Event::get('IPAddress::isSafe', true) === false) {
            self::block();
            return false;
        }
        return true;
    }
}
