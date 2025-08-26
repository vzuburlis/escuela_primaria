<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Cache
{
    public static $page_name = '';
    public static $uniques = [];
    public static $cachePath = LOG_PATH . '/cacheItem/';

    public static function filePath($name, $uniques = [])
    {
        $caching_file = ($name[0] == '*') ? 'log/cacheItem/' : self::$cachePath;
        $caching_file .= strtr($name, ['/' => '-']) . '_' . implode('_', $uniques);
        return $caching_file;
    }

    public static function set($name, $data, $uniques = [])
    {
        $caching_file = self::filePath($name, $uniques);
        return @file_put_contents($caching_file, $data);
    }

    public static function get($name, $time = 3600, $uniques = [])
    {
        if (!is_array($uniques)) {
            $uniques = [$uniques];
        }
        $caching_file = self::filePath($name, $uniques);

        if (file_exists($caching_file) && filemtime($caching_file) + $time > time()) {
            return file_get_contents($caching_file);
        } else {
            if ($uniques !== null) {
                $glob_path = ($name[0] == '*') ? 'log/cacheItem/' : self::$cachePath;
                array_map('unlink', glob($glob_path . strtr($name, ['/' => '-']) . '*'));
            }
        }
        return null;
    }

    public static function remove($name)
    {
        $name = self::$cachePath . strtr($name, ['/' => '-']);
        @array_map('unlink', glob($name . '*'));
    }

    public static function remember($name, $time, $fn, $uniques = [])
    {
        if ($data = self::get($name, $time, $uniques)) {
            return $data;
        }
        if ($uniques === []) {
            $data = $fn();
        } else {
            $data = $fn($uniques);
        }
        self::set($name, $data, $uniques);
        return $data;
    }

    public static function page($name, $time, $uniques = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== "GET" && http_response_code() != 404) {
            return;
        }
        if (UserAgent::isGoogle() || Session::userId() > 0) {
            return;
        }
        if ($data = self::get($name, $time, $uniques)) {
            $controller = Router::getController();
            $action = Router::getAction();
            if (isset(Config::$onaction[$controller][$action])) {
                foreach (Config::$onaction[$controller][$action] as $fn) {
                    $fn();
                }
            }
            echo $data;
            Log::time(http_response_code() ?? 200);
            exit;
        }
        ob_start();
        self::$page_name = $name;
        self::$uniques = $uniques;
        self::$cachePath = realpath(LOG_PATH . '/cacheItem') . '/';

        register_shutdown_function(function () {
            if (http_response_code() === 404) {
                if (strpos(self::$page_name, '404') !== 0) {
                    return;
                }
            }
            $out2 = ob_get_contents();
            self::set(self::$page_name, $out2, self::$uniques ?? []);
        });
    }

    public static function time($time)
    {
        @header('Cache-Control: max-age=' . $time);
        @header('Expires: ' . gmdate('D, d M Y H:i:s', (($_SERVER['REQUEST_TIME'] ?? time()) + $time)) . ' GMT');
    }
}
