<?php

return [
  'fields' => [
    'items' => [
      'type' => 'list',
      'fields' => [
        'image' => [
          'type' => 'media','default' => 'assets/core/photo.png',
        ],
        'title (opcional)' => [],
        'description (opcional)' => [],
        'url (opcional)' => [],
      ],
    ],
    'text' => [
      'type' => 'codemirror',
      'group' => 'text',
      'allow_tags' => true,
      'default' => '<div><h2>Your Title</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>'
    ],
    'side' => [
      'default' => 2,
      'type' => 'radio',
      'title' => 'Show text',
      'options' => ['Right', 'Left', 'No'],
    ],
    'images-size' => [
      'title' => 'Image width',
      'type' => 'radio',
      'options' => ['cover' => 'Cover','contain' => 'Normal'],
      'default' => 'contain',
    ],
    'carousel-size' => [
      'title' => 'Height',
      'default' => '500px',
    ],
    'carousel-full-width' => [
      'title' => 'Full width',
      'type' => 'radio',
      'options' => [0 => 'No',1 => 'Yes'],
      'default' => 0
    ],
    'duration-in-seconds' => [
      'title' => 'Duration (sec)',
      'type' => 'number',
      'default' => 3
    ],
    'button-title' => [
      'title' => 'Button title',
      'type' => 'text',
    ],
    'color-text' => [
      'title' => 'Text color',
      'type' => 'color',
    ],
    'button-font-size' => [
      'title' => 'Button size',
      'type' => 'select',
      'options' => ['12' => 'Small','18' => 'Normal','25' => 'big']
    ],
    'text-align' => [
      'type' => 'select',
      'options' => ['start' => 'Start','center' => 'Center','end' => 'End']
    ],
    'vertical-align' => [
      'type' => 'range',
    ],
  ],
  'keys' => 'page,post,widget'
];
