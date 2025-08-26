<?php

use Gila\Config;
use Gila\DB;

return [
  'name' => 'page',
  'title' => 'Pages',
  'pagination' => 15,
  'id' => 'id',
  'tools' => ['new_item'],
  'bulk_actions' => ['edit'],
  'csv' => ['id','title','slug','updated','publish'],
  'commands' => ['blocks','page_seo','clone','delete'],
  'lang' => 'core/lang/admin/',
  'qkeys' => ['slug','publish'],
  'js' => ['src/core/tables/page.js','src/core/assets/admin/blocks_btn.js'],
  'metadata' => true,
  'permissions' => [
    'read' => true,
    'create' => ['admin', 'web_editor'],
    'update' => ['admin', 'web_editor', 'editor'],
    'delete' => ['admin', 'web_editor']
  ],
  'search_box' => true,
  'search_boxes' => Config::getArray('languages') ? ['language'] : [],
  'filters' => [
    'language' => $_GET['language'] ?? Config::lang()
  ],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'style' => 'width:5%',
      'edit' => false,
      'create' => false
    ],
    'title' => [
      'title' => 'Title',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'group' => 'title',
      'required' => true,
      'rules' => 'maxlength:255',
      'maxlength' => 255,
    ],
    'slug' => [
      'title' => 'Route',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'alt' => '(' . Gila\Config::tr('Home') . ')',
      'group' => 'title',
      'create' => false,
      'helptext' => 'Leave empty if this is the homepage',
      'helptext_es' => 'Dejar vacía si esta es la página de inicio',
      'clone' => false,
    ],
    'description' => [
      'title' => 'Description',
      'input_type' => 'textarea',
      'qtype' => 'VARCHAR(255) DEFAULT NULL',
      'list' => false,
      'create' => false,
      'group' => 'title',
      'helptext' => 'This is the text that will be displayed in the listing of the page. For better SEO, use 120-160 letters.',
      'helptext_es' => 'Este es el texto que se mostrará en el listado de la página. Para un mejor SEO, use 120-160 letras.',
      'rules' => 'maxlength:255',
      'maxlength' => 255,
    ],
    'image' => [
      'title' => 'Thumbnail',
      'list' => false,
      'create' => false,
      'type' => 'media2',
      'qtype' => 'VARCHAR(255)',
      'input_style' => 'grid-column:span 1',
    ],
    'publish' => [
      'title' => 'Status',
      'style' => 'width:8%',
      'type' => 'checkbox',
      'type' => 'select',
      'options' => [0 => 'Draft',1 => 'Published',2 => 'To review'],
      'option_colors' => [0 => 'grey',1 => 'green',2 => 'orange'],
      'edit' => true,
      'qtype' => 'TINYINT DEFAULT 0',
      'input_style' => 'grid-column:span 2',
      'clone' => false,
      'default' => 0,
    ],
    'template' => [
      'title' => 'Template',
      'template' => 'page',
      'type' => 'template',
      'edit' => true,
      'create' => false,
      'list' => false,
      'qtype' => 'VARCHAR(30) DEFAULT NULL',
      'input_style' => 'grid-column:span 2',
    ],
    'language' => [
      'title' => 'Language',
      'type' => 'select',
      'input_type' => 'language',
      'options' => Config::languageOptions(),
      'show' => (!empty(Config::get('languages'))),
      'qtype' => 'VARCHAR(2) DEFAULT NULL',
      'input_style' => 'grid-column:span 2',
      'default' => Config::lang(),
    ],
    //'parent_id'=> [
    //  'title'=>'Directory',
    //  'input_type'=>'select',
    //  'options'=>[0=>'-'],
    //  'qoptions'=> "SELECT id, `title` FROM page WHERE RIGHT(slug,1)='/';",
    //  'list'=>false,
    //  'qtype'=>'INT UNSIGNED DEFAULT 0',
    //  'input_style'=>'grid-column:span 2',
    //],
    'meta_robots' => [
      'title' => 'Meta robots',
      'type' => 'select',
      'options' => ['' => '', 'noindex' => 'noindex', 'nofollow' => 'nofollow', 'noindex, nofollow' => 'none'], //noimageindex
      'qtype' => 'VARCHAR(20) DEFAULT NULL',
      'list' => false,
      'create' => false,
      'input_style' => 'grid-column:span 2',
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
      'qtype' => 'LONGTEXT',
      'allow_tags' => true,
      'purify' => false,
      'clone' => true,
    ],
    'group_id' => [
      'title' => 'Group access',
      'type' => 'meta',
      'meta_key' => 'page_group_id',
      'input_type' => 'select',
      'list' => false,
      'create' => false,
      'options' => [0 => '-'],
      'qoptions' => "SELECT id,usergroup FROM usergroup",
      'input_style' => 'grid-column:span 2',
    ],
  ],
  'events' => [
    ['create', function (&$row) {
        if ($row['slug'] == '') {
            $slug = Slugify::text($row['title']);
            while (DB::value("SELECT id FROM `page` WHERE slug=?", $slug)) {
                $slug = substr(bin2hex(random_bytes(10)), 0, 10);
            }
            $row['slug'] = Slugify::text($slug);
        }
    }],
    ['change', function (&$row) {
        $id = $row['id'] ?? $_GET['id'];
        $query = "SELECT id FROM `page` WHERE publish=1 AND slug=? AND id!=? AND `language`=?;";
        if ($row['publish'] == 1 && $other = DB::getOne($query, [$row['slug'], $id, $row['language']])) {
            Table::$error = __('Another page has the same path') . " (ID:{$other['id']})";
        }
        DB::query("DELETE FROM redirect WHERE from_slug=?;", [$row['slug']]);
      // create redirection if the page path changes
        if ($prev = DB::getOne("SELECT id,slug FROM page WHERE publish=1 AND id=?", [$id])) {
            if (!empty($prev['slug']) && $prev['slug'] != $row['slug']) {
                if (!DB::getOne("SELECT id FROM page WHERE slug=? AND id!=?", [$prev['slug'], $id])) {
                    // do not redirect id there is another page with this path
                    DB::query("INSERT INTO redirect(from_slug,to_slug,`active`) VALUES(?,?,1);", [$prev['slug'], $row['slug']]);
                }
            }
        }
    }]
  ]
];
