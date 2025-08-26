<?php

use Gila\DB;
use Gila\TableSchema;
use Gila\Table;

Table::$options = false;
$ctp = 'src/core/tables/';
$tables = [
  'post', 'postcategory', 'page', 'redirect','user', 'usergroup',
  'blockslog', 'block', 'menu', //'links',
];

foreach ($tables as $t) {
    TableSchema::update(include $ctp . $t . '.php');
}
