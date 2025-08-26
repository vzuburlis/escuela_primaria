<?php

return [
  'fields' => [
    'images' => [
      'type' => 'list',
      'fields' => [
        'image' => ['type' => 'media'],
        'title' => [],
        'caption' => [],
        'text' => [],
      ],
      'default' => '[["$p=l1.jpg",""],["$p=l2.jpg","",""],["$p=l3.jpg","",""],["$p=l2.jpg","",""],["$p=l1.jpg","",""]]'
    ],
    'items_to_show' => [
      'title' => 'Items',
      'type' => 'select',
      'options' => ['auto' => 'auto','1' => '1','2' => '2','3' => '3','4' => '4','5' => '5','6' => '6'],
    ],
    'item_height' => [
      'type' => 'range',
      'min' => 200,
      'max' => 400
    ],
    'item_width' => [
      'type' => 'range',
      'min' => 160,
      'max' => 300
    ],
    'item_radius' => [
    ],
    'footer_height' => [
      'type' => 'range',
      'min' => 0,
      'max' => 400,
      'default' => 200,
    ],
  ],
  'keys' => 'page,post,widget,media',
  'group' => 'media',
  'index' => '5'
];
