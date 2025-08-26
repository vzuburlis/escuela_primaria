<?php

if (strpos($data['text'], '<div') !== 0) {
    $data['text'] = '<div>' . $data['text'] . '</div>';
}

$innerPrepend = '';
if (!empty($data['text-bg-color']) && !empty($data['text-bg-alfa']) && $data['text-bg-alfa'] > 0) {
    $bg = $data['text-bg-color'];
    $innerPrepend .= '<div style="background-color:rgba(';
    $innerPrepend .= hexdec($bg[1] . $bg[2]) . ',' . hexdec($bg[3] . $bg[4]) . ',' . hexdec($bg[5] . $bg[6]);
    $innerPrepend .= ',' . htmlentities($data['text-bg-alfa'] ?? '0') . ');';
    $innerPrepend .= 'position:absolute;width:100%;height:100%;top:0;z-index:-1"></div>';
}

$areaStyle = "";
$areaStyle .= 'text-align:' . htmlentities($data['text-align'] ?? 'left');
$areaStyle .= ';align-items:' . htmlentities($data['align-items'] ?? 'unset');
$areaStyle .= ';justify-items:' . htmlentities($data['justify-items'] ?? 'unset');
$areaStyle .= ';gap:' . htmlentities($data['gap'] ?? '1') . 'em';
if (!empty($data['padding'])) {
    $areaStyle .= ';padding:' . htmlentities($data['padding']);
}

if (!empty($data['container-mw'])) {
    $areaStyle .= ';max-width:' . htmlentities($data['container-mw']);
}
if (!empty($data['height'])) {
    $areaStyle .= ';height:' . htmlentities($data['height']) . ';';
}

$container = 'container';
if (!empty($data['container-class'])) {
    $container = 'container-' . $data['container-class'];
}
if (!empty($data['grid'])) {
    $container .= ' grid__' . $data['grid'];
}

if (!empty($data['is-form']) && $data['is-form'] == 1) {
  //echo '<form oninput="console.log(0)"><style>input:invalid{  border: none;outline: 1px solid red;}</style>';
}
?>

<?=View::cssAsync('core/widgets.css')?>

<div class="text-block <?=$container?> componentarea columnarea lazy" data-field="text"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>
<?=($data['hide-grid'] && $data['hide-grid'] == 1 ? 'data-hg=1 ' : '')?>
style="<?=$areaStyle?>">
  <?=$data['text']?>
</div>

<?php
if (!empty($data['is-form']) && $data['is-form'] == 1) {
  //echo '</form>';
}
