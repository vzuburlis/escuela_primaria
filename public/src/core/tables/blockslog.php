<?php

return [
  'name' => 'blockslog',
  'title' => 'Block Logs',
  'pagination' => 15,
  'id' => 'id',
  'tools' => ['add_popup','csv'],
  'lang' => 'core/lang/admin/',
  'qkeys' => ['content','content_id'],
  'permissions' => [
    'read' => ['admin', 'editor'],
    'create' => ['admin', 'editor'],
    'update' => ['admin', 'editor'],
    'delete' => ['admin', 'editor']
  ],
  'search_box' => true,
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'edit' => false,
      'create' => false
    ],
    'content' => [
      'qtype' => 'VARCHAR(80) DEFAULT NULL',
      'group' => 'title'
    ],
    'content_id' => [
      'qtype' => 'VARCHAR(80) DEFAULT NULL',
      'group' => 'title'
    ],
    'draft' => [
      'title' => 'Public',
      'style' => 'width:8%',
      'type' => 'checkbox',
      'edit' => true,
      'create' => false,
      'qtype' => 'TINYINT DEFAULT 0'
    ],
    'created' => [
      'title' => 'Updated',
      'type' => 'date',
      'searchbox' => 'period',
      'edit' => false,
      'list' => false,
      'create' => false,
      'qtype' => 'INT'
    ],
    'blocks' => [
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'LONGTEXT'
    ],
    'user_id' => [
      'title' => 'User',
      'edit' => false,
      'list' => false,
      'create' => false,
      'qtype' => 'INT UNSIGNED DEFAULT 1',
    ],
  ]
];
