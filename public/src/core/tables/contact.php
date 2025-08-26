<?php

$upload_csv = ['name','phone','email','linkedin'];

return [
  'name' => 'contact',
  'title' => 'Contacts',
  'commands' => ['edit_popup','delete'],
  'tools' => ['add_popup', 'upload_csv', 'csv', 'remove_orphan'],
  'pagination' => 25,
  'id' => 'id',
  'lang' => 'email-marketing/lang/',
  'meta_table' => ['contactmeta', 'contact_id', 'metakey', 'metavalue'],
  'upload_csv' => $upload_csv,
  'search_box' => true,
  'search_boxes' => ['segment'],
  'bulk_actions' => ['delete'],
  'unix_times' => true,
  'unique' => 'email',
  'filters' => [
    'isCompany' => 0
  ],
  'permissions' => [
    'read' => ['admin','market'],
    'create' => ['admin','market'],
    'update' => ['admin','market'],
    'delete' => ['admin','market']
  ],
  'fields' => [
    'id' => [
      'create' => false,'edit' => false
    ],
    'name' => [
      'title' => 'Name',
      'qtype' => 'VARCHAR(120)',
      'rules' => 'maxlength:255',
      'maxlength' => 120,
      'required' => true,
    ],
    'email' => [
      'qtype' => 'VARCHAR(120)',
      'rules' => 'maxlength:120',
    ],
    'phone' => [
      'title' => 'Phone',
      'qtype' => 'VARCHAR(30)',
      'rules' => 'maxlength:30',
      'input_style' => 'grid-column:span 2',
    ],
    'linkedin' => [
      'title' => 'Linkedin',
      'type' => 'meta',
      'input_type' => 'text',
      'meta_key' => 'linkedin',
      'list' => false,
      'create' => false,
      'input_style' => 'grid-column:span 2',
    ],
    'website' => [
      'title' => 'Website',
      'type' => 'meta',
      'input_type' => 'text',
      'meta_key' => 'website',
      'list' => false,
      'create' => false,
      'input_style' => 'grid-column:span 2',
    ],
    'isCompany' => [
      'qtype' => 'TINYINT DEFAULT 0',
      'type' => 'checkbox',
      'show' => false,
      'edit' => false,
      'create' => false,
    ],
    'isArchived' => [
      'qtype' => 'TINYINT DEFAULT 0',
      'type' => 'checkbox',
      'show' => false,
      'edit' => false,
      'create' => false,
    ],
    'segment' => [
      'title' => 'Lists',
      'type' => 'meta',
      'input_type' => 'v-select-multiple',
      'meta_key' => 'segment',
      'qoptions' => "SELECT `id`,`name` FROM `contact_list`",
      'create' => false,
    ],
    'email_opens' => [
      'title' => 'Emails',
      'qcolumn' => '(SELECT COUNT(*) FROM campaign_recipient WHERE contact_id=contact.id AND opens>0)',
      //'eval'=>"cv=item.email_opens+\"/\"+item.email_send",
      'eval' => "cv='<a href=\"admin/content/campaign_recipient?email='+item.email+'\">'+item.email_opens+\"/\"+item.email_send+' → </a>'",
      'create' => false,
      'edit' => false,
    ],
    'email_send' => [
      'qcolumn' => '(SELECT COUNT(*) FROM campaign_recipient WHERE contact_id=contact.id)',
      'show' => false,
      'create' => false,
      'edit' => false,
    ],
  ],
  'events' => [
    ['cm.create', function (&$row) {
        if (DB::value("SELECT id FROM contact WHERE email=?", [$row['email']])) {
            $err = 'There is another contact with this email';
            $erres = 'Hay otro contacto con ese correo electrónico';
            Response::error(Config::tr($err, ['es' => $erres]));
        }
    }],
    ['created', function (&$row) {
        if (empty($row['segment']) && isset($_REQUEST['segment'])) {
            DB::query("INSERT INTO contactmeta(contact_id,metakey,metavalue) VALUES(?,'segment',?)", [$row['id'], $_REQUEST['segment']]);
        }
    }],
    ['update', function ($row) {
        $prev = DB::getOne("SELECT * FROM contact WHERE id=?;", [$row['id']]);
        if ($row['name'] && $row['email'] && $prev['name'] != $row['name'] && $prev['email'] != $row['email']) {
            $err = 'You can fix only the name or the email. The contact should refer to same person/entity';
            $erres = 'Puedes arreglar solo el nombre o el correo electrónico. El contacto debe referirse a la misma persona/entidad.';
            Response::error(Config::tr($err, ['es' => $erres]));
        }
    }],
    ['delete', function ($id) {
        if (0 < DB::value("SELECT COUNT(*) FROM contactmeta WHERE contact_id=? AND metakey NOT IN('segment','country','tax_system')", [$id])) {
            Response::error('No puedes eliminar ese contacto mientras tiene mas datos', 200);
        }
    }],
  ]
];
