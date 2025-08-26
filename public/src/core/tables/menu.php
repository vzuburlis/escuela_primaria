<?php

return [
  'name' => 'menu',
  'title' => 'Menus',
  'id' => 'id',
  'fields' => [
    'id' => [
    ],
    'menu' => [
      'qtype' => 'VARCHAR(255) UNIQUE'
    ],
    'data' => [
      'qtype' => 'TEXT'
    ]
  ]
];
