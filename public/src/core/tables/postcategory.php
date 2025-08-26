<?php

return [
  'name' => 'postcategory',
  'title' => 'Categories',
  'tools' => ['add_popup','csv'],
  'commands' => ['edit_popup','delete'],
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
      'title' => [
        'title' => 'Name',
        'qtype' => 'VARCHAR(80) DEFAULT NULL'
      ],
      'slug' => [
        'title' => 'Route',
        'title' => 'Slug',
        'qtype' => 'VARCHAR(120) DEFAULT NULL'
      ],
      'description' => [
        'title' => 'Description',
        'list' => false,
        'input_type' => 'textarea',
        'qtype' => 'VARCHAR(200) DEFAULT NULL'
      ],
      'featured' => [
        'title' => 'Featured',
        'type' => 'checkbox',
        'qtype' => 'VARCHAR(1)',
        'default' => 1,
      ]
  ],
  'events' => [
    ['change', function (&$row) {
        if (empty($row['slug'])) {
            $row['slug'] = Slugify::text($row['title']);
        }
    }]
  ]
];
