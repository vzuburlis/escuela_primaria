<?php

return [
  'name' => 'user_notification',
  'id' => 'id',
  'permissions' => [
    'read' => ['admin'],
    'create' => ['admin'],
    'update' => ['admin'],
    'delete' => ['admin']
  ],
  'fields' => [
    'id' => [],
    'user_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
    'type' => [
      'qtype' => 'VARCHAR(30)',
      'max' => '30',
    ],
    'details' => [
      'qtype' => 'TEXT',
    ],
    'url' => [
      'qtype' => 'VARCHAR(255)',
      'maxlength' => '255',
    ],
    'unread' => [
      'qtype' => 'TINYINT DEFAULT 1',
    ],
    'created' => [
      'qtype' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    ]
  ]
];
