<?php

$table = [
    'name' => 'student',
    'title' => 'Calificacion',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'search_boxes' => ['academic_year_id','grade_level','period_id'],
    'filters' => [
      'role' => 5,
    ],
    'permissions' => [
      'read' => ['admin'],
      'create' => ['admin'],
      'update' => ['admin'],
      'delete' => ['admin']
    ],
    'fields' => [
      'user_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Student',
      ],
      'grade_level' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Materia',
      ],
      'period_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Periodo',
        'show' => false,
      ],
      'academic_year_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Materia',
        'show' => false,
      ],
    ],
];

if (isset($_GET['period_id'])) {
    $subjects = DB::getList("SELECT * FROM academic_subject WHERE grade_level=?", [$_GET['period_id']]);
    foreach ($subjects as $subject) {
        $table['fields']['ev' . $i] = [
        'type' => 'meta',
        'title' => '<small>(0-5)<br>' . $grav . '</small>',
        'meta_table' => ['academic_grade', 'user_id', 'subject_id', 'grade'],
        'meta_key' => $subject,
        'td_style' => 'text-align:center',
        'inline_edit' => true,
        'default' => 0,
        'placeholder' => 01,
        'min' => 0,
        'max' => 10,
        'edit' => false,
        'create' => false,
        'contentlog' => true,
        ];
    }
}
