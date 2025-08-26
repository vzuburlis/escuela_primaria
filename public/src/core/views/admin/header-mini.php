
<?php

?>
<style>
  /*#mainmenu>li{display:none}#mainmenu>li.menu-open{display:block}*/
  #mainmenu a>i{display:block}
  #mainmenu>li>a{font-size:150%;}
  #mainmenu>li>ul{display:inline-flex!important}
  .main-footer,.main-header,#mainContent{margin-left:4em!important}
  .main-sidebar{width:4em!important}
  .main-sidebar a>img{width:1em!important}
</style>

<!-- Navbar -->
  <nav class="main-header navbar navbar-expand" style="width:auto">
    <?php include 'src/core/views/admin/navbar-right.php'; ?>
  </nav>
  <!-- /.navbar -->

<div class="wrapper">

<?php
$submenu = Menu::getSubmenu(Config::$amenu)[0];
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4"
style="background:<?=$submenu['color'] ?? '#151515'?>">
    <!-- Sidebar -->
    <div class="h-100">
      <!-- Sidebar Menu -->
      <ul class="nav nav-sidebar d-flex justify-space-between h-100 p-1" id="mainmenu"
      role="menu" data-accordion="false">
        <li style="padding:0.1em">
          <img src="assets/gila-logo.png" alt="logo" style="filter:contrast(0) brightness(2.5)">
        </li>
      <?php foreach ($submenu['children'] as $item) : ?>
        <li><a href="<?=$item[1]?>" class="nav-link" title="<?=$item[0]?>">
          <i class="fa fa-<?=$item['icon']?> text-white"></i>
        </a></li>
      <?php endforeach; ?>
        <li><a href="admin/apps" class="nav-link">
          <i class="fa fa-th text-white"></i>
        </a></li>
      </ul>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content" id="mainContent">
      <div class="">
