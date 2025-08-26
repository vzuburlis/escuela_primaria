<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2022 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Log
{
    private static $savedStat = false;
    private $handlers = [];
    private $file = null;
    public static $starttime;

    public static function error($error)
    {
        error_log($error);
    }

    public static function time($point = '')
    {
        if (!Config::get('debug_time')) {
            return;
        }
        if (!isset(self::$starttime)) {
            self::$starttime = microtime(true);
        }

        $end = microtime(true);
        $log = new Logger(LOG_PATH . '/timeDebug.log');
        $log->log(round($end - self::$starttime, 6), $point, ['queries' => DB::$queries ?? '','uri' => Router::$url ?? '', 'ip' => $_SERVER['REMOTE_ADDR'] ?? '']);
        self::$starttime = $end;
    }

    public static function debug($message, array $context = [])
    {
        $log = new Logger(LOG_PATH . '/debug.' . date("Y-m") . '.log');
        $log->debug($message, $context);
    }

    public static function file($file, array $context = [])
    {
        $log = new Logger(LOG_PATH . '/' . $file . '.' . date("Y-m") . '.log');
        $log->debug($message, $context);
    }
}
