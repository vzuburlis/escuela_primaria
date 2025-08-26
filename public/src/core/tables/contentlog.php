<?php

return [
  'name' => 'contentlog',
  'title' => 'Content Logs',
  'pagination' => 15,
  'id' => 'id',
  'tools' => ['delete_all'],
  'bulk_actions' => ['delete'],
  'commands' => ['delete'],
  'delete_all_but' => 20,
  'lang' => 'core/lang/admin/',
  'qkeys' => ['content','content_id'],
  'permissions' => [
    'read' => ['admin', 'editor'],
    'create' => ['admin', 'editor'],
    'update' => ['admin', 'editor'],
    'delete' => ['admin', 'editor']
  ],
  'search_boxes' => ['content'],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'edit' => false,
      'create' => false
    ],
    'content' => [
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
    ],
    'content_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
    'field' => [
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
    ],
    'data' => [
      'style' => 'width:8%',
      'qtype' => 'LONGTEXT',
      'edit' => true,
      'create' => false,
      'show' => false,
    ],
    'created' => [
      'title' => 'Updated',
      'type' => 'date',
      'searchbox' => 'period',
      'edit' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ],
    'user_id' => [
      'title' => 'User',
      'edit' => false,
      'list' => false,
      'create' => false,
      'qtype' => 'INT UNSIGNED DEFAULT 0',
    ],
  ]
];
