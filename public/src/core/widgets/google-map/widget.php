<?php

return [
  'fields' => [
    'address' => [
      'default' => 'Mexico City'
    ],
    'height' => [
      'type' => 'range',
      'default' => 400,
      'min' => 200,
      'max' => 600,
      'step' => 25
    ]
  ],
  'keys' => 'removed',
  'group' => 'other',
  'index' => '9'
];
