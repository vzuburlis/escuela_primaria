<?php

return [
  'name' => 'block',
  'title' => 'Blocks',
  'pagination' => 25,
  'id' => 'id',
  'tools' => ['add_popup','csv'],
  'commands' => ['edit_popup','delete'],
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
    'uid' => [
      'qtype' => 'VARCHAR(80)',
      'max' => 80,
    ],
    'instances' => [
      'qtype' => 'INT UNSIGNED DEFAULT 1',
      'edit' => false,
      'create' => false,
    ],
    'data' => [
      'qtype' => "TEXT",
      'type' => 'codemirror',
      'list' => false,
      'edit' => true,
      'allow_tags' => true,
    ],
  ],
];
