<?php

return [
  'name' => 'metafield_option',
  'title' => 'Meta field options',
  'tools' => ['add_popup','csv'],
  'lang' => 'core/lang/admin/',
  'commands' => ['edit_popup','delete'],
  'permissions' => [
    'create' => ['admin'],
    'read' => ['admin'],
    'update' => ['admin'],
    'delete' => ['admin']
  ],
  'search_box' => true,
  'id' => 'id',
  'qkeys' => ['metafield_id'],
  'fields' => [
    'id' => [
      'show' => false,'create' => false,'edit' => false,
    ],
    'metafield_id' => [
      'title' => 'Metafield',
      'qtype' => 'INT UNSIGNED',
    ],
    'label' => [
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 80,
      'required' => true
    ],
    'image' => [
      'title' => 'Image',
      'qtype' => 'VARCHAR(255)',
      'type' => 'media2',
    ],
    'color' => [
      'input_type' => 'color',
      'qtype' => 'VARCHAR(10)',
      'default' => '#5cadfd',
    ],
  ],
  'events' => [
    ['create', function (&$row) {
        if (empty($row['metafield_id'])) {
            $row['metafield_id'] = $_REQUEST['metafield_id'] ?? DB::value("SELECT MAX(id) FROM metafield_option");
        }
    }]
  ]
];
