<?php

return [
  'fields' => [
    'n_post' => [
      'title' => 'Number of posts',
      'default' => '4',
    ],
    'category' => [
      'title' => 'Category',
      "type" => "select",
      "options" => Gila\Post::categoryOptions()
    ]
  ],
  'keys' => 'page,post,widget',
  'group' => 'blog',
  'links' => [
    ['label' => 'Edit posts', 'tr' => ['es' => 'Editar publicaciones'], 'url' => 'admin/content/post']
  ],
  'index' => 4
];
