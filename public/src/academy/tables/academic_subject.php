<?php

$table = [
    'name' => 'academic_subject',
    'title' => 'Subjects',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','upload_csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'permissions' => [
      'read' => ['admin','edit_students','teacher'],
      'create' => ['admin','edit_students'],
      'update' => ['admin','edit_students'],
      'delete' => ['admin','edit_students']
    ],
    'search_boxes' => ['grade_level'],
    'fields' => [
      'id' => [
        'edit' => false,
        'create' => false,
      ],
      'title' => [
        'qtype' => 'VARCHAR(255)',
      ],
      'grade_level' => [
        'title' => 'Nivel de grado',
        'qtype' => 'TINYINT',
        'input_type' => 'number',
        'input_style' => 'grid-column:span 2',
      ],
    ],
];
