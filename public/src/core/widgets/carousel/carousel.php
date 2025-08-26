<div class="side-image <?= ($data['carousel-full-width'] == '1') ? 'position-absolute w-100 start-0 p-0' : 'container' ?>"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
<?php if (!isset($data['side']) || $data['side'] == 2) : ?>
    <?php include __DIR__ . '/carousel-div.php' ?>
<?php else : ?>
  <div data-container="img" class="<?=($data['side'] == 0 ? 'col1' : 'col2')?>">
    <?php include __DIR__ . '/carousel-div.php' ?>
  </div>
  <div data-field="text" class="<?=($data['side'] == 0 ? 'col2' : 'col1')?> componentarea">
    <?=$data['text']?>
  </div>
<?php endif; ?>
</div>

<?php if ($data['carousel-full-width'] == '1') {
    $c_size = 'height:50vw;';
    if (!empty($data['carousel-size'])) {
        if (is_numeric($data['carousel-size'])) {
            $c_size = 'height:' . (intval($data['carousel-size']) * 10) . 'vw;';
        } else {
            $c_size = 'height:' . htmlentities($data['carousel-size']) . ';';
        }
    }
    ?>
  <div style="pointer-events:none;<?=$c_size?>" class="position-relative mx-5"></div>
<?php } ?>
