<?php

return [
  'name' => 'option',
  'title' => 'Website options',
  'tools' => ['add_popup','csv'],
  'csv' => ['id','userrole'],
  'lang' => 'core/lang/admin/',
  'commands' => ['edit_popup','delete'],
  'permissions' => [
    'create' => ['admin'],
    'read' => ['admin'],
    'update' => ['admin'],
    'delete' => ['admin']
  ],
  'search_box' => true,
  'id' => 'id',
  'qkeys' => ['option'],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,'create' => false,
    ],
    'option' => [
      'title' => 'Options',
      'qtype' => 'VARCHAR(255)'
    ],
    'value' => [
      'title' => 'Value',
      'qtype' => 'TEXT',
    ],
  ]
];
