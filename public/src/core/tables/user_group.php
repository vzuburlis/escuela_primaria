<?php

$table = [
  'name' => 'user_group',
  'title' => Config::tr('Memberships', ['es' => 'Membresias']),
  'pagination' => 15,
  'pagination_top' => true,
  'tools' => ['add_popup'],
  'commands' => ['edit_popup','delete'],
  'id' => 'id',
  'lang' => 'core/lang/admin/',
  'metadata' => true,
  'unix_times' => true,
  'invite' => ['username','email','group_id'],
  'permissions' => [
    'read' => ['admin','admin_user'],
    'create' => ['admin','admin_user'],
    'update' => ['admin','admin_user'],
    'delete' => ['admin']
  ],
  //'csv'=> ['id','username','email'],
  //'invite'=> ['username','email','firstrole'],
  'search_box' => true,
  'search_boxes' => ['group_id'],
  'fields' => [
    'id' => [
      'create' => false,
      'edit' => false,
      'show' => false,
    ],
    'user_id' => [
      'title' => 'User',
      'type' => 'select',
      'qoptions' => 'SELECT user.id, username FROM user',
      'search_query' => '(SELECT username FROM user WHERE id=user_id)',
      'qtype' => 'INT UNSIGNED DEFAULT NULL',
      'create' => !isset($_REQUEST['user_id']),
      'edit' => !isset($_REQUEST['user_id']),
    ],
    'group_id' => [
      'title' => 'Group',
      'type' => 'select',
      'qoptions' => 'SELECT id, usergroup FROM usergroup',
      'qtype' => 'INT UNSIGNED DEFAULT NULL'
    ],
    'expire_at' => [
      'title' => Config::tr('Expiration', ['es' => 'Expiración']),
      'type' => 'time',
      'input_type' => 'date',
      'display_type' => 'date',
      'qtype' => 'INT UNSIGNED',
      'default' => time() + 30 * 86400,
      'input_style' => 'grid-column:span 2',
    ],
  ],
  'events' => [
    ['create', function (&$row) {
        if (!isset($row['user_id'])) {
            $row['user_id'] = $_REQUEST['user_id'] ?? 0;
        }
    }],
    ['created', function (&$row) {
        if (User::$invitationSent > 0 && !Config::inPackages('workspace')) {
            return;
        }
        sendGroupInvitation($row, $row['group_id']);
    }]
  ]
];

if (!function_exists('sendGroupInvitation')) {
    function sendGroupInvitation($data)
    {
        Config::addLang('core/lang/login/');
        $group = DB::getOne("SELECT * FROM usergroup WHERE id=?", $data['group_id']);
        if (!$group) {
            return;
        }
        $user = DB::getOne("SELECT * FROM user WHERE id=?", $data['user_id']);
        if (!$group || !$user) {
            return;
        }
        if (empty($user['email'])) {
            return;
        }

        $subject = __('You were added in a group', ['es' => 'Te agregaron a un grupo']);
        $txt = __('The user {sender} has added you in the group {group_name}', [
        'es' => '{sender} te agrego en el grupo {group_name}',
        ]);
        $msg = strtr($txt, ['{sender}' => Session::key('user_name'), '{group_name}' => $group['usergroup']]);
        if (Config::inPackages('workspace')) {
            $txt = __('You can join group workspace from this link', [
            'es' => 'Puedes entrar en el espacio de trabajo de grupo desde aqui',
            ]);
            $link = "\n" . Config::base('admin/workspace?group_id=' . $group['id']);
            $msg .= "\n\n" . $txt . $link;
        }
        $msg .= "\n\n";
        $headers = "From: " . Config::get('title') . " <noreply@{$_SERVER['HTTP_HOST']}>";
        Email::send(['email' => $user['email'], 'subject' => $subject, 'message' => $msg, 'headers' => $headers]);
    }
}

if (isset($GLOBALS['user_id']) || isset($_REQUEST['user_id'])) {
    $table['filters'] = [
    'user_id' => $GLOBALS['user_id'] ?? $_REQUEST['user_id']
    ];
    $table['fields']['user_id']['show'] = false;
}

return $table;
