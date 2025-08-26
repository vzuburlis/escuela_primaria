<?php
$menuName = 'mainmenu';
$menuName = (Config::lang() == Config::get('language')) ? $menuName : $menuName . '.' . Config::lang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu = '{"type":"menu","children":' . $_POST['mainmenu'] . '}';
    Menu::setContents($menuName, $menu);
    Response::success();
}
View::script('lib/vue/vue.min.js');
View::script('core/lang/content/' . Config::get('language') . '.js');
View::script('core/admin/media.js');
View::script('core/admin/vue-components.js');
?>
<style>.g-switch{z-index:1;vertical-align: middle;}
#mainmenu-form>div>*{min-width:30%;display:block}</style>

<div class="gm-12">
<?php View::alerts(); ?>
<form id="mainmenu-form" method="post" action="<?=Config::base('blocks/mainmenu')?>" class="g-form">
  <input type="hidden" name="submit-btn">

  <br>
  <?php
    $menu = Menu::getData($menuName)['children'];

    echo Form::input(
        'mainmenu',
        [
        'type' => 'menu'
        ],
        json_encode($menu),
        __("Menu")
    )
    ?>


</form>
</div>
