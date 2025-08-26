<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Config
{
    public static $widget = [];
    public static $package;
    public static $amenu;
    public static $widget_area = [];
    public static $option = [];
    public static $content;
    public static $contentInit = [];
    public static $mt;
    public static $base_url;
    public static $lang;
    public static $langPaths = [];
    public static $langWords = [];
    public static $overrideWords = [];
    public static $langLoaded = false;
/**
  * Adds language translations from a json file
  * @param $path (string) Path to the folder/prefix of language json files
  */
    public static function addLang($path)
    {
        if (in_array($path, self::$langPaths)) {
            return;
        }

        if (self::$langLoaded === true) {
            self::loadLang($path);
        }
        self::$langPaths[] = $path;
    }

    public static function loadLang($path)
    {
        $filepath = self::src() . '/' . $path . self::lang() . '.json';
        if (file_exists($filepath)) {
            self::$langWords = @array_merge(self::$langWords, json_decode(file_get_contents($filepath), true));
        }
    }

    public static function class($class, $path)
    {
        global $classMap;
        $classMap[$class] = $path;
    }

    public static function addList($list, $el, $el2 = null)
    {
        if (!isset($GLOBALS['list'][$list])) {
            $GLOBALS['list'][$list] = [];
        }
        if ($el2) {
            $GLOBALS['list'][$list][$el] = $el2;
        } else {
            $GLOBALS['list'][$list][] = $el;
        }
    }

    public static function getList($list)
    {
        return $GLOBALS['list'][$list] ?? [];
    }

  /**
  * Registers new widgets
  * @param $list (Assoc Array) Widgets to register
  * @code self::widgets( [‘wdg’=>’my_package/widgets/wdg’] ); @endcode
  */
    public static function widgets($list)
    {
        foreach ($list as $k => $item) {
            self::$widget[$k] = $item;
        }
    }

  /**
  * Registers new content type
  * @param $key (string) Name of content type
  * @param $path (string) Path to the table file
  * @code self::content( 'mytable', 'package_name/content/mytable.php' ); @endcode
  */
    public static function content($key, $path)
    {
        if (!is_array($key)) {
            self::$content[$key] = $path;
        } else {
            foreach ($key as $c) {
                self::$content[$c] = $path . '/tables/' . $c . '.php';
            }
        }
    }

  /**
  * Make changes in a content type when it is initialized
  * @param $key (string) Name of content type
  * @param $init (function) Funtion to run
  * @code self::contentInt( 'mytable', function(&$table) { $table['fileds']['new_field']=[];} ); @endcode
  */
    public static function contentInit($key, $init)
    {
        @self::$contentInit[$key][] = $init;
        if (isset(Table::$tableList[$key])) {
            unset(Table::$tableList[$key]);
        }
    }

  /**
  * Returns the list of active packages
  * @return Array
  */
    public static function packages()
    {
        return self::getArray('packages');
    }

    public static function inPackages($p)
    {
        return in_array($p, self::getArray('packages'));
    }

  /**
  * Add new elements on administration menu
  * @param $key (string) Index name
  * @param $item (assoc array) Array with data
  * Indices 0 for Display name, 1 for action link
  * @code self::amenu('item', ['Item','controller/action','icon'=>'item-icon']); @endcode
  */
    public static function amenu($key, $item = [])
    {
        if (!is_array($key)) {
            $list = [];
            $list[$key] = array_merge(self::$amenu[$key] ?? [], $item);
        } else {
            $list = $key;
        }
        foreach ($list as $k => $i) {
            self::$amenu[$k] = $i;
        }
    }

  /**
  * Add a child element on administration menu item
  * @param $key (string) Index of parent item
  * @param $item (assoc array) Array with data
  * @code self::amenu_child('item', ['Child Item','controller/action','icon'=>'item-icon']); @endcode
  */
    public static function amenu_child($key, $item, $idx = null)
    {
        if (!isset(self::$amenu[$key])) {
            return;
        }
        if (!isset(self::$amenu[$key]['children'])) {
            self::$amenu[$key]['children'] = [];
        }
        if ($idx) {
            self::$amenu[$key]['children'][$idx] = $item;
        } else {
            self::$amenu[$key]['children'][] = $item;
        }
    }

    public static function loadEnv()
    {
        if (!file_exists('../.env')) {
            return;
        }
        $lines = file('../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            $a = explode('=', $line, 2);
            $key = trim($a[0]);
            $value = trim($a[1]);
            if (!array_key_exists($key, $_SERVER)) {
                putenv(sprintf('%s=%s', $key, $value));
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    public static function lang($lang = null)
    {
        if ($lang !== null) {
            self::$lang = $lang;
        }
        if (!isset(self::$lang)) {
            self::$lang = self::get('language');
        }
        return self::$lang;
    }

    public static function languageOptions()
    {
        $opt = [];
        $res = include __DIR__ . '/../lang/languages.php';
        $list = Config::getArray('languages') ?? [Config::lang()];
        foreach ($res as $key => $name) {
            if (in_array($key, $list) || $key == Config::lang()) {
                $opt[$key] = $name;
            }
        }
        return $opt;
    }

  /**
  * Sets the value of configuration attribute
  * @param $option (string) Name of the attribute
  * @param $value (optional) The value to set
  */
    public static function set($op, $value, $save = true)
    {
        if ($value === (self::$option[$op] ?? null)) {
            return;
        }
        @$GLOBALS['config'][$op] = $value;
        @self::$option[$op] = $value;
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if ($save == false) {
            return;
        }
        if (DB::value("SELECT COUNT(*) FROM `option` WHERE `option`=?", [$op]) > 0) {
            DB::query("UPDATE `option` SET `value`=? WHERE `option`=?;", [$value, $op]);
        } else {
            DB::query("INSERT INTO `option`(`option`,`value`) VALUES(?,?);", [$op, $value]);
        }
        @unlink(LOG_PATH . '/load.php');
    }

    public static function let($option, $value)
    {
        self::set($option, $value, false);
    }

  /**
  * Gets the value of configuration attribute
  * @param $key (string) Name of the attribute
  * @return The configuration value
  */
    public static function get($key)
    {
        if (isset(self::$option[$key])) {
            return self::$option[$key];
        }
        $_k = strtr($key, ['_' => '-']);
// temporal
        if (isset(self::$option[$_k])) {
            return self::$option[$_k];
        }
        if ($key == 'default_controller') {
            return 'blog';
        }
        return $_ENV[$key] ?? ($_SERVER[$key] ?? ($GLOBALS['config'][$key] ?? ($GLOBALS['config'][$_k] ?? null)));
    }

    public static function getArray($key)
    {
        $array = self::get($key) ?? [];
        if (is_string($array)) {
            return empty($array) ? [] : json_decode($array, true);
        }
        return $array;
    }

  /**
  * @return Password hash
  */
    public static function hash($pass)
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

  /**
  * Returns an option value
  * @param $option (string) Option name
  * @param $default (optional) The value to return if this option has not saved value
  * @return The option value
  */
    public static function getOption($option, $default = '')
    {
        return self::get($option);
    }
    public static function loadOptions()
    {
        DB::connect();
        $res = DB::get('SELECT `option`,`value` FROM `option`;');
        DB::close();
        foreach ($res as $r) {
            self::$option[$r[0]] = $r[1];
        }
    }

  /**
  * Sets an option value
  * @param $option (string) Option name
  * @param $value (optional) The value to set
  */
    public static function setOption($option, $value)
    {
        self::set($option, $value);
    }

  /**
  * Returns modification times in seconds
  * @param $arg (string or array) Indeces
  */
    public static function mt($arg)
    {
        self::loadMt();
        if (is_array($arg)) {
            $array = [];
            foreach ($arg as $a) {
                $array[] = self::$mt[$a] ?? 0;
            }
            return $array;
        } else {
            return self::$mt[$arg] ?? 0;
        }
    }

  /**
  * Loads modification times from the file
  */
    public static function loadMt()
    {
        if (isset(self::$mt)) {
            return;
        }
        self::$mt = [];
        if ($mt = @include LOG_PATH . '/mt.php') {
            self::$mt = $mt;
        }
    }

  /**
  * Updates the modification times for an index/indeces
  * @param $arg (string or array) Indeces
  */
    public static function setMt($arg)
    {
        self::loadMt();
        if (is_array($arg)) {
            foreach ($arg as $a) {
                self::$mt[$a] = time();
            }
        } else {
            self::$mt[$arg] = time();
        }
        file_put_contents(LOG_PATH . '/mt.php', '<?php return ' . var_export(self::$mt, true) . ';');
    }

    public static function canonical($str)
    {
        $tmp = self::$base_url;
        self::$base_url = self::get('base');
        View::$canonical = self::base($str);
        self::$base_url = $tmp ?? self::$base_url;
    }

    /**
  * Creates a url
  * @param $str (string) URL
  * @return The full url to print
  */
    public static function base($str = null)
    {
        if (!isset(self::$base_url)) {
            if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
                    $scheme = $_SERVER['REQUEST_SCHEME'] ?? (substr(self::get('base'), 0, 5) == 'https' ? 'https' : 'http');
                    self::$base_url = $scheme . '://' . $_SERVER['HTTP_HOST'];
                    self::$base_url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')) . '/';
            } else {
                  self::$base_url = self::get('base') ?? '';
            }
            self::$base_url = htmlentities(self::$base_url);
        }
        if (empty($str)) {
            if (self::lang() !== self::get('language')) {
                    return self::$base_url . self::lang();
            }
            return self::$base_url;
        }
        return self::$base_url . self::url($str);
    }

    public static function url($url, $params = [])
    {
        if ($url === '#' || $url === '') {
            $url = Router::path() . $url;
        } else {
            $var = explode('/', $url);
            if ($var[0] != 'admin' && self::get('default_controller') === $var[0]) {
                if (!Page::inCachedList('')) {
                    $url = substr($url, strlen($var[0]) + 1);
                }
            }
        }
        if (self::lang() !== self::get('language') && strpos($url, self::lang() . '/') !== 0) {
            $url = self::lang() . '/' . $url;
        }

        if ($gpt = Router::request('g_preview_theme') && Session::hasPrivilege('admin')) {
            $params['g_preview_theme'] = $gpt;
        }
        $q = http_build_query($params);
        if (!empty($q)) {
            $url .= strpos($url, '?') ? '&' . $q : '?' . $q;
        }
        return htmlentities($url);
    }


  /**
  * Loads all load files from packages
  */
    public static function load()
    {
        self::$option = [];
        self::loadOptions();
        self::loadEnv();
        include_once __DIR__ . "/../load.php";
        foreach (self::packages() as $package) {
            if (file_exists(self::src() . "/$package/load.php")) {
        //echo $package.',';
                include_once self::src() . "/$package/load.php";
            }
        }
    }

  /**
  * Creates the folder if does not exist and return the path
  * @param $path (string) Folder path
  */
    public static function dir($path)
    {
        if (file_exists($path)) {
            return $path;
        }
        $p = explode('/', strtr($path, ["\\" => "/"]));
        $path = '';
        foreach ($p as $folder) {
            if ($folder != null) {
                $path .= $folder . '/';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }
        }
        return $path;
    }

    public static function tr($key, $alt = null)
    {
        if (self::$langLoaded === false) {
            foreach (self::$langPaths as $path) {
                    self::loadLang($path);
            }
            self::$langLoaded = true;
        }
        if (!empty(self::$overrideWords[$key])) {
            return self::$overrideWords[$key];
        }
        if (isset(self::$langWords[$key])) {
            if (!empty(self::$langWords[$key])) {
                    return $alt[self::lang()] ?? self::$langWords[$key];
            }
        }
        if ($alt !== null) {
            if (is_array($alt)) {
                    return $alt[self::lang()] ?? $key;
            }
            return $alt;
        }
        return $key;
    }

    public static function word($key, $list)
    {
        if (isset($list[self::lang()])) {
            self::$langWords[$key] = $list[self::lang()];
        }
    }

    public static function include($path)
    {
        return include 'src/core/' . $path;
    }

    public static function src($p = null)
    {
        return (SRC_PATH ?? 'src') . ($p ? '/' . $p : '');
    }
}
