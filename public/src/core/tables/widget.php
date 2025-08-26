<?php

$widget_areas = ['x' => '(None)'];
foreach (Gila\Config::$widget_area as $value) {
    $widget_areas[$value] = $value;
}
$widgets = [];
if (Gila\Table::$options) {
    foreach (Gila\Widget::getList('widget') as $k => $value) {
        $widgets[$k] = $k;
    }
}

return [
  'name' => 'widget',
  'title' => 'Widgets',
  'id' => 'id',
  'tools' => ['add_popup'],
  'commands' => ['edit_widget','clone','delete'],
  'list' => ['id','widget','title','area','pos','language','active'],
  'csv' => ['id','widget','title','area','pos','language','active'],
  'lang' => 'core/lang/admin/',
  'js' => ['src/core/tables/widget.js'],
  'sort_pos' => true,
  'sort_filter' => 'area',
  'search_box' => true,
  'permissions' => [
    'create' => ['admin','web_editor'],
    'update' => ['admin','web_editor'],
    'delete' => ['admin','web_editor']
  ],
  'search_boxes' => ['area'],
  'fields' => [
    'id' => [
      'title' => 'ID',
      'edit' => false,
      'create' => false,
    ],
    'widget' => [
      'title' => 'Type',
      'type' => 'select',
      'options' => $widgets,
      'create' => true,
      'qtype' => 'VARCHAR(255)'
    ],
    'title' => [
      'title' => 'Title',
      'qtype' => 'VARCHAR(255)',
      'maxlength' => 80,
    ],
    'area' => [
      'title' => 'Widget Area',
      'type' => 'select',
      'options' => $widget_areas,
      'qtype' => 'VARCHAR(255)',
      'create' => true,
    ],
    'active' => [
      'title' => 'Active',
      'type' => 'checkbox',
      'edit' => false,
      'create' => false,
      'qtype' => 'TINYINT DEFAULT 1',
      'toggle_values' => [0,1]
    ],
    'data' => [
      'title' => 'Data', 'list' => false, 'edit' => false, 'create' => false,
      'type' => 'text', 'allow_tags' => true,
      'qtype' => 'TEXT'
    ],
    'language' => [
      'title' => 'Language',
      'edit' => false,'create' => false,
      'qtype' => 'VARCHAR(2) DEFAULT NULL'
    ]
  ],
  'events' => [
    ['change', function (&$row) {
        if (!isset($row['data']) || $row['data'] !== null) {
            return;
        }
        $wdgt_options = Gila\Widget::getFields($row['widget']);
        $default_data = [];
        foreach ($wdgt_options as $key => $op) {
            if (isset($op['default'])) {
                $def = $op['default'];
            } else {
                $def = '';
            }
            $default_data[$key] = $def;
        }
        $row['data'] = json_encode($default_data);
    }]
  ]
];
