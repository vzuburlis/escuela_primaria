<?php

$lorem = '<div><h2 style="color:#181818;font-family:Arial;font-size:26px">Lorem Ipsum</h2><p style="color:#181818;font-size:16px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacinia lacinia enim, a malesuada urna convallis at. Etiam finibus, odio gravida fringilla luctus, urna diam pellentesque nunc, ut elementum tellus dolor non quam. Donec blandit varius hendrerit.</p></div>';

return [
  'fields' => [
    'text' => [
      'type' => 'codemirror',
      'group' => 'text',
      'allow_tags' => true,
      'default' => $lorem
    ],
    'text-align' => [
      'type' => 'radio', 'default' => 'center',
      'options' => ['left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'align-items' => [
      'type' => 'radio', 'default' => 'start', 'title' => 'Vertical align',
      'options' => ['start' => 'Top','center' => 'Center','end' => 'Bottom']
    ],
    'gap' => [
      'title' => 'Gap',
      'type' => 'range',
      'default' => '1',
      'min' => 0,
      'step' => 0.5,
      'max' => 4.0
    ],
    'padding' => [
      'type' => 'radio', 'default' => '', 'title' => 'Padding',
      'options' => ['' => 'Default','0px' => 'None']
    ],
    'grid' => [
      'type' => 'select', 'default' => '',
      'options' => [
        '' => 'Default','2c' => '2 columns','3c' => '3 columns','3c1c' => '3/1 columns',
        '4c' => '4 columns','4c2c' => '4/2 columns','bricks' => 'Bricks',
        '1f' => 'Golden ration','f1' => 'Golden ration (R)','first-fluid' => 'First fluid',
        '1v_1' => '1| 1','1_1v' => '1 1|','1v_1_1' => '1| 1 1','1_1v_1' => '1 1| 1','1_1_1v' => '1 1 1|'
      ]
    ]
    //'text-bg-color'=>[
    //  'type'=>'color-input',
    //  'title'=>'Background color'
    //],
    //'text-bg-alfa'=>[
    //  'title'=>'Background color opacity',
    //  'type'=>'range',
    //  'default'=>0,
    //  'min'=>0,
    //  'step'=>0.1,
    //  'max'=>1.0
    //]
  ],
  'keys' => 'email,template,campaign',
  'group' => 'text-email',
  'index' => 2
];
