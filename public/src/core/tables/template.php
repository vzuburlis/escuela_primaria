<?php

return [
  'name' => 'template',
  'title' => 'Templates',
  'id' => 'id',
  'tools' => ['add_popup'],
  'csv' => ['id','title','updated','publish'],
  'commands' => ['edit_popup','edit_blocks','clone','previewT','testT','delete'],
  'lang' => 'core/lang/admin/',
  'qkeys' => ['active'],
  'js' => ['src/core/tables/template.js'],
  'email_content' => true,
  'permissions' => [
    'read' => ['admin', 'editor'],
    'create' => ['admin', 'editor'],
    'update' => ['admin', 'editor'],
    'delete' => ['admin', 'editor']
  ],
  'search_box' => true,
  'search_boxes' => ['event'],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'edit' => false,
      'create' => false
    ],
    'title' => [
      'title' => 'Title',
      'qtype' => 'VARCHAR(80) DEFAULT NULL',
      'group' => 'title'
    ],
    'event' => [
      'title' => 'Type',
      'type' => 'select',
      'options' => [
        'campaign' => 'Campaign','welcome' => 'Welcome',
      ],
      //  user_activation,
      //  user_password_reset,
      //  user_invite,
      //  create_website,
      //  user_registration,
      //  create_website_reminder,
      'qtype' => 'VARCHAR(30) DEFAULT NULL',
      'rules' => 'maxlength:30',
    ],
    'language' => [
      'type' => 'language',
      'qtype' => 'VARCHAR(2) DEFAULT NULL'
    ],
    'active' => [
      'title' => 'Active',
      'style' => 'width:8%',
      'type' => 'checkbox',
      'edit' => true,
      'qtype' => 'TINYINT DEFAULT NULL'
    ],
    'updated' => [
      'title' => 'Updated',
      'type' => 'date',
      'searchbox' => 'period',
      'edit' => false,
      'list' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ],
    'blocks' => [
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'TEXT DEFAULT \'[{"_type":"text","text":"<div><p>Hello {{user.username}}<br><br>This is an html message, you can start editing it</p></div>"}]\'',
    ],
    //'text'=>[
    //  'list'=>false,
    //  'create'=> false,
    //  'allow_tags'=> true,
    //  'type'=>'textarea'
    //]
  ]
];
