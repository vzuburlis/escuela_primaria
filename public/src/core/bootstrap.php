<?php

use Gila\Config;
use Gila\Router;
use Gila\Event;
use Gila\DB;
use Gila\Session;
use Gila\Logger;

$site_folder = 'sites/' . ($_SERVER['HTTP_HOST'] ?? '');
if (file_exists($site_folder) || is_link($site_folder)) {
    define('SITE_PATH', $site_folder . '/');
    define('LOG_PATH', $site_folder . '/log');
    define('TMP_PATH', 'tmp');
    define('CONFIG_PHP', $site_folder . '/config.php');
    define('FS_ACCESS', false);
} else {
    $site_folder = str_replace('/www.', '', $site_folder);
    if (is_dir($site_folder)) {
        define('SITE_PATH', $site_folder . '/');
        define('LOG_PATH', $site_folder . '/log');
        define('TMP_PATH', 'tmp');
        define('CONFIG_PHP', $site_folder . '/config.php');
        define('FS_ACCESS', false);
    } else {
        define('SITE_PATH', '');
        define('LOG_PATH', 'log');
        define('TMP_PATH', 'tmp');
        define('CONFIG_PHP', 'config.php');
        define('FS_ACCESS', true);
    }
}

// seperate query string from request uri
$up = explode('?', $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = $up[0];
if (empty($_GET) && !empty($up[1])) {
    $_GET = urldecode($up[1]);
}


ini_set("error_log", "log/error.log");
include_once __DIR__ . '/autoload.php';

@include_once CONFIG_PHP;
if (empty($GLOBALS['config'])) {
    Config::include('install/index.php');
    exit;
}

$_GET['p'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if (
    FS_ACCESS === true && Config::get('domain') && isset($_SERVER['HTTP_HOST'])
    && Config::get('domain') != $_SERVER['HTTP_HOST']
) {
    http_response_code(404);
    exit;
}
if (
    !empty(Config::getArray('trusted_domains')) &&
    isset($_SERVER['HTTP_HOST']) &&
    !in_array($_SERVER['HTTP_HOST'], Config::get('trusted_domains')) &&
    strpos(Config::get('base'), $_SERVER['HTTP_HOST']) === false
) {
    header('Location: ' . Config::get('base') . substr($_SERVER['REQUEST_URI'], 1));
}
if (
    Config::get('base_redirect') == 1 &&
    strpos(Config::get('base'), $_SERVER['HTTP_HOST'] ?? '') === false
) {
    header('Location: ' . Config::get('base') . substr($_SERVER['REQUEST_URI'], 1));
}
if (
    Config::get('base_redirect') !== 0 &&
    strpos(Config::get('base'), $_SERVER['HTTP_HOST'] ?? '') === false &&
    strpos($_SERVER['HTTP_HOST'], 'gilacms') > 0
) {
    header('Location: ' . Config::get('base') . substr($_SERVER['REQUEST_URI'], 1));
}

DB::set($GLOBALS['config']['db']);
if (!@include LOG_PATH . '/load.php') {
    Config::load();
    Gila\Package::updateLoadFile();
}

Router::setPath($_GET['p'] ?? '');

if (Config::get('env') == 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

@include SITE_PATH . '/load.php';
@include 'src/app/load.php';

Event::fire('load');

function __($key, $alt = null)
{
    return Config::tr($key, $alt);
}
function public_path($path)
{
    return Gila\FileManager::publicPath($path);
}

$theme = Config::get('theme');
if (isset($_GET['g_preview_theme']) && Session::hasPrivilege('admin')) {
    $gtheme = strtr($_GET['g_preview_theme'], ['.' => '','\\' => '','/' => '']);
    if (file_exists('themes/' . $gtheme)) {
        $theme = $gtheme;
    }
}
if (file_exists("themes/$theme/load.php")) {
    include "themes/$theme/load.php";
}
if ($cors = Config::getArray('cors')) {
    foreach ($cors as $url) {
        @header('Access-Control-Allow-Origin: ' . $url);
    }
}
View::$cdn_host = Config::get('cdn_host') ?? Config::get('base');


Router::run();
