<!DOCTYPE html>
<html lang="<?=Config::lang()?>" <?=(FS_ACCESS?'translate="no"':'')?>>
<?php
View::head();
View::script('core/gila.min.js');

$widthClass = Config::get('theme.page-width');
if ($widthClass=='container-fluid') {
  $widthClass = '';
}

$bstyle = "position:relative;background-image:".(Config::get('theme.page-background')?'url(\''.Config::get('theme.page-background').'\')':'unset');
$bstyle .= ";min-height:100vh;background-size:cover";
$battachment = Config::get('theme.attachment');
if (!empty($battachment) && $battachment=='fixed') {
  $bstyle .= ';background-attachment: fixed;';
}
?>

<body style="<?=$bstyle?>"
 class="<?=(Router::getPath()==''&&Router::getController()!='blocks'?'home':'')?> <?=$widthClass?>" id="top">

<?php
if (!isset($page) && Config::get('theme.header-overlay')=='all') {
  //Config::let('theme.header-overlay', 'no');
}
$tmp = Config::get('theme.header-template')??'';
if (!empty($tmp)) {
  View::renderFile('header--'.$tmp.'.php');
  return;
}
// continues the default header template
?>

<header id="header"
class="position-<?=Config::get('theme.header-position')?>
 overlay-<?=Config::get('theme.header-overlay')?>">
<?php
 if ($text = Config::get('theme.announcement-text')) {
  if ($link = Config::get('theme.announcement-link')) {
    $link = htmlentities($link);
    $text = strip_tags($text,'<b><a><i><u><sub><strong>');
    $text = "<a style='color:".(Config::get('theme.topbar-color')??'white')."' href='$link'>$text &rsaquo;&rsaquo;</a>";
  }
  echo "<div class='header-topbar' style='background:".Config::get('theme.announcement-color');
  echo ";color:".(Config::get('theme.topbar-color')??'white');
  echo ";text-align:center;justify-content:center;padding:1em 0'>$text</div>";
}
?>
<div class="container header-container" style="grid-template-columns:auto 1fr auto">
  <a class="only-sr only-focus" href="<?=Gila\View::$canonical?>#main" rel="nofollow">Skip to main content</a>

  <div class="g-nav-toggler" onclick="g('#nav').toggleClass('display');" style="font-size: 2em;text-align:right;padding-right: 0.5em;">
    <span>&#9776;</span>
  </div>

  <?php View::includeFile('header.logo.php'); ?>
  
  <nav id="nav" class="inline-flex g-nav g-nav-mobile"
  style="text-align:<?=Config::get('theme.menu-align')??'center'?>;">
    <div class="g-nav-toggler" onclick="g('#nav').toggleClass('display');" style="font-size: 1.5em;text-align:right; padding-right: 0.5em;color:#333">&#10799;</div>
    <ul class="g-nav" style="margin:0" data-menu="mainmenu">
      <?php View::menu('mainmenu');?>
      <?php Event::fire('mainmenu.after');?>
      <?php View::includeFile('header.user-menu.php') ?>
    </ul>
  </nav>
  <div>
    <?php Gila\Event::fire('header.actions') ?>
  </div>

  <?php /*!--div class="g-nav-mobile">
    <?=View::widgetBody('search')?>
  </div--*/ ?>
</div>
<?php Gila\Event::fire('header.after') ?>
</header>

<div id="main">
