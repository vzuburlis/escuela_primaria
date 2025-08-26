<?php

return [
  'fields' => [
    'n_post' => [
      'title' => 'Number of posts',
      'default' => '3',
    ],
    'category' => [
      'title' => 'Category',
      "type" => "postcategory"
    ],
    'align' => [
      'type' => 'radio', 'default' => 'left',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'language' => [
      'type' => 'language'
    ],
  ],
  'keys' => 'page,blog,widget',
  'group' => 'blog',
  'links' => [
    ['label' => 'Edit posts', 'tr' => ['es' => 'Editar publicaciones'], 'url' => 'admin/content/post']
  ],
  'index' => 4
];
