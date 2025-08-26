<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class TableSchema
{
    public function __construct($name, $initRows = [])
    {
        if (empty($name)) {
            error_log('DB.php', 3, 'log/error_table_not_loaded.log');
        }

        $gtable = new Table($name);
        $table = $gtable->getTable();
      // UPDATE/CREATE TABLE
        self::update($table, $initRows);
    }

    public static function update($table, $initRows = [])
    {
        if (is_string($table)) {
            $gtable = new Table($name);
            $table = $gtable->getTable();
        }
        $tname = $table['name'] ?? null;
        if (empty($tname)) {
            return;
        }
        $id = $table['id'] ?? 'id';
        if ($table['sort_pos'] ?? null) {
            $table['fields']['pos'] = [
            'qtype' => 'INT DEFAULT 0',
            ];
        }

      // IF TABLE EXISTS
        $tables = DB::getList("SHOW TABLES;");
        $tableExists = in_array($tname, $tables);
      // CREATE TABLE
        $qtype = $table['fields'][$id]['qtype'] ?? 'INT NOT NULL AUTO_INCREMENT';
        $q = "CREATE TABLE IF NOT EXISTS $tname($id $qtype,PRIMARY KEY (`$id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        DB::query($q);

      // DESCRIBE
        $rows = DB::getRows("DESCRIBE $tname;");
        $dfields = [];
        foreach ($rows as $row) {
            $dfields[$row[0]] = [
            'type' => $row[1],
            'null' => $row[2],
            'key' => $row[3]
            ];
        }

      // ADD COLUMNS
        if (isset($table['unix_times']) && $table['unix_times'] === true) {
            $table['fields']['created_at'] = ['qtype' => 'INT UNSIGNED'];
            $table['fields']['updated_at'] = ['qtype' => 'INT UNSIGNED'];
        }
        foreach ($table['fields'] as $fkey => $field) {
            if (isset($field['qtype']) && $fkey != $id) {
                $qtype = $field['qtype'];
                $column = $field['qname'] ?? $fkey;
                if (strpos($column, '(') !== false) {
                    continue;
                }
                if (!isset($dfields[$fkey])) {
                    $q = "ALTER TABLE $tname ADD `$column` {$qtype};";
                    DB::query($q);
                } else {
                    $_type = $dfields[$fkey]['type'];
                    if ($_type != substr(strtolower($field['qtype']), 0, strlen($_type))) {
                        $q = "ALTER TABLE $tname MODIFY `$column` {$qtype};";
                        DB::query($q);
                    }
                }
            }
        }

        // ADD KEYS
        if (isset($table['qkeys'])) {
            foreach ($table['qkeys'] as $key) {
                if (empty($dfields[$key]['key'])) {
                      $q = "ALTER TABLE $tname ADD KEY `$key` (`$key`);";
                      DB::query($q);
                }
            }
        }
        // ADD INDEXES
        if (isset($table['indexes'])) {
            foreach ($table['indexes'] as $key=>$index) {
                if (empty($dfields[$key]['key'])) {
                      $q = "CREATE INDEX $key ON $tname($index);";
                      DB::query($q);
                }
            }
        }

        // CREATE META TABLE
        if (isset($table['meta_table'])) {
            $m = $table['meta_table'];
            $q = "CREATE TABLE IF NOT EXISTS `{$m[0]}`(id INT NOT NULL AUTO_INCREMENT,
        `{$m[1]}` INT DEFAULT NULL,
        `{$m[2]}` VARCHAR(80) DEFAULT NULL,
        `{$m[3]}` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (`id`),  KEY `{$m[1]}` (`{$m[1]}`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            DB::query($q);
        }
        if (isset($table['metadata'])) {
            $q = "CREATE TABLE IF NOT EXISTS `metadata`(id INT NOT NULL AUTO_INCREMENT,
        `content_id` INT DEFAULT NULL,
        `metakey` VARCHAR(255) DEFAULT NULL,
        `metavalue` TEXT DEFAULT NULL,
        PRIMARY KEY (`id`),  KEY `content_id` (`content_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            DB::query($q);
        }

        // INITIAL ROWS
        if ($tableExists === false && !empty($initRows)) {
            $gtable = new Table($tname);
            foreach ($initRows as $row) {
                $gtable->createRow($row);
            }
        }
    }
}
