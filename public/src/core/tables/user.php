<?php

$table =  [
  'name' => 'user',
  'title' => 'Users',
  'pagination' => 15,
  'pagination_top' => true,
  'tools' => ['add_popup','csv'],
  'commands' => ['edit_popup','delete'],
  'clone' => ['username','about'],
  'bulk_actions' => FS_ACCESS ? ['edit'] : [],
  'id' => 'id',
  'lang' => 'core/lang/admin/',
  'meta_table' => ['usermeta', 'user_id', 'vartype', 'value'],
  'settings' => FS_ACCESS,
  'js' => ['src/core/tables/user.js'],
  'permissions' => [
    'read' => ['admin','admin_user'],
    'create' => ['admin','admin_user'],
    'update' => ['admin','admin_user'],
    'delete' => ['admin']
  ],
  'csv' => ['id','username','email'],
  'invite' => ['username','email','firstrole','firstgroup'],
  'search_box' => true,
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,
      'create' => false
    ],
    'photo' => [
      'type' => 'meta',
      'input_type' => 'media2',
      'display_type' => 'user_photo',
      'title' => 'Photo',
      'meta_key' => 'photo',
      'create' => false,
      'input_style' => 'grid-column:span 1',
      'single' => true,
    ],
    'username' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(80)',
      'width' => '20%',
      'input_style' => 'grid-column:span 2',
    ],
    'email' => [
      'title' => 'Email',
      'type' => 'email',
      'qtype' => 'VARCHAR(80) UNIQUE',
      'required' => true,
      'placeholder' => 'email@example.com',
      'width' => '20%',
      'input_style' => 'grid-column:span 2',
    ],
    'pass' => [
      'list' => false,
      'type' => 'password',
      'title' => 'Password',
      'qtype' => 'VARCHAR(120)'
    ],
    'userrole' => [
      'title' => 'Roles',
      'type' => 'meta',
      'input_type' => 'role',
      'edit' => true,
      'meta_key' => 'role',
      'options' => [],
      'qoptions' => "SELECT `id`,`userrole` FROM userrole",
      'input_style' => 'grid-column:span 2',
    ],
    'firstrole' => [
      'title' => 'Role',
      'edit' => true,
      'list' => false,
      'edit' => false,
      'create' => false,
      'type' => 'select',
      'search' => false,
      'options' => ['' => '-'],
      'qoptions' => "SELECT `id`,`userrole` FROM userrole ORDER BY userrole ASC",
      'input_style' => 'grid-column:span 1',
    ],
    'usergroup' => [
      'title' => 'Groups',
      'type' => 'meta',
      'edit' => true,
      'meta_key' => 'group',
      'options' => [],
      'qoptions' => ["id","usergroup","usergroup"],
      'width' => '15%',
      'input_style' => 'grid-column:span 2',
    ],
    'firstgroup' => [
      'title' => 'Group',
      'edit' => true,
      'list' => false,
      'edit' => false,
      'create' => false,
      'type' => 'select',
      'search' => false,
      'options' => ['' => '-'],
      'qoptions' => "SELECT `id`,`usergroup` FROM usergroup ORDER BY usergroup ASC",
      'input_style' => 'grid-column:span 1',
    ],
    'manager' => [
      'type' => 'meta',
      'title' => 'Manager',
      'list' => false,
      'meta_key' => 'manager_id',
      'options' => ['' => '-'],
      'qoptions' => 'SELECT `id`,`username` FROM user;',
      'input_style' => 'grid-column:span 2',
      'single' => true,
    ],
    'active' => [
      'type' => 'checkbox',
      'title' => 'Active',
      'qtype' => 'TINYINT DEFAULT 1'
    ],
    'created' => [
      'title' => 'Created',
      'type' => 'date',
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
      'width' => '20%',
    ],
    'updated' => [
      'title' => 'Updated',
      'type' => 'date',
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
      'width' => '20%',
    ],
    'language' => [
      'title' => 'Language',
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'VARCHAR(2)'
    ],
  ],
  'children' => [],
  'events' => [
    ['create', function (&$row) {
        if (!empty($row['email'])) {
            if (User::getByEmail($row['email'])) {
                Table::$error = __('Email already in use');
                $row = false;
                return;
            }
            if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                Table::$error = __('Wrong email format');
                $row = false;
                return;
            }
        }
    }],
    ['created', function (&$row) {
        if (!empty($row['firstrole'])) {
            $row['userrole'] = $row['firstrole'];
            User::meta($row['id'], 'role', $row['firstrole']);
        }
        if (isset($row['userrole']) && isset($row['email'])) {
            User::sendInvitation($row);
        }
        if (isset($row['firstgroup'])) {
            User::meta($row['id'], 'group', $row['firstgroup']);
            DB::table('user_group')->create(['user_id' => $row['id'], 'group_id' => $row['firstgroup'], 'expire_at' => time() + 30 * 84600]);
        }
    }],
    ['change', function (&$row) {
        if (isset($row['userrole'])) {
            $roles = is_array($row['userrole']) ? $row['userrole'] : explode(',', $row['userrole']);
            $level = Gila\Session::level();
            foreach ($roles as $roleId) {
                if ($level < Gila\User::roleLevel($roleId)) {
                    Response::code(500);
                }
            }
        }
        if (isset($row['pass']) && !empty($row['pass'])) {
            if (substr($row['pass'], 0, 7) != "$2y$10$") {
                $row['pass'] = Config::hash($row['pass']);
            }
        }
    }],
    ['update', function (&$row) {
        if (isset($row['pass']) && !empty($row['pass'])) {
            if (substr($row['pass'], 0, 7) != "$2y$10$") {
                $row['pass'] = Config::hash($row['pass']);
            }
        }
    }],
    ['delete', function ($id) {
        DB::query("DELETE FROM user_group WHERE id=?", [$id]);
    }]
  ]
];

if (Config::get('user_group') == 1) {
    $table['fields']['usergroup'] = [
    'title' => 'Groups',
    'edit' => false,
    'options' => [],
    'qcolumn' => "(SELECT GROUP_CONCAT(group_id) FROM user_group WHERE user_group.user_id=user.id)",
    'join_table' => ['user_group','user_id', 'group_id'],
    'qoptions' => ["id","usergroup","usergroup"],
    'join_list_fields' => ['expire_at'],
    'width' => '15%',
    'input_style' => 'grid-column:span 2',
    //'eval'=>"cv=cv+'<i class=\"fa fa-pencil\"></i>'",
    'href' => 'this.command(\'select_groups\',{id})',
    ];
    $table['children']['user_group'] = [
    'list' => ['id','group_id','expire_at'],
    'parent_id' => 'user_id',
    ];
}

if (FS_ACCESS) {
    $table['fields']['usergroup']['list'] = true;
    $table['fields']['userrole']['join_table'] = ['usermeta','user_id', 'group_id'];
    $table['fields']['userrole']['href'] = 'this.command(\'select_roles\',{id})';
}

return $table;
