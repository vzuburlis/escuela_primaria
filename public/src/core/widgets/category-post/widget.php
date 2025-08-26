<?php

return [
  'fields' => [
    'n_post' => [
      'title' => 'Number of posts',
      'default' => '3',
    ],
    'category' => [
      'title' => 'Category',
      "type" => "select",
      "options" => Gila\Post::categoryOptions()
    ],
    'align' => [
      'type' => 'radio', 'default' => 'left',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'mustache' => [
      'group' => 'mustache',
      'default' => file_get_contents(__DIR__ . '/category-post--grid.mustache'),
      'type' => 'codemirror',
      'allow_tags' => true,
    ]
  ],
  'keys' => 'page,post,widget',
  'group' => 'blog',
  'links' => [
    ['label' => 'Edit posts', 'tr' => ['es' => 'Editar publicaciones'], 'url' => 'admin/content/post']
  ],
  'index' => 4
];
