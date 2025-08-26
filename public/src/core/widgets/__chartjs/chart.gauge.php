<?php

$chartData = include __DIR__ . '/chartData.php';
$total = $data['total'] ?? (float)$chartData['datasets'][0]['label'];
$value = $data['value'] ?? (float)$chartData['labels'][0];
$data = [
  $total / 4,
  $total / 2,
  $total * 3 / 4,
  $total,
]
?>

<script>


window.addEventListener("load",function(event) {
  var <?=$ctx?> = document.getElementById('<?=$canvas_id?>').getContext('2d');
  window.<?=$chart?> = new Chart(<?=$ctx?>,   {
  type: 'gauge',
  data: {
    //labels: ['Success', 'Warning', 'Warning', 'Error'],
    datasets: [{
      data: <?=json_encode($data)?>,
      value: <?=$value?>,
      backgroundColor: ['green', 'yellow', 'orange', 'red'],
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    title: {
      display: false,
      text: '<?=$data['title'] ?? 'Gauge'?>'
    },
    layout: {
      padding: {
        bottom: 30
      }
    },
    needle: {
      // Needle circle radius as the percentage of the chart area width
      radiusPercentage: 2,
      // Needle width as the percentage of the chart area width
      widthPercentage: 3.2,
      // Needle length as the percentage of the interval between inner radius (0%) and outer radius (100%) of the arc
      lengthPercentage: 80,
      // The color of the needle
      color: 'rgba(0, 0, 0, 1)'
    },
    valueLabel: {
      formatter: Math.round
    }
  }})
});

</script>
