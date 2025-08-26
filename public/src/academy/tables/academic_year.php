<?php

$table = [
    'name' => 'academic_year',
    'title' => Config::tr('Academic years', ['es' => 'Años académicos']),
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'permissions' => [
      'read' => true,
      'create' => ['admin','edit_students'],
      'update' => ['admin','edit_students'],
      'delete' => ['admin','edit_students']
    ],
    'fields' => [
      'id' => [
        'edit' => false,
        'create' => false,
      ],
      'name' => [
        'qtype' => 'VARCHAR(255)',
        'title' => 'Name',
      ],
      'periods' => [
        'qtype' => 'TINYINT',
        'title' => 'Periodos',
        'default' => 3,
        'input_type' => 'number',
        'required' => true,
      ],
    ],
];

return $table;
