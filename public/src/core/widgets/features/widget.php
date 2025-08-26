<?php

$f = __('Title', [
  'en' => 'Title',
  'es' => 'Título',
  'el' => 'Τίτλος'
]);
$txt = __('_feature_description', [
  'en' => 'A brief description of this feature',
  'es' => 'Una breve descripción de esta característica',
  'el' => 'Μια σύντομη περιγραφή αυτής της δυνατότητας'
]);


$default = '[["$p=l1.jpg", "' . $f . ' 1","' . $txt . '"],["$p=l2.jpg", "' . $f . ' 2","' . $txt . '"],["$p=l3.jpg", "' . $f . ' 3","' . $txt . '"]]';

return [
  'fields' => [
    'features' => [
      'type' => 'list',
      'fields' => [
        'image' => ['type' => 'media','default' => '$p=l1.jpg'],
        'name' => [],
        'description' => [],
        'url' => [],
      ],
      'default' => $default
    ],
    'align' => [
      'type' => 'radio', 'default' => 'center',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'image-orientation' => [
      'type' => 'select',
      'options' => [
        '' => 'Normal',
        'square__item' => 'Square','square__sm' => 'Square SM',
        'circle__item' => 'Circle','circle__sm' => 'Circle SM',
        'landscape__item' => 'Landscape','landscape__sm' => 'Landscape SM',
        'portrait__item' => 'Portrait','portrait__sm' => 'Portrait SM',
        'wide__item' => '16:9','wide__item' => '16:9','portrait__arc' => 'Arc'
      ],
      'default' => 'landscape__item'
    ],
    'link_text' => [
      'default' => __('Learn More', ['es' => 'Leer mas', 'el' => 'Περισσότερα'])
    ],
    'link_target' => [
      'type' => 'select',
      'options' => ['_self' => 'Same tab','_blank' => 'New tab']
    ],
    'link_type' => [
      'type' => 'select',
      'options' => ['' => 'None','g-btn' => 'Button A','g-btn-secondary' => 'Button B']
    ],
    'mustache' => [
      'group' => 'mustache',
      'default' => file_get_contents(__DIR__ . '/features.mustache'),
      'type' => 'codemirror',
      'allow_tags' => true,
    ]
  ],
  'keys' => 'page',
  'group' => 'features',
  'index' => 3
];
