<?php

$usersq = "(SELECT COUNT(*) FROM user,usermeta WHERE user_id=user.id AND vartype='group' AND `value`=usergroup.id)";
if (Config::get('user_group') == 1) {
    $usersq = "(SELECT COUNT(*) FROM user_group WHERE group_id=usergroup.id AND (expire_at IS NULL OR expire_at>" . time() . "))";
}

return [
  'name' => 'usergroup',
  'title' => 'Groups',
  'id' => 'id',
  'tools' => ['add_popup', 'csv'],
  'csv' => ['id', 'usergroup'],
  'lang' => 'core/lang/admin/',
  'commands' => ['edit_popup', 'delete'],
  'permissions' => [
    'create' => ['admin','admin_user'],
    'read' => ['admin','admin_user'],
    'update' => ['admin','admin_user'],
    'delete' => ['admin','admin_user']
  ],
  'search_box' => true,
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,
      'create' => false
    ],
    'logo' => [
      'qtype' => 'VARCHAR(255)',
      'display_type' => 'media',
      'type' => 'media2',
      'input_style' => 'grid-column:span 1',
    ],
    'usergroup' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(255)',
      'input_style' => 'grid-column:span 2',
      'maxlength' => 255,
      'required' => true,
    ],
    'description' => [
      'title' => 'Description',
      'max-width' => '350px',
      'qtype' => 'VARCHAR(200)'
    ],
    'users' => [
      'title' => 'Users',
      'qcolumn' => $usersq,
      'eval' => "cv='<a href=\"admin/users?usergroup='+item.id+'\">'+item.users+' " . Config::tr('Users') . " → </a>'",
      'edit' => false,
      'create' => false,
    ],
    'expire_at' => [
      'title' => Config::tr('Expiration', ['es' => 'Expiración']),
      'type' => 'time',
      'input_type' => 'date',
      'display_type' => 'date',
      'qtype' => 'INT UNSIGNED',
      'default' => null,
      'input_style' => 'grid-column:span 2',
      'show' => false,
      'create' => (Config::get('user_group') == 1),
      'edit' => (Config::get('user_group') == 1),
    ],
  ],
  'events' => [
    ['delete', function (&$id) {
        DB::query("DELETE FROM usermeta WHERE vartype='group' AND `value`=?", [$id]);
    }],
  ]
];
