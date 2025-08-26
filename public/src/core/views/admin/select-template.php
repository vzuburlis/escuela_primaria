<head>
  <base href="<?=Config::base()?>">
  <?php View::$stylesheet = [] ?>
  <?=View::cssAsync('core/gila.min.css')?>
</head>

<div class="centered" style="width:100%;max-width:900px">
    <h2>Elige  modelo</h2>
<div class="gm-grid container" style="display:grid; grid-gap:1em; grid-template-columns:repeat(auto-fit, minmax(260px,1fr));
justify-items:center;margin-bottom:1em;overflow: auto;padding: 2em 2px;">
<?php

$templates = glob(__DIR__ . '/../../block-templates/*.json');

$href = Config::base() . 'blocks/display?t=page&id=' . htmlentities($id) . '&template=empty';
echo '<div class=""><a href="' . $href . '">';
echo '<div style="width:244px;height:163px;border:1px solid grey;background:white"></div><p>' . __('Empty') . '</p></a></div>';

foreach ($templates as $file) {
    $data = json_decode(file_get_contents($file), true);
    $href = Config::base() . 'blocks/display?t=page&id=' . htmlentities($id) . '&template=' . $data['file'];
    echo '<div class=""><a href="' . $href . '">';
    echo '<img src="lzld/thumb?size=244&src=' . $data['preview'] . '"><p>' . $data['name'] . '</p></a></div>';
}
?>
</div>
</div>
