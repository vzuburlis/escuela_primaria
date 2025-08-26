<?php

$academic_year_id = $_GET['academic_year_id'] ?? DB::value("SELECT MAX(id) FROM academic_year;");

$table = [
    'name' => 'academic_grade',
    'title' => 'Clasificaciónes',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'unix_times' => true,
    'search_boxes' => ['academic_year_id','group_id','period_id','subject_id'],
    'filters' => [
      'academic_year_id' => $academic_year_id,
    ],
    'permissions' => [
      'read' => true,//['admin','edit_students','teacher','parent'],
      'create' => ['admin'],
      'update' => ['admin'],
      'delete' => ['admin']
    ],
    'fields' => [
      'id' => [
        'title' => 'Level',
        'show' => false,
        'edit' => true,
        'create' => false,
      ],
      'grade_level' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Grado',
        'show' => false,
      ],
      'subject_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Materia',
      ],
      'period_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Periodo',
      ],
      'user_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Estudinte',
      ],
      'grade' => [
        'qtype' => 'DECIMAL(6,2)',
      ],
    ],
];

return $table;
