<?php

return [
  'name' => 'notification_type',
  'id' => 'id',
  'metadata' => true,
  'permissions' => [
    'read' => ['admin'],
    'create' => ['admin'],
    'update' => ['admin'],
    'delete' => ['admin']
  ],
  'fields' => [
    'id' => [],
    'type' => [
      'qtype' => 'VARCHAR(30)',
      'maxlength' => '30',
    ],
    'description' => [
      'qtype' => 'VARCHAR(255)',
      'maxlength' => '255',
    ],
    'roles' => [
      'type' => 'meta',
      'input_type' => 'v-select-multiple',
      'create' => false,
      'meta_key' => 'notification_type_role',
      'qoptions' => "SELECT `id`,`userrole` FROM `userrole`",
    ],
    'email' => [
      'qtype' => 'TINYINT DEFAULT 0',
      'type' => 'checkbox',
    ],
    'autoremove' => [
      'qtype' => 'TINYINT DEFAULT 0',
      'options' => [
        0 => 'Never',30 => '30 Days',180 => '180 Days',
      ]
    ]
  ]
];
