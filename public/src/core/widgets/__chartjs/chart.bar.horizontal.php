<?php

$chartData = include __DIR__ . '/chartData.php';
foreach ($chartData['datasets'] as $ids => $ds) {
    $chartData['datasets'][$ids]['backgroundColor'] = $chartData['datasets'][$ids]['backgroundColor'] ?? [];
    $chartData['datasets'][$ids]['borderWidth'] = $chartData['datasets'][$ids]['borderWidth'] ?? 0;
    if (!isset($chartData['datasets'][$ids]['backgroundColor'])) {
        $chartData['datasets'][$ids]['backgroundColor'] = [];
        $colorsN = count($chartColors);
        foreach ($ds['data'] as $iv => $value) {
            $chartData['datasets'][$ids]['backgroundColor'][$iv] = $chartColors[$iv % $colorsN];
        }
    }
}

$options = $data['options'] ?? [
  'responsive' => true,
  'legend' => $data['legend'] == '' ? ['display' => false] : ['position' => $data['legend']],
  'title' => $data['title'] == '' ? ['display' => false] : ['display' => true,'text' => $data['title']],
  'scales' => [
    'xAxes' => [[
      'ticks' => ['beginAtZero' => true]
    ]]
  ]
];
?>

<script>
var randomScalingFactor = function() {
  return Math.round(Math.random() * 100);
};

window.addEventListener("load",function(event) {
  var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
  var <?=$chart?> = new Chart(<?=$ctx?>, {
    type: 'horizontalBar',
    data: <?=json_encode($chartData)?>,
    options: <?=json_encode($options)?>
  });
});

</script>
