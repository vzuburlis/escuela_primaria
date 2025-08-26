
<div class="container" style="text-align:center" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
  <div class="gallery-columns columns-<?=($widget_data->columns ?? 3)?>">
    <?php if (isset($widget_data->images)) {
        foreach (json_decode($widget_data->images) as $img) { ?>
    <img src="<?=htmlentities(View::thumb($img[0], 300))?>">
        <?php }
    } ?>
  </div>
</div>

