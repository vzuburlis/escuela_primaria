<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Widget
{
    public static $wd = [];

    public static function getById($id)
    {
        $res = DB::query("SELECT * FROM widget WHERE id=?", $id);
        return mysqli_fetch_object($res);
    }

    public static function getByWidget($w)
    {
        return DB::query("SELECT * FROM widget WHERE widget=?", $w);
    }

    public static function getActiveByArea($area)
    {
        DB::connect();
        return DB::get(
            "SELECT * FROM widget WHERE active=1 AND area=?
    AND (`language` IS NULL OR language='' OR language=?) ORDER BY pos;",
            [$area, Config::lang()]
        );
    }

    public static function update($data)
    {
        $widget = self::getById($data['widget_id']);
        $fields = self::getFields($widget->widget);

        foreach ($data['option'] as $key => $value) {
            $allowed = $fields[$key]['allow_tags'] ?? false;
            $purify = $fields[$key]['purify'] ?? true;
            if ($purify === true) {
                $data['option'][$key] = HtmlInput::purify($data['option'][$key], $allowed);
            }
        }
        $widget_data = isset($data['option']) ? json_encode($data['option']) : '[]';
        $title = HtmlInput::purify($data['widget_title']);

        DB::query(
            "UPDATE widget SET data=?,area=?,pos=?,title=?,active=?,`language`=? WHERE id=?",
            [$widget_data, $data['widget_area'], $data['widget_pos'] ?? 0, $title,
            $data['widget_active'] ?? 0, $data['widget_language'] ?? 'NULL', $data['widget_id']]
        );

        $r = DB::getOne("SELECT id,title,widget,area,pos,`language`,active FROM widget WHERE id=?", [$data['widget_id']]);
        Response::success([
        'fields' => ['id','title','widget','area','pos','language','active'],
        'rows' => [[$r['id'],$r['title'],$r['widget'],$r['area'],$r['pos'],$r['language'],$r['active']]],
        'items' => [$r],
        'totalRows' => 1]);
    }

    public static function path($w)
    {
        if (!isset(Config::$widget[$type])) {
            return 'src/custom/widgets/' . $type . '/widget.php';
        }
        return Config::src() . Config::$widget[$type] . '/widget.php';
    }

    public static function dataFile($widget)
    {
        if (!isset(Config::$widget[$widget])) {
            $widget = explode('--', $widget)[0];
        }
        if (isset(Config::$widget[$widget])) {
            return Config::src() . Config::$widget[$type] . '/widget.php';
        }
        $wa = self::explode($widget);
        $widget_file = "src/" . $wa[1] . '/widgets/' . $wa[0] . '/widget.php';
    }

    public static function getWidgetFile($widget)
    {
        $_widget = explode('@', $widget);
        if (count($_widget)> 0) {
            $widget_file = 'src/' . $_widget[1] . '/widgets/' . $_widget[0] . '/widget.php';
            if (file_exists($widget_file)) {
                return $widget_file;
            }
        }
        if (!isset(Config::$widget[$widget])) {
            $widget = explode('--', $widget)[0];
        }
        $widget_file = Config::src() . '/' . Config::$widget[$widget] . '/widget.php';
        if (file_exists($widget_file) === false) {
            $wa = self::explode($widget);
            $widget_file = "src/" . $wa[1] . '/widgets/' . $wa[0] . '/widget.php';
        }
        return $widget_file;
    }

    public static function getFields($widget)
    {
        $widgetData = self::$wd[$widget] ?? include self::getWidgetFile($widget);
        self::$wd[$widget] = $widgetData;
        return $widgetData['fields'] ?? $widgetData;
    }

    public static function getKeys($widget)
    {
        $widgetData = self::$wd[$widget] ?? include self::getWidgetFile($widget);
        self::$wd[$widget] = $widgetData;
        return $widgetData['keys'] ?? '';
    }

    public static function getData($widget)
    {
        $widgetData = self::$wd[$widget] ?? include self::getWidgetFile($widget);
        self::$wd[$widget] = $widgetData;
        return $widgetData ?? [];
    }

    public static function getList($term = null)
    {
        $primary = [];
        $secondary = [];
        $widgets = [];

        $folders = scandir("src/core/widgets");
        foreach ($folders as $w) {
            if ($w[0] != '.' && $w[0] != '_') {
                      $widgets[$w] = "core/widgets/$w";
            }
        }
        foreach (Config::packages() as $p) {
            $folders = scandir("src/$p/widgets");
            foreach ($folders as $w) {
                if ($w[0] != '.' && $w[0] != '_') {
                    if (!isset(Config::$widget[$w])) {
                        $w .= '@' . $p;
                    }
                    if (!in_array($w, Config::getList('blocked-widgets'))) {
                          $widgets[$w] = "$p/widgets/$w";
                    }
                }
            }
        }
        $widgets = array_merge($widgets, Config::$widget);

        foreach ($widgets as $widget => $value) {
            $keys = self::getKeys($widget);
            if ($keys === 'removed') {
                continue;
            }
            if (in_array($term, explode(',', $keys))) {
                $primary[$widget] = $value;
            } elseif ($term === null || $keys === '' || ($keys != 'widget' && !in_array($term, ['email','template','campaign']))) {
                $secondary[$widget] = $value;
            }
        }
        return array_merge($primary, $secondary);
    }

    public static function explode($str)
    {
        $x = explode('@', $str);
        return [$x[0], $x[1] ?? 'core'];
    }
}
