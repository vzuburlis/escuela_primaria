<?php

return [
  'name' => 'user_file',
  'title' => 'User files',
  'commands' => ['edit_popup','delete'],
  'tools' => ['add_popup'],
  'id' => 'id',
  'pagination' => 25,
  'search_box' => true,
  'search_boxes' => ['user_id'],
  'fields' => [
    'id' => [
      'create' => false,'edit' => false
    ],
    'user_id' => [
      'qtype' => 'INT UNSIGNED',
      'display_type' => 'user_photo',
      'object' => ['id','username','photo'],
      'table' => 'user',
      'show' => false,
    ],
    'path' => [
      'qtype' => 'VARCHAR(255)',
      'type' => 'media2',
      'display_type' => 'media',
    ],
    'size' => [
      'qtype' => 'INT UNSIGNED',
      'title' => 'Bytes',
    ],
    'used_at' => [
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
      'display_type' => 'Timedate',
    ],
    //'access_level'=>[
    //  'qtype'=>'TINYINT DEFAULT 0',
    //  'type'=>'select',
    //  'options'=>[0=>'Private', 1=>'Owner groups', 2=>'Public'],
    //  'option_colors'=>[0=>'red', 1=>'orange', 2=>'green'],
    //],
  ]
];
