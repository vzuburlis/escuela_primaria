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
$queries = Config::getList('chartjs-query');
if (isset($queries[$data['data']])) {
    $data = array_replace_recursive($queries[$data['data']], $data);
    $data['query'] = $queries[$data['data']]['query'];
    $data['data'] = 'mysql';
}
if (!isset($data['data'])) {
    $data['data'] = 'array';
}

include __DIR__ . '/../__chartjs/chart.' . $data['type'] . '.php';
