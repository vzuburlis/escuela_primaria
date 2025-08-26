<?php

return [
  'name' => 'table_options',
  'title' => 'Table options',
  'pagination' => 15,
  'tools' => ['add_popup','csv'],
  'commands' => ['edit_popup','delete'],
  'id' => 'id',
  'lang' => 'core/lang/admin/',
  'permissions' => [
    'read' => ['admin'],
    'create' => ['admin'],
    'update' => ['admin'],
    'delete' => ['admin']
  ],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,
      'create' => false
    ],
    'table' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(120)'
    ],
    'user_id' => [
      'title' => 'User',
      'qtype' => 'INT UNSIGNED DEFAULT 0'
    ],
    'data' => [
      'title' => 'Data',
      'qtype' => 'TEXT',
      'input_type' => 'codemirror',
      'list' => false
    ]
  ],
];
