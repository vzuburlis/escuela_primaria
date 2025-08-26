
<style>
.mce-window.mce-in {
  z-index: 99999 !important;
}
.tox-dialog-wrap {
  z-index: 99999 !important;
}
</style>
<form id="widget_options_form" class="notranslate edit-item-form">
<input type="hidden" value="<?=$widget->id?>" id='widget_id' name='widget_id'>

<div style="grid-column:span 2">
  <div class="g-label">Title</div>
  <input id="widget_title" name="widget_title" value="<?=htmlentities($widget->title)?>" class="form-control">
</div>

<div style="grid-column:span 2">
  <div class="g-label"><?=__('Widget Area')?></div>
  <select  id="widget_area" name="widget_area" class="form-select">
    <?php
    foreach (Config::$widget_area as $value) {
        $sel = ($widget->area == $value ? 'selected' : '');
        echo '<option value="' . $value . "\" $sel>" . ucfirst($value) . '</option>';
    }
    ?>
  </select>
</div>

<?php
$languages = Config::get('languages') ?? [Config::get('language')];
?>
<div style="grid-column:span 1">
  <div class="g-label"><?=__('Language')?></div>
  <select  id="widget_language" name="widget_language" class="form-select">
    <?php
    echo '<option value="">*</option>';
    foreach ($languages as $language) {
        $sel = ($widget->language == $language ? 'selected' : '');
        echo '<option value="' . $language . "\" $sel>" . strtoupper($language) . '</option>';
    } ?>
  </select>
</div>

<?=Form::html(['widget_active' => [
  'type' => 'checkbox', 'input_style' => "grid-column:span 1",
]], ['widget_active' => $widget->active ?? 0], 'option[', ']');?>


<?php

$widget_data = json_decode(Gila\DB::value("SELECT data FROM widget WHERE id=? LIMIT 1;", $widget->id));
$fields = Gila\Widget::getFields($widget->widget);

if (isset($fields)) {
    foreach ($fields as $key => $op) {
        $values[$key] = isset($widget_data->$key) ? $widget_data->$key : '';
    }
}
echo Form::html($fields, $values, 'option[', ']');
echo "</form>";
