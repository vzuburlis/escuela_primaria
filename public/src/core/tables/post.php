<?php

return [
  'name' => 'post',
  'title' => 'Posts',
  'pagination' => 15,
  'id' => 'id',
  'clean_residual' => true,
  'tools' => ['add_popup'],
  'bulk_actions' => ['edit','approve'],
  'approve' => ['publish','1'],
  'csv' => ['id','title','slug','user_id','updated','publish','post'],
  'commands' => ['edit_blocks','edit_seo','clone','delete'],
  'lang' => 'core/lang/admin/',
  'qkeys' => ['slug','publish','user_id'],
  'metadata' => true,
  //'clone'=>['image','title','blocks','categories','slug','user_id','language'],
  'js' => ['src/core/tables/post.js'],
  'permissions' => [
    'read' => ['admin','editor','writer'],
    'create' => ['admin','editor','writer'],
    'update' => ['admin','editor','writer'],
    'delete' => ['admin','editor','writer']
  ],
  'search_box' => true,
  'search_boxes' => Config::getArray('languages') ? ['categories','user_id', 'language'] : ['categories','user_id'],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'create' => false,'edit' => false
    ],
    'image' => [
      'title' => '',
      'display_type' => 'media',
      'input_type' => 'media2',
      'qtype' => 'VARCHAR(255)',
      'input_style' => 'grid-column:span 1',
    ],
    'title' => [
      'title' => 'Title',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'required' => true,
      'rules' => 'maxlength:255',
      'maxlength' => 255,
    ],
    'categories' => [
      'edit' => true,
      'type' => 'meta',
      'meta_key' => 'post.category',
      'title' => 'Categories',
      'qoptions' => ['id','title','postcategory'],
      'input_style' => 'grid-column:span 2',
    ],
    'description' => [
      'title' => 'Description',
      'show' => false,
      'input_type' => 'textarea',
      'qtype' => 'VARCHAR(255)',
      'rules' => 'maxlength:255',
      'maxlength' => '255',
    ],
    'user_id' => [
      'title' => 'User',
      'type' => 'select',
      'qoptions' => 'SELECT DISTINCT user.id, username FROM user,usermeta WHERE user.id=user_id AND vartype="role"',
      'qtype' => 'INT UNSIGNED DEFAULT NULL',
      'list' => false
    ],
    'language' => [
      'type' => 'select',
      'input_type' => 'language',
      'title' => 'Language',
      'qtype' => 'VARCHAR(2) DEFAULT NULL',
      'options' => Config::languageOptions(),
      'show' => (!empty(Config::get('languages'))),
      'default' => Config::lang(),
      'input_style' => 'grid-column:span 1',
    ],
    'publish' => [
      'title' => 'Status',
      'qtype' => 'TINYINT DEFAULT 0',
      'style' => 'width:8%',
      'type' => 'select',
      'options' => ['0' => 'Draft','1' => 'Published','2' => 'To review'],
      'option_colors' => ['0' => 'grey','1' => 'green','2' => 'orange'],
      'inline_edit' => true,
      'option_edit' => true,
      'edit' => true,
      'default' => 0,
      'clone' => false,
      'input_style' => 'grid-column:span 1',
    ],
    'post' => [
      'list' => false,
      'title' => 'Post',
      'create' => false,
      'edit' => Config::get('post_text') ?? false,
      'type' => 'textarea',
      'input_type' => 'tinymce',
      'allow_tags' => true,
      'qtype' => 'TEXT'
    ],
    'updated' => [
      'title' => 'Updated',
      'type' => 'date',
      'searchbox' => 'period',
      'edit' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ],
    'created' => [
      'title' => 'Created',
      'type' => 'date',
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
      'clone' => false,
    ],
    'publish_at' => [
      'title' => 'Date',
      'input_type' => 'date',
      'list' => false,
      'edit' => true,
      'create' => false,
      'type' => 'time',
      'qtype' => 'INT UNSIGNED',
      'clone' => false,
      'input_style' => 'grid-column:span 2',
    ],
    'blocks' => [
      'list' => false,
      'edit' => false,
      'create' => false,
      'qtype' => 'TEXT',
      'allow_tags' => true,
      'purify' => false,
    ],
    'slug' => [
      'title' => 'Route',
      'show' => false,
      'create' => false,
      'qtype' => 'VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL',
      'rules' => 'maxlength:255',
    ],
  ],
  'events' => [
    ['create', function (&$row) {
        if (empty($row['slug'])) {
            $slug = Slugify::text($row['title']);
            while (empty($slug) || DB::value("SELECT id FROM `post` WHERE slug=?", $slug)) {
                $slug .= substr(bin2hex(random_bytes(10)), 0, 10);
            }
            $row['slug'] = Slugify::text($slug);
        }
    }],
    ['change', function (&$row) {
        $row['publish'] = $row['publish'] ?? 0;
        if (empty(trim($row['slug']))) {
            if (empty($row['title'])) {
                $slug .= substr(bin2hex(random_bytes(10)), 0, 10);
            } else {
                $row['slug'] = Slugify::text($row['title']);
            }
        }
        if (empty($row['publish_at']) && $row['publish'] == 1) {
            $row['publish_at'] = time();
        }
    }],
  ]
];
