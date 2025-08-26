
<div class="container" style="display: flex;justify-content: space-around;"
data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?=View::css('core/widgets.css')?>
<?=View::cssAsync('core/circle-bar.css')?>
<?php
foreach (json_decode($data['items'], true) as $item) {
    $value = $item[0]; ?>
<div style="width:160px;">
<div class="circle-bar" style="background-color:<?=$data['color']?>4f">
  <div class="circle">
    <div class="mask full r<?=$value?>">
      <div class="fill r<?=$value?>" style="background-color:<?=$data['color']?>"></div>
    </div>
    <div class="mask half">
      <div class="fill r<?=$value?>" style="background-color:<?=$data['color']?>"></div>
    </div>
    <div class="inside-circle"> <?=$value?>% </div>
  </div>
</div>
<div><?=$item[1]?></div>
</div>
    <?php
}
?>
</div>
