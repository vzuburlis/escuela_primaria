<?php

return [
  'fields' => [
    'images' => [
      'type' => 'list',
      'fields' => [
        'image' => ['type' => 'media'],
        'caption' => []
      ],
      'default' => '[["$p=l1.jpg",""],["$p=l2.jpg",""],["$p=l3.jpg",""],["$p=l2.jpg",""],["$p=l1.jpg",""]]'
    ],
    'items-to-show' => [
      'title' => 'Items',
      'type' => 'select',
      'options' => ['auto' => 'auto','1' => '1','2' => '2','3' => '3','4' => '4','5' => '5','6' => '6'],
    ],
    'item-width' => [
      'type' => 'range',
      'min' => 160,
      'max' => 300
    ],
    'cover-display' => [
      'type' => 'checkbox',
      'default' => '1',
    ],
  ],
  'keys' => 'page,post,widget,media',
  'group' => 'media',
  'index' => '5'
];
