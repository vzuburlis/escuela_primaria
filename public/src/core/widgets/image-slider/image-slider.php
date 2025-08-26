
<?=Gila\View::cssAsync('lib/glider/glider.min.css')?>
<?=Gila\View::script('lib/glider/glider.min.js')?>
<?php
$elementID = 'glider' . $data['widget_id'];
?>
<div class="glider-contain text-center">
<div class="container myglider" data-container="*" id=<?=$elementID?> style="scrollbar-width: none;overflow:hidden">
<?php
if ($data['cover-display'] ?? true) :
    foreach (json_decode($data['images'], true) as $image) {
        echo '<div><div style="height:600px;text-align:center;background-size:cover;background-position:center;background-image:url(' . htmlentities(View::thumb($image[0], 600)) . ')">';
        echo '<br>' . $image[1] . '</div></div>';
    }
else :
    foreach (json_decode($data['images'], true) as $image) {
        echo '<div style="display:flex;align-items:center;margin:10px"><img src="' . htmlentities(View::thumb($image[0], 800)) . '" alt="' . $image[1] . '">';
        echo '</div>';
    }
endif;
?>
</div>
<button role="button" aria-label="Previous" class="glider-prev" id="<?=$elementID?>-prev">&lsaquo;</button>
<button role="button" aria-label="Next" class="glider-next" id="<?=$elementID?>-next">&rsaquo;</button>
<div role="tablist" id="<?=$elementID?>dots"></div>
<script>
window.addEventListener('load', function(){
  new Glider(document.querySelector('#<?=$elementID?>'), {
    slidesToShow: '<?=$data['items-to-show'] ?? 'auto'?>',
    itemWidth: <?=$data['item-width'] ?? '"auto"'?>,
    dots: '#<?=$elementID?>dots',
    draggable: true,
    arrows: {
      prev: '#<?=$elementID?>-prev',
      next: '#<?=$elementID?>-next'
    }
  });
});
</script>
</div>

