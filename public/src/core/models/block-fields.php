<?php

return [
  'bg-color' => [
    'type' => 'color-input',
    'title' => 'Background color',
    'palette' => ['var(--p1color)','var(--p2color)','var(--p3color)','var(--p4color)'],
    'group' => 'bg',
    'ci' => 0,
    'paletteLabels' => ['Primary','Assent','Heading','Font']
  ],
  'background' => [
    'type' => 'media2',
    'title' => 'Background image',
    'group' => 'bg',
  ],
  'positionY' => [
    'title' => 'Position Y',
    'type' => 'select',
    'options' => ['' => '-','top' => 'Top','center' => 'Center','bottom' => 'Bottom'],
    'group' => 'bg',
    'default' => 'top'
  ],
  'positionX' => [
    'title' => 'Position X',
    'type' => 'select',
    'options' => ['' => '-','left' => 'Left','center' => 'Center','right' => 'Right'],
    'group' => 'bg',
  ],
  'background-size' => [
    'title' => 'Background size',
    'type' => 'color-input',
    'type' => 'radio',
    'options' => ['' => '-','auto' => 'Auto','cover' => 'Cover','contain' => 'Contain'],
    'group' => 'bg',
    'ci' => 1,
  ],
  'background-repeat' => [
    'title' => 'Background repeat',
    'type' => 'color-input',
    'type' => 'radio',
    'options' => [
      '' => '-','repeat' => 'Repeat','no-repeat' => 'No repeat',
      'repeat-x' => 'Repeat X','repeat-y' => 'Repeat Y','space' => 'Space','round' => 'Round',
    ],
    'group' => 'bg',
    'ci' => 1,
  ],
  'background-color' => [
    'type' => 'color-input',
    'title' => 'Color overlay',
    'palette' => ['var(--p1color)','var(--p2color)','var(--p3color)','var(--p4color)'],
    'group' => 'bg',
    'ci' => 1,
    'paletteLabels' => ['Primary','Assent','Heading','Font']
  ],
  'alfa' => [
    'title' => 'Background color opacity',
    'type' => 'range',
    'group' => 'bg',
    'default' => '0',
    'min' => 0,
    'step' => 0.1,
    'max' => 1.0
  ],
  'attachment' => [
    'title' => 'Background attachment',
    'type' => 'radio', 'options' => ['' => 'No', 'fixed' => 'Fixed'],
    'group' => 'bg',
  ],
  'video' => [
    'group' => 'bg',
    'type' => 'video',
  ],
  'backgroundSM' => [
    'type' => 'media2',
    'title' => 'Small background',
    'group' => 'bg',
  ],
  'aspect-ratio' => [
    'default' => '',
    'type' => 'text',
    'title' => 'Aspect ratio',
    'placeholder' => '4/3',
    'group' => 'size',
  ],
  'id' => [
    'title' => 'Anchor ID',
    'group' => 'advanced',
    'type' => 'id',
  ],
  'class' => [
    'title' => 'Class',
    'group' => 'advanced',
  ],
  'height' => [
    'default' => '',
    'type' => 'text',
    'min' => 1,
    'max' => 100,
    'step' => 1,
    'options' => ['' => 'auto','25vh' => '25%','40vh' => '40%','50vh' => '50%','60vh' => '60%','65vh' => '65%','70vh' => '70%','75vh' => '75%','80vh' => '80%','85vh' => '85%','90vh' => '90%','95vh' => '95%','100vh' => '100%'],
    'title' => 'Height',
    'group' => 'size',
  ],
  'padding-top' => [
    'title' => 'Padding top',
    'group' => 'spacing',
  ],
  'padding-bottom' => [
    'title' => 'Padding bottom',
    'group' => 'spacing',
  ],
  'animation' => [
    'type' => 'select',
    'options' => ['' => 'None','fade-in' => 'Fade','expand' => 'Expand','move-left' => 'Left','move-right' => 'Right','move-up' => 'Up','move-down' => 'Down'],
    'group' => 'advanced',
  ],
  'lines-top' => [
    'title' => 'Lines top',
    'type' => 'radio', 'options' => ['' => 'No','waves' => 'Waves','diagonal' => 'Diagonal'], 'default' => '',
    'group' => 'spacing',
  ],
  'lines-bottom' => [
    'title' => 'Lines bottom',
    'type' => 'radio', 'options' => ['' => 'No','waves' => 'Waves','diagonal' => 'Diagonal'], 'default' => '',
    'group' => 'spacing',
  ],
  'display' => [
    'title' => 'Display',
    'type' => 'select',
    'options' => ['' => Config::tr('All', ['es' => 'Todos']), 'd-md-none' => 'Mobile', 'd-lg-none' => 'Mobile+Tablet', 'd-none d-md-block' => 'Tablet+Desktop', 'd-none d-lg-block' => 'Desktop'],
    'group' => 'display',
  ],
  'overflow' => [
    'type' => 'select', 'default' => '',
    'options' => [
      '' => 'auto','hidden' => 'hidden','visible' => 'visible','scroll' => 'scroll'
    ],
    'group' => 'display',
  ]
];
