<?php

return [
  'name' => 'file_tag',
  'title' => 'File tags',
  'commands' => ['edit_popup','delete'],
  'tools' => ['add_popup'],
  'id' => 'id',
  'pagination' => 25,
  'search_box' => true,
  'fields' => [
    'id' => [
      'create' => false,'edit' => false
    ],
    'file_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
    'tag' => [
      'qtype' => 'VARCHAR(50)',
      'maxlength' => 50
    ],
  ]
];
