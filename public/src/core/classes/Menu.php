<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Menu
{
    private static $active = false;
    public static $editableLinks = false;
    public static $liClass = '';
    public static $liActive = '';
    public static $ulClass = 'dropdown-menu';
    public static $aClass = '';
    public static $iClass = '';
    public static $ddIcon = '';
    public static $span = true;

    public static function getContents($menu)
    {
        if ($data = Cache::get('menu--' . $menu, 86400, [Config::mt('menu')])) {
            return $data;
        }
        if ($data = DB::value("SELECT `data` FROM menu WHERE `menu`=?;", [$menu])) {
            Cache::set('menu--' . $menu, $data, [Config::mt('menu')]);
            return $data;
        }

        return "{type:\"menu\",children:[]}";
    }

    public static function setContents($menu, $data)
    {
        Config::setMt('menu');
        DB::query("REPLACE INTO menu(`menu`,`data`) VALUES(?,?);", [$menu, $data]);
        $menuLN = $menu . '.' . Config::lang();
        Cache::remove($menuLN . '_data');
    }

    public static function getData($menu)
    {
        $menuLN = $menu . '.' . Config::lang();
        $data =  Cache::remember($menuLN . '_data', 86400, function ($u) {
            return DB::value(
                "SELECT `data` FROM menu WHERE `menu` IN (?,?) ORDER BY CHAR_LENGTH(menu) DESC",
                [$u[1], $u[2]]
            );
        }, [Config::mt('menu'), $menuLN, $menu]);

        if ($data && $data != '[]') {
            return json_decode($data, true);
        }
        return self::defaultData();
    }

    public static function remove($menu)
    {
        Cache::set('menu--' . $menu);
    }

    public static function defaultData()
    {
        $widget_data = (object) array('type' => 'menu','children' => []);
        $widget_data->children[] = ['type' => 'link','url' => '','name' => __('Home')];
        $ql = "SELECT id,title FROM postcategory;";
        $pages = DB::get($ql);
        foreach ($pages as $p) {
            $widget_data->children[] = ['type' => "postcategory",'id' => $p[0]];
        }
        foreach (Page::genPublished() as $p) {
            if (count($widget_data->children) < 6 && !empty($p['slug'])) {
                $widget_data->children[] = ['type' => 'page','id' => $p[0]];
            }
        }
        return (array) $widget_data;
    }

    public static function convert($data)
    {
        if ($type = $data['type'] ?? $_type) {
            if (isset($data['children'])) {
                $children = [];
                foreach ($data['children'] as $mi) {
                    $children[] = self::convert($mi);
                }
            }
            if ($type == 'menu') {
                return $children;
            }
            if ($type == 'dir') {
                return ['name' => $data['title'] ?? ($data['name'] ?? ''), 'url' => '#',
                'children' => $children];
            }
            if ($type == 'page') {
                if ($r = Page::getById(@$data['id'])) {
                    $url = $r['slug'];
                    if (Config::lang() !== Config::get('language')) {
                        $url = Config::lang() . '/' . $url;
                    }
                    if (self::$editableLinks) {
                        $url = self::$editableLinks . '/' . $r['id'];
                    }
                    $name = $r['title'];
                    return ['name' => $name, 'url' => $url];
                }
                if (is_string($data['id']) && !is_numeric($data['id'])) {
                    $prefix = (Config::lang() !== Config::get('language')) ? '/' . Config::lang() : '';
                    return ['name' => __(ucfirst($data['id'])), 'url' => $prefix . '/' . $data['id']];
                }
            }
            if ($type == 'postcategory') {
                $ql = "SELECT id,title,slug FROM postcategory WHERE id=?;";
                $res = DB::query($ql, @$data['id']);
                while ($r = mysqli_fetch_array($res)) {
                    $url = "category/" . $r[0] . '/' . $r[2];
                    $name = $r[1];
                }
                return ['name' => $name, 'url' => $url];
            }
            if ($res = MenuItemTypes::get($data)) {
                list($url, $name) = $res;
                return ['name' => $name, 'url' => $url];
            }
            if ($mtype = Config::getList('menu.types')[$type] ?? null) {
                if ($name = $mtype['options'][$data['id']]) {
                    return ['name' => $name, 'url' => $data['id'] ?? '#'];
                }
            }
        }
        if (self::$editableLinks && $r = Page::getBySlug($data['url'])) {
            return ['type' => $data['type'], 'name' => $data['title'] ?? $data['name'], 'url' => self::$editableLinks . '/' . $r['id']];
        } else {
            return ['type' => $data['type'], 'name' => $data['title'] ?? $data['name'], 'url' => $data['url'] ?? '#'];
        }
    }

    public static function getHtml($items, $base = '')
    {
        $html = '';
        self::$active = false;
        $current = !empty($base) ? $base : $_SERVER['REQUEST_URI'];
        if (strpos($current, Config::lang() . '/') === 0) {
            $current = substr($current, 2);
        }
        $current = explode('?', $current)[0];

        foreach ($items as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            $url = $item['url'] ?? $item[1];
            $label = $item['title'] ?? ($item['name'] ?? $item[0]);
            if (isset($item['access'])) {
                if (!Session::hasPrivilege($item['access'])) {
                    continue;
                }
            }
            $icon = '';
            if (isset($item['icon'])) {
                if (file_exists('src/core/assets/icon/' . $item['icon'] . '.svg')) {
                    $icon .= '<img class="' . self::$iClass . '" src="src/core/assets/icon/' . $item['icon'] . '.svg">';
                } else {
                    $icon .= "<i class='" . self::$iClass . " fa fa-" . $item['icon'] . "'></i>";
                }
            }

            $url = ($url == '#') ? 'javascript:void(0)' : htmlentities($url);
            $badge = '';
            if (isset($item['counter'])) {
                $c = is_callable($item['counter']) ? $item['counter']() : $item['counter'];
                if ($c > 0) {
                    $badge = " <span class=\"g-badge\">$c</span>";
                }
            }
            $liClass = self::$liClass;
            $aClass = self::$aClass;
            $ddIcon = self::$ddIcon;
            $adropDown = false;
            if (isset($item['children'])) {
                $liClass .= ' dropdown';
                $aClass .= empty($ddIcon) ? ' dropdown-toggle' : '';
                $adropDown = true;
              // data-bs-toggle='dropdown'
                $childrenHtml = '<ul class="' . self::$ulClass . '">';
                $childrenHtml .= self::getHtml($item['children'], $base);
                $childrenHtml .= "</ul>";

                if (self::$active === true) {
                    self::$active = false;
                    $liClass .= ' ' . self::$liActive;
                }
            } else {
                $childrenHtml = '';
                $ddIcon = '';
            }

            if (trim($url, '/') == trim($current, '/')) {
                self::$active = true;
                $liClass .= ' active';
                $aClass .= ' active';
            } else {
                $liClass .= ' nosf-' . $url . '-' . $current;
            }
            if ($item['type'] == 'btn') {
                $aClass .= ' btn btn-primary text-white';
            }
            if ($item['type'] == 'btn2') {
                $aClass .= ' btn btn-secondary';
            }

            $liClass = $liClass !== '' ? ' class="' . $liClass . '"' : '';
            $aClass = $aClass !== '' ? ' class="' . $aClass . '"' : '';
            if ($adropDown) {
                $html .= "<li$liClass><a$aClass data-bs-toggle='dropdown' href='" . $url . "'>";
            } else {
                $html .= "<li$liClass><a$aClass href='" . $url . "'>";
            }
            if (!empty($icon)) {
                $html .= $icon;
            }
            if (self::$span) {
                $html .= '<span>' . Config::tr($label) . $badge . $ddIcon . '</span></a>';
            } else {
                $html .= '' . Config::tr($label) . $badge . $ddIcon . '</a>';
            }
            $html .= $childrenHtml . '</li>';
        }
        return $html;
    }

    public static function getSubmenu($items, $base = null)
    {
        $base = $base ?? Config::url('');
        if (strpos($base, Config::lang() . '/') === 0) {
            $base = substr($base, 3);
        }
        foreach ($items as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            $current = !empty($base) ? $base : $_SERVER['REQUEST_URI'];
            if (strpos($current, Config::lang() . '/') === 0) {
                $current = substr($current, 3);
            }

            if (isset($item['access'])) {
                if (!Session::hasPrivilege($item['access'])) {
                    continue;
                }
                if (isset($item['package']) && $current == 'admin/package_options/' . $item['package']) {
                    return [$item];
                }
                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                              $url = $child['url'] ?? $child[1];
                        if (trim($url, '/') == trim($current, '/')) {
                            return [$item];
                        }
                    }
                }
            }
        }
        return [];
    }

    public static function getMenuTypes()
    {
        $pages = DB::getOptions("SELECT id,title FROM `page`;");
        foreach (Config::getList('menu.pages') as $p) {
            $pages[$p[0]] = $p[1];
        }
        return [
        'page' => [
        'label' => __('Page'),
        'options' => $pages,
        ],
        ] + Config::getList('menu.types');
    }

    public static function getList()
    {
        return DB::getList("SELECT `menu` FROM `menu`;");
    }

    public static function bootstrap($args = [])
    {
        self::$liClass = 'nav-item';
        self::$liActive = 'menu-open';
        self::$ulClass = $args['ulClass'] ?? 'dropdown-menu';
        self::$aClass = 'nav-link';
        self::$iClass = 'mr-2';
        self::$ddIcon = '';
        self::$span = false;
    }

    public static function fromHtml(&$html)
    {
        $links = [
        'contact' => Config::tr('Contact', ['es' => 'Contacto']),
        'team' => Config::tr('Team', ['es' => 'Equipo']),
        'gallery' => Config::tr('Gallery', ['es' => 'Galeria']),
        'services' => Config::tr('Services', ['es' => 'Servicios']),
        'testimonials' => Config::tr('Testimonials', ['es' => 'Testimonios']),
        'contacto' => 'Contacto',
        'servicios' => 'Servicios',
        ];
        $items = [];
        foreach ($links as $link => $label) {
            if (strpos($html, " id=\"$link\"") > 0) {
                $items[] = ['type' => 'link', 'url' => $_SERVER['REQUEST_URI'] . '#' . $link, 'title' => $label];
            }
        }
        return ['type' => 'menu','children' => $items];
    }
}
