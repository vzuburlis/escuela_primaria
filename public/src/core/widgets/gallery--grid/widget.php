<?php

return [
  'fields' => [
    'images' => [
      'type' => 'list',
      'fields' => [
        'image' => [
          'type' => 'media','default' => 'assets/core/photo.png'
        ],
        'caption' => []
      ],
      'default' => '[["$p=l1.jpg"],["$p=l2.jpg"],["$p=l3.jpg"]]'
    ],
    'orientation' => [
      'type' => 'select',
      'options' => [
        'square__item' => 'Square','square__sm' => 'Square SM',
        'circle__item' => 'Circle','circle__sm' => 'Circle SM',
        'landscape' => 'Landscape','landscape__sm' => 'Landscape SM',
        'portrait' => 'Portrait','portrait__sm' => 'Portrait SM',
        'wide__item' => '16:9','wide__item' => '16:9','portrait__arc' => 'Arc'
      ],
      'default' => 'square'
    ],
    'filter' => [
      'type' => 'select',
      'options' => [
        'none' => 'None','blur(4px)' => 'Blur','brightness(130%)' => 'Brightness','grayscale(100%)' => 'Greyscale',
        'sepia(100%)' => 'Sepia','contrast(160%)' => 'Contrast'//,'drop-shadow(8px 8px 10px grey)'=>'Shadow'
      ]
    ],
    'titles' => [
      'title' => Config::tr('Show titles', ['es' => 'Mostrar titulos']),
      'type' => 'checkbox',
      'default' => 0,
    ],
    'gap' => [
      'type' => 'range',
      'min' => 0,
      'default' => 1,
      'max' => 4
    ],
    'class' => [
      'title' => Config::tr('Class', ['es' => 'Clase']),
      'default' => '',
    ],
  ],
  'keys' => 'page,post,widget',
  'group' => 'media',
];
