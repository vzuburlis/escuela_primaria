<?php

return [
  'fields' => [
    'items' => [
      'type' => 'list',
      'fields' => [
        'image' => ['type' => 'media','default' => '$p=l1.jpg'],
        'name' => [],
        'text' => [],
        'url' => [],
      ],
      'default' => '[["$p=l1.jpg", "Feature", "Lorem ipsum dolor sit." ],["$p=l2.jpg", "Feature", "Lorem ipsum dolor sit."],["$p=l3.jpg", "Feature", "Lorem ipsum dolor sit."]]'
    ],
    'link_text' => [
      'default' => 'Learn more'
    ]
  ],
  'keys' => 'page,post,features',
  'group' => 'features',
  'index' => '3'
];
