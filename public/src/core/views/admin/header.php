<?php

use Gila\Config;
use Gila\View;
use Gila\Session;
use Gila\Menu;

?>
<!DOCTYPE html>
<html lang="<?=Config::lang()?>" translate="no">

<head>
  <base href="<?=Config::base()?>">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="icon" type="image/png" href="<?=View::thumb(Config::get('favicon') ?? 'assets/gila-logo.png', 200)?>">
  <?php foreach (Gila\View::$stylesheet as $link) {
        Gila\View::$css[] = $link;
        echo '<link href="' . $link . '" rel="stylesheet">';
  } ?>
  <?=View::script("core/gila.min.js")?>
  <?=View::script('lib/vue/vue.min.js');?>
  <?=View::css("core/gila.min.css")?>
  <?php Menu::bootstrap(['ulClass' => 'nav nav-treeview']); ?>
  <?=View::css('core/adminlte/adminlte.css')?>
  <?=View::css('core/adminlte/OverlayScrollbars.min.css')?>
  <?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
  
  <meta name="robots" content="noindex nofollow" />
  <meta name="google" value="notranslate">
   <style>
  .gila-darkscreen{z-index:1100}.content-wrapper{background-color:unset}
  [class*=sidebar-dark-]{background-color:#101621}
  .nav-sidebar .nav-treeview>.nav-item>.nav-link>.nav-icon{display:none}
  .nav-sidebar .nav-treeview>.nav-item>.nav-link{padding-left:30px}
  <?=(Config::get('admin_background') ? 'background:url("' . Config::get('admin_background') . '")' : '')?>
  .widget-area-dashboard{background:inherit}
  .widget-area-dashboard .widget,.widget-title{/*background:rgba(255,255,255,0.9);*/}
  #main-wrapper{margin:auto;max-width:1200px;border:none;margin-top:2em}#main-wrapper>.wrapper{background:var(--main-bg-color)}
  .widget-area-dashboard .widget-mysql-chart{width:100%}
  .sidebar a>img{width:18px!important}
  hr{border:1px solid}
  body{font-family:Roboto,Arial}
  :root{';
    --main-primary-color:#ed6d1c;
    --admin-primary-color:#ed6d1c;
<?php
if ($palette = Config::get('admin_palette')) {
    $p = json_decode($palette, true);
    foreach ($p as $k => $c) {
        echo '--main-palette-' . $k . ':' . $c . ';';
    }
    echo '--main-a-color:' . $p[0] . ';';
    echo '--main-primary-color:' . $p[0] . ';';
}
?>
  }
  .btn-primary, .btn-primary:hover, .btn-primary:focus{
    background-color:var(--main-primary-color)!important;
    border-color:var(--main-primary-color)!important;
  }
  .btn-outline-primary, .btn-outline-primary:hover, .btn-outline-primary:focus{
    color:var(--main-primary-color)!important;
    border-color:var(--main-primary-color)!important;
    background-color:inherit;
  }
  .main-header{
    position:fixed; right:0; left:0; z-index:9;
    background-color:inherit;
  }
  .main-sidebar{
    line-height:1.2;
  }
  .content-wrapper{
    margin-top:60px;
  }
  </style>

</head>


<body class="hold-transition layout-fixed bg-lightgrey" >
<?php
if (Session::key('admin_layout') == 'mini') {
    include 'src/core/views/admin/header-mini.php';
    return;
}
if (Session::key('admin_layout') == 'suite') {
    include 'src/core/views/admin/header-suite.php';
    return;
}
?>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light d-print-none">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
      </li>
    </ul>

    <?php include 'src/core/views/admin/navbar-right.php'; ?>
  </nav>
  <!-- /.navbar -->

<div class="wrapper">

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 d-print-none">
    <!-- Brand Logo -->
    <a href="admin" class="brand-link" style="min-height:50px">
<?php if ($logo = Config::get('admin_logo')) { ?>
    <img src="<?=$logo?>" alt="logo" class="brand-image" style="opacity: .8">
<?php } else { ?>
    <img src="assets/gila-logo.png" alt="logo" class="brand-image" style="opacity: .8">
    <div class="brand-text d-inline text-white">GILA CMS</div>
<?php } ?>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <?=Menu::getHtml(Config::$amenu)?>
      </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content" id="mainContent">
      <div class="">
