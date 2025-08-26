<?php
if (!isset($gtable)) {
    $gtable = new Table($table, Session::permissions());
}
$t = $gtable->getTable();
if (!isset($t['root_tables']) && !isset($t['child_tables'])) {
    include __DIR__ . '/content-vue.php';
    return;
}
$nav_items = [];
$row = $gtable->getRow(['id' => $id]);
$crow = $row;

// crow is not found
$first_label = $crow['name'] ?? $crow['title'];
foreach ($t['root_tables'] as $rtable => $rfield) {
    $_GET[$rfield] = $crow[$rfield];
    $_REQUEST[$rfield] = $crow[$rfield];
    $GLOBALS[$rfield] = $crow[$rfield];
    $ptable = DB::table($rtable);
    $crow = $ptable->getWith('id', $crow[$rfield]);
    array_unshift($nav_items, [
    'label' => $crow['name'] ?? $crow['title'],
    'url' => 'admin/contentNav/' . $rtable . '/' . $crow['id'],
    ]);
}
?>
<div class="mt-3 p-2 bg-light" style="font-size:18px">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="admin/content/<?=($rtable ?? $table)?>"><i class="fa fa-home"></i></a>
      </li>
<?php foreach ($nav_items as $item) : ?>
      <li class="breadcrumb-item">
        <a href="<?=$item['url']?>"><?=$item['label']?></a>
      </li>
<?php endforeach; ?>
      <li class="breadcrumb-item">
        <span><?=$first_label?></span>
      </li>
    </ol>
  </nav>
</div>

<?php

$tables = [];
$gtables = [];
$links = [];
foreach ($t['child_tables'] as $rtable => $rfield) {
  //! GLOBALS & REQUEST are used from table schema
    $_GET[$rfield] = $row['id'];
    $_REQUEST[$rfield] = $row['id'];
    $GLOBALS[$rfield] = $row['id'];
    $tables[] = $rtable;
    $ctable = new Table($rtable, Session::permissions());
    $gtables[] = $ctable;
    $links[] = [$ctable->getTable()['title'], function () {
    }];
}


$baseUrl = 'admin/contentNav/' . $table . '/' . $id;
if (count($links) > 1) {
    View::part('core@tabs', ['links' => $links,'baseUrl' => $baseUrl,'cookie_name' => 'admin_cnav_' . $table]);
}

$tab = Request::get('tab') ?? 0;
$table = $tables[$tab];
$gtable = $gtables[$tab];


if (isset($gtable->getTable()['child_tables'])) : ?>
  <script>
  document.body.addEventListener("load", function(){
    gtableFieldDisplay.title = function(rv) {
      return '<a href="admin/contentNav/<?=$table?>">'+rv.title+'</a>'
    }
  })
  </script>
<?php endif;

include __DIR__ . '/content-vue.php';
