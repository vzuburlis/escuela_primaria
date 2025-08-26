<?php
Menu::bootstrap();
$menu_align = Config::get('theme.menu-align')??'end';
if ($menu_align=='left') $menu_align='start';
if ($menu_align=='right') $menu_align='end';
$class_menu_align = 'justify-content-'.$menu_align;
?>

<header id="header"
class="position-<?=Config::get('theme.header-position')?>
 overlay-<?=Config::get('theme.header-overlay')?>">
<?php
 if ($text = Config::get('theme.announcement-text')) {
  if ($link = Config::get('theme.announcement-link')) {
    $link = htmlentities($link);
    $text = "<a style='color:".(Config::get('theme.topbar-color')??'white')."' href='$link'>$text &rsaquo;&rsaquo;</a>";
  }
  echo "<div class='header-topbar' style='background:".Config::get('theme.announcement-color');
  echo ";color:".(Config::get('theme.topbar-color')??'white');
  echo ";text-align:center;justify-content:center;padding:1em 0'>$text</div>";
}
?>
<div class="container">
  <a class="only-sr only-focus" href="<?=Gila\View::$canonical?>#main" rel="nofollow">Skip to main content</a>
  
<nav class="navbar navbar-expand-lg w-100">
<div class="container-fluid">
  <span class="d-lg-none">
    <?php View::includeFile('header.logo_link.php'); ?>
  </span>
  <button class="navbar-toggler d-print-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
  </button>
  <div class="w-100">
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav" style="flex-direction:column">
    <ul id="nav" class="navbar-nav align-items-center ml-auto <?=$class_menu_align?> w-100" data-menu="mainmenu">
      <?php
      $menu_data = self::$menu['mainmenu'] ?? Menu::getData('mainmenu');
      $items = Menu::convert($menu_data);
      if (ceil(count($items)/2)>0) {
        $ch = array_chunk($items, ceil(count($items)/2), true);
        echo Menu::getHtml($ch[0]);
        echo '<li class="d-none d-lg-inline" id="headerlogo">';
        View::includeFile('header.logo_link.php');  
        echo '</li>';
        echo Menu::getHtml($ch[1]??[]);
      } else {
        View::includeFile('header.logo_link.php');  
        echo Menu::getHtml($items);
        echo '</li>';
      }
      ?>
      <?php Event::fire('mainmenu.after');?>
    </ul>
    <ul class="nav navbar-nav navbar-right align-items-center" style="min-width:200px">
      <?php View::includeFile('header.user-menu.php') ?>
    </ul>
  </div>
  </div>
</div>
</nav>

</div>
<?php Gila\Event::fire('header.after') ?>
</header>

<div id="main">

