<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class HtmlInput
{
    public static $eventAttributes;
    public static $blockAttributes;
    public static $allowed_js = [
    '//www.instagram.com/embed.js',
    'https://platform.twitter.com/widgets.js',
    'https://assets.calendly.com/assets/external/widget.js',
    'https://static.elfsight.com/platform/platform.js',
    'https://www.tiktok.com/embed.js',
    ];
    public static function purify($value, $allowed_tags = false)
    {
        if ($allowed_tags === false) {
            return strip_tags($value);
        }
        if ($allowed_tags !== true) {
            $value = strip_tags($value, $allowed_tags);
        } else {
            $allowed_tags = '<a><abbr><address><area><article><aside><audio><b><base><bdi><bdo><blockquote><body><br><button><canvas><caption><cite><code><col><colgroup><data><datalist><dd><del><details><dfn><dialog><div><dl><dt><em><embed><fieldset><figcaption><figure><footer><form><head><header><hgroup><h1><h2><h3><h4><h5><h6><hr><html><i><iframe><img><input><ins><kbd><label><legend><li><link><main><map><mark><menu><menuitem><meta><meter><nav><noscript><object><ol><optgroup><option><output><p><param><picture><pre><progress><q><rp><rt><ruby><s><samp><script><section><select><small><source><span><strike><strong><style><sub><summary><sup><svg><table><tbody><td><template><textarea><tfoot><th><thead><time><title><tr><track><tt><u><ul><var><video><wbr><path><circle><ellipse><polygon><rect><polyline><line>';
            $value = strip_tags($value, $allowed_tags);
        }

        if ($response = Event::get('HtmlInput::purify', null, $value)) {
            return $response;
        }

        if (empty(trim($value))) {
            $value = '';
        } elseif (class_exists("DomDocument")) {
            $js_clean = Session::level() == 10 ? false : true;
            $value = self::DOMSanitize($value, $js_clean);
        }

        $tD = 'javascript&#8282;';
        $value = strtr($value, ['javascript&#x3a;' => $tD,'javascript&#58;' => $tD,'javascript&colon;' => $tD,'javascript:' => $tD]);
        $meta_tags = self::getMetaTags($value);
        if (isset($meta_tags['refresh'])) {
            foreach ($meta_tags['refresh'] as $content) {
                $value = strtr($value, [$content => '']);
            }
        }
        $value = strtr($value, ['%7B%7B' => '{{','%7D%7D' => '}}']);
        return $value;
    }

    public static function DOMSanitize($value, $js = true)
    {
      // TODO: remove specific style attributes like box-sizing
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8"?>' . $value);
        if ($js) {
            $tags = $dom->getElementsByTagName('script');
            foreach (iterator_to_array($tags) as $tag) {
                if (!in_array($tag->getAttribute('src'), self::$allowed_js)) {
                    // allowed sources
                          Email::send([
                            'subject' => 'Script tag removed from editor',
                            'message' => 'User ' . Session::key('user_email') . ' from ' . Config::base() . ' tried to include this script:\n' . ($tag->getAttribute('src') ?? '[empty src]') . '\n' . ($tag->nodeValue ?? '[nodeValue]'),
                            'email' => Config::get('gcloud.email'),
                          ]);
                            $tag->parentNode->removeChild($tag);
                }
            }
            $tags = $dom->getElementsByTagName('*');
            foreach ($tags as $tag) {
                foreach (self::$eventAttributes as $attr) {
                    $tag->removeAttribute($attr);
                }
                foreach (self::$blockAttributes as $attr) {
                    $tag->removeAttribute($attr);
                }
                if (empty($tag->getAttribute('style'))) {
                    $tag->removeAttribute('style');
                }
                if (!empty($tag->getAttribute('data-bg'))) {
                    $tag->removeAttribute('data-bg');
                }
                if (!empty($tag->getAttribute('data-load'))) {
                    $children = $tag->childNodes;
                    foreach ($children as $child) {
                        $tag->removeChild($child);
                    }
                }
            }
        }

        if (Config::get('bg_lazy')) {
            $tags = $dom->getElementsByTagName('div');
            foreach (iterator_to_array($tags) as $tag) {
                if (strpos($tag->getAttribute('class'), 'lazy') > -1) {
                    $style = $tag->getAttribute('style');
                    preg_match('/background-image: url\((.*)\)/', $style, $m);
                    if ($v = $m[1]) {
                            $tag->setAttribute('data-image', $v);
                            $rmv = 'background-image: url(' . $v . ')';
                            $tag->setAttribute('style', strtr($style, [$rmv => '']));
                    }
                }
            }
        }

        $tags = $dom->getElementsByTagName('img');
        foreach (iterator_to_array($tags) as $tag) {
            if (empty($tag->getAttribute('alt'))) {
                $tag->setAttribute('alt', '');
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $value = $dom->saveHTML($body);
        $value = strtr($value, ['<body>' => '', '</body>' => '']);
        if (substr($value, 1, 8) == '<p> </p>') {
            $value = substr($value, 8);
        }
        return $value;
    }

    public static function getMetaTags($str)
    {
        $pattern = '
~<\s*meta\s

  (?=[^>]*?
  \b(?:name|property|http-equiv)\s*=\s*
  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
)

[^>]*?\bcontent\s*=\s*
  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
[^>]*>

~ix';
        if (preg_match_all($pattern, $str, $out)) {
            return array_fill_keys($out[1], $out[2]);
        }
        return array();
    }
}

HtmlInput::$eventAttributes = [
  'onafterprint',
  'onbeforeprint',
  'onbeforeunload',
  'onerror',
  'onhashchange',
  'onload',
  'onmessage',
  'onoffline',
  'ononline',
  'onpagehide',
  'onpageshow',
  'onpopstate',
  'onresize',
  'onstorage',
  'onunload',
  'onblur',
  'onchange',
  'onfocus',
  'oninput',
  'oninvalid',
  'onreset',
  'onsearch',
  'onselect',
  'onsubmit',
  'onkeydown',
  'onkeypress',
  'onkeyup',
  'onclick',
  'ondblclick',
  'onmousedown',
  'onmousemove',
  'onmouseout',
  'onmouseover',
  'onmouseup',
  'onmousewheel',
  'onwheel',
  'oncopy',
  'oncut',
  'onpaste',
  'onabort',
  'oncanplay',
  'oncanplaythrough',
  'oncuechange',
  'ondurationchange',
  'onemptied',
  'onended',
  'onerror',
  'onloadeddata',
  'onloadedmetadata',
  'onloadstart',
  'onpause',
  'onplay',
  'onplaying',
  'onprogress',
  'onratechange',
  'onseeked',
  'onseeking',
  'onstalled',
  'onsuspend',
  'ontimeupdate',
  'onvolumechange',
  'onwaiting'
];
HtmlInput::$blockAttributes = [
  'data-lt-tmp-id',
  'data-gramm',
];
