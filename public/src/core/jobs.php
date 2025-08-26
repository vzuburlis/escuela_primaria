<?php

define('SRC_PATH', 'src');
define('THEMES_PATH', 'themes');
$starttime = microtime(true);
error_reporting(E_ALL);
ini_set("error_log", "log/error.log");
include __DIR__ . '/autoload.php';

if (isset($argv) && isset($argv[1])) {
  # cd /var/www/html/ && php src/core/jobs.php <folder inside sites/>
    $site_folder = 'sites/' . $argv[1];
    define('SITE_PATH', $site_folder . '/');
    define('LOG_PATH', $site_folder . '/log');
    define('TMP_PATH', $site_folder . '/tmp');
    define('CONFIG_PHP', $site_folder . '/config.php');
    define('FS_ACCESS', false);
} else {
  # cd /var/www/html/ && php src/core/jobs.php
    $site_folder = '';
    define('SITE_PATH', '');
    define('LOG_PATH', 'log');
    define('TMP_PATH', 'tmp');
    define('CONFIG_PHP', 'config.php');
    define('FS_ACCESS', true);
}

ini_set("error_log", "log/error.log");
@include_once __DIR__ . '/autoload.php';

include CONFIG_PHP;
Gila\DB::set($GLOBALS['config']['db']);
$log = new Gila\Logger(LOG_PATH . '/jobsDebug.log');
if (!@include LOG_PATH . '/load.php') {
    Config::load();
    Gila\Package::updateLoadFile();
    $log->debug('Load file updated '.SITE_PATH);
}
@include 'src/app/load.php';

function __($key, $alt = null)
{
    return Config::tr($key, $alt);
}
function public_path($path)
{
    return Gila\FileManager::publicPath($path);
}

function isCronTime($c)
{
    if ($c == '@hourly') {
        return (date('i') == 0);
    }
    if ($c == '@daily') {
        return (date('G i') == '0 01'); // 00:01
    }
    if ($c == '@monthly') {
        return (date('j G i') == '1 0 01'); // 1st 00:01
    }
    if ($c == '@weekly') {
        return (date('w G i') == '0 0 01'); // Sun 00:01
    }
    $t = explode(' ', $c); // minute hour day month weekday
    if (count($t) != 5) {
        return false;
    }
    if ($t[0] != '*' && !in_array(date('i'), explode(',', $t[0]))) {
        return false;
    }
    if ($t[1] != '*' && date('G') != explode(',', $t[1])) {
        return false;
    }
    if ($t[2] != '*' && date('j') != explode(',', $t[2])) {
        return false;
    }
    if ($t[3] != '*' && date('n') != explode(',', $t[3])) {
        return false;
    }
    if ($t[4] != '*' && date('w') != explode(',', $t[4])) {
        return false;
    }
    return true;
}

$dt = round(microtime(true) - $starttime, 6);
if ($dt > 0.05) {
 $log->debug($dt . ' 1 ' . Config::base());
}

foreach (Gila\Config::getList('package-cronjobs') as $package) {
    if (Gila\Config::inPackages($package)) {
        $_sd = microtime(true);
        include_once 'src/' . $package . '/cronjobs.php';
        $dt = round(microtime(true) - $_sd, 6);
        if ($dt > 0.15) {
          $log->debug($dt . ' ; ' . Config::base(). ': ' .$package);
        }
    }
}

if (FS_ACCESS === true) {
    @include_once 'src/app/cronjobs.php';
}
