<?php

use Gila\DB;
use Gila\TableSchema;
use Gila\Table;

Table::$options = false;
$ctp = __DIR__ . '/tables/';
$tables = [
  'post', 'postcategory', 'page', 'redirect','user', 'usergroup',
  'user_group', 'userrole', 'user_notification', 'notification_type','sessions', 'widget',
  'menu', 'table_options','tableschema', 'metafield',
  'metafield_option', 'event_log', 'user_file','file_tag', 'contentlog',
  'blockslog', 'block', 'template', 'links', //'metadata',
];

foreach ($tables as $t) {
    TableSchema::update(include $ctp . $t . '.php');
}

TableSchema::update(include $ctp . 'contact.php');
Gila\Config::content('contact', 'core/tables/contact.php');
new TableSchema('contact');

Gila\Config::dir(LOG_PATH . '/stats');
Gila\Config::dir(LOG_PATH . '/debug');
Gila\Config::dir(LOG_PATH . '/cacheItem');

