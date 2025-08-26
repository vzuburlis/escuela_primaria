<div class="lazy container" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
<?=View::css('core/widgets.css')?>
<?php
@include_once 'src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
$template = empty($data['mustache']) ? file_get_contents(__DIR__ . '/features.mustache') : $data['mustache'];
$m = new Mustache_Engine(['entity_flags' => ENT_QUOTES,'preserve_handlebar_syntax' => true]);

$mdata = [
  'items' => json_decode($data['features'], true),
  'text_align' => $data['align'] ?? 'center',
  'orientation' => $data['image-orientation'] ?? '',
  'link_type' => $data['link_type'] ?? 'g-btn-secondary',
  'link_target' => $data['link_target'] ?? '_self',
  'link_text' => $data['link_text'] ?? 'Learn More',
];
foreach ($mdata['items'] as $i => $item) {
    $mdata['items'][$i] = [
    'image' => View::thumb($item[0], 300),
    'name' => $item[1] ?? '',
    'description' =>  $item[2] ?? '',
    'url' =>  $item[3] ?? '',
    ];
}
$template = strtr($template, ['%7B%7B' => '{{','%7D%7D' => '}}']);
echo $m->render($template, $mdata);
?>
</div>
