<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Table
{
    public $table;
    public $permissions;
    public static $error = null;
    public static $options = true;
    public static $tableList = [];
    public static $basicOps = [
    'gt' => '>', 'ge' => '>=', 'lt' => '<', 'le' => '<=', '<>' => '<>'
    ];

    public function __construct($tbl, $permissions = ['admin'])
    {
        $this->permissions = $permissions;

        if (!empty(self::$tableList[$tbl])) {
            $this->table = self::$tableList[$tbl];
            return;
        }
        if ($data = Cache::get('tableschema--' . $tbl, 86400)) {
            $this->table = json_decode($data, true);
            self::$tableList[$tbl] = $this->table;
            return;
        }
        if ($this->loadSchema($tbl) == false) {
            $error = "Table $tbl could not be loaded ". Config::base();
            trigger_error($error, E_USER_WARNING);
            error_log($error, 3, 'log/error.log');
            return;
        };

        if (isset($this->table['lang'])) {
            Config::addLang($this->table['lang']);
        }
        foreach ($this->table['fields'] as $key => &$field) {
            if (isset($field['qoptions']) && self::$options) {
                if (is_array($field['qoptions'])) {
                    if (!isset($field['options'])) {
                        $field['options'] = [];
                    }
                    $o = $field['qoptions'];
                    if (isset(Config::$content[$o[2]])) {
                        $optionTable = new Table($o[2]);
                        $res = $optionTable->getRows($o[3] ?? [], ['select' => [$o[0],$o[1]],'limit' => false]);
                        foreach ($res as $el) {
                            $field['options'][$el[$o[0]]] = $el[$o[1]];
                        }
                    }
                } else {
                    $res = DB::getOptions($field['qoptions']);
                    if (!empty($field['options'])) {
                        $field['options'] = array_replace($field['options'], $res);
                    } else {
                        $field['options'] = $res;
                    }
                }
                if (!json_encode($field['options'])) {
                    die('Key options with problem:' . $key);
                }
            }
            if (isset($field['title'])) {
                $field['title'] = Config::tr($field['title']);
            } else {
                $field['title'] = ucfirst(Config::tr($key));
            }
        }

      //if (isset($this->table['children'])) {
      //  foreach ($this->table['children'] as $key => &$child) {
      //    $child_table = new Table($key, $permissions);
      //    $child['table'] = $child_table->getTable();
      //  }
      //}
        $this->table['title'] = Config::tr($this->table['title'] ?? $this->table['name']);

        if (!isset($this->table['permissions'])) {
            $this->table['permissions'] = [];
        }
        $p = &$this->table['permissions'];
        if (!isset($p['create'])) {
            $p['create'] = ['admin'];
        }
        if (!isset($p['read'])) {
            $p['read'] = ['admin'];
        }
        if (!isset($p['update'])) {
            $p['update'] = ['admin'];
        }
        if (!isset($p['delete'])) {
            $p['delete'] = ['admin'];
        }

        if (isset($this->table['cache']) && $this->table['cache'] == true) {
            Cache::set('tableschema--' . $tbl, json_encode($this->table));
        }
        self::$tableList[$tbl] = $this->table;
    }

    public static function exist($tbl)
    {
        if (isset(Config::$content[$tbl])) {
            return true;
        }
        if (DB::value("SELECT id FROM tableschema WHERE `name`=?;", [$tbl])) {
            return true;
        }
        return false;
    }

    public function loadSchema($tbl): bool
    {
        if (isset(Config::$content[$tbl])) {
            $path = Config::src() . '/' . Config::$content[$tbl];
        } elseif (file_exists($tbl)) {
            $path = $tbl;
        } else {
            $path = null;
            if ($json = DB::value("SELECT `data` FROM tableschema WHERE `name`=?;", [$tbl])) {
                $this->table = json_decode($json, true);
            } else {
                trigger_error($tbl . ' table not found', E_USER_WARNING);
                return false;
            }
        }

        if ($path) {
            $this->table = include $path;
            if (isset($table)) {
                $this->table = $table;
            }
        }

        if ($ext = $this->table['extends'] ?? null) {
            $baseTable = include Config::src() . '/' . (Config::$content[$ext] ?? $ext);
            $this->table = self::extend_recursive($baseTable, $this->table);
            if (isset($this->table['replaces'])) {
                foreach ($this->table['replaces'] as $key => $el) {
                    $this->table[$key] = $el;
                }
            }
            if (isset(Config::$content[$ext])) {
                $this->loadMetafields($ext);
            }
        }

        if (!isset($this->table['pagination'])) {
            $this->table['pagination'] = 15;
        }

        if (isset($this->table['filter_owner']) && Session::userId()> 0) {
            $user_key = $this->table['filter_owner'];
            @$this->table['filters'][$user_key] = Session::userId();
            foreach (['search_boxes','csv','edit','create'] as $key) {
                if (isset($this->table[$key])) {
                    $this->table[$key] = array_diff($this->table[$key], [$user_key]);
                }
                $this->table['fields'][$user_key][$key] = false;
            }
        }

        $this->addSortPos();

      // changes from addons
        if (isset(Config::$contentInit[$this->table['name']])) {
            foreach (@Config::$contentInit[$this->table['name']] as $init) {
                $init($this->table);
            }
        }
        if (isset(Config::$contentInit[$tbl])) {
            foreach (@Config::$contentInit[$tbl] as $init) {
                $init($this->table);
            }
        }

        $this->loadMetafields($tbl);

        return true;
    }

    public function addSortPos()
    {
        if (!($this->table['sort_pos'] ?? false)) {
            return;
        }

        $this->table['orderby'] = $this->table['orderby'] ?? 'pos ASC';
        $this->table['sort'] = 'pos';
        $this->table['fields']['pos'] = [
        'show' => false,'edit' => false,'create' => false,
        'qtype' => 'INT DEFAULT 0',
        ];

        if (!isset($this->table['events'])) {
            $this->table['events'] = [];
        }
        $this->table['events'][] = ['create', function (&$row) {
            $tbl = $this->table['name'];
            if (isset($this->table['sort_filter'])) {
                $filter = $this->table['sort_filter'];
                $row[$filter] = Request::get($filter);
                $row['pos'] = DB::value("SELECT MAX(pos) FROM $tbl WHERE $filter=?;", [$row[$filter]]) + 1;
            } else {
                $row['pos'] = DB::value("SELECT MAX(pos) FROM $tbl;") + 1;
            }
        }];
        $this->table['events'][] = ['delete', function ($id) {
            $row = DB::getOne("SELECT * FROM {$this->table['name']} WHERE id=?", [$id]);
            $tbl = $this->table['name'];
            if (isset($this->table['sort_filter'])) {
                $filter = $this->table['sort_filter'];
                DB::query("UPDATE $tbl SET pos=pos-1 WHERE pos>? AND $filter=?", [$row['pos'],$row[$filter]]);
            } else {
                DB::query("UPDATE $tbl SET pos=pos-1 WHERE pos>?", [$row['pos']]);
            }
        }];
    }

    public static function extend_recursive($table, $extTable)
    {
        foreach ($extTable as $key => $el) {
            if (is_array($el) && !is_numeric($key) && $key != 'options') {
                $table[$key] = self::extend_recursive($table[$key] ?? [], $el);
            } elseif (is_numeric($key)) {
                if (!in_array($el, $table)) {
                    $table[] = $el;
                }
            } else {
                $table[$key] = $el;
            }
        }
        return $table;
    }

    public function loadMetafields($tbl)
    {
        $mfs =  Cache::remember($tbl . '_mfs', 86400, function ($u) {
            $mfs = DB::getAssoc("SELECT * FROM metafield WHERE `content`=?;", [$u[0]]);
            foreach ($mfs as &$mf) {
                $mf['options'] = DB::getAssoc("SELECT * FROM metafield_option WHERE metafield_id=?;", [$mf['id']]);
            }
            return json_encode($mfs);
        }, [$tbl, Config::mt('metafield')]);

        if ($mfs = json_decode($mfs, true)) {
            if (is_array($mfs)) {
                foreach ($mfs as $f) {
                    $fkey = !empty($f['fkey']) ? $f['fkey'] : 'f' . $f['id'];
                    $new = [
                    'title' => $f['name'],
                    'show' => $f['show_value'] == 1 ? true : false,
                    'csv' => $f['csv'] == 1 ? true : false,
                    'list' => true,
                    'edit' => true,
                    'type' => 'meta',
                    'input_type' => $f['type'],
                    'display_type' => $f['type'],
                    'meta_key' => 'f' . $f['id'],
                    'meta_table' => ['metadata', 'content_id', 'metakey', 'metavalue'],
                    ];
                    if ($f['maxlength'] > 0) {
                        $new['maxlength'] = $f['maxlength'];
                    }
                    if ($f['csv'] == 1) {
                        if (isset($this->table['csv'])) {
                            $this->table['csv'][] = $fkey;
                        }
                        if (isset($this->table['upload_csv'])) {
                            $this->table['upload_csv'][] = $fkey;
                        }
                    }
                    if (!empty($f['description'])) {
                        $new['helptext'] = $f['description'];
                    }
                    if (!empty($f['options'])) {
                        $new['options'] = $f['options'];
                    }
                    if ($f['span'] > 0) {
                        $new['input_style'] = 'grid-column:span ' . $f['span'];
                    }
                    if ($f['span'] == 'max') {
                        $new['input_style'] = 'grid-column:span 1/-1';
                    }
                    if (!empty($f['after'])) {
                        $after_index = array_search($f['after'], array_keys($this->table['fields']));
                    }
                    if ($after_index) {
                        $this->table['fields'] = array_slice($this->table['fields'], 0, $after_index + 1) + [ $fkey => $new ] + $this->table['fields'];
                    } else {
                        $this->table['fields'][$fkey] = $new;
                    }
                }
            }
        }
    }

    public function name()
    {
        return $this->table['name'];
    }

    public function id()
    {
        return $this->table['id'] ?? 'id';
    }

    public function fieldAttr($field, $attr)
    {
        if (isset($this->table['fields'][$field])) {
            if (isset($this->table['fields'][$field][$attr])) {
                return $this->table['fields'][$field][$attr];
            }
        }
        return false;
    }

    public function fields($output = 'list')
    {
        if (!isset($this->table[$output])) {
            $this->table[$output] = [];
            foreach ($this->table['fields'] as $k => $f) {
                if (!isset($f[$output]) || $f[$output] === true) {
                    $this->table[$output][] = $k;
                }
            }
        }
        return $this->table[$output];
    }

    public function select(&$fields = null)
    {
        $select = [];
        if ($fields === null) {
            $fields = $this->fields();
        }
        if ($fields === '*') {
            $fields = $this->fields('*');
        }

        foreach ($fields as $key => $value) {
            if (!$this->fieldAttr($value, 'jt')  && !isset($this->table['children'][$value])) {
                $select[$key] = $this->getColumnKey($value);
            }
        }
        return implode(',', $select);
    }

    public function getColumnKey($value, $select = true)
    {
        if ($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
            return $qcolumn . ($select ? ' as ' . $value : '');
        }
        if (@$this->table['fields'][$value]['type'] === 'meta') {
            list($mt, $vt) = $this->getMT($value);
            $this_id = $this->table['fields'][$value]['meta_id'] ?? $this->name() . "." . $this->id();
            if ($this->table['fields'][$value]['single'] ?? false) {
                $qcolumn = "(SELECT `{$mt[3]}` FROM {$mt[0]} ";
                $qcolumn .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$mt[2]}='{$vt}' LIMIT 1)";
            } else {
                $qcolumn = "(SELECT GROUP_CONCAT(`{$mt[3]}`) FROM {$mt[0]} ";
                $qcolumn .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$mt[2]}='{$vt}')";
            }
            return $qcolumn . ($select ? ' as ' . $value : '');
        }
        if (isset($this->table['left_join']) && $value == 'id') {
            return $this->table['name'] . '.' . DB::res($value);
        }
        return '`' . DB::res($value) . '`';
    }

    public function selectsum($groupby)
    {
        $select = $this->fields();

        foreach ($select as $key => $value) {
            if ($this->fieldAttr($value, 'type') === "number") {
                $select[$key] = 'SUM(' . $value . ') as ' . $value;
            } elseif ($groupby === $value) {
                $select[$key] = $this->getColumnKey($value);
              //if ($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
              //  //$select[$key] = $qcolumn.' as '.$value;
              //  $select[$key] = $this->getColumnKey($value);
              //}
              //if (@$this->table['fields'][$value]['type'] === 'meta') {
              //  list($mt, $vt) = $this->getMT($value);
              //  $this_id = $this->name().".".$this->id();
              //  $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[3]}`) FROM {$mt[0]} ";
              //  $select[$key] .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$mt[2]}='{$vt}') as ".$value;
              //}
            } else {
                $select[$key] = "'' as " . $value;
            }
        }

        return implode(',', $select);
    }

    public function startIndex($args)
    {
        $ppp = $this->itemsPerPage();
        if ($page = $args['page'] ?? 1) {
            return ($page - 1) * $ppp;
        }
        return 0;
    }

    public function orderby($orders = null)
    {
        $_orders = [];
        if (is_string($orders)) {
            $orders = explode(',', $orders);
        }

        if ($orders) {
            foreach ($orders as $key => $order) {
                $order = DB::res($order);
                $o = is_numeric($key) ? explode('_', $order) : [$key, $order];
                if (!array_key_exists($o[0], $this->table['fields'])) {
                    continue;
                }
                if ($o[1] === 'a') {
                    $o[1] = 'ASC';
                }
                if ($o[1] === 'd') {
                    $o[1] = 'DESC';
                }
                $_orders[] = $o[0] . ' ' . $o[1];
            }
        }
        if (isset($this->table['orderby'])) {
            $_orders[] = $this->table['orderby'];
        }

        $by = $_orders !== [] ? implode(',', $_orders) : $this->id() . ' DESC';
        return " ORDER BY $by";
    }

    public function groupby($group)
    {
        if ($group === null) {
            if ($group = $this->table['groupby'] ?? null) {
                return " GROUP BY $group";
            }
            return '';
        }
        return " GROUP BY $group";
    }

    public function limit($limit = null)
    {
        if ($limit === null) {
            $limit = $this->startIndex();
            if (isset($this->table['pagination'])) {
                $limit .= ',' . $this->table['pagination'];
            } else {
                return '';
            }
        } elseif (is_array($limit)) {
            $limit = implode(',', $limit);
        } elseif ($limit === false) {
            return '';
        }
        return DB::res(" LIMIT $limit");
    }

    public function limitPage($args)
    {
        $ppp = $this->itemsPerPage();
        if ($page = $args['page'] ?? 1) {
            $offset = ($page - 1) * $ppp;
            return " LIMIT $offset, $ppp";
        }
        return '';
    }

    public function itemsPerPage()
    {
        return $this->table['pagination'] ?? 25;
    }

    public function event($event, &$data)
    {
        if (isset($this->table['events'])) {
            foreach ($this->table['events'] as $ev) {
                if ($ev[0] === $event) {
                    $ev[1]($data);
                }
            }
        }
    }

    public function set(&$fields = null)
    {
        $set = [];
        if ($fields === null) {
            $fields = $_POST;
        }
        if (isset($this->table['filters']) && !isset($this->table['override_filters'])) {
            foreach ($this->table['filters'] as $k => $f) {
                if (!is_array($f)) {
                    if (isset($fields[$k])) {
                        $fields[$k] = $f;
                    }
                }
            }
        }
        $this->event('change', $fields);

        foreach ($fields as $key => $value) {
            if (array_key_exists($key, $this->table['fields'])) {
                $field = $this->table['fields'][$key];
                if ($this->fieldAttr($key, 'qcolumn') && !$this->fieldAttr($key, 'qtype')) {
                    continue;
                }
                if (in_array($this->fieldAttr($key, 'type'), ['joins','meta']) || $this->id() == $key) {
                    continue;
                }
                if ($this->fieldAttr($key, 'max') && $this->fieldAttr($key, 'max')< $key) {
                    continue;
                }
                if (Config::get('contentlog') && $this->fieldAttr($key, 'contentlog')) {
                    $xvalue = DB::value("SELECT `$key` FROM {$this->table['name']} WHERE {$this->id()}=?", [$fields[$this->id()]]);
                    if ($value !== $xvalue) {
                        DB::query("INSERT INTO contentlog(user_id,content,content_id,field,`data`) VALUES(?,?,?,?,?)", [
                        Session::userId(), $this->table['name'], $fields[$this->id()], $key, $xvalue
                        ]);
                    }
                }

                $value = $this->altValue($key, $value, false);
                if ($value === null || (empty($value) && $value!= 0 && isset($field['qtype']) && in_array(explode(' ', $field['qtype'])[0], ['INT','SMALLINT','TINYINT']))) {
                    $set[] = "`$key`=NULL";
                } else {
                    $set[] = "`$key`='" . DB::res($value) . "'";
                }
            }
        }
        if (isset($this->table['unix_times']) && $this->table['unix_times'] === true) {
            $set[] = 'updated_at=' . time();
        }
        if ($set != []) {
            return ' SET ' . implode(',', $set);
        }
        return '';
    }

    public function updateJoins($id, &$fields = null)
    {
        if ($fields === null) {
            $fields = $_POST;
        }

        foreach ($fields as $key => $value) {
            if (@$this->table['fields'][$key]['type'] === 'joins') {
                $jt = $this->table['fields'][$key]["join_table"] ?? $this->table['fields'][$key]["jt"];
                $arrv = explode(",", $value);
                DB::query("DELETE FROM {$jt[0]} WHERE `{$jt[1]}`=?;", [$id]);
                foreach ($arrv as $arrv_k => $arrv_v) {
                    DB::query("INSERT INTO {$jt[0]}(`{$jt[1]}`,`{$jt[2]}`)
          VALUES(?,?);", [$id,$arrv_v]);
                }
                continue;
            }
        }
    }

    public function updateMeta($id, &$fields = null)
    {
        if ($fields === null) {
            $fields = $_POST;
        }

        foreach ($fields as $key => $value) {
            if (@$this->table['fields'][$key]['type'] === 'meta') {
                list($mt, $vt) = $this->getMT($key);
                if (is_string($value)) {
                    if (@$this->table['fields'][$key]['values'] === 1) {
                        $arrv = [$value];
                    } elseif ($value[0] == '[' || $value[0] == '{') {
                        $arrv = json_decode($value, true);
                    } elseif ($value !== null) {
                        $arrv = explode(',', $value);
                    }
                } elseif (is_numeric($value)) {
                    $arrv = [$value];
                } else {
                    $arrv = $value;
                }
                DB::query("DELETE FROM {$mt[0]} WHERE `{$mt[1]}`=? AND `{$mt[2]}`=?;", [$id,$vt]);
                if ($arrv) {
                    foreach ($arrv as $arrv_k => $arrv_v) {
                        if ($arrv_v != '' && $arrv_v != null) {
                            if ($allowed = $this->fieldAttr($key, 'allow_tags')) {
                                $arrv_v = HtmlInput::purify($arrv_v, $allowed);
                            } else {
                                $arrv_v = strip_tags($arrv_v);
                            }
                            DB::query("INSERT INTO {$mt[0]}(`{$mt[1]}`,`{$mt[3]}`,`{$mt[2]}`)
              VALUES(?,?,?);", [$id,$arrv_v,$vt]);
                        }
                    }
                }
                continue;
            }
        }
    }

    public function getMT($key)
    {
        $vt = $this->table['fields'][$key]['meta_key'] ?? '';
        if (isset($this->table['fields'][$key]['meta_table'])) {
            $mt = $this->table['fields'][$key]['meta_table'];
        } elseif (isset($this->table['meta_table'])) {
            $mt = $this->table['meta_table'];
        } elseif (isset($this->table['metadata'])) {
            $mt = ['metadata', 'content_id', 'metakey', 'metavalue'];
        }
        return [$mt, $vt];
    }

    public function where($fields = null)
    {
        $filters = [];
        $left_join = '';
        if (isset($this->table['left_join'])) {
            foreach ($this->table['left_join'] as $t => $on) {
                $left_join .= ' LEFT JOIN ' . $t . ' ON ' . $on;
            }
        }
        if (!empty($this->table['where'])) {
            $filters[] = $this->table['where'];
        }
        if (empty($fields) && empty($filters)) {
            return $left_join;
        }
        if (is_string($fields)) {
            parse_str($fields, $fields);
        }
        if (isset($this->table['filters']) && !isset($this->table['override_filters'])) {
            foreach ($this->table['filters'] as $k => $f) {
                if (!is_array($f)) {
                    $fields[$k] = $f;
                } else {
                    if (is_array($fields[$k])) {
                        foreach ($f as $k1 => $f1) {
                            $fields[$k][$k1] = $f1;
                        }
                    }
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (isset($this->table['fields'][$key]['options']) && $value == 'null') {
                continue;
            }
            if (!is_numeric($key)) {
                if (array_key_exists($key, $this->table['fields'] ?? [])) {
                    $n = explode('.', $key);
                    if (isset($n[1])) {
                        $value = [$n[1]=> $value];
                    }
                    if (is_array($value)) {
                        $key = $this->getColumnKey($key, false);
                        foreach ($value as $_key => $_value) {
                            if (is_string($_value)) {
                                $subvalue = DB::res($_value);
                            } else {
                                $subvalue = $_value;
                            }
                            if (isset(self::$basicOps[$_key]) && ($_key== '<>' || !empty($subvalue))) {
                                if (is_string($subvalue) && $subvalue[1]== '?') {
                                    View::render('404.php');
                                    exit;
                                }
                                $filters[] = $key . self::$basicOps[$_key] . $subvalue;
                                continue;
                            }
                            if ($_key === 'gts') {
                                $filters[] = "$key>'$subvalue'";
                            }
                            if ($_key === 'lts') {
                                $filters[] = "$key<'$subvalue'";
                            }
                            if ($_key === 'begin') {
                                $filters[] = "$key like '$subvalue%'";
                            }
                            if ($_key === 'end') {
                                $filters[] = "$key like '%$subvalue'";
                            }
                            if ($_key === 'has') {
                                $filters[] = "$key like '%$subvalue%'";
                            }
                            if ($_key === 'in') {
                                if (is_array($subvalue)) {
                                    foreach ($subvalue as $i => $v) {
                                        if (is_string($v)) {
                                                $subvalue[$i] = "'$v'";
                                        }
                                    }
                                    $subvalue = DB::res(implode(',', $subvalue));
                                }
                                $filters[] = "$key IN($subvalue)";
                            }
                            if ($_key === '!in') {
                                $filters[] = "$key NOT IN($subvalue)";
                            }
                            if ($_key === 'inset') {
                                $filters[] = "FIND_IN_SET('$subvalue', $key)>0";
                            }
                            if ($_key === 'not') {
                                $filters[] = "$key!='$subvalue'";
                            }
                            if ($_key === 'is') {
                                if ($subvalue == 'empty') {
                                    $filters[] = "($key IS NULL OR $key='')";
                                }
                                if ($subvalue == 'null') {
                                    $filters[] = "$key IS NULL";
                                }
                            }
                        }
                    } elseif (@$this->table['fields'][$key]['type'] == 'meta') {
                        $key = $this->getColumnKey($key, false);
                        if ($value == null) {
                              $filters[] = "$key IS NULL";
                        } else {
                            $value = DB::res($value);
                            $filters[] = "FIND_IN_SET('$value', $key)>0";
                        }
                    } else {
                        $ckey = $this->getColumnKey($key, false);
                        if (@$this->table['fields'][$key]['type'] == 'date') {
                            $ckey = "SUBSTRING($key,1,10)";
                        }
                        if ($value === null) {
                            $filters[] = "$ckey IS NULL";
                        } else {
                            $value = DB::res($value);
                            $filters[] = "$ckey='$value'";
                        }
                    }
                }
            }
        }

        if (!empty($fields['search'])) {
            $terms = $this->table['search_exact'] ? [$fields['search']] : explode(' ', trim($fields['search']));
            foreach ($terms as $value) {
                $value = DB::res($value);
                $search_filter = [];
              //if (isset($this->table['fulltext'])) {
              //  $search_filter[] = "MATCH(ft_key) AGAINST('{$value}')";
              //}
                foreach ($this->getFields('search') as $key => $field) {
                    if (isset($field['fulltext'])) {
                        $search_filter[] = "(MATCH(" . $field['fulltext'] . ") AGAINST('{$value}'))";
                    }
                    if (isset($field['search_query'])) {
                        $search_filter[] = $field['search_query'] . " LIKE '%{$value}%'";
                    } elseif (isset($field['qcolumn'])) {
                        $search_filter[] = $field['qcolumn'] . " LIKE '%{$value}%'";
                    } elseif (!isset($field['meta_key'])) {
                        $key = $this->getColumnKey($key, false);
                        $search_filter[] = "$key LIKE '%{$value}%'";
                    }
                }
                $filters[] = '(' . implode(' OR ', $search_filter) . ')';
            }
        }

        $where = '';
        if ($filters != []) {
            $where = ' WHERE ' . implode(' AND ', $filters);
        }

        return $left_join . $where;
    }

    public function require($action, $field = null)
    {
        if (!self::can($action, $field)) {
            http_response_code(403);
            exit;
        }
    }

    public function can($action, $field = null)
    {
        $array = $this->table['permissions'][$action] ?? [];

        if ($field != null && isset($this->table['fields'][$field]['permissions'])) {
            $array = $this->table['fields'][$field]['permissions'][$action];
        }

        if (is_bool($array)) {
            if (Session::userId() == 0) {
                return  $this->table['permissions']['public'] ?? false;
            }
            return $array;
        }
        if (!is_array($array)) {
            if (is_callable($array)) {
                return $array();
            }
            $array = explode(' ', $array ?? '');
        }

        foreach ($array as $value) {
            if (in_array($value, $this->permissions)) {
                return true;
            }
        }

        return false;
    }

    public function update()
    {
        trigger_error('Table::update() is obsolate', E_USER_WARNING);
        return true;
    }

    public function updateRow($id, $data)
    {
        if (is_array($id) && !is_array($data)) {
            $tmp = $data;
            $data = $id;
            $id = $tmp;
        }
        $set = $this->set($data);
        DB::query("UPDATE {$this->name()}{$set} WHERE {$this->id()}=?;", $id);
        $this->updateMeta($id, $data);
        $this->updateJoins($id, $data);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getFields($output = '')
    {
        if ($output === '') {
            return $this->table['fields'];
        }
        $fields = [];
        foreach ($this->fields($output) as $fkey) {
            $fields[$fkey] = $this->table['fields'][$fkey];
        }
        return $fields;
    }

    public function getEmpty()
    {
        $row = [];
        foreach ($this->fields('create') as $key) {
            $fv = '';
            $field = $this->table['fields'][$key];
            if (isset($field['default'])) {
                $fv = is_callable($field['default']) ? $field['default']() : $field['default'];
            } elseif (isset($field['type'])) {
                if (isset($field['options'])) {
                    $fv = '0';
                }
                if ($field['type'] === 'date') {
                    $fv = date('Y-m-d');
                }
                if ($field['type'] === 'time') {
                    $fv = time();
                }
                if ($field['type'] === 'number') {
                    $fv = '0';
                }
            }
            $row[] = $fv;
            $row[$key] = $fv;
        }
        return $row;
    }

    public function getMeta($id, $type = null)
    {
        if (!isset($this->table['meta_table'])) {
            return null;
        }
        $m = $this->table['meta_table'];
        if ($type !== null) {
            return DB::getList("SELECT {$m[3]} FROM {$m[0]}
        WHERE {$m[1]}=? AND $m[2]=?;", [$id, $type]);
        } else {
            $list = [];
            $gen = DB::gen("SELECT {$m[2]},{$m[3]} FROM {$m[0]} WHERE {$m[1]}=?;", $id);
            foreach ($gen as $row) {
                @$list[$row[0]][] = $row[1];
            }
            return $list;
        }
    }

    public function getByTimeRange($column, $start, $end)
    {
        if (is_string($start)) {
            $start = strtotime($start);
        }
        if (is_string($end)) {
            $end = strtotime($end);
        }
        $filters = [$column => [
            'ge' => $start,
            'lt' => $end,
        ]];
        return $this->getRows($filters);
    }

    public function getRow($filters, $args = [])
    {
        $args['limit'] = 1;
        return $this->getRows($filters, $args)[0] ?? null;
    }

    public function getRows($filters = [], $args = [])
    {
        if (!$this->can('read')) {
            return [];
        }

        $orderby = isset($args['orderby']) ? $this->orderby($args['orderby']) : $this->orderby();
        $limit = isset($args['limit']) ? $this->limit($args['limit']) : $this->limitPage($args);
        $where = $this->where($filters);
        if (isset($args['groupby'])) {
            $groupby = $this->groupby($args['groupby']);
            $counter = ',COUNT(*) AS _total';
            $select = isset($args['select']) ? $this->selectsum($args['groupby'], $args['select']) : $this->selectsum($args['groupby']);
            $q = "SELECT $select$counter FROM {$this->name()}$where$groupby$orderby$limit;";
        } else {
            $select = isset($args['select']) ? $this->select($args['select']) : $this->select();
            $q = "SELECT $select FROM {$this->name()}$where$orderby$limit;";
        }
        $res = DB::getAssoc($q);
        if ($error = DB::error()) {
            self::$error = $error;
        }

        if (isset($this->getTable()['children']) && is_array($this->getTable()['children'])) {
            foreach ($this->getTable()['children'] as $key => $child) {
                if ($child['select'] ?? false || in_array($key, $args['select'] ?? [])) {
                    $table = new Table($key);
                    foreach ($res as &$row) {
                        $filter = [$child['parent_id'] => $row['id']];
                        $args = [];
                        if (isset($child['orderby'])) {
                            $args['orderby'] = $child['orderby'];
                        }
                        $row[$key] = $table->getRows($filter, $args);
                    }
                }
            }
        }
        if (isset($this->getTable()['fields'])) {
            foreach ($this->getTable()['fields'] as $key => $child) {
                if (isset($child['object'])) {
                          $table = new Table($child['table']);
                    foreach ($res as &$row) {
                        $id = $row[$key];
                        $row[$key] = $table->getRow(['id' => $id]);
                    }
                }
            }
        }

        $this->event('select', $res);
        return $res;
    }

    public function get($args = [])
    {
        return $this->getRows($args['where'] ?? [], $args);
    }

    public function getOne($args = [])
    {
        return $this->getRow($args['where'] ?? [], $args);
    }

    public function getWith($col, $val)
    {
        return $this->getRow([$col => $val]);
    }

    public function getPage($args = [])
    {
        $page = $args['page'] ?? ($_REQUEST['page'] ?? 1);
        $items = $this->getRows($args['where'] ?? $args, $args);
        $ppp = ($args['limit']?? $this->table['pagination']) ?? 25;
        $where = $this->where($args['where'] ?? $args);
        $groupby = $this->groupby($args['groupby'] ?? null);
        $totalPages = $page;
        if (count($items) == $ppp) {
            $total = DB::value("SELECT COUNT(*) FROM {$this->name()}$where$groupby;");
            $totalPages = ceil($total / $ppp);
        }

        return [
        'items' => $items,
        'page' => $page,
        'totalPages' => $totalPages,
        'total' => $total,
        ];
    }

    public function getRowsIndexed($filters = [], $args = [])
    {
        $rows = $this->getRows($filters, $args);
        foreach ($rows as &$row) {
            if (is_array($row)) {
                      $row = array_values($row);
            }
        }
        return $rows;
    }

    public function getAllRows($args = [])
    {
        return $this->getRows(null, $args);
    }

    public function totalRows(&$filters = [])
    {
        if (!$this->can('read')) {
            return;
        }
        $where = $this->where($filters);
        $res = DB::value("SELECT COUNT(*) FROM {$this->name()}$where;");
        return (int)$res;
    }

    public function deleteRow($id)
    {
        $this->event('delete', $id);
        if ($user_col = $gtable['filter_owner'] ?? null) {
            $row = $gtable->getWith('id', $id);
            if ($row[$user_col] != Session::userId() && !Session::hasPrivilege('admin')) {
                Log::debug(Session::key('user_email') . " tried to delete row " . $id . " from table " . $this->table['name'] . " without ownership");
                Response::error('You cannot delete this item', 200);
            }
        }
        $res = DB::query("DELETE FROM {$this->name()} WHERE {$this->id()}=?;", $id);
        if ($this->table['clean_residual'] ?? false) {
            foreach ($this->table['fields'] as $key => $field) {
                if ($field['type'] == 'meta') {
                          list($mt, $vt) = $this->getMT($key);
                          DB::query("DELETE FROM {$mt[0]} WHERE `{$mt[1]}`=? AND `{$mt[2]}`=?;", [$id, $vt]);
                }
            }
            foreach ($this->table['child_tables'] as $table => $field) {
                DB::query("DELETE FROM $table WHERE $field=?;", [$id]);
            }
        }
        Log::debug(Session::key('user_email') . " deleted row " . $id . " from table " . $this->table['name']);
    }

    public function create($data = [])
    {
        return $this->createRow($data);
    }

    public function save($data, $key)
    {
        $id = $this->getRow([$key => $data[$key]])['id']?? null;
        if (!$id) {
            $id = $this->createRow($data);
        } else {
            $this->updateRow($id, $data);
        }
        return $id;
    }

    public function createRow($data = [])
    {
        if ($this->can('create') === false) {
            self::$error = 'Missing permission to create';
            return 0;
        }
        $insert_fields = [];
        $insert_values = [];
        $binded_values = [];
        $this->event('create', $data);
        if ($data === false) {
            return 0;
        }

        foreach ($this->table['fields'] as $key => $field) {
            if (isset($field['qtype']) && isset($data[$key])) {
                if (in_array(explode(' ', $field['qtype'])[0], ['INT','SMALLINT','TINYINT']) && (!isset($data[$key]) || $data[$key]== null || $data[$key]== '')) {
                    continue;
                }
                if (isset($field['max']) && $field['max']< $data[$key]) {
                    continue;
                }
                $insert_fields[] = '`' . $key . '`';
                $data[$key] = $this->altValue($key, $data[$key]);
                $insert_values[] = $data[$key];
                $binded_values[] = '?';
            }
        }
        if (isset($this->table['unix_times']) && $this->table['unix_times'] === true) {
            $insert_fields[] = 'created_at';
            $insert_fields[] = 'updated_at';
            $binded_values[] = time();
            $binded_values[] = time();
        }
        $fnames = implode(',', $insert_fields);
        $binded = implode(',', $binded_values);
        $q = "INSERT INTO {$this->name()}($fnames) VALUES($binded);";
        if (!empty($insert_values)) {
            $res = DB::query($q, $insert_values);
        } else {
            $res = DB::query($q);
        }
        $data['id'] = DB::$insert_id;

        if (isset($data['meta'])) {
            if ($mt = $this->table['meta_table']) {
                foreach ($data['meta'] as $key => $arr) {
                    if (!is_array($arr)) {
                        $arr = [$arr];
                    }
                    foreach ($arr as $v) {
                        $q = "INSERT INTO {$mt[0]}({$mt[1]},{$mt[2]},{$mt[3]}) VALUES(?,?,?);";
                        $res = DB::query($q, [$data['id'], $key, $v]);
                    }
                }
            }
        }
        $this->event('created', $data);
        return $data['id'];
    }

    public function getContentPage($filters, $args)
    {
        $fieldlist = isset($args['id']) ? 'edit' : 'list';
        $result['fields'] = $this->fields($fieldlist);
        $result['rows'] = [];
        $res = $this->getRows($filters, array_merge($args, ['select' => $result['fields']]));

        foreach ($this->getFields($fieldlist) as $key => $field) {
            if (isset($field['parseInt'])) {
                foreach ($res as &$r) {
                    $r[$key] = (int)$r[$key];
                }
            }
        }
        $result['items'] = $res;
        foreach ($res as $r) {
            if (is_array($f)) {
                $result['rows'][] = array_values($r);
            }
        }
        if ($error = Table::$error) {
            $result['error'] = $error;
        }
        $total = count($result['items']);
        if ((!isset($args['page']) || $args['page'] < 2) && !isset($args['limit']) && $total < $this->itemsPerPage()) {
            $result['startIndex'] = 0;
            $result['totalRows'] = $total;
        } else {
            $result['startIndex'] = $this->startIndex($args);
            $result['totalRows'] = $this->totalRows($filters);
        }

        return $result;
    }

    public function altValue($key, $value, $new = true)
    {
        if ($this->fieldAttr($key, 'type') == 'json') {
            if (empty($value)) {
                $value = [];
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }
        if ($this->fieldAttr($key, 'type') == 'time') {
            if (empty($value)) {
                $value = null;
            }
            if (!is_numeric($value)) {
                date_default_timezone_set(Config::get('timezone'));
                $value = $value ? strtotime($value) : time();
            }
        }
        if ($this->fieldAttr($key, 'type') == 'slug' && is_string($value)) {
            $value = Slugify::text($value);
        }
        if ($allowed = $this->fieldAttr($key, 'allow_tags')) {
            $purify = $this->table['fields'][$key]['purify'] ?? true;
            if ($purify === true) {
                $value = $value = HtmlInput::purify($value, $allowed);
            }
        } elseif (is_string($value)) {
            $value = strip_tags($value);
        }
        if ($rules = $this->fieldAttr($key, 'rules')) {
            $value = Request::validateValue($value, $rules, $this->fieldAttr($key, 'title') ?? $key, $key, $new);
            if (isset(Request::$errors[0])) {
                Response::json([
                'success' => false,
                'error' => Request::$errors[0]
                ]);
            }
        }

        if ($value === '' && !$this->fieldAttr($key, 'empty')) {
            if ($def = $this->fieldAttr($key, 'default')) {
                $value = $def;
            }
        }

        return $value;
    }

    public function moveRow($id, $steps)
    {
        $field = $this->getTable()['sort'] ?? 'pos';
        $f = $this->getTable()['sort_filter'] ?? null;
        $filter = '';
        if ($f) {
            $fv = DB::value("SELECT $f FROM {$this->name()} WHERE {$this->id()}=?;", [$id]);
            $filter = "$f='$fv' AND ";
        }
        $x = DB::value("SELECT $field FROM {$this->name()} WHERE {$this->id()}=?;", [$id]);
        $y = $x + $steps;
        if ($x > $y) {
            DB::query("UPDATE {$this->name()} SET $field=$field+1 WHERE $filter $field>=? AND $field<?", [$y, $x]);
        }
        if ($x < $y) {
            DB::query("UPDATE {$this->name()} SET $field=$field-1 WHERE $filter $field<=? AND $field>?", [$y, $x]);
        }
        DB::query("UPDATE {$this->name()} SET $field=? WHERE {$this->id()}=?;", [$y, $id]);
        // if there is a duplicate position, autofix it
        if ($f) {
            $count = DB::value("SELECT id FROM {$this->name()} GROUP BY $f,$field ORDER BY COUNT(*) DESC;");
        } else {
            $count = DB::value("SELECT id FROM {$this->name()} GROUP BY $field ORDER BY COUNT(*) DESC;");
        }
        if ($count > 1) {
            error_log($this->name() . ' column pos had to be adjusted');
            if ($f) {
                $ids = DB::getList("SELECT id FROM {$this->name()} WHERE $f='$fv' ORDER BY $field ASC;");
            } else {
                $ids = DB::getList("SELECT id FROM {$this->name()} ORDER BY $field ASC;");
            }
            foreach ($ids as $i => $id) {
                DB::query("UPDATE {$this->name()} SET $field=? WHERE {$this->id()}=?;", [$i, $id]);
            }
        }
    }

    public function form($id = 0, $args = [], $list = 'create')
    {
        if ($id > 0) {
            $fields = $this->fields('edit');
            $ql = "SELECT {$this->select($fields)} FROM {$this->name()}{$this->where(['id'=>$id])};";
            $getFields = $this->getFields('edit');
            $values = DB::getOne($ql);
        } else {
            $getFields = $this->getFields($list);
            $values = $this->getEmpty();
            $filters = $this->getTable()['filters'] ?? [];
            foreach ($filters as $filter) {
                if (isset($args[$filter])) {
                    $values[$filter] = $args[$filter];
                }
            }
          // replace
        }
        $fieldValues = [];
        foreach ($getFields as $key => $field) {
            if (isset($field['type']) && $field['type'] == 'meta') {
                $fieldValues[$key] = explode(',', $values[$key]);
            }
        }

        return Form::html($getFields, $values);
    }
}
