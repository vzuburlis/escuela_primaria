<?php

use edu\models\User;

$table = [
    'extends' => 'usergroup',
    'title' => 'Grupos',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'permissions' => [
      'read' => ['admin','edit_students','teacher'],
      'create' => ['admin','edit_students'],
      'update' => ['admin','edit_students'],
      'delete' => ['admin','edit_students']
    ],
    'search_boxes' => ['academic_year_id'],
    'fields' => [
      'logo' => [
        'edit' => 'false',
        'show' => false,
        'create' => false,
      ],
      'description' => [
        'edit' => 'false',
        'show' => false,
        'create' => false,
      ],
      'grade_level' => [
        'title' => 'Nivel',
        'qtype' => 'TINYINT',
        'input_style' => 'grid-column:span 1',
      ],
      'users' => [
        'title' => 'Estudiantes',
        'eval' => "cv='<a href=\"/admin-students?&tab=0&usergroup='+item.id+'\">'+item.users+' " . Config::tr('Estudiantes') . " → </a>'",
        'edit' => false,
        'create' => false,
      ],
      'expire_at' => [
        'edit' => false,
        'create' => false,
        'list' => false,
      ],
      'academic_year_id' => [
        'title' => 'Año',
        'qtype' => 'INT UNSIGNED',
        'type' => 'select',
        'options' => ['' => 'N/A'],
        'qoptions' => 'SELECT id,`name` FROM academic_year',
        'required' => true,
        'input_style' => 'grid-column:span 2',
      ],
      'teacher_id' => [
        'title' => 'Maestro',
        'qtype' => 'INT UNSIGNED',
        'type' => 'select',
        'qoptions' => 'SELECT id, username FROM user WHERE id IN(' . implode(',', User::getIdsWithPermission('teacher')) . ')', //only admin users
      ],
    ],
];

return $table;
