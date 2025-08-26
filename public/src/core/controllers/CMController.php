<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace core\controllers;

use Gila\User;
use Gila\Config;
use Gila\Form;
use Gila\Session;
use Gila\Router;
use Gila\Table;
use Gila\Request;
use Gila\Response;
use Gila\DB;
use Gila\FileManager;
use Gila\View;
use Gila\Controller;
use Gila\Email;
use Gila\Cache;
use Gila\Log;

/**
* Lists content types and shows grid content data
*/
class CMController extends Controller
{
    private $table;
    private $permissions;

    public function __construct()
    {
        @header('X-Robots-Tag: noindex, nofollow', true);
        $this->permissions = Session::permissions();
        $this->table = Router::param("t", 1);
        if ($this->table == 'crm_opportunity_activity') {
            $this->table = 'crm_activity';
        }
        if (!Table::exist($this->table)) {
            @http_response_code(404);
            exit;
        }
    }

    public function index_GET($table, $id = null)
    {
        header('Content-Type: application/json');
        $gtable = new Table($table, $this->permissions);
        $gtable->require('read');
        if ($id) {
            $res = $gtable->getRow(['id' => $id], $_GET);
        } else {
            $res = $gtable->getPage($_GET);
        }
        Response::success($res);
    }

    public function index_POST()
    {
        header('Content-Type: application/json');
        $post = $_POST;
        $return = [];
        foreach ($post as $k => $query) {
            $action = $query['action'];
            if ($action == 'list') {
                $return[$k] = self::list($query['table'], $query['filters'], $query);
            }
            if ($action == 'list_rows') {
                $return[$k] = self::list_rows($query['table'], $query['filters'], $query);
            }
            if ($action == 'describe') {
                $return[$k] = self::describe($query['table']);
            }
        }
        echo json_encode($return, JSON_PRETTY_PRINT);
    }

    public function describe_GET($table)
    {
        $gtable = new Table($this->table, $this->permissions);
        $gtable->require('read');
        $table = $gtable->getTable();
        foreach ($table['fields'] as &$field) {
            unset($field['qtype']);
            unset($field['qoptions']);
            unset($field['qcolumn']);
        }
        header('Content-Type: application/json');
        echo $table;
    }


  /**
  * Lists registries of content type
  */
    public function listAction()
    {
        header('Content-Type: application/json');
        echo json_encode(self::list($this->table, $_GET, $_GET), JSON_PRETTY_PRINT);
    }

    public function listAllAction()
    {
        header('Content-Type: application/json');
        $_GET['select'] = '*';
        echo json_encode(self::list($this->table, $_GET, $_GET), JSON_PRETTY_PRINT);
    }

    public function list($table, $filters, $args)
    {
        $gtable = new Table($table, $this->permissions);
        $gtable->require('read');
        $res = $gtable->getRows($filters, $args);
        return $res;
    }

    public function getAction()
    {
        header('Content-Type: application/json');
        $table = new Table($this->table, $this->permissions);
        $gtable->require('read');
        if ($id = Router::param("id", 2)) {
            $filter = array_merge($_REQUEST, [$table->id() => $id]);
            $row = $table->getRow($filter, $filter);
        } else {
            $row = $table->getRow($_REQUEST, $_REQUEST);
        }
        foreach ($table->getTable()['children'] as $key => $child) {
            $ctable = new Table($key);
            $filter = [$child['parent_id'] => $id];
            $row[$key] = $ctable->getRows($filter);
        }
        echo json_encode($row, JSON_UNESCAPED_UNICODE);
    }

    public function list_rows_GET()
    {
        $get = Request::get();
        unset($get['p']);
        unset($get['_tm']);
        header('Content-Type: application/json');
        $result = self::list_rows($this->table, $get, $get);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public static function list_rows($table, $filters, $args)
    {
        $gtable = new Table($table);
        $gtable->require('read');
        if (isset($gtable->getTable()['cache_min_time'])) {
            $cacheDuration = (int)$gtable->getTable()['cache_min_time'];
            @header('Cache-Control: max-age=' . $cacheDuration);
            @header('Expires: ' . gmdate('D, d M Y H:i:s', ($_SERVER['REQUEST_TIME'] + $cacheDuration)) . ' GMT');
        }
        $result = [
            '_tm' => $_GET['_tm'] ?? time() * 1000,
        ];

        $args['page'] = $args['page'] ?? 1;
        unset($filters['page']);

        if (isset($gtable->getTable()['cache_time']) && empty($filters) && $args==['page'=>1]) {
            $u = $gtable->getTable()['cache_uniques'] ?? [$gtable->name()];
            $json = Cache::remember('cm_page_u' . Session::userId() . $gtable->name(), $gtable->getTable()['cache_time'], function($u) use ($gtable, $args) {
                $result = $gtable->getContentPage($filters, $args);
                return json_encode($result, JSON_UNESCAPED_UNICODE);
            }, Config::mt($u));
            $result = json_decode($json, true);
            // test cache
            $result2 = $gtable->getContentPage($filters, $args);
            if ($result == $result2) {
                $result['cached'] = true;
                Log::debug('list_tows.cache ok');
            } else {
                Log::debug('list_tows.cache !!');
                return $result2;
            }

            return $result;
        } else {
            $result = $gtable->getContentPage($filters, $args);
        }

        return $result;
    }

    public function csvAction()
    {
        $gtable = new Table($this->table, $this->permissions);
        $orderby = Router::request('orderby', []);
        $gtable->require('read');

      // filename to be downloaded
        $filename = $this->table . date('Y-m-d') . ".csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $fields = $gtable->fields('csv');
        echo implode(',', $fields) . "\n";
        $ql = "SELECT {$gtable->select($fields)}
      FROM {$gtable->name()}{$gtable->where($_GET)}{$gtable->orderby($orderby)};";
        $res = DB::query($ql);
        while ($r = mysqli_fetch_row($res)) {
            foreach ($r as &$str) {
                $str = preg_replace("/\t/", '', $str);
                $str = preg_replace("/\r?\n/", '', $str);
                if ($str == 'null') {
                    $str = '';
                }
                if (!is_numeric($str)) {
                    if ($str[0] == '=' || $str[0] == '-' || $str[0] == '+' || $str[0] == '@') {
                        $str = '\'' . $str;
                    }
                    $str = '"' . strtr($str, ['"' => '""']) . '"';
                }
            }
            echo implode(',', $r) . "\n";
        }
    }

    public function get_empty_csvAction()
    {
        $gtable = new Table($this->table, $this->permissions);
        $gtable->require('create');
        $filename = $this->table . "-example.csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $fields = $gtable->fields('upload_csv');
        echo implode(',', $fields);
    }

    public function upload_csvAction()
    {
        $gtable = new Table($this->table, $this->permissions);
        $gtable->require('create');
        $lines = 0;
        $tfields = $gtable->getTable()['fields'];
        $filename = $_FILES["file"]["tmp_name"];
        $fields = $gtable->fields('upload_csv');
        $nfields = count($fields);
        $idField = $gtable->id();
        $uniqueK = $gtable->getTable()['unique'] ?? null;
        $inserted = 0;
        $foptions = [];
      // fill values from the table query, only csv/upload_csv
        $_cols = $gtable->getTable()['upload_csv'] ?? ($gtable->getTable()['csv'] ?? []);

        if (@$_FILES["file"]["size"] > 0) {
            $file = fopen($filename, "r");
            while (($row = fgetcsv($file, 10000, ",")) !== false) {
                $lines++;
                if ($lines == 1) {
                    $fieldIndex = array_flip($row);
                    $columns = $row;
                    continue;
                }
                if (count($row) < $nfields) {
                    continue;
                }
                $data = [];
                $values = [];
                $rowFields = [];
                foreach ($columns as $key) {
                    if ($key != $idField || !empty($row[$fieldIndex[$key]])) {
                        $rowFields[] = $key;
                        $str = $row[$fieldIndex[$key]];
                        if (is_string($str)) {
                                $str = mb_convert_encoding($str, 'utf-8');
                        }
                        $values[] = $str;
                        $data[$key] = $str;
                    }
                }

                foreach ($_cols as $key) {
                    if (empty($data[$key]) && !empty($_GET[$key])) {
                        $data[$key] = $_GET[$key];
                    }
                    if (isset($tfields[$key]['maxlength'])) {
                        $data[$key] = substr($data[$key], 0, $tfields[$key]['maxlength']);
                    }
                    if (!empty($data[$key]) && isset($tfields[$key]['options']) && !isset($tfields[$key]['options'][$data[$key]])) {
                        $sv = array_search($data[$key], $tfields[$key]['options']);
                        if ($sv === false) {
                            if (isset($tfields[$key]['options_table'])) {
                                $ot = $tfields[$key]['options_table'];
                                $aa = array_merge([$ot[2] => $data[$key]], $ot[3] ?? []);
                                DB::table($ot[0])->create($aa);
                                $_id = DB::$insert_id;
                                $tfields[$key]['options'][$_id] = $data[$key];
                                $data[$key] = $_id;
                            } else {
                                $data[$key] = 0;
                            }
                        } else {
                            $data[$key] = $sv;
                        }
                    }
                    if (empty($data[$key]) && isset($tfields[$key]['options']) && isset($tfields[$key]['options'][0])) {
                            $data[$key] = 0;
                    }
                }
                // create or update a row
                if (empty($row[$fieldIndex[$idField]])) {
                    if ($_field = $gtable->getTable()['copy_from_src'] && !empty($data[$_field])) {
                        $src = $data[$_field];
                        if (!FileManager::isImage($src)) {
                                continue;
                        }
                        $file = 'assets/uploads/' . time() . '.' . $ext;
                        copy($src, $file);
                        $data[$_field] = $file;
                    }

                    if (!$uniqueK) {
                        $id = $gtable->createRow($data);
                    } elseif (!empty($row[$fieldIndex[$uniqueK]])) {
                        $id = DB::value("SELECT id FROM {$gtable->name()} WHERE {$uniqueK}=?;", $row[$fieldIndex[$uniqueK]]);
                        if (!$id) {
                            $id = $gtable->createRow($data);
                        }
                    }
                } else {
                    $id = $row[$fieldIndex[$idField]] ?? null;
                    if ($uniqueK) {
                        $id = DB::value("SELECT id FROM {$gtable->name()} WHERE {$uniqueK}=?;", $row[$fieldIndex[$uniqueK]]);
                    }
                    if (!DB::value("SELECT id FROM {$gtable->name()} WHERE {$gtable->id()}=?;", $id)) {
                        $id = $gtable->createRow($data);
                    } else {
                        $set = $gtable->set($data);
                        if ($error = Table::$error) {
                            Response::error($error);
                        }
                        DB::query("UPDATE {$gtable->name()}{$set} WHERE {$gtable->id()}=?;", $id);
                    }
                    if (DB::error()) {
                          Response::error(DB::error());
                    }
                }
                if ($id) {
                    $gtable->updateMeta($id, $data);
                    $gtable->updateJoins($id, $data);
                }
            }
            fclose($file);
        }
        Response::success();
    }

  /**
  * Updates registries of content type
  */
    public function update_rowsAction()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $gtable = new Table($this->table, $this->permissions);
        $id = $_GET['id'] ?? 0;

        if ($id != 0 && $id != 'new' && $gtable->can('update')) {
            $id = $_GET['id'];
            if ($user_col = $gtable->getTable()['filter_owner'] ?? null) {
                $row = DB::getOne("SELECT $user_col FROM {$gtable->name()} WHERE id=?", [$id]);
                if ($row[$user_col] != Session::userId() && !Session::hasPrivilege('admin')) {
                      Response::error('You cannot edit this item', 200);
                }
            }
        } else {
            $id = $gtable->createRow($_POST);
            if ($id === 0) {
                Response::error(empty(Table::$error) ? DB::error() : Table::$error, 200);
            }
        }

        $result = [];
        $ids = explode(',', (string)$id);
        $result['ids'] = $ids;
        $result['fields'] = $gtable->fields();
        $result['items'] = [];
        $result['rows'] = [];

        foreach ($ids as $id) {
            $data = $_POST;
            if (isset($_GET['id']) && $id > 0) {
                $data[$gtable->id()] = $data[$gtable->id()] ?? $id;
                $gtable->event('update', $data);
                $set = $gtable->set($data);
                $q = "UPDATE {$gtable->name()}{$set} WHERE {$gtable->id()}=?;";
                $res = DB::query($q, $id);
            }
            $gtable->updateMeta($id, $data);
            $gtable->updateJoins($id, $data);

            if ($error = Table::$error) {
                Response::error($error, 200);
            }
            if (DB::error()) {
                $result['error'][] = DB::error();
            }
            $q = "SELECT {$gtable->select()} FROM {$gtable->name()} WHERE {$gtable->id()}=?;";
            $result['items'][] = $gtable->getRows([$gtable->id() => $id])[0];
            $result['rows'][] = $gtable->getRowsIndexed([$gtable->id() => $id])[0];
        }

        Config::setMt($gtable->name());
        echo json_encode($result);
    }

    public function join_table_GET()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('update');
        include __DIR__ . '/../views/admin/content_join_table.php';
    }

    public function join_table_POST()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('update');
        $id = $_GET['id'] ?? 0;
        $data = Request::post();
        $field = $gtable->getTable()['fields'][Request::get('field')] ?? null;
        $join_table = $field['join_table'] ?? null;
        $jtable = DB::table($join_table[0]);
        if (!$jtable) {
            Response::code(404);
        }

        $current_list = DB::get("SELECT id,{$join_table[2]} FROM {$join_table[0]}
      WHERE {$join_table[1]}=?", [$id]);
        foreach ($current_list as $row) {
            if (empty($data['selected'][$row[$join_table[2]]])) {
                DB::query("DELETE FROM {$join_table[0]} WHERE id=?", [$row['id']]);
            }
        }

        foreach ($data['selected'] as $_id => $v) {
            if ($v == 1) {
                $r = [
                    $join_table[1] => $id,
                    $join_table[2] => $_id,
                ];
                foreach ($data as $fkey => $arr) {
                    if ($fkey != 'selected') {
                        $r[$fkey] = $arr[$_id] ?? null;
                    }
                }
                if (
                    $jid = DB::value("SELECT id FROM {$join_table[0]}
      WHERE {$join_table[1]}=? AND {$join_table[2]}=?", [$id, $_id])
                ) {
                    $jtable->event('update', $r);
                    $set = $jtable->set($r);
                    $q = "UPDATE {$jtable->name()}{$set} WHERE {$jtable->id()}=?;";
                    $res = DB::query($q, $jid);
                } else {
                    $jtable->create($r);
                }
            }
        }

        Response::success([
        'items' => $gtable->getRows([$gtable->id() => $id]),
        'current_list' => $current_list,
        'selected' => $data['selected'],
        ]);
    }

    public function movePos_POST($table)
    {
        $gtable = new Table($table, $this->permissions);
        $gtable->moveRow($_GET['id'], (int)Request::post('steps'));
        Response::success();
    }

    public function create_POST($table)
    {
        header('Content-Type: application/json');
        if ($table == 'crm_opportunity_activity') {
            $table = 'crm_activity';
        }
        $gtable = new Table($table, $this->permissions);
        $data = Request::post();
        $id = $gtable->createRow($data);
        if ($row = $gtable->getRow(['id' => $id])) {
            Response::success($row);
        }
        Response::error('Post could not be created');
    }

    public function empty_rowAction()
    {
        header('Content-Type: application/json');
        $gtable = new Table($this->table, $this->permissions);
        $result['fields'] = $gtable->fields('create');
        $result['items'][0] = $gtable->getEmpty();
        Response::success($result);
    }

  /**
  * Insert new registry of content type
  */
    public function insert_row_POST()
    {
        header('Content-Type: application/json');
        $gtable = new Table($this->table, $this->permissions);
        $gtable->require('create');
        $result = [];
        $data = $_POST;

        if (isset($_POST['id'])) {
            $gtable->event('create', $data);
            $fields = $gtable->fields('clone');
            if (($idkey = array_search($gtable->id(), $fields)) !== false) {
                unset($fields[$idkey]);
            }
            $res = $gtable->getOne(['where' => [$gtable->id() => $_POST['id']], 'select' => $fields]);
            $id = $gtable->createRow($res);
            if ($id === 0) {
                Response::error('Row could not be created');
                exit;
            }
            if ($children = $gtable->getTable()['children']) {
                foreach ($children as $key => $child) {
                    if ($child['clone'] ?? false) {
                                $ctable = new Table($key);
                                $filter = [$child['parent_id'] => $_POST['id']];
                                $rows = $ctable->getRows($filter);
                        foreach ($rows as $row) {
                            $row[$child['parent_id']] = $id;
                            unset($row['id']);
                            $ctable->create($row);
                        }
                    }
                }
            }
        } else {
            $id = $gtable->createRow($data);
            if ($id === 0) {
                Response::error("Row was not created. Does this table exist?");
                exit;
            } else {
                $q = "UPDATE {$gtable->name()} {$gtable->set($data)} WHERE {$gtable->id()}=?;";
                DB::query($q, $id);
            }
        }

        $result['fields'] = $gtable->fields();
        $r = DB::getAssoc("SELECT {$gtable->select()} FROM {$gtable->name()} WHERE {$gtable->id()}=?;", $id)[0];
        foreach ($r as &$el) {
            if ($el == null) {
                $el = '';
            }
        }
        $result['items'] = [$r];
        $result['rows'] = [array_values($r)];
        Response::success($result);
    }

  /**
  * Delete registry of content type
  */
    public function delete_POST()
    {
        header('Content-Type: application/json');
        $gtable = new Table($this->table, $this->permissions);
        $data = Request::post();
        if ($gtable->can('delete')) {
            $ids = explode(',', $data['id']);
            foreach ($ids as $id) {
                $gtable->deleteRow($id);
            }
            Response::success(['id' => $data['id']]);
        } else {
            Response::error("Missing permissions to delete", 403);
        }
    }

    public function delete_all_POST()
    {
        header('Content-Type: application/json');
        $gtable = new Table($this->table, $this->permissions);
        $last = $gtable->getTable()['delete_all_but'] ?? null;
        if ($gtable->can('delete') && $last !== null) {
            if ($last > 0) {
                $ids = DB::getList("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT $last");
                $ids = implode(',', $ids);
                DB::query("DELETE FROM {$this->table} WHERE id NOT IN($ids);");
            } else {
                DB::query("DELETE FROM {$this->table};");
            }
            Response::success();
        } else {
            Response::error("Missing permissions to delete", 403);
        }
    }

    public function merge_POST()
    {
        header('Content-Type: application/json');
        $gtable = new Table($this->table, $this->permissions);
        $data = Request::post();
        $merge_but = $gtable->getTable()['merge_but'] ?? [];
        $merge_concat = $gtable->fields('merge_concat');
        $rows = [];
        $updates = [];
        if (!$gtable->can('delete')) {
            Response::error("Missing permissions to merge", 403);
        }
        $ids = explode(',', $data['id']);
        $max_id = 0;
        foreach ($ids as $id) {
            $rows[$id] = $gtable->getRow(['id' => $id]);
            $max_id = max($max_id, $id);
        }
        foreach ($rows as $row) {
            foreach ($merge_but as $fkey) {
                if ($rows[$max_id][$fkey] != $row[$fkey]) {
                          $fname = $gtable->getTable()['fields'][$fkey]['title'] ?? $fkey;
                          Response::error("Values for {$fname} should be the same", 403);
                }
            }
            foreach ($merge_concat as $fkey) {
                if ($rows[$max_id][$fkey] != $row[$fkey]) {
                          $updates[$fkey] .= ($rows[0][$fkey]['merge_delimiter'] ?? ',') . $row[$fkey];
                }
            }
        }
        if (!empty($updates)) {
            $set = $gtable->set($data);
            $q = "UPDATE {$gtable->name()}{$set} WHERE {$gtable->id()}=?;";
            $res = DB::query($q, $id);
        }
        $cildren = array_merge($gtable->getTable()['residual'] ?? [], $gtable->getTable()['children'] ?? []);
        foreach ($ids as $id) {
            if ($max_id != $id) {
                foreach ($children as $ckey => $child) {
                    $parent_id = $child['parent_id'] ?? null;
                    if ($parent_id) {
                        DB::query("UPDATE {$ckey} SET {$parent_id}=? WHERE {$parent_id}=?;", [$max_id, $id]);
                    }
                }
                DB::query("DELETE FROM {$this->table} WHERE id=?;", $id);
            }
        }
        Response::success(['id' => $data['id']]);
    }

    public function edit_link_GET()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $keys = array_keys($gtable->getTable()['fields']);
        $filter = [];
        foreach ($_GET as $f => $v) {
            if (in_array($f, $keys)) {
                      $filter[$f] = $v;
            }
        }
        $id = DB::value("SELECT id FROM {$gtable->name()} {$gtable->where($filter)};");
        if (!$id) {
            $id = $gtable->create($filter);
        }
        $_GET['id'] = $id;
        $this->edit_formAction();
    }

    public function edit_bulk_GET()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('update');
        $callback = Request::get('callback') ?? $t . '_action';
        $list = Request::get('list');
        $html = '';
        $id = Router::param("id", 2);

        $gtable->event('onedit', $_GET);
        $fields = $gtable->fields('edit');
        $ql = "SELECT {$gtable->select($fields)} FROM {$gtable->name()}{$gtable->where($_GET)};";
        $res = DB::getAssoc($ql)[0];
        if (!$res) {
            return;
        }
        $getFields = $gtable->getFields('edit');
        $values = $res;

        $fieldOptions = [];
        $fieldValues = [];
        foreach ($getFields as $key => $field) {
            if (isset($field['type']) && $field['type'] == 'meta') {
                $fieldValues[$key] = explode(',', $values[$key]);
            }
        }
        $html .= '<select class="form-control mb-3" onchange="g_form_popup_select_field(this.value)">';
        $html .= '<option disabled selected>' . Config::tr('Select field', ['es' => 'Elige campo']) . '</option>';
        foreach ($getFields as $key => $field) {
            $label = $field['label'] ?? ($field['title'] ?? $key);
            $html .= '<option value="' . $key . '">' . $label . '</option>';
        }
        $html .= '</select>';

        $html .= '<form id="' . $t . '-edit-item-form" data-table="' . $t . '" data-id="' . htmlentities($id) . '" class="notranslate"';
        $html .= ' action="javascript:' . $callback . '()" data-values=\'' . htmlentities(json_encode($fieldValues, JSON_UNESCAPED_UNICODE)) . '\'>';
        $html .= '<button style="position:absolute;top:-1000px"></button>';
        foreach ($getFields as $key => $field) {
            $html .= '<div class="edit-item-form bulk-edit bulk-edit-' . $key . '" style="display:none">';
            $field['label'] = false;
            $html .= Form::html([$key => $field], [$key => $values[$key]]);
            $html .= '</div>';
        }
        $html .= '</form>';
        echo $html;
    }

    public function edit_form_GET()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('update');
        $callback = Request::get('callback') ?? $t . '_action';
        $list = Request::get('list');
        $html = '';
        $id = Router::param("id", 2);
        $id = (int)$id;

        if ($id > 0) {
            $gtable->event('onedit', $_GET);
            $fields = $gtable->fields('edit');
            $ql = "SELECT {$gtable->select($fields)} FROM {$gtable->name()}{$gtable->where($_GET)};";
            $res = DB::getAssoc($ql)[0];
            $getFields = $gtable->getFields('edit');
            $values = $res;
        } else {
            $gtable->event('oncreate', $_GET);
            $getFields = $gtable->getFields($list ?? 'create');
            $values = $gtable->getEmpty();
            $filters = $gtable->getTable()['filters'] ?? [];
            foreach ($filters as $_key => $filter) {
                if (!is_array($filter) && isset($_GET[$_key])) {
                          $values[$_key] = $_GET[$_key];
                }
            }
          // replace
        }
        $fieldValues = [];
        foreach ($getFields as $key => $field) {
            if (isset($field['type']) && $field['type'] == 'meta') {
                $fieldValues[$key] = explode(',', $values[$key]);
            }
            if (isset($field['option_selectable'])) {
                $getFields[$key]['options'] = $field['option_selectable'];
            }
        }
        $html .= '<form id="' . $t . '-edit-item-form" data-table="' . $t . '" data-id="' . $id . '" class="notranslate"';
        $html .= ' action="javascript:' . $callback . '()" data-values=\'' . htmlentities(json_encode($fieldValues, JSON_UNESCAPED_UNICODE)) . '\'>';
        $html .= '<button style="position:absolute;top:-1000px"></button>';
        $html .= '<div class="edit-item-form">';
        $html .= Form::hiddenInput();
        $html .= Form::html($getFields, $values);

        $child_id = '<span id="edit_popup_child"></span>';
        foreach ($gtable->getTable()['children'] ?? [] as $ckey => $child) {
            $html .= $child_id;
            $ctable = new Table($child['table'] ?? $ckey, $this->permissions);
            $child_id = '';
            $html .= '<g-table v-if="id>0" gtype="' . $ckey . '" gchild=1 ';
            $html .= 'gtable="' . htmlentities(json_encode($ctable->getTable())) . '" ';
            $html .= 'gfields="' . htmlentities(json_encode($child['list'])) . '" ';
            $html .= ':gfilters="\'&amp;' . $child['parent_id'] . '=\'+id">';
            $html .= '</g-table>';
        }

        $html .= '</div></form>';
        echo $html;
    }

    public function select_rowAction()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('read');

        echo '<div id="gtable_select_row"><g-table ';
        echo 'gtable="' . htmlentities(json_encode($gtable->getTable())) . '" ';
        echo 'gfields="' . htmlentities(json_encode($gtable->fields('list'))) . '" ';
        echo 'gtype="' . $this->table . '">';
        echo '</g-table>';
        echo '</div>';
    }

    public function open_tableAction()
    {
        $t = htmlentities($this->table);
        $gtable = new Table($t, $this->permissions);
        $gtable->require('read');

        echo '<div id="gtable_open_table" class="p-2"><g-table ';
        echo 'gtable="' . htmlentities(json_encode($gtable->getTable())) . '" ';
        echo 'gfields="' . htmlentities(json_encode($gtable->fields('list'))) . '" ';
        if (isset($_POST)) {
            echo 'gfilters="&' . htmlentities(http_build_query($_POST)) . '" ';
        }
        echo 'gtype="' . $this->table . '">';
        echo '</g-table>';
        echo '</div>';
    }

    public function updateOptions_POST($table)
    {
        if (Session::userId() == 0) {
            Response::code(403);
        }
        $data = Request::post('data');
        $settings = [];
        foreach ($data as $i => $f) {
            $settings[$i] = [
            'show' => $f['show'],
            ];
        }
        $user_id = Session::userId();
        if (($_POST['default'] ?? false) && Session::hasPrivilege('admin')) {
            $user_id = 0;
        }
        $settings = json_encode($settings);
        if ($id = DB::value("SELECT id FROM table_options WHERE user_id=? AND `table`=?", [$user_id, $table])) {
            DB::query("UPDATE table_options SET `data`=? WHERE id=?", [$settings, $id]);
        } else {
            DB::query("INSERT INTO table_options(`table`,user_id,`data`) VALUES(?,?,?)", [$table, $user_id, $settings]);
        }
        Response::success();
    }

    public function removeOptions_POST($table)
    {
        DB::query("DELETE FROM table_options WHERE `table`=? AND user_id=?;", [$table, Session::userId()]);
        Response::success();
    }

    public function email_form_GET($table, $id)
    {
        $row = DB::table($table)->getOne(['where' => ['id' => $id]]);
        View::set('email', $row['contact_email'] ?? $row['email']);
        View::set('table', $table);
        View::set('id', $id);
        View::renderFile('admin/email-form');
    }

    public function email_form_POST($table, $id)
    {
        $data = Request::validate([
        'email' => 'email|required',
        'subject' => 'required',
        'message' => 'required',
        'signature_id' => '',
        'from_email' => 'email',
        ]);
        if ($user = DB::getOne("SELECT username as name FROM user WHERE email=? UNION SELECT `name` FROM contact WHERE email=?;", [$data['email'], $data['email']])) {
            $subs = Email::trData([
            'name' => $user['name'],
            'firstname' => ucwords(explode(' ', trim($user['name']))[0]),
            ]);
            $subs['{name}'] = $user['name'];
            $subs['{firstname}'] = ucwords(explode(' ', trim($user['name']))[0]);
            $data['message'] = strtr($data['message'], $subs);
            $data['subject'] = strtr($data['subject'], $subs);
        }
        $data['html'] = $data['message'];
        if (isset($data['signature_id']) && (int)$data['signature_id'] > 0) {
            $signature = DB::value("SELECT message FROM email_signature WHERE id=?", [$data['signature_id']]) ?? '';
            $data['html'] .= '<br>' . $signature;
        }
        $from = $data['from_email'] ?? Session::key('user_email');
        $data['from'] = ['name' => Session::key('user_name'), 'email' => $from];

        if (Config::inPackages('email-marketing') && $gtable = DB::table('crm_activity')) {
            $aid = $gtable->createRow(['type' => 'email', 'content' => $table, 'content_id' => (int)$id, 'user_id' => Session::userId(), 'title' => $data['subject'], 'description' => $data['html']]);
            $data['html'] .= '<img src="' . Config::get('base') . 'email-marketing/pxa/' . $aid . '.png?t=crm_activity" alt="">';
        }
        Email::send($data);
        Response::success(['message' => 'email sent']);
    }

    public function speed_test_GET()
    {
        global $starttime;
        $sq = ['bota','cha','van'];
        echo DB::value("SELECT count(*) FROM shop_product WHERE image IS NULL OR image='';") . '<br>';
        echo DB::value("SELECT id FROM shop_sku WHERE image IS NOT NULL;") . '<br>';
        for ($i = 0; $i < 10; $i++) {
            foreach ($sq as $s) {
                $res = DB::query("SELECT shop_stock.id,shop_product.image,`sku_id`,shop_product.title as title,`store_id`,`qty`,`min_qty` FROM shop_stock LEFT JOIN shop_sku ON sku_id=shop_sku.id LEFT JOIN shop_product ON shop_sku.product_id=shop_product.id
      WHERE (title LIKE '$$s%') ORDER BY title ASC LIMIT $i,20");
            }
        }
        $endtime = microtime(true);
        echo ' t=' . round($endtime - $starttime, 6);
        exit;
    }
}
