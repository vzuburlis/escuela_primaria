<?php

return [
  'fields' => [
    'image' => [
      'type' => 'media2',
      'default' => '$p=l1.jpg'
    ],
    'caption' => [
      'helptext' => 'A small explanation text to display below the image',
      'helptext_es' => 'Un texto explicativo que se muestra debajo de la imagen.',
      'type' => 'textarea'
    ],
    'alt_text' => [
      'helptext' => 'Alternative text to render in screen readers',
      'helptext_es' => 'Texto alternativo para renderizar en lectores de pantalla',
      'type' => 'textarea'
    ]
  ],
  'keys' => 'page,post,widget,media',
  'group' => 'media',
  'index' => 5
];
