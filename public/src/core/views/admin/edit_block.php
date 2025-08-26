
<form id="widget_options_form" class="g-form notranslate"
style="display: grid; grid-template-columns: 1fr 1fr;gap:1em">
<input type="hidden" value="<?=$widget_id?>" id='widget_id' name='widget_id'>
<?php
Gila\Config::loadLang('core/lang/editor/');
$widget_data = [];
$exludeFields = ['_type', 'alfa', 'background-color', 'bg-color',
'positionY', 'video', 'height', 'id', 'animation', 'lines-top', 'lines-bottom', 'attachment'];

$widget = include Widget::getWidgetFile($type);


if ($group && $group != '*') {
    $fields = [];
    foreach ($widget['fields'] as $key => $field) {
        if ($field['group'] && $field['group'] == $group) {
            $fields[$key] = $field;
        }
    }
} else {
    if ($pos !== 'new') {
        foreach ($widget['fields'] as $key => $field) {
            if (isset($field['type'])) { // && in_array($field['type'], ['list','text'])) {
                $fields[$key] = $field;
            }
        }
    } else {
        $fields = $widget['fields'];
    }
}

if ($pos !== 'new') {
    $widget_data = $widgets[$pos];
}


if (isset($fields)) {
    foreach ($fields as $key => $op) {
        $values[$key] = $fields[$key]['default'] ?? '';
        if ($pos !== 'new') {
            if ($fields[$key]['group'] != 'mustache') {
                unset($fields[$key]['default']);
            }
            $values[$key] = $widget_data[$key] ?? '';
        } elseif (isset($fields[$key]['group']) && $fields[$key]['group'] == 'text') {
            unset($fields[$key]);
        }
        if (in_array($key, $exludeFields)) {
            unset($fields[$key]);
        }
        if (isset($fields[$key]['group'])) {
            if ($fields[$key]['group'] == 'text' && $group !== 'text') {
                unset($fields[$key]);
            }
            if ($fields[$key]['group'] == 'mustache' && $group !== 'mustache') {
                unset($fields[$key]);
            }
        }

        if (isset($fields[$key]['group']) && $fields[$key]['group'] == 'mustache') {
            $values[$key] = strtr($widget_data[$key], ['%7B%7B' => '{{','%7D%7D' => '}}']) ?? '';
        }
    }
}

echo Gila\Form::html($fields, $values, 'option[', ']');
echo "</form>";

if ($pos == 'new') {
    if (isset($widget['links'])) {
        foreach ($widget['links'] as $link) {
            echo '<a href="' . $link['url'] . '" target="_blank" class="g-btn">' . __($link['label'], $link['tr'] ?? null) . ' &rarr;</a>';
        }
    }
}
