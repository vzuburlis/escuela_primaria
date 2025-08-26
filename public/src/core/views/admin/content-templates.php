<style>
  :root{
  --admin-primary-color:var(--main-primary-color);
}

</style>

<h2><?=__('Select template', ['es' => 'Elige plantilla'])?></h2>
<?php
$types = [
  1 => __('Website', ['es' => 'Sitio web']), 2 => __('Page', ['es' => 'Página']), 3 => __('Section', ['es' => 'Sección']),
];
if (FS_ACCESS || !class_exists('Website')) {
    $types[10] = __('My templates', ['es' => 'Mis plantillas']);
}

$type = $_GET['type'] ?? 2; //landing page
if (!isset($_GET['type']) && DB::value("SELECT COUNT(*) FROM page;") == 0) {
    $type = 1; //website
}
$postUrl = 'https://' . Config::get('gcloud.domain') . '/addons/page_templates?type=' . $type . '&key=' . Config::get('license');
if ($_GET['q']) {
    $postUrl .= '&q=' . urlencode($_GET['q']);
}
$res = new HttpPost($postUrl, [], ['method' => 'GET']);
$templates = [];
if ($data = $res->json()) {
    $templates = $data->items;
}

if ($type == 2) {
    array_unshift($templates, [
    'title' => Config::tr('Empty', ['es' => 'Vacia']),
    'image' => null,
    'id' => 0,
    ]);
}
?>
<div class="row mx-0">
<ul class="col-md-6 nav nav-pills mt-2">
  <?php foreach ($types as $k => $name) : ?>
  <li class="nav-item">
    <a class="nav-link <?=($type == $k ? 'active' : '')?>" href="blocks/contentNew/<?=$table?>?type=<?=$k?>"><?=$name?></a>
  </li>
  <?php endforeach; ?>
</ul>
<form action="blocks/contentNew/<?=$table?>" method="get" class="col-md-6 d-flex justify-content-end" style="height:fit-content">
  <input type=hidden value="<?=$type?>" name="type">
  <input class="form-control" name="q" value="<?=htmlentities($_GET['q'] ?? '')?>" style="max-width:260px">
  &nbsp;&nbsp;<button class="btn btn-outline-primary"><?=__('Search', ['es' => 'Buscar'])?></button>
</form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px,1fr));
    gap:1em;margin-top:2em;justify-items:center">
<?php

foreach ($templates as $i => $tm) {
    $tm = (array)$tm;
    $href = Config::base('blocks/contentCreate/' . $table . '/' . $tm['id']);
    ?>
      <div style="text-align:center;position:relative;display:inline-block;margin-top:1em;">
      <div class="text-align-left small"><?=__($tm['title'])?></div>
      <a href="<?=$href?>" onclick="g.loader()">
        <div style="overflow:hidden;text-align:center">
          <div style="width:300px;height:150px;background-color:lightgrey;background-position:center;
          background-size:contain;background-repeat:no-repeat"
          data-image="<?='https://' . Config::get('gcloud.domain') . '/' . $tm['image']?>"
          class="border lazy" :title="<?=__($tm['title'])?>"></div>
        </div>
      </a>
      </div>
    <?php
} ?>
</div>
