<?php

if (strpos($data['text'], '<div') !== 0) {
    $data['text'] = '<div>' . $data['text'] . '</div>';
}

$container = 'container';
if (!empty($data['container-class'])) {
    $container .= ' container-' . $data['container-class'] . ' ' . $data['container-class'];
}
?>

<?=View::cssAsync('core/widgets.css')?>

<div class="text-block container componentarea columnarea lazy" data-field="text">
  <?=$data['text']?>
</div>

