<?php

return [
  'fields' => [
    'prefix' => [],
    'src' => [
      'type' => 'select',
      'options' => [
        'demo' => '--Demo--', 0 => Gila\Config::tr('Pages')
      ]
    ],
    'link_text' => [
      'default' => 'Learn more'
    ],
  ],
  'keys' => 'page',
  'group' => 'features',
  'index' => '3'
];
