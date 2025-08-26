<?php

$lorem = '<div><h2>Lorem Ipsum</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacinia lacinia enim, a malesuada urna convallis at. Etiam finibus, odio gravida fringilla luctus, urna diam pellentesque nunc, ut elementum tellus dolor non quam. Donec blandit varius hendrerit.</p></div>';

return [
  'fields' => [
    'text' => [
      'type' => 'codemirror',
      'group' => 'text',
      'allow_tags' => true,
      'default' => $lorem
    ],
    'text-align' => [
      'type' => 'radio', 'default' => '', 'title' => 'Text align',
      'options' => ['' => '-','left' => 'Left','center' => 'Center','right' => 'Right']
    ],
    'justify-items' => [
      'type' => 'radio', 'default' => '', 'title' => 'Horizontal align',
      'options' => ['' => '-','start' => 'Left','center' => 'Center','end' => 'Right','stretch' => 'Stretch']
    ],
    'align-items' => [
      'type' => 'radio', 'default' => '', 'title' => 'Vertical align',
      'options' => ['' => '-','start' => 'Top','center' => 'Center','end' => 'Bottom']
    ],
    'padding' => [
      'type' => 'radio', 'default' => '', 'title' => Config::tr('Padding', ['es' => 'Relleno']),
      'options' => ['' => 'Default','0px' => 'None','30px' => '30px'],
    ],
    'grid' => [
      'type' => 'select', 'default' => '',
      'options' => [
        '' => 'Default','2c' => '2 columns','3c' => '3 columns','3c1c' => '3 (break to 1)',
        '4c' => '4 columns','4c2c' => '4 (break to 2)','5c' => '5 columns','bricks' => 'Bricks',
        '1f' => '1 φ','f1' => 'φ 1','first-fluid' => 'First fluid',
        '1v_1' => '1V 1','1_1v' => '1 1V','1v_1_1' => '1V 1 1','1_1v_1' => '1 1V 1','1_1_1v' => '1 1 1V'
      ]
    ],
    'gap' => [
      'title' => 'Gap',
      'type' => 'range',
      'default' => '1',
      'min' => 0,
      'step' => 0.5,
      'max' => 4.0
    ],
    'hide-grid' => [
      'title' => Config::tr('Hide grid of columns', ['es' => 'Oculta red de columnas']),
      'type' => 'checkbox', 'default' => '',
    ],
    'container-class' => [
      'type' => 'radio', 'default' => '', 'title' => 'Container width',
      'options' => ['' => 'Default','fluid' => 'Fluid','sm' => 'Small','md' => 'Medium','lg' => 'Large','xl' => 'Extra large'],
      'group' => 'size',
    ],
    'container-mw' => [
      'default' => '', 'title' => 'Container max width',
      'group' => 'size',
    ],
    'is-form' => [
      'default' => '', 'title' => 'Behave as form',
      'type' => 'checkbox',
      'group' => 'advanced',
    ],
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
  'keys' => 'page,post,widget',
  'group' => 'text',
  'index' => 2
];
