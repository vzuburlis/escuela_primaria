
<script src="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.2.0/glide.min.js" integrity="sha512-IkLiryZhI6G4pnA3bBZzYCT9Ewk87U4DGEOz+TnRD3MrKqaUitt+ssHgn2X/sxoM7FxCP/ROUp6wcxjH/GcI5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Required Core Stylesheet -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.2.0/css/glide.core.min.css" integrity="sha512-YQlbvfX5C6Ym6fTUSZ9GZpyB3F92hmQAZTO5YjciedwAaGRI9ccNs4iw2QTCJiSPheUQZomZKHQtuwbHkA9lgw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.2.0/css/glide.theme.min.css" integrity="sha512-wCwx+DYp8LDIaTem/rpXubV/C1WiNRsEVqoztV0NZm8tiTvsUeSlA/Uz02VTGSiqfzAHD4RnqVoevMcRZgYEcQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php
$elementID = 'glider' . $data['widget_id'];
$itemsToShow = $data['items-to-show'] ?? ($data['items-to-show'] ?? 3);
if (UserAgent::info()['device'] == 'MOBILE') {
    $itemsToShow = 1;
}
?>
<div class="glide text-center" id=<?=$elementID?>>
<div class="glide__track" data-glide-el="track">
<ul class="glide__slides">
<?php
  $item_height = $data['item_height'] ?? '300';
  $item_width = $data['item_width'] ?? $data['item_width'];
  $elStyle = "height:{$item_height}px;";
  $elStyle .= "width:{$item_width}px;";
if (!empty($data['item_radius']) > 0) {
    $elStyle .= "border-radius:{$data['item_radius']};";
}
foreach (json_decode($data['images'], true) as $i => $image) {
    echo '<li class="glide__slide p-4" onclick="' . $elementID . '_go(' . $i . ')">';
    echo '<h6 class="text-align-center glide__name" style="width:' . $data['item-width'] . 'px">' . $image[1] . '</h6>';
    echo '<div id=' . $elementID . 'Img' . $i . ' class="glide__img" style="' . $elStyle . 'text-align:center;background-size:cover;background-position:center;background-image:url(' . htmlentities(View::thumb($image[0], 400)) . ')"></div>';
    echo '</li>';
}
?>
</ul>
</div>

<div class="text-align-center p-2 position-relative glide__footer" style="height:<?=($data['footer_height'] ?? '300')?>px">
<?php
foreach (json_decode($data['images'], true) as $i => $image) {
    echo '<div style="position:absolute;width:100%;opacity:0" class="glide__text" id=' . $elementID . 'Text' . $i . '>';
    echo '<h3 class="glide__caption">' . htmlentities($image[2]) . '</h3>';
    echo '<p>' . htmlentities(nl2br($image[3])) . '</p>';
    echo '</div>';
}
?>
</div>
</div><!--/glide-->

<style>
.glide__text, .glide__name {
  opacity: 0;
}
.opacity-1{
  opacity:1!important;
}
#<?=$elementID?> .glide__slide .glide__img, .glide__text, .glide__name {
  transition: all 0.6s;
}
#<?=$elementID?> .glide__img {
  transform: scale(0.8);
}
#<?=$elementID?> .glide__slide--active{
  z-index:1;
}
#<?=$elementID?> .glide__slide--active .glide__img {
  transform: scale(1)!important;
}
#<?=$elementID?> .glide__slide--active .glide__name {
  opacity: 1!important;
}

#<?=$elementID?> .glide__slide {
  margin:auto;
  justify-content:center;
  display:grid;
}

/*
@media only screen and (max-width:600px){
  #<?=$elementID?> .glide__slide {
    margin-left:-60px
  }
}
*/
</style>



<script>
var V<?=$elementID?>;
window.addEventListener('load', function() {
  V<?=$elementID?> = new Glide(document.querySelector('#<?=$elementID?>'), {
    type: 'carousel',
    startAt: 0,
    perView: <?=$itemsToShow?>,
    animationDuration: 200,
    focusAt:'center',
  });

  V<?=$elementID?>.on(['run'], function(){
    i = V<?=$elementID?>.index
    g('#<?=$elementID?> .glide__text').removeClass('opacity-1')
    g('#<?=$elementID?> .glide__text').all[i].classList.add('opacity-1')
  })
  
  V<?=$elementID?>.mount()
  g('#<?=$elementID?> .glide__text').all[0].classList.add('opacity-1')
});

function <?=$elementID?>_go(i) {
  V<?=$elementID?>.go('='+(i))
}

function <?=$elementID?>_mo(i) {
  V<?=$elementID?>.go('='+(i))
}

</script>
