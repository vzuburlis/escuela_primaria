<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Page
{
    public static function getById($id)
    {
        if (Session::userId() > 0) {
            return DB::getOne("SELECT id,title,description,updated,`language`,publish,slug,template FROM `page` WHERE id=?;", [$id]);
        }
        $page = Cache::remember('page_data', 86400, function ($u) {
            $page = DB::getOne("SELECT id,title,description,updated,`language`,publish,slug,template FROM `page` WHERE id=?;", [$u[0]]);
            return json_encode($page);
        }, [$id, Config::mt('page')]);
        return json_decode($page, true);
    }


    public static function getByIdSlug($id, $published = true)
    {
        $publish = $published ? 'publish=1 AND' : '';
        $query = 'SELECT id,title,`description`,`image`,updated,`language`,publish,slug,template FROM `page`';

        $res = DB::query(
            "$query WHERE $publish (id=? OR (slug=? AND `language`=?))",
            [$id, $id, Config::lang()]
        );

        if ($row = mysqli_fetch_array($res)) {
            if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
                $blocks = json_decode($blocks);
                $row['page'] = View::blocks($blocks, 'page_' . $row['id']);
            }
            return $row;
        } else {
            $res = DB::query(
                "$query WHERE $publish (id=? OR slug=?)",
                [$id, $id]
            );
            if ($row = mysqli_fetch_array($res)) {
                if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
                    $blocks = json_decode($blocks);
                    $row['page'] = View::blocks($blocks, 'page_' . $row['id']);
                }
                return $row;
            }
        }

        if (empty($id) && $published) {
            $res = DB::query(
                "$query WHERE publish=1 AND `language`=?
        UNION $query WHERE publish=1;",
                [Config::lang()]
            );
            if ($row = mysqli_fetch_array($res)) {
                if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
                    $blocks = json_decode($blocks);
                    $row['page'] = View::blocks($blocks, 'page_' . $row['id']);
                }
                return $row;
            }
        }

        return null;
    }

    public static function getBySlug($id)
    {
        return DB::getOne("SELECT id,title,updated,publish,slug,`language`
    FROM `page` WHERE publish=1 AND slug=?;", [$id]);
    }

    public static function genPublished()
    {
        $ql = "SELECT id,title,slug,`language` FROM `page` WHERE publish=1;";
        $res = DB::query($ql);
        while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
        return;
    }

    public static function redirect($id)
    {
        $to = DB::value("SELECT `to_slug` FROM redirect WHERE active=1 AND `from_slug`=?;", [$id]);
        if ($to !== null && !str_starts_with($to, 'https:')) {
            $to = Config::base($to);
        }
        return $to;
    }

    public static function inCachedList($id)
    {
        $array = Cache::remember('page_cache_list', 86400, function ($u) {
            $ql = "SELECT slug FROM `page` WHERE publish=1";
            return json_encode(DB::getList($ql));
        }, [Config::mt('page')]);
        return (in_array($id, json_decode($array, true)));
    }

    public static function addThemeOptions($options)
    {
        $cc = ['logo','title','primary-color','accent-color','heading-font','heading-color','body-color','page-background-color','body-font','css'];
        $poly = [
        'theme.logo' => 'assets/core/logo-b.png',
        'theme.user-menu' => 0,
        'theme.primary-color' => '#ed6d1c',
        'theme.accent-color' => '#3eb5c9',
        'theme.body-color' => '#181818',
        'theme.btn-color' => 'var(--main-primary-color)',
        'theme.heading-color' => '#181818',
        ];
        foreach ($poly as $c => $value) {
            Config::$option[$c] = $value;
        }
        foreach ($options as $c => $value) {
            Config::$option['theme.' . $c] = $value;
        }
    }

    public static function search($s)
    {
        $p = DB::table('page')->getRows(['search' => $s,'publish' => 1], [
        'select' => ['id','description','title','slug','image']
        ]);
        foreach ($p as &$r) {
            $r['img'] = $r['image'];
            $r['url'] = $r['slug'];
        }
        return $p;
    }

    public static function display($page)
    {
        if (isset($page['role_id']) && $page['role_id'] > 0) {
          //echo '<h2 class="my-4 p-4">Contenido disponible solo por miembros</h2>';
            return;
        }
        if (isset($page['group_id']) && $page['group_id'] > 0 && Session::inGroup($page['group_id'])) {
            echo '<h2 class="my-4 p-4">Contenido disponible solo por miembros</h2>';
            return;
        }
        echo $page['text'];
    }
}
