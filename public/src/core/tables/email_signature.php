<?php

return [
  'name' => 'email_signature',
  'title' => 'Signatures',
  'tools' => ['add_popup','csv'],
  'commands' => ['edit_popup','clone','delete'],
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
      'user_id' => [
        'title' => 'User',
        'qtype' => 'INT UNSIGNED',
        'type' => 'admin_user',
      ],
      'title' => [
        'title' => 'Name',
        'qtype' => 'VARCHAR(80) DEFAULT NULL'
      ],
      'message' => [
        'title' => 'Message',
        'list' => false,
        'input_type' => 'tinymce',
        'qtype' => 'TEXT DEFAULT NULL',
        'allow_tags' => true,
      ]
  ],
];
