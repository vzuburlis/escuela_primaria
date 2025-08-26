<?=View::css('core/gila.min.css')?>
<div class="lazy" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?php
$data['n_post'] = $data['n_post'] ?? 4;
$data['category'] = $data['category'] ?? null;
$gen = Gila\Post::getPosts(
    ['posts' => $data['n_post'], 'category' => $data['category'], 'language' => Config::lang()]
);

$posts = [];
foreach ($gen as $i => $r) {
    $posts[$i] = $r;
    $posts[$i]['href'] = Config::url('blog/' . $r['id'] . '/' . $r['slug']);
    $posts[$i]['img_thumb'] = htmlentities(View::thumb($r['img'], 400));
    $posts[$i]['created'] = date('F j, Y', strtotime($r['created']));
    $posts[$i]['description'] = ($r['description'] ?? $r['post']);
}

$mdata = [
  'posts' => $posts,
  'text_align' => htmlentities($data['align'] ?? 'center'),
  'category' => $data['category'] ?? 0,
];

require_once 'src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
$template = empty($data['mustache']) ? file_get_contents(__DIR__ . '/category-post--grid.mustache') : $data['mustache'];
$m = new Mustache_Engine(['entity_flags' => ENT_QUOTES,'preserve_handlebar_syntax' => true]);
$template = strtr($template, ['%7B%7B' => '{{','%7D%7D' => '}}']);
echo $m->render($template, $mdata);
?>
</div>

