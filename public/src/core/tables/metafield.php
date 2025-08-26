<?php

return [
  'name' => 'metafield',
  'title' => 'Meta fields',
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
  'qkeys' => ['content'],
  'fields' => [
    'id' => [
      'edit' => false,
      'create' => false,
    ],
    'content' => [
      'title' => 'Tabla',
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 255,
      'input_style' => 'grid-column:span 2',
    ],
    'name' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 255,
      'input_style' => 'grid-column:span 2',
    ],
    'fkey' => [
      'title' => 'Column key',
      'helptext' => 'Cannot change after',
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 255,
      'show' => false,
      //'edit'=>false,
      'input_style' => 'grid-column:span 2',
    ],
    'description' => [
      'title' => 'Description',
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 255,
    ],
    'type' => [
      'title' => 'Type',
      'qtype' => 'VARCHAR(255)',
      'type' => 'select',
      'options' => [
        'text' => 'Text',
        'textarea' => 'Rich text',
        'checkbox' => 'Checkbox',
        'select' => 'Select',
        'number' => 'Number',
        'date' => 'Date',
        'media' => 'Media',
        'gallery' => 'Gallery',
        'user' => 'Admin user',
        'id' => 'Search ID',
        'items' => 'Child items',
      ],
      'input_style' => 'grid-column:span 1',
    ],
    'span' => [
      'title' => 'Input size',
      'qtype' => 'VARCHAR(255)',
      'type' => 'select',
      'options' => ['' => 'Default',1 => 'span 1',2 => 'span 2',3 => 'span 3',4 => 'span 4',5 => 'span 5','max' => 'Max',],
      'input_style' => 'grid-column:span 1',
    ],
    'show_value' => [
      'title' => 'Display on table',
      'qtype' => 'TINYINT DEFAULT 1',
      'type' => 'checkbox',
      'input_style' => 'grid-column:span 1',
    ],
    'after' => [
      'title' => 'After column (key)',
      'qtype' => 'VARCHAR(255)',
      'input_style' => 'grid-column:span 1',
    ],
    'csv' => [
      'title' => 'Include in CSV',
      'qtype' => 'TINYINT DEFAULT 1',
      'type' => 'checkbox',
      'input_style' => 'grid-column:span 1',
    ],
    'maxlength' => [
      'title' => 'Max. length',
      'helptext' => 'Maximum letters for text',
      'qtype' => 'SMALLINT UNSIGNED DEFAULT 255',
      'type' => 'number',
      'default' => 255,
      'placeholder' => '∞',
      'input_style' => 'grid-column:span 1',
    ]
  ],
  'children' => [
    'metafield_option' => [
      'list' => ['id','label','image','color'],
      'parent_id' => 'metafield_id',
    ]
  ]
];
