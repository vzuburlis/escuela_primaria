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
  <?php View::includeFile('header.logo.php'); ?>
  <button class="navbar-toggler d-print-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <?=(Config::get('theme.navbar-toggler-menu')==1 ? __('Menu', ['es'=>'Menú']) : '')?>
      <span class="navbar-toggler-icon"></span>
  </button>
  <div class="w-100">
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul id="nav" class="navbar-nav ml-auto <?=$class_menu_align?> w-100 gap-1" data-menu="mainmenu">
      <?php View::menu('mainmenu');?>
      <?php Event::fire('mainmenu.after');?>
    </ul>
    <ul class="nav navbar-nav navbar-right align-items-center">
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
