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
<div class="g-nav-toggler float-right mt-1" onclick="g('#nav').toggleClass('display');" style="font-size: 2.5em;text-align:right;padding-right: 0.5em;">
  <span>&#9776;</span>
</div>

<div id="headerlogo" class="container header-container d-block text-center">
  <?php View::includeFile('header.logo_link.php'); ?>
</div>

<div class="container header-container d-block text-center">
  <a class="only-sr only-focus" href="<?=Gila\View::$canonical?>#main" rel="nofollow">Skip to main content</a>
  
  <nav id="nav" class="inline-flex g-nav g-nav-mobile"
  style="text-align:<?=Config::get('theme.menu-align')??'center'?>;">
    <div class="g-nav-toggler" onclick="g('#nav').toggleClass('display');" style="font-size: 1.5em;text-align:right; padding-right: 0.5em;color:#333">&#10799;</div>
    <ul class="g-nav" style="margin:0" data-menu="mainmenu">
      <?php View::menu('mainmenu');?>
      <?php Event::fire('mainmenu.after');?>
      <?php View::includeFile('header.user-menu.php') ?>
    </ul>
  </nav>
  <?php /*!--div class="g-nav-mobile">
    <?=View::widgetBody('search')?>
  </div--*/ ?>
</div>
<?php Gila\Event::fire('header.after') ?>
</header>

<div id="main">
