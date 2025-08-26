<?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
<?php
$c_size = 'height:50vw;';
if (!empty($data['carousel-size'])) {
    if (is_numeric($data['carousel-size'])) {
        $c_size = 'height:' . (intval($data['carousel-size']) * 10) . 'vw;';
    } else {
        $c_size = 'height:' . htmlentities($data['carousel-size']) . ';';
    }
}
?>
<div id="Carousel<?=$data['widget_id']?>" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php if (isset($data['items'])) {
        foreach (json_decode($data['items']) as $i => $item) { ?>
        <button type="button" data-bs-target="#Carousel<?=$data['widget_id']?>" data-bs-slide-to="<?=$i?>" <?= $i == 0 ? 'class="active"' : '' ?> aria-current="true" aria-label="Slide <?=$i + 1?>"></button>
        <?php }
    }?>
  </div>
  <div class="carousel-inner">
    <?php if (isset($data['items'])) {
        foreach (json_decode($data['items']) as $i => $item) { ?>
        <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>" data-bs-interval="<?=( ($data['duration-in-seconds'] > 0) ? intval($data['duration-in-seconds']) * 1000 : 5000) ?>">
          <div class="d-block w-100" style="<?=$c_size?>background-image: url(<?=htmlentities(View::thumb($item[0], 1200))?>);
          background-size:<?=$data['images-size'] ?? 'cover'?>;background-repeat: no-repeat; background-position:center"> </div>
          <div class="carousel-caption d-none d-md-block <?=( isset($data['text-align']) ? 'text-' . $data['text-align'] : '') ?>"
          style="height:min-content;<?=( isset($data['vertical-align']) ? 'top:' . (($data['vertical-align']) * 10) . '%;' : '') ?><?=( isset($data['bg-opacity']) ? 'background-color:rgba(0,0,0,' . (($data['bg-opacity']) * 0.1) . ');' : '') ?>">
              <?php if (isset($item[1])) { ?>
              <h2 class="h2" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $item[1] ?></h2>
              <?php } ?>
              <?php if (isset($item[2])) { ?>
              <p class="h4" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $item[2] ?></p>
              <?php } ?>
              <?php if (!($item[3]) == '') { ?>
              <a class="btn btn-success" style="font-size:<?= (isset($data['button-font-size']) ? $data['button-font-size'] . 'px' : '24px') ?>" href="<?=$item[3]?>"><?= $data['button-title'] ?></a>
              <?php } ?>
          </div>
          <div class="d-md-none <?=( isset($data['text-align']) ? 'text-' . $data['text-align'] : '') ?>"
          style="height:min-content;<?=( isset($data['vertical-align']) ? 'margin-top:34px;margin-bottom:34px;' : '') ?><?=( isset($data['bg-opacity']) ? 'background-color:rgba(0,0,0,' . (($data['bg-opacity']) * 0.1) . ');' : '') ?>">
              <?php if (!empty($item[1])) { ?>
              <h2 class="h2" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $item[1] ?></h2>
              <?php } ?>
              <?php if (isset($item[2])) { ?>
              <p class="h4" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $item[2] ?></p>
              <?php } ?>
              <?php if (!($item[3]) == '') { ?>
              <a class="btn btn-success" style="font-size:<?= (isset($data['button-font-size']) ? $data['button-font-size'] . 'px' : '24px') ?>" href="<?=$item[3]?>"><?= $data['button-title'] ?></a>
              <?php } ?>
          </div>
        </div>
        <?php }
    }?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#Carousel<?=$data['widget_id']?>" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#Carousel<?=$data['widget_id']?>" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
