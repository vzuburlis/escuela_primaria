<?php

$table = [
    'extends' => 'user',
    'title' => 'Estudiantes',
    'pagination' => 25,
    'id' => 'id',
    'lang' => 'core/lang/admin/',
    'meta_table' => ['usermeta', 'user_id', 'vartype', 'value'],
    'search_boxes' => ['usergroup'],
    'permissions' => [
      'read' => ['admin','edit_students'],
      'create' => ['admin','edit_students'],
      'update' => ['admin','edit_students'],
      'delete' => ['admin','edit_students']
    ],
    'replaces' => [
      'invite' => [],
      'tools' => ['add_popup','upload_csv','csv'],
      'commands' => ['edit_popup','delete'],
      'children' => null,
      'upload_csv' => ['username','curp','usergroup'],
    ],
    'filters' => [
      'userrole' => Config::get('academy.student_role'),
    ],
    'override_filters' => true,
    'fields' => [
      'username' => [
        'title' => 'Name',
        'qtype' => 'VARCHAR(255)',
        'width' => '20%',
        'input_style' => 'grid-column:span 2',
      ],
      'usergroup' => [
        'title' => 'Groups',
        'create' => false,
        'options' => [],
        'qcolumn' => "(SELECT GROUP_CONCAT(group_id) FROM user_group WHERE user_group.user_id=user.id)",
        'join_table' => ['user_group','user_id', 'group_id'],
        'qoptions' => ["id","usergroup","usergroup"],
        'width' => '15%',
        'join_list_fields' => null,
        'input_style' => 'grid-column:span 2',
        //'eval'=>"cv=cv+'<i class=\"fa fa-pencil\"></i>'",
        'href' => 'this.command(\'select_groups\',{id})',
      ],
      'firstgroup' => [
        'create' => true,
      ],
      'userrole' => [
        'title' => 'Roles',
        'type' => 'meta',
        'input_type' => 'role',
        'create' => false,
        'meta_key' => 'role',
        'options' => [],
        'qoptions' => "SELECT `id`,`userrole` FROM userrole",
        'input_style' => 'grid-column:span 2',
        'list' => false,
        'edit' => false,
      ],
      'curp' => [
        'title' => 'CURP',
        'type' => 'meta',
        'input_type' => 'text',
        'create' => true,
        'meta_key' => 'user.curp',
        'input_style' => 'grid-column:span 2',
        'show' => true,
        'edit' => true,
      ],
      'pass' => [
        'list' => false,
        'create' => false,
        'edit' => false,
      ],
      'email' => [
        'list' => false,
        'create' => false,
        'edit' => false,
      ],
      'photo' => [
        'list' => false,
        'create' => false,
        'edit' => false,
      ],
      'manager' => [
        'list' => false,
        'create' => false,
        'edit' => false,
      ],
      'language' => [
        'list' => false,
        'create' => false,
        'edit' => false,
      ],
      'grade_level' => [
        'title' => 'Ult. nivel',
        'edit' => false,
        'create' => false,
        'qcolumn' => '(SELECT MAX(grade_level) FROM usergroup ug,user_group WHERE ug.id=group_id AND user_id=user.id)',
        'show' => false,
      ],
    ],
    'events' => [
      ['create', function (&$row) {
        if ($role = Config::get('academy.student_role')) {
            $row['firstrole'] = $role;
        }
        if (empty($row['pin'])) {
            $row['pin'] = rand(1, 9999);
        }
      }]
    ]
];

$table['children'] = [];

if (Config::get('academy.student_ogrades') == 2) {
    $table['fields']['pin'] = [
    'title' => 'PIN',
    'type' => 'meta',
    'input_type' => 'text',
    'create' => true,
    'meta_key' => 'user.pin',
    'input_style' => 'grid-column:span 1',
    'maxlength' => 6,
    'show' => true,
    'edit' => true,
    ];
}

return $table;
