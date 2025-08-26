<!-- User Dropdown Menu -->
<span class="nav-item dropdown float-right" style="vertical-align:middle">
  <span type="button" class="nav-link my-0 py-0" data-bs-toggle="dropdown" id="menu-toggle">
    <div style="border-radius:50%; width:36px;height:36px;background:url(<?=Session::key('user_photo')??'assets/core/default-user.png'?>); background-position: center; background-size:cover;"></div>
  </span>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="right:0;left:auto">
    <div class="dropdown-header small">
      <?=Session::key('user_name')?><br><?=Session::key('user_email')?>
    </div>
    <div class="dropdown-divider"></div>
    <a href="<?=Config::base('user/profile')?>" class="dropdown-item"><?=__("My Profile")?></a>
<?php foreach(Config::getList('user-menu') as $link): ?>
    <a href="<?=$link['href']?>" class="dropdown-item"><?=__($link['label'])?></a>
<?php endforeach; ?>
    <a href="user/logout" class="dropdown-item"><?=__("Logout")?></a>
  </div>
</span>
