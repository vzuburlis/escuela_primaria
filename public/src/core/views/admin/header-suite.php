<?php
$submenu = Menu::getSubmenu(Config::$amenu);
if (isset($submenu[0]['package']) && Session::hasPrivilege('admin')) {
    $submenu[0]['children'][] = ['🛠','admin/package_options/' . $submenu[0]['package'],'access' => 'admin'];
}
?>
<style>
  /*#mainmenu>li{display:none}#mainmenu>li.menu-open{display:block}*/
  #mainmenu a>i, #mainmenu a>img{display:none}
  /*#mainmenu>li>a{cursor:default;font-size:150%;display:inline;}*/
  #mainmenu>li>a:hover{cursor:default}
  #mainmenu>li:nth-child(1)>a:hover{cursor:pointer}
  #mainmenu>li>ul{display:inline-flex!important}
  .main-footer,.main-header{margin-left:0!important}
  .main-header .nav-link{color:#181818}
  .main-header .nav-link.active{color:<?=$submenu[0]['color'] ?? '#000000'?>!important}
  .content-wrapper{margin:60px auto!important;}
</style>
<!-- Navbar -->
  <nav class="main-header navbar navbar-expand border-0"
  style="background:white; border-bottom:1px solid <?=$submenu[0]['color'] ?? '#333333'?>!important">
    <!-- Left navbar links -->
    <div id="mainmenu" class="d-flex align-items-center">
      <a href="admin/apps" class="nav-link mx-2">
        <i class="fa fa-th" style="display:inline;vertical-align: middle;"></i>
      </a>
      <span><?=__($submenu[0]['title'])?></span>
      <ul class="navbar-nav" style="max-height:38px;overflow:hidden"><?=Menu::getHtml($submenu[0]['children'], Config::url(''))?></ul>
    </div>

    <?php include 'src/core/views/admin/navbar-right.php'; ?>
  </nav>
  <!-- /.navbar -->

<div class="wrapper">

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content" id="mainContent">
      <div class="">
