
<div class="container" style="text-align:center" data-container="*">
<div class="gallery--grid gallery--grid-<?=$data['orientation']?>"
style="gap:<?=$data['gap'] ?? 1?>em;filter:<?=$data['filter'] ?? 'none'?>;">
<?=View::css('core/widgets.css')?>
<?php if (isset($data['images'])) {
    foreach (json_decode($data['images']) as $img) { ?>
    <div data-image="<?=htmlentities(View::thumb($img[0], 400))?>"
    onclick="lbImage('<?=$img[0]?>','<?=$img[1]?>')" style="cursor:pointer"
    class="gallery-item photo-<?=$data['orientation']?> <?=(!empty($data['class']) ? $data['class'] : '')?> lazy position-relative">
        <?=($data['titles'] == 1 ? '<div class="gallery-item-title">' . $img[1] . '</div>' : '')?>
    </div>
    <?php }
} ?>
</div>
<script>
function lbImage(x,y='') {
  if (typeof appEditMenu!='undefined') return
  document.body.insertAdjacentHTML('afterend','<div style="z-index:99999;position:fixed;left:0;top:0;bottom:0;right:0;background:#000000E0" onclick="this.remove()"><div style="text-align:center;max-height:100%;top: 50%;left: 50%;position:fixed;transform: translate(-50%,-50%);"><img src="'+x+'" style="max-height:95vh"><p style="color:white">'+y+'</p></div></div>')
}
</script>
</div>

