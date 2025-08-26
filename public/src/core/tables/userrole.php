<?php

return [
  'name' => 'userrole',
  'title' => 'Roles',
  'id' => 'id',
  'tools' => ['add_popup','csv'],
  'csv' => ['id','userrole'],
  'lang' => 'core/lang/admin/',
  'commands' => ['edit_popup','delete'],
  'permissions' => [
    'create' => ['admin','admin_userrole'],
    'read' => ['admin','admin_userrole'],
    'update' => ['admin','admin_userrole'],
    'delete' => ['admin','admin_userrole']
  ],
  'search_box' => true,
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,
      'create' => false
    ],
    'userrole' => [
      'title' => 'Role',
      'qtype' => 'VARCHAR(80)'
    ],
    'level' => [
      'title' => 'Level',
      'qtype' => 'TINYINT DEFAULT 1',
      'type' => 'select',
      'options' => [0 => '0',1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => '10'],
      'input_style' => 'grid-column:span 1',
    ],
    'redirect_url' => [
      'title' => 'Redirect path',
      'qtype' => 'VARCHAR(255)',
      'rules' => 'maxlength:255',
      'maxlength' => '255',
      'input_style' => 'grid-column:span 2',
      'show' => false,
    ],
    'description' => [
      'title' => 'Description',
      'qtype' => 'VARCHAR(255)',
      'rules' => 'maxlength:255',
      'maxlength' => '255',
    ],
    'users' => [
      'title' => 'Users',
      'qcolumn' => "(SELECT COUNT(*) FROM user,usermeta WHERE user_id=user.id AND vartype='role' AND `value`=userrole.id)",
      'eval' => "cv='<a href=\"admin/users?userrole='+item.id+'\">'+item.users+' " . __('Users') . " → </a>'",
      'edit' => false,
      'create' => false,
    ]
  ]
];
