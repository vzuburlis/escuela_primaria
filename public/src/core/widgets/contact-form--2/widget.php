<?php

return [
  'fields' => [
    "success_msg" => [
      "default" => "The email was send successfully.",
      "type" => "textarea"
    ],
    'border-radius' => [
      'type' => 'radio',
      'options' => ['0' => 'No','0.5em' => '0.5em','1em' => '1em','2em' => '2em'],
      'default' => '0'
    ],
    'justify-button' => [
      'type' => 'radio',
      'options' => ['left' => 'Left','center' => 'Center','flex-end' => 'Right'],
      'default' => 'left'
    ],
    'button-width' => [
      'type' => 'radio',
      'options' => ['auto' => 'Auto','25%' => '25%','50%' => '50%','100%' => '100%'],
      'default' => 'auto'
    ]
  ],
  'keys' => 'removed',
  //'keys'=>'page,post,widget',
  'group' => 'contact',
  'index' => '9'
];
