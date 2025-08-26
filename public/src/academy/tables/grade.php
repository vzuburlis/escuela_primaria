<?php

$table = [
    'name' => 'grade',
    'title' => 'Calificacion',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'filters' => ['academic_year_id','period_id'],
    'permissions' => [
      'read' => ['admin'],
      'create' => ['admin'],
      'update' => ['admin'],
      'delete' => ['admin']
    ],
    'fields' => [
      'academic_year_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Materia',
      ],
      'user_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Student',
      ],
      'subject_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Materia',
      ],
      'period_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Periodo',
      ],
    ],
];
