<?php

$table = [
    'extends' => 'user',
    'title' => 'Maestros',
    'pagination' => 25,
    'id' => 'id',
    'lang' => 'core/lang/admin/',
    'meta_table' => ['usermeta', 'user_id', 'vartype', 'value'],
    'search_boxes' => [],
    'permissions' => [
      'read' => ['admin','edit_teachers'],
      'create' => ['admin','edit_teachers'],
      'update' => ['admin','edit_teachers'],
      'delete' => ['admin','edit_teachers']
    ],
    'replaces' => [
      'invite' => [],
      'tools' => ['add_popup','upload_csv'],
      'commands' => ['email','edit_popup','delete'],
      'children' => null,
      'upload_csv' => ['username','email'],
      'csv' => ['username','email'],
    ],
    'filters' => [
      'userrole' => ['!in' => (Config::get('academy.student_role') ?? 0) . ',' . (Config::get('academy.parent_role') ?? 0)],
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
        'create' => true,
        'default' => Config::get('academy.teacher_role') ?? ''
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
        if ($role = Config::get('academy.teacher_role')) {
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
