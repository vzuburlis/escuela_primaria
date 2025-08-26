<?php

$widget_data = [];
$widget = include Widget::getWidgetFile($type);
$fields = include Config::src() . '/core/models/block-fields.php';
$widget_data['widget_id'] = $widget_id;
$widget_data['fields'] = $fields;
if (isset($widget['fields'])) {
    foreach ($widget['fields'] as $key => $field) {
        $widget_data['fields'][$key] = $field;
        if (empty($widget_data['fields'][$key]['group'])) {
            $widget_data['fields'][$key]['group'] = 'param';
        }
        if ($widget_data['fields'][$key]['group'] == 'text') {
            $widget_data['fields'][$key]['group'] = 'param';
        }
    }
}
foreach ($widget_data['fields'] as $key => $field) {
    if (!isset($widget_data['fields'][$key]['title'])) {
        $widget_data['fields'][$key]['title'] = Config::tr(ucfirst(strtr($key, ['_' => ' '])));
    } else {
        $widget_data['fields'][$key]['title'] = Config::tr($widget_data['fields'][$key]['title']);
    }
}
if ($id !== 'new') {
    $widget_data['data'] = $widgets[$pos];
}
$widget_data['links'] = $widget['links'];
echo json_encode($widget_data);
