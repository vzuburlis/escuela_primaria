<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class UserAgent
{
    public static function info($user_agent = null)
    {
        if ($user_agent == null) {
            return null;
        }

        $device = 'SYSTEM';
        $os    = "Unknown OS";
        $os_array = [
        '/windows phone 8/i'    => 'Windows Phone 8',
        '/windows phone os 7/i' => 'Windows Phone 7',
        '/windows nt 6.3/i'     => 'Windows 8.1',
        '/windows nt 6.2/i'     => 'Windows 8',
        '/windows nt 6.1/i'     => 'Windows 7',
        '/windows nt 6.0/i'     => 'Windows Vista',
        '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     => 'Windows XP',
        '/windows xp/i'         => 'Windows XP',
        '/windows nt 5.0/i'     => 'Windows 2000',
        '/windows me/i'         => 'Windows ME',
        '/win98/i'              => 'Windows 98',
        '/win95/i'              => 'Windows 95',
        '/win16/i'              => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i'        => 'Mac OS 9',
        '/linux/i'              => 'Linux',
        '/ubuntu/i'             => 'Ubuntu',
        '/iphone/i'             => 'iPhone',
        '/ipod/i'               => 'iPod',
        '/ipad/i'               => 'iPad',
        '/android/i'            => 'Android',
        '/blackberry/i'         => 'BlackBerry',
        '/webos/i'              => 'Mobile'
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os = $value;
                $device = !preg_match('/(windows|mac|linux|ubuntu)/i', $os)
                  ? 'MOBILE' : (preg_match('/phone/i', $os_platform) ? 'MOBILE' : 'SYSTEM');
                if (in_array($os, ['Mobile','iPhone','Android'])) {
                    $device = 'MOBILE';
                }
            }
        }

        $browser = "Unknown Browser";
        $browser_array = [
        '/msie/i'       => 'IE',
        '/firefox/i'    => 'Firefox',
        '/safari/i'     => 'Safari',
        '/chrome/i'     => 'Chrome',
        '/opera/i'      => 'Opera',
        '/netscape/i'   => 'Netscape',
        '/maxthon/i'    => 'Maxthon',
        '/konqueror/i'  => 'Konqueror',
        '/mobile/i'     => 'Handheld Browser'
        ];
        foreach ($browser_array as $regex => $value) {
            if ($found) {
                break;
            }
            if (preg_match($regex, $user_agent, $result)) {
                $browser = $value;
            }
        }

        return ['os' => $os, 'device' => $device, 'browser' => $browser];
    }

    public static function isGoogle($user_agent = null)
    {
        if ($user_agent === null) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        if (strpos($user_agent, 'Googlebot') !== false) {
            return true;
        }
        return false;
    }

    public static function isBot($user_agent = null)
    {
        if ($user_agent === null) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ip = explode('.', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            if ($ip[0] == 54 && $ip[1] >= 144 && $ip[1] <= 221) {
                //  51.222.0.0 - 51.222.255.255
                // 23.20.0.0 -23.23.255.255
                // 107.20.0.0 - 107.23.255.255,
                // 99.85.128.0 - 99.87.191.255,
                return true;
            }
        }
        $good_bots = ['SaaSHub','bingbot','BingPreview','MJ12bot','Twitterbot',
        'Googlebot','newspaper','NetcraftSurveyAgent','panscient.com','python-requests',
        'facebookexternalhit','Baiduspider','Slackbot','zgrab','evc-batch',
        'Applebot','Python-urllib','bitlybot', 'YandexMetrika', 'Go-http-client',
        'CensysInspect','Siteimprove','aiohttp','Barkrowler','Mediapartners-Google',
        'Scrapy','compatible;','Discordbot','article-parser','HackerNews',
        'node-superagent','RandomSurfer', 'CheckMarkNetwork','Dataprovider.com','Apache-HttpClient',
        'MegaIndex','Infosecbot', 'RestSharp', 'Lighthouse', 'Uptimebot',
        ' bot','github.com','meta-externalagent'
        ];

        if ($user_agent === null || strlen($user_agent) < 40 || strpos($user_agent, 'Bot') !== false) {
            return true;
        }
        foreach ($good_bots as $bot) {
            if (strpos($user_agent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }
}
