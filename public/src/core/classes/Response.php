<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2022 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Response
{
    public static function json($args, $code = 200)
    {
        @http_response_code($code);
        echo json_encode($args);
        exit;
    }

    public static function success($args = [], $code = 200)
    {
        @http_response_code($code);
        echo json_encode(array_merge(['success' => true], $args), JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error($message = '', $code = 401)
    {
        @http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }

    public static function code($code)
    {
        @http_response_code($code);
        ob_end_clean();
        exit;
    }

    public static function location($path, $params = [])
    {
        $q = http_build_query($params);
        if (!empty($q)) {
            $path .= strpos($path, '?') ? '&' . $q : '?' . $q;
        }
        @header('Location: ' . $path);
        exit;
    }
}

// HTTP response codes
// 204 No content
// 205 Reset Content (js reload page)
// 206 Partial Content (filtered)
// 400 Bad request (wrong data format, missing params)
// 401 Unauthorized (not logged in)
// 403 Forbidden (do not have permissions/access)
// 404 Not found
// 405 Method not allowed
// 406 Not Acceptable (response requested not supported)
// 407 Proxy Authentication Required
// 409 Conflict (method disactivated from settings)
// 501 Not Implemented
// 511 Network Authentication Required (get-login pages)

// Methods
// HEAD is like GET withput the body
