<?=View::script('core/chartjs/Chart.bundle.min.js')?>
<?=View::script('core/chartjs/chartjs-gauge.js')?>
<?php
if (empty($data)) {
    $data = [
    'type' => $_GET['type'] ?? 'bar',
    'query' => '',
    'data' => 'query',
    'title' => 'Title'
    ];
}
$wid = $data['widget_id'] ?? 0;
$canvas_id = 'wdgt' . $wid . 'cnvs';
$ctx = 'wdgt' . $wid . 'ctx';
$chart = 'wdgt' . $wid . 'chart';
?>
<style>.widget-mysql-chart{grid-column: span 2;}</style>
<div style="width:100%;" class="container">
  <canvas id="<?=$canvas_id?>"></canvas>
</div>
<?php
$ds = $data['data_src'];
$dim1 = $data['set1'];
$val = $functions[$data['function']] ?? 'COUNT(*)';
if (empty($data['set2'])) {
    $dim2 = "\"$ds\"";
    $data['query'] = "SELECT $dim1 A,$dim2 B,$val C FROM $ds GROUP BY $dim1";
} else {
    $dim2 = $data['set2'];
    $data['query'] = "SELECT $dim1 A,$dim2 B,$val C FROM $ds GROUP BY $dim1,$dim2";
}
$data['data'] = 'mysql';

include __DIR__ . '/../__chartjs/chart.' . $data['type'] . '.php';
