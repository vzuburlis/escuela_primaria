<?php

namespace Gila;

use core\controllers\BlocksController;

class PageBlocks
{
    public static function getBlockTemplates($type = null)
    {
        $cached = Cache::remember('blockTemplates_', 86400, function () {
            $widgets = [];
            $res = new HttpPost('https://' . Config::get('gcloud.domain') . '/addons/templates?key=' . Config::get('license') ?? '', [], ['method' => 'GET']);
            if ($data = $res->json()) {
                $widgets = (array)$data->items;
            }
            return json_encode($widgets);
        });
        return json_decode($cached, true);
    }

    public static function getBlockWidgets($type = null)
    {
        $widgets = [];
        if (empty($type)) {
            error_log('DB.php', 3, 'log/error_table_not_loaded.log');
        }
        $contentTable = new Table($type);
        $email_content = $contentTable->getTable()['email_content'] ?? false;
        $list = Widget::getList($type);
        foreach ($list as $k => $w) {
            $key = $k . '@' . explode('/', $w)[0];
            $c = ['name' => $c['name_es'] ?? $key];
            $c['preview'] = 'lzld/widget_image/' . $key;
            $widget_data = Widget::getData($k);
            if (empty($widget_data)) {
                continue;
            }
            $c['keys'] = $widget_data['keys'] ?? '';
            $c['group'] = $widget_data['group'] ?? 'other';
            $c['index'] = $widget_data['index'] ?? 10;
          // dont list blocks for email in other content types
            if (isset($widget_data['group']) && $widget_data['group'] == 'email' && $email_content == false) {
                continue;
            }
            $widgets[$key] = $c;
        }
        return $widgets;
    }

    public static function getBlockGlobals($type = null)
    {
        $widgets = [];
        $prototypes = DB::get("SELECT * FROM block;") ?? [];
        foreach ($prototypes as $proto) {
            $type = '#' . $proto['uid'];
            $widgets[$type] = [];
            $widgets[$type]['index'] = 3;
            $widgets[$type]['id'] = $proto['uid'];
            $widgets[$type]['name'] = json_decode($proto['data'])->_type;
            $widgets[$type]['keys'] = 'page,widget';
            $widgets[$type]['group'] = 'saved';
        }
        return $widgets;
    }

    public static function getPrototypes($type = null)
    {
        return self::getBlockWidgets($type) + self::getBlockTemplates($type) + self::getBlockGlobals($type);
    }


    public static function getGroups($content_blocks, $type)
    {
        return [
        'text' => __('Text'),
        'features' => __('Features'),
        'blog' => __('Blog'),
        'eshop' => __('Eshop'),
        'media' => __('Media'),
        'contact' => __('Contact'),
        'social' => __('Social'),
        'saved' => '💾 ' . __('Saved', ['es' => 'Guardados']),
        'other' => __('Other', ['es' => 'Otro']),
        ];
    }

    public static function cleanup($content, $id)
    {
      // all drafts but the last
        $bid = DB::value("SELECT MAX(m.id) FROM blockslog m WHERE m.content=? AND m.content_id=?", [$content, $id]);
        DB::query(
            "DELETE FROM blockslog
    WHERE content=? AND content_id=? AND draft=1 AND id<?",
            [$content, $id, $bid]
        );
    }

    public static function getCustomPrototype($type)
    {
        $data = \Gila\Widget::getData($type);
        $x = [];
        $x['keys'] = $data['keys'] ?? '';
        $x['group'] = $data['group'] ?? '';
        $x['index'] = $data['index'] ?? 10;
        $x['custom'] = true;
        $x['name'] = $type;
        return $x;
    }

    public static function create($type, $tmp)
    {
        $data = [
        'blocks' => $tmp['blocks'], 'title' => $tmp['title']
        ];

        if ($type == 'page') {
            $data['slug'] = DB::uid($type, 'slug', 20);
            $data['language'] = Config::lang();
            $data['publish'] = 0;
            $data['template'] = $tmp['template'];
        }
        if ($type == 'page' && !DB::getOne("SELECT * FROM page WHERE slug='';")) {
            $options = json_decode($tmp['options'], true);
            $keys = ['heading-font','body-font','primary-color','accent-color','heading-color','body-color','page-background-color','css'];
            if ($tmp['type'] == 1) {
                foreach ($keys as $k) {
                    if (Config::get('theme.' . $k) === null && isset($options[$k])) {
                                  Config::set('theme.' . $k, $options[$k]);
                    }
                }
            }
            $data['slug'] = '';
            $data['publish'] = 1;
        }
        if ($type == 'page_user') {
            $data['user_id'] = Session::userId();
            $slug = Slugify::text($row['title']);
            $slug .= substr(bin2hex(random_bytes(8)), 0, 8);
            $data['slug'] = $slug;
        }

        $id = DB::create($type, $data);
        return $id;
    }

    public static function replaceContent($content, $pid)
    {
        //include_once 'src/core/controllers/BlocksController.php';
        $widgets = BlocksController::readBlocks($content, $pid);
        $address =  Config::get('business_address') ?? 'Mexico City';
        foreach ($widgets as $i => &$widget) {
          // replace the contact data
            if ($widget['_type'] == 'google-map' || $widget['_type'] == 'google-map-text') {
                $widgets[$i]['address'] = $address;
            }
            if (isset($widget['text'])) {
                if (!empty(Config::get('phone'))) {
                    $widgets[$i]['text'] = strtr($widget['text'], [
                    '55 XXXX XXXX' => Config::get('phone'),
                    '55XXXXXXXX' => Config::get('phone'),
                    '55 XX XX XX XX' => Config::get('phone'),
                    '55 xx xx xx xx' => Config::get('phone'),
                    ]);
                }
                if (!empty(Config::get('email'))) {
                    $widgets[$i]['text'] = strtr($widget['text'], [
                    'email@example.com' => Config::get('email'),
                    'misitio@example.com' => Config::get('email'),
                    ]);
                }
                foreach ($keys as $key) {
                    if (!empty(Config::get($key))) {
                            $widgets[$i]['text'] = strtr($widget['text'], [
                            '{{' . $key . '}}' => Config::get($key),
                            ]);
                    }
                }
                if (!empty($address)) {
                    $widgets[$i]['text'] = strtr($widget['text'], [
                    'Mexico City' => $address,
                    'México City' => $address,
                    'México, City' => $address,
                    'Centro CDMX' => $address,
                    'Mexico+City' => strtr($address, [' ' => '+']),
                    'Centro+CDMX' => strtr($address, [' ' => '+']),
                    'Mexico%20City' => urlencode($address),
                    'Centro%20CDMX' => urlencode($address),
                    ]);
                }
            }
        }
        BlocksController::saveBlocks($content, $pid, $widgets);
    }

    public static function getCustomClasses()
    {
        $cclasses = [];
        $css = Config::get('theme.css');
        foreach (Config::getList('get-stylesheet-classes') as $src) {
            $css .= "\n" . file_get_contents($src);
        }
        preg_match_all('/\.([a-zA-Z0-9_-]+)\s*(?=[{,])/', $css, $matches);
        return array_combine($matches[1], $matches[1]);
    }
}
