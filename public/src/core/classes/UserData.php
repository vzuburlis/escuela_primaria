<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class UserData
{
    public static function breakFullname($str)
    {
        $n = explode(' ', $str);
        if (count($n) == 2) {
            $firstname = $n[0];
            $lastname = $n[1];
        }
        if (count($n) == 3) {
            $firstname = $n[0];
            $lastname = implode(' ', [$n[1], $n[2]]);
        }
        if (count($n) > 3) {
            $firstname = implode(' ', [$n[0], $n[1]]);
            $lastname = implode(' ', [$n[2], $n[3]]);
        }
        return [$firstname, $lastname];
    }

    public static function genderByName($str, $def = '')
    {
        $name = explode(' ', $str)[0];
        $froots = ['a'];
        $mroots = ['is', 'n', 'o'];
        foreach ($froots as $r) {
            if (str_ends_with($name, $r)) {
                      return 'F';
            }
        }
        foreach ($mroots as $r) {
            if (str_ends_with($name, $r)) {
                      return 'M';
            }
        }
        return $def;
    }
}
