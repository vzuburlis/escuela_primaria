<?php

return [
  'name' => 'email_template',
  'title' => 'Templates',
  'tools' => ['add_popup'],
  'commands' => ['edit_popup','delete'],
  'lang' => 'core/lang/admin/',
  'id' => 'id',
  'csv' => ['id','title'],
  'permissions' => [
    'read' => ['admin','editor'],
    'update' => ['admin','editor'],
    'create' => ['admin','editor'],
  ],
  'fields' => [
    'id' => [
      'edit' => false,
      'create' => false
    ],
    'title' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(80) DEFAULT NULL'
    ],
    'message' => [
      'title' => 'Message',
      //'helptext'=>'You can use &#123;&#123; name&#125;&#125; ',
      'show' => false,
      'input_type' => 'tinymce',
      'qtype' => 'TEXT DEFAULT NULL',
      'allow_tags' => true,
    ],
    'type' => [
      'qtype' => 'VARCHAR(20) DEFAULT NULL',
      'show' => false,
      'edit' => false,
      'create' => false,
    ]
  ],
];
