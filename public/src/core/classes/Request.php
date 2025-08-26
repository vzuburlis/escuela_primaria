<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2022 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Request
{
    public static $errors = [];
    private static $load;

    public static function validate($args, $autoexit = true)
    {
        self::load();
        foreach ($args as $key => $rules) {
            $data[$key] = self::validateParam($key, $rules);
        }

        if ($autoexit && !empty(self::$errors)) {
            Response::json([
            'success' => false,
            'error' => self::$errors[0]
            ]);
        }
        return $data;
    }

    public static function all()
    {
        self::load();
        return array_merge($_GET, $_POST ?? []);
    }

    public static function load()
    {
        if (!isset(self::$load)) {
            if (empty($_POST)) {
                $_POST = json_decode(file_get_contents('php://input'), true);
            }
        }
        self::$load = true;
    }

    public static function get($key = null)
    {
        if ($key === null) {
            return $_GET ?? [];
        }
        return $_GET[$key] ?? null;
    }

    public static function post($key = null)
    {
        self::load();
        if ($key === null) {
            return $_POST ?? [];
        }
        return $_POST[$key] ?? null;
    }

    public static function key($key)
    {
        self::load();
        return $_POST[$key] ?? ($_GET[$key] ?? null);
    }

    public static function validateParam($key, $rules)
    {
        $value = self::key($key);
        return self::validateValue($value, $rules, $key);
    }

    public static function validateValue($value, $rules, $key = '', $fkey = '', $new = true)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        if (empty($value) && in_array('nullable', $rules)) {
            return $value;
        }

        foreach ($rules as $line) {
            $part = explode('??', $line);
            $err = $part[1] ?? null;
            $part1 = explode(':', $part[0]);
            $rule = $part1[0];
            $params = $part1[1] ?? '';
            $p = explode(',', $params);

            if ($rule === 'required' && empty($value)) {
                self::$errors[] = $err ?? __($key) . __(' is required');
            }
            if ($rule === 'array' && is_array($value) === false) {
                self::$errors[] = $err ?? __($key) . __(' is not an array');
            }
            if ($rule === 'date' && strtotime($value) === false) {
                self::$errors[] = $err ?? __($key) . __(' is not a valid date');
            }
            if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                self::$errors[] = $err ?? __($key) . __(' is not a valid email');
            }
            if ($rule === 'url' && !empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                self::$errors[] = $err ?? __($key) . __(' is not a valid url');
            }
            if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                self::$errors[] = $err ?? __($key) . __(' is not a number');
            }
            if ($rule === 'match' && preg_match($params, $value) > 0) {
                self::$errors[] = $err ?? __($key) . __(' does not match the pattern');
            }
            if ($rule === 'strip_tags' && strip_tags($value, $params) != $value) {
                self::$errors[] = $err ?? __($key) . __(' has tags that are not allowed');
            }
            if ($rule === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                if ($value === null) {
                    self::$errors[] = $err ?? __($key) . __(' value is not boolean');
                }
            }
            if ($rule === 'unique' && $new) {
                $tname = DB::res($p[0]);
                $field = DB::res($fkey);
                if (0 < DB::value("SELECT COUNT(*) FROM $tname WHERE $field=?", [$value])) {
                    self::$errors[] = $err ?? __($key) . __(' value already exists');
                }
            }
            if ($rule === 'int') {
                if (!is_numeric($value)) {
                    self::$errors[] = $err ?? __($key) . __(' is not a number');
                } else {
                    $value = (int)$value;
                }
            }
            if ($rule === 'min' && $value < $p[0]) {
                self::$errors[] = $err ?? __($key) . __(' must be al least ') . $p[0];
            }
            if ($rule === 'max' && $value > $p[0]) {
                self::$errors[] = $err ?? __($key) . __(' should be maximun ') . $p[0];
            }
            if ($rule === 'minlength' && strlen($value) < $p[0]) {
                self::$errors[] = $err ?? __($key) . __(' length must be al least ') . $p[0];
            }
            if ($rule === 'maxlength' && strlen($value) > $p[0]) {
                self::$errors[] = $err ?? __($key) . __(' length should be maximun ') . $p[0];
            }
            if ($rule === 'length' && strlen($value) != $p[0]) {
                self::$errors[] = $err ?? __($key) . __(' length should be ') . $p[0];
            }
        }
        return $value;
    }

    public static function filters()
    {
        $filters = [];
        foreach ($_GET as $key => $value) {
            if (!empty($value)) {
                $x = explode('.', $key);
                if (isset($x[1])) {
                    if (!isset($filters[$x[0]])) {
                        $filters[$x[0]] = [];
                    }
                    $filters[$x[0]][$x[1]] = $value;
                } else {
                    $filters[$key] = $value;
                }
            }
        }
        return $filters;
    }
}
