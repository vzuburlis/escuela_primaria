<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class DB
{
    private static $dbhost;
    private static $user;
    private static $pass;
    private static $dbname;
    private static $default_db;
    private static $connected = false;
    private static $link;
    public static $insert_id;
    public static $result;
    public static $readOnly = false;
    public static $profiling = false;
    public static $replicas = [];
    public static $queries = 0;

    public static function set($db)
    {
        self::close();
        if (!isset(self::$default_db)) {
            self::$default_db = $db;
        }
        self::$dbhost = $db['host'] ?? 'localhost';
        self::$user = $db['user'] ?? Config::get('db_user');
        self::$pass = $db['pass'] ?? Config::get('db_pass');
        self::$dbname = $db['name'];
        date_default_timezone_set(Config::get('timezone') ?? 'America/Mexico_City');
    }

    public static function restore()
    {
        self::close();
        self::set(self::$default_db);
    }

    public static function connect()
    {
        if (self::$connected) {
            return;
        }
        try {
            if (self::$readOnly && count(self::$replicas) > 0) {
                $x = rand(0, count(self::$replicas) - 1);
                $rep = self::$replicas[$x];
                self::$link = mysqli_connect($rep['host'], $rep['user'], $rep['pass'], $rep['name']);
            } else {
                self::$link = mysqli_connect(self::$dbhost, self::$user, self::$pass, self::$dbname);
            }
            if (self::$link === false) {
                Event::fire('Db::connect_error');
                exit;
            }
            self::$link->query('SET @@session.time_zone="-06:00";');

            Event::fire('Db::connect');
            if (self::$profiling) {
                self::$link->query('SET profiling=1;');
            }
            self::$connected = true;
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
            self::$connected = false;
        }
    }

    public static function close()
    {
        if (!self::$connected) {
            return;
        }
        if (self::$profiling) {
            $pres = self::$link->query('SHOW profiles;');
            while ($r = mysqli_fetch_row($pres)) {
                if ($r[1] > 0.6) {
                    $line = date("Y-m-d H:i:s") . ', ' . ($_SERVER['REMOTE_ADDR']??'') . ' ' . Session::userId() . '@' . ($_SERVER['HTTP_HOST'] ?? '');
                    $line .= ', ' . ($r[1] ?? 0) . ', "' . strtr($r[2], ['"' => '\\"']) . "\"\n";
                    file_put_contents('log/db.takelong.log', $line, FILE_APPEND);
                }
                if ($r[1] > 0.006) {
                    $q = substr($r[2], 0, 40).'...';
                    if (isset($_GET['search'])) {
                        $q .= ' search=' . ($_GET['search']);
                    }
                    if (isset($_GET['id'])) {
                        $q .= ' id=' . ($_GET['id']);
                    }
                    if (isset($_GET['page'])) {
                        $q .= ' page=' . ($_GET['page']);
                    }
                    if (empty($_GET['id']) && 1==($_GET['page']??1) && empty($_GET['search']) && strpos($r[2],' WHERE ')==-1) {
                        $q .= " CACHEABLE";
                    } else {
                        $q .=  substr($r[2], strpos($r[2],' WHERE '), 40).'...';
                    }
                    $line = ($r[1] ?? 0) . ', ' . $q . "\n";
                    file_put_contents('log/db.searches.log', $line, FILE_APPEND);
                }
            }
        }
        mysqli_close(self::$link);
        self::$connected = false;
    }

    public static function query($q, $args = null)
    {
        if (!self::$connected) {
            self::connect();
        }
        if (self::$connected == false) {
            return null;
        }

        if ($args === null) {
            $res = @self::$link->query($q);
        } else {
            self::execute($q, $args);
            $res = self::$result;
        }
        self::$insert_id = self::$link->insert_id;
        return $res;
    }

    public static function log($folder = false)
    {
        self::$profiling = $folder;
    }

    public static function execute($q, $args = null)
    {
        if (!is_array($args)) {
            $argsBkp = $args;
            $args = [$argsBkp];
        }
        self::$queries++;

        self::$result = self::$link->execute_query($q, $args);
        if (self::$result === false && self::$link->error) {
            error_log('DB: ' . self::$link->error . " ({$q})\n", 3, 'log/error.log');
        }
        self::$insert_id = self::$link->insert_id;
    }

    public static function res($v)
    {
        if (!self::$connected) {
            self::connect();
        }
        return mysqli_real_escape_string(self::$link, $v);
    }

    public static function multi_query($q)
    {
        if (!self::$connected) {
            self::connect();
        }
        $res = self::$link->multi_query($q);
        self::close();
        return $res;
    }

    public static function get($q, $args = null)
    {
        $arr = [];
        $res = self::query($q, $args);
        if ($res) {
            while ($r = mysqli_fetch_array($res)) {
                $arr[] = $r;
            }
        }
        self::close();
        return $arr;
    }

    public static function getOne($q, $args = null)
    {
        return self::get($q, $args)[0] ?? null;
    }

    public static function gen($q, $args = null)
    {
        $res = self::query($q, $args);
        if ($res) {
            while ($r = mysqli_fetch_array($res)) {
                yield $r;
            }
        }
        self::close();
    }

    public static function getRows($q, $args = null)
    {
        $arr = [];
        $res = self::query($q, $args);
        self::close();
        if ($res) {
            while ($r = mysqli_fetch_row($res)) {
                $arr[] = $r;
            }
        }
        return $arr;
    }

    public static function getAssoc($q, $args = null, $index = null)
    {
        $arr = [];
        $res = self::query($q, $args);
        self::close();
        if ($res) {
            if ($res === true) {
                return [];
            }
            if (!$index) {
                while ($r = mysqli_fetch_assoc($res)) {
                          $arr[] = $r;
                }
            } else {
                while ($r = mysqli_fetch_assoc($res)) {
                        $arr[$r[$index]] = $r;
                }
            }
        }
        return $arr;
    }

    public static function getList($q, $args = null)
    {
        $arr = [];
        $garr = self::getRows($q, $args);
        foreach ($garr as $key => $value) {
            $arr[] = $value[0];
        }
        return $arr;
    }

    public static function getOptions($q, $args = null)
    {
        $arr = [];
        $garr = self::getRows($q, $args);
        foreach ($garr as $key => $value) {
            $arr[$value[0]] = $value[1];
        }
        return $arr;
    }

    public static function error()
    {
        return self::$link->error ?? null;
    }

    public static function value($q, $p = null)
    {
        if ($res = self::getOne($q, $p)) {
            return $res[0];
        }
        return null;
    }

    public static function create($table, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $f => $v) {
            $fields[] = '`' . $f . '`';
            $values[] = '"' . self::res($v) . '"';
        }
        $fields = implode(',', $fields);
        $values = implode(',', $values);

        self::query("INSERT INTO $table($fields) VALUES($values);");
        return self::$insert_id ?? null;
    }

    public static function table($name)
    {
        if (empty($name)) {
            error_log('DB.php', 3, 'log/error_table_not_loaded.log');
        }
        $table = new Table($name);
        return $table;
    }

    //public static function getRow($name, $id)
    //{
    //    if (empty($name)) {
    //        error_log('DB.php', 3, 'log/error_table_not_loaded.log');
    //    }
    //    $row = new Row($name, $id);
    //    return $row;
    //}

    public static function tableExists($name)
    {
        return (DB::value("SHOW TABLES LIKE '$name'") == name);
    }

    public static function uid($tbl, $col = 'uid', $size = 16)
    {
        do {
            $hex = substr(bin2hex(random_bytes($size)), 0, $size);
        } while (strlen($hex) < $size || self::value("SELECT $col FROM $tbl WHERE $col='$hex'"));
        return $hex;
    }

    public static function link($source_type, $source_id, $target_type, $target_id, $relation_type = null)
    {
        DB::query("INSERT INTO links(source_type, source_id, target_type, target_id)
        VALUES(?,?,?,?)" ,[$source_type, $source_id, $target_type, $target_id]);
    }

    public static function unlink($source_type, $source_id, $target_type, $target_id, $relation_type = null)
    {
        DB::query("DELETE FROM links WHERE source_type=? AND source_id=? AND target_type=? AND target_id=?", [$source_type, $source_id, $target_type, $target_id]);
    }

    public static function getTargetIds($source_type, $source_id, $target_type, $relation_type = null)
    {
        return DB::getList("SELECT target_id FROM links WHERE source_type=? AND source_id=? AND target_type=?", [$source_type, $source_id, $target_type]);
    }

    public static function getSourceIds($source_type, $target_type, $target_id, $relation_type = null)
    {
        return DB::getList("SELECT source_id FROM links WHERE source_type=? AND target_type=? AND target_id=?", [$source_type, $target_type, $target_id]);
    }

    public static function unlinkAll($type, $id)
    {
        DB::query("DELETE FROM links WHERE (source_type=? AND source_id=?) OR (target_type=? AND target_id=?)", [$type, $id]);
    }

    public static function getMeta($meta,$id)
    {
        return self::value("SELECT metavalue FROM metadata WHERE content_id=? AND metakey=?",  [$id,$meta]);
    }

    public static function getMetaList($meta,$id)
    {
        return self::getList("SELECT metavalue FROM metadata WHERE content_id=? AND metakey=?",  [$id,$meta]);
    }
}
