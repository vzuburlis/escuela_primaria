<?php

$defaultData = <<<EOT
<div>
    <h1 class="section-heading">Your Title</h1>
    <p class="lead-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
    <a href="#" class="btn btn-primary btn-lg ">Download</a>
    <a href="#" class="btn btn-secondary btn-grey btn-lg" style="margin-left:10px">Learn more</a>
</div>
EOT;

return [
  'fields' => [
    'text' => [
      'type' => 'codemirror',
      'group' => 'text',
      'allow_tags' => true,
      'default' => $defaultData
    ],
    'height' => [
      'default' => '75vh',
      'type' => 'radio',
      'options' => ['25vh' => '25%','50vh' => '50%','75vh' => '75%','100vh' => '100%']
    ],
    'align' => [
      'type' => 'radio',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right'],
      'default' => 'center'
    ],
    'text_size' => [
      'type' => 'radio','default' => '120%',
      'options' => ['100%' => '100%','120%' => '120%','140%' => '140%']
    ],
    'background' => [
      'type' => 'media2',
      'default' => '$p=wr1.jpg'
    ],
    'colors' => [
      'type' => 'block-colors',
      'title' => 'Background',
      'default' => 'dark'
    ],
  ],
  'keys' => 'page,post,widget,cta',
  'index' => 1
];
