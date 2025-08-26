<?php

return [
  'name' => 'metadata',
  'title' => 'Table meta',
  'commands' => ['edit_popup','delete'],
  'tools' => ['add_popup'],
  'id' => 'id',
  'pagination' => 25,
  'search_box' => true,
  'indexes' => [
    'ind_key' => 'content_id,metakey',
  ],
  'fields' => [
    'id' => [
      'create' => false,'edit' => false
    ],
    'content_id' => [
      'qtype' => 'INT UNSIGNED',
    ],
    'metakey' => [
      'qtype' => 'VARCHAR(255)',
    ],
    'metavalue' => [
      'qtype' => 'INT UNSIGNED',
    ],
  ]
];
