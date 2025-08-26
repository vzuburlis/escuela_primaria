<div class="container">
  <h2 class="my-4 text-center"><h2 class="my-4 text-center">
    <?=__("Select an application", ['es' => 'Elige una aplicación'])?>
  </h2>
<div class="row">

<?php if (!DB::getOne("SELECT id FROM page;")) :
    $href = './blocks/contentNew/page';
    if (isset($_COOKIE['template_id'])) {
        $href = 'blocks/contentCreate/page/' . $_COOKIE['template_id'];
    }
    ?>
  <div class="col-sm-4 my-3">
    <a href="<?=$href?>">
      <img class=rounded src=assets/vj/ab1.png style="width:100%">
    </a>
  </div>
<?php endif; ?>
<?php if (Config::inPackages('facturapi') && !Config::get('facturapi.organizationId')) : ?>
  <div class="col-sm-4 my-3">
    <a href="./admin-facturapi/createFactura?_use_app=facturapi">
      <img class=rounded src=assets/vj/ab2.png style="width:100%">
    </a>
  </div>
<?php endif; ?>
<?php if (DB::value("SELECT COUNT(*) FROM user;") < 2) : ?>
  <div class="col-sm-4 my-3">
    <a href="./admin/users">
    <img class=rounded src=assets/vj/ab3.png style="width:100%">
    </a>
  </div>
<?php endif; ?>
</div>
<div class="row">
<?php
foreach (Config::$amenu as $key => $app) :
    if (is_string($key) && Session::hasPrivilege('admin ' . ($app['access'] ?? ''))) : ?>
<div class="col-sm-4 my-3">
<a href="<?=$app['children'][0][1] ?? $app[1]?>" style="text-decoration:none">
  <div class="d-block">
  <div style="width:58px;height:58px;display:inline-block;text-align:center;padding:12px;text-shadow:2px 2px 4px #282828;background:<?=$app['color'] ?? '#282828'?>"
  class="rounded">
            <?php if (file_exists('src/core/assets/icon/' . $app['icon'] . '.svg')) : ?>
  <img class="text-white" src="src/core/assets/icon/<?=$app['icon']?>.svg">
            <?php else : ?>
  <i class="fa fa-2x fa-<?=$app['icon']?> text-white"></i>
            <?php endif; ?>
  </div>
  <span class="px-2" style="font-size:22px;color:var(--main-color);"><?=__($app[0])?></span>
  </div>
</a>
</div>

    <?php endif;
endforeach; ?>
</div>
  <p class="my-4 text-center">
    <a href="admin/packages">
    <?=__("Activate more apps from Administration\Packages", ['es' => 'Active mas apps desde Administración\Paquetes'])?> 🡪
    </a>
  </p>
</div>
