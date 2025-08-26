<?php

return [
  'fields' => [
    'items' => [
      'type' => 'list',
      'fields' => [
        'value' => [
          'title' => 'Value (%)',
          'type' => 'int'
        ],
        'label' => [
        ]
      ],
      'default' => '[[30,""],[30,""],[30,""]]'
    ],
    'color' => [
      'type' => 'color'
    ]
  ],
  'keys' => 'page',
  'group' => 'chart',
];
