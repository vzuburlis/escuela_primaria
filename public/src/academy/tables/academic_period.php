<?php

$table = [
    'name' => 'academic_period',
    'title' => 'Periodo',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'permissions' => [
      'read' => ['admin','edit_students'],
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
      ],
    ],
];
