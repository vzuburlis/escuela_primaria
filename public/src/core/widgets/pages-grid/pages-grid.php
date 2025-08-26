
<div class="block-items-grid container" data-container="*"
<?=($data['animation'] ? 'style="animation:' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<style>.widget-items{grid-column: 1/span 4;text-align: center;}
.items-grid>div{text-align:left;padding:1em;display:grid;grid-template-columns:1fr 2fr;grid-gap: 0.5em;}
.items-grid img,.items-grid svg{max-width:100%;height:auto;min-width:70px}
.items-grid h3{margin-top:0}</style>
<div class="items-grid" style="display: grid; grid-gap:20px;padding:20px;justify-content: center; grid-template-columns: repeat(auto-fit, minmax(240px,320px)); width:100%">
<?php
$table = new Gila\Table('page');
$filters = ['slug' => ['begin' => $data['prefix']], 'language' => Gila\Config::lang()];
if ($data['src'] == 'demo') {
    $pages = Config::include('data/demo.pages.php');
} else {
    $pages = DB::getAssoc("SELECT title,`image`,slug,blocks FROM page WHERE publish=1 AND slug like '{$data['prefix']}%' ORDER BY id DESC");
}
foreach ($pages as $page) {
    $image = $page['image'] ?? null;
    $blocks = json_decode($page['blocks'], true);
    if (!$image) {
        foreach ($blocks as $block) {
            if (in_array($block['_type'], ['gallery','gallery--grid'])) {
                        $image = json_decode($block['images'])[0][0] ?? $image;
            }
        }
    }
    ?>
  <div>
    <div>
      <?=View::imgLazy($image, 300)?>
    </div>
    <div>
      <h3><?=htmlentities($page['title'])?></h3>
      <!--p></p-->
      <a href="<?=Config::base($page['slug'])?>"><?=$data['link_text'] ?? '→'?></a>
    </div>
  </div>
<?php } ?>
</div>

</div>
