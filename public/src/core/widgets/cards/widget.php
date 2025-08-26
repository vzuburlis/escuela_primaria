<?php

return [
  'fields' => [
    'align' => [
      'type' => 'radio', 'default' => 'center',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'link_text' => [],
    'cards' => [
      'type' => 'list',
      'fields' => [
        'image' => ['type' => 'media'],
        'title' => [],
        'text' => [],
        'link_url' => []
      ],
      'default' => '[["$p=l2.jpg","Card","","",""],["$p=l3.jpg","Card","","",""],["$p=l1.jpg","Card","","",""]]'
    ]
  ],
  'keys' => 'page',
  'group' => 'features',
];
