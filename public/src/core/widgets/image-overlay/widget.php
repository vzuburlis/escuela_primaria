<?php

$defaultData = <<<EOT
<div>
    <h1 class="section-heading">Your Title</h1>
    <p class="lead-text" style="color:#FFFFFF">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
    <a href="#" class="btn btn-primary">Download</a>
    <a href="#" class="btn btn-outline-primary" style="margin-left:10px">Learn more</a>
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
    'align' => [
      'type' => 'radio',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right'],
      'default' => 'center',
      'title' => 'Text align'
    ],
    'text_size' => [
      'title' => 'Text size',
      'type' => 'radio','default' => '120%',
      'options' => ['100%' => '100%','120%' => '120%','140%' => '140%']
    ],
    'background' => [
      'title' => 'Background image',
      'type' => 'media2',
      'default' => 'assets/themes/unique-x/wr1.jpg'
    ],
    'height' => [
      'default' => '75vh',
      'type' => 'select',
      'options' => ['' => 'auto','25vh' => '25%','40vh' => '40%','50vh' => '50%','60vh' => '60%','65vh' => '65%','70vh' => '70%','75vh' => '75%','80vh' => '80%','85vh' => '85%','90vh' => '90%','95vh' => '95%','100vh' => '100%'],
      'edit' => false
      ]
  ],
  'keys' => 'removed',
  'group' => 'CTA',
  'index' => 2
];
