<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Email
{
    private static $template = [];

    public static function send($args)
    {
        $args['email'] = $args['email'] ?? Config::get('admin_email');
        if (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }
        if (in_array(explode('@', $args['email'])[1], ['example.com', 'gmial.com'])) {
            return;
        }

        $args['subject'] = $args['subject'] ?? 'Message from ' . Config::get('base');
        $server = $_SERVER['HTTP_HOST'] ?? Config::get('host');
        $args['message'] = $args['message'] ?? strip_tags($args['html'] ?? '');

        if (isset($args['from']) && !isset($args['headers'])) {
            if (is_string($args['from'])) {
                $args['headers'] = 'From: ' . $args['from'];
            } else {
                $name = $args['from']['name'] ?? Config::get('title');
                $email = $args['from']['email'];
                $args['headers'] = "From: $name <$email>";
            }
        }
        $args['headers'] = $args['headers'] ?? 'From: ' . Config::get('title') . " <noreply@$server>";


        if (isset($args['event'])) {
            $args['template_id'] = self::templateId($args['event'], $args['language'] ?? Config::lang());
        }

        if (isset($args['template_id'])) {
            if (empty($args['data'])) {
                $args['data'] = self::data($args['email']);
            }
            self::useTemplate($args);
        }

        if ($args['message'] === '' && isset($args['post'])) {
            foreach ($args['post'] as $key) {
                $label = is_array($key) ? $key[1] : $key;
                $value = is_array($key) ? $_POST[$key[0]] : $_POST[$key];
                $args['message'] .= "$label:\n" . htmlentities($value) . "\n\n";
            }
        }

        if (Event::get('sendmail', false, $args) === false) {
            mail($args['email'], $args['subject'], $args['message'], $args['headers']);
        }
    }

    public static function trData($data, $prefix = '')
    {
        $response = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $response = array_merge($response, self::trData($value, $key . '.'));
            } else {
                $response['{{' . $prefix . $key . '}}'] = $value;
                $response['<%' . $prefix . $key . '%>'] = $value;
            }
        }
        return $response;
    }

    public static function templateId($event, $lang = null)
    {
        $id = DB::value(
            "SELECT id FROM template WHERE `event`=? AND active=1 AND `language`=?;",
            [$event, $lang ?? Config::lang()]
        ) ?? null;
        return $id;
    }

    public static function sendTo($to, $args)
    {
        if (!is_array($to)) {
            $to = [$to];
        }
        if (Event::get('sendTo', false, $args) === false) {
            return;
        }
        $data = $args['data'] ?? [];
        foreach ($to as $user) {
            $args['email'] = $user['email'] ?? $user;
            $args['data'] = $user['data'] ?? $data;
            self::send($args);
        }
    }

    public static function useTemplate(&$args)
    {
        $tid = $args['template_id'];
        if (!isset(self::$template[$tid])) {
            $table = $args['template_table'] ?? 'template';
            $temp = DB::getOne("SELECT * FROM `{$table}` WHERE id=?;", [$tid]);
            if (isset($temp['blocks'])) {
                $blocks = json_decode($temp['blocks'], true);
                View::$blockSections = false;
                $html = View::blocks($blocks, 'template_' . $tid);
            } else {
                $html = $temp['message'] ?? $temp['body'];
            }
            View::$blockSections = true;
            self::$template[$tid] = [
            'subject' => $temp['title'],
            'html' => $html
            ];
        }

        $translations = self::trData($args['data']);
        $args['subject'] = self::$template[$tid]['subject'];
        $args['html'] = self::$template[$tid]['html'];
        $args['subject'] = strtr($args['subject'], $translations);
        $args['html'] = strtr($args['html'], [
        '%7B%7B' => '{{', '%7D%7D' => '}}',
        'src="' . Config::base() . 'assets/' => 'src="' . Config::base('assets/'),
        ':url(assets/' => ':url(' . Config::get('base') . 'assets/',
        ':url(\'assets/' => ':url(\'' . Config::get('base') . 'assets/',
        'src="assets/' => 'src="' . Config::get('base') . 'assets/',
        'src="sites/' => 'src="' . Config::get('base') . 'sites/',
        'src="tmp/' => 'src="' . Config::get('base') . 'tmp/',
        'src=\'tmp/' => 'src=\'' . Config::get('base') . 'tmp/',
        ]);
        $args['html'] = strtr($args['html'], $translations);

        $args['message'] = $args['html'];
    }


    public static function data($email)
    {
        if ($user = DB::getOne("SELECT username as name FROM user WHERE email=? UNION SELECT `name` FROM contact WHERE email=?;", [$email, $email])) {
            $data = [
            'name' => $user['name'],
            'firstname' => ucwords(explode(' ', trim($user['name']))[0]),
            ];
        }
        return $data ?? [];
    }
}
