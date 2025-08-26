<?=View::cssAsync('core/circle-bar.css')?>
<?php $value = $data['value'] ?>
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
