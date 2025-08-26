<?php

return [
  'fields' => [
    'image' => [
      'type' => 'media2',
      'default' => '$p=s1.jpg',
      'group' => 'img'
    ],
    'side' => [
      'type' => 'radio',
      'options' => ['Left', 'Right'],
      'group' => 'img'
    ],
    'success_msg' => [
      'default' => 'The email was send succesfully.',
      'type' => 'textarea',
      'group' => 'form'
    ],
    'border-radius' => [
      'type' => 'radio',
      'options' => ['0' => 'No','0.5em' => '0.5em','1em' => '1em','2em' => '2em'],
      'default' => '0',
      'group' => 'form'
    ],
    'justify-button' => [
      'type' => 'radio',
      'options' => ['left' => 'Left','center' => 'Center','flex-end' => 'Right'],
      'default' => 'left',
      'group' => 'form'
    ],
    'button-width' => [
      'type' => 'radio',
      'options' => ['auto' => 'Auto','25%' => '25%','50%' => '50%','100%' => '100%'],
      'default' => 'auto',
      'group' => 'form'
    ]
  ],
  'keys' => 'removed',
  //'keys'=>'page',
  'group' => 'contact',
];
