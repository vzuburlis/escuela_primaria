<?php

return [
  'name' => 'links',
  'title' => 'Table links',
  'commands' => ['edit_popup','delete'],
  'tools' => ['add_popup'],
  'id' => 'id',
  'pagination' => 25,
  'search_box' => true,
  'indexes' => [
    'ind_source' => 'source_type,source_id',
    'ind_target' => 'target_type,target_id',
  ],
  'fields' => [
    'id' => [
      'create' => false,'edit' => false
    ],
    'source_type' => [
      'qtype' => 'VARCHAR(255)',
    ],
    'source_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
    'target_type' => [
      'qtype' => 'VARCHAR(255)',
    ],
    'target_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
  ]
];

// created_at DATETIME DEFAULT CURRENT_TIMESTAMP