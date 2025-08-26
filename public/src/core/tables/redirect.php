<?php

return [
  'name' => 'redirect',
  'title' => '301 Redirects',
  'pagination' => 15,
  'id' => 'id',
  'tools' => ['add_popup'],
  'commands' => ['edit_popup','delete'],
  'lang' => 'core/lang/admin/',
  'permissions' => [
    'read' => ['admin', 'web_editor'],
    'create' => ['admin', 'web_editor'],
    'update' => ['admin', 'web_editor'],
    'delete' => ['admin', 'web_editor']
  ],
  'search_box' => true,
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'edit' => false,
      'create' => false,
    ],
    'from_slug' => [
      'title' => 'From',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'rules' => 'maxlength:120',
    ],
    'to_slug' => [
      'title' => 'To',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'rules' => 'maxlength:120',
    ],
    'active' => [
      'style' => 'width:8%',
      'type' => 'checkbox',
      'default' => 1,
      'qtype' => 'TINYINT DEFAULT 1'
    ]
  ]
];
