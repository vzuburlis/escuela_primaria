
<div class="features-grid lazy container" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?=View::css('core/widgets.css')?>
<?php foreach (json_decode(@$widget_data->features) as $feature) {
    echo '<div style="text-align:' . htmlentities($data['align'] ?? 'center') . '">';
    if (empty($data['image-orientation'])) {
        echo View::imgLazy($feature[0], 400);
    } else {
        ?>
  <div data-image="<?=htmlentities(View::thumb($feature[0], 400))?>" 
  style="display:inline-block;max-width:100%"
  class="gallery-item photo-<?=$data['image-orientation']?> lazy">
  </div>
        <?php
    }
    echo '<h3>' . htmlentities($feature[1]) . '</h3>';
    echo '<p class="inline-edit-">' . htmlentities($feature[2]) . '</p>';
    if ($feature[3] != '') {
        $class = $data['link_type'] ?? 'g-btn-secondary';
        echo '<br><a href="' . htmlentities($feature[3]) . '" target="' . ($data['link_target'] ?? '_self') . '"
    class="btn-features ' . $class . '">' . ($data['link_text'] ?? 'Learn More') . '</a>';
    }
    echo '</div>';
} ?>
</div>

