<?php

$academic_year_id = $_GET['academic_year_id'] ?? DB::value("SELECT MAX(id) FROM academic_year;");

$table = [
    'extends' => 'user',
    'title' => 'Padres y tutores',
    'pagination' => 25,
    'id' => 'id',
    'lang' => 'core/lang/admin/',
    'meta_table' => ['usermeta', 'user_id', 'vartype', 'value'],
    'search_boxes' => [],
    'permissions' => [
      'read' => ['admin','edit_parents'],
      'create' => ['admin','edit_parents'],
      'update' => ['admin','edit_parents'],
      'delete' => ['admin','edit_parents']
    ],
    'replaces' => [
      'invite' => [],
      'tools' => ['add_popup','upload_csv'],
      'commands' => ['email','edit_side','delete'],
      'children' => null,
      'upload_csv' => ['username','email'],
      'csv' => ['username','email'],
    ],
    'filters' => [
      'userrole' => Config::get('academy.parent_role'),
    ],
    'override_filters' => true,
    'fields' => [
      'username' => [
        'title' => 'Name',
        'qtype' => 'VARCHAR(80)',
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
        'href' => 'this.command(\'select_groups\',{id})',
        'show' => false,
        'edit' => false,
      ],
      'students' => [
        'title' => 'Estudiantes',
        'type' => 'meta',
        'edit' => true,
        'create' => false,
        'meta_key' => 'user_tutee',
        'options' => [],
        'qoptions' => "(SELECT user.id, CONCAT(usergroup,' ',username) FROM user,usergroup,user_group WHERE user_group.user_id=user.id AND user_group.group_id=usergroup.id AND academic_year_id=$academic_year_id ORDER BY CONCAT(usergroup,' ',username) ASC)",
      ],
      'firstgroup' => [
        'create' => false,
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
      'pass' => [
        'list' => false,
        'create' => false,
        'edit' => true,
      ],
      'email' => [
        'list' => true,
        'create' => true,
        'edit' => true,
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
    ],
    'events' => [
      ['create', function (&$row) {
        if ($role = Config::get('academy.parent_role')) {
            $row['firstrole'] = $role;
        }
        if (empty($row['active'])) {
            $row['active'] = 1;
        }
      }]
    ]
];

$table['children'] = [];

return $table;
