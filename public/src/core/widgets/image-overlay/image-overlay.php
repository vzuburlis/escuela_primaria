<?php

if (strpos($data['text'], '<div') !== 0) {
    $data['text'] = '<div>' . $data['text'] . '</div>';
}
$areaStyle = "";
$areaStyle .= 'text-align:' . htmlentities($data['align'] ?? 'left');
$areaStyle .= ';align-items:' . htmlentities($data['align-items'] ?? 'unset');
$areaStyle .= ';gap:' . htmlentities($data['gap'] ?? '1') . 'em';
?>

  <?=View::css('core/widgets.css')?>
  <style>.overlay-text{width:50%;left:25%;position: absolute; top: 50%;transform: translateY(-50%);}
  @media screen and (max-width:600px){.overlay-text{width:80%;left:10%}}
  .overlay-text .g-btn,.overlay-text .btn{font-size:100%;padding:0.6em}</style>
  <div style="height:<?=$data['height']?>">
  <div style="font-size:<?=htmlentities($data['text_size'] ?? '120%')?>;
  <?=($data['align'] == 'left' ? 'left:10%' : '')?>;
  <?=($data['align'] == 'right' ? 'right:10%' : '')?>;" class="overlay-text">
    <div class="lazy componentarea" data-field="text"
    <?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>
    style="<?=$areaStyle?>">
      <?=$data['text']?>
    </div>
  </div>
  </div>

