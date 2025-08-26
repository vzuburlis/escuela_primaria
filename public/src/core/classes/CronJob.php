<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class CronJob
{
    public static $page_name = '';
    public static $uniques = [];
    public static $cachePath = LOG_PATH . '/cacheItem/';

    public static function hourly($job)
    {
        if (date('i') == 0) {
            $job();
        }
    }

    public static function onHour($h, $job)
    {
        if (date('G') == $h) {
            $job();
        }
    }
}
