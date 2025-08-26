<?php
Menu::bootstrap();
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
  <button class="navbar-toggler d-print-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
  </button>
  <div class="w-100">
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav" data-menu="mainmenu">
      <ul class="navbar-nav ml-auto">
        <?php View::menu('mainmenu');?>
        <?php Event::fire('mainmenu.after');?>
        <?php View::includeFile('header.user-menu.php') ?>
      </ul>
    </div>
  </div>
  <?php View::includeFile('header.logo.php'); ?>
</div>
</nav>

</div>
<?php Gila\Event::fire('header.after') ?>
</header>

<div id="main">
