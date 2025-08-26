<?php Event::fire('admin.navbar.before')?>
    <!-- Right navbar links -->
    <ul class="navbar-nav" style="flex-direction:row;margin-left:auto">
      <!-- Navbar Search -->
      <li class="nav-item" style="display:none">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fa fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fa fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <?php if (Session::level() > 0) : ?>
      <!-- Help Dropdown Menu -->
            <?php if (!FS_ACCESS && !Config::get('hide-dropdown-help')) {
                include 'src/core/views/admin/dropdown-help.php';
            } ?>
      <!-- Notifications Dropdown Menu -->
            <?php
            foreach (Config::getList('badge') as $b) {
                $bcounts = $b['count'] ? $b['count']() : ''; ?>
      <li class="nav-item dropdown" style="">
                <?=$b['icon']?>
          <span class="badge bg-danger navbar-badge pe-none user-select-none"><?=$bcounts?></span>
      </li>
                <?php
            }
            $notifications = UserNotification::countNew();
            if ($notifications > 0) : ?>
      <li class="nav-item dropdown" style="">
        <a class="nav-link" href="admin/notifications">
          <i class="fa fa-bell"></i>
          <span class="badge badge-warning navbar-badge"><?=$notifications?></span>
        </a>
      </li>
            <?php endif; ?>
            <?php Event::fire('admin.navbar.item')?>
      <?php endif; ?>
      <!-- Admin Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link my-0 py-0" data-toggle="dropdown" href="#" id="menu-toggle">
          <div style="border-radius:50%; width:36px;height:36px;background:url(<?=Session::key('user_photo') ?? 'assets/core/default-user.png'?>); background-position: center; background-size:cover;"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-header small">
            <?=Session::key('user_name')?><br><?=Session::key('user_email')?>
          </div>
          <a href="admin/profile" class="dropdown-item"><?=__('My Profile')?></a>
          <a href="admin/layout" class="dropdown-item"><?=__('Layout')?></a>
          <a href="admin/sessions" class="dropdown-item"><?=__('Sessions')?></a>
          <?php foreach (Config::getList('user-menu') as $link) : ?>
            <a href="<?=$link['href']?>" class="dropdown-item"><?=__($link['label'])?></a>
          <?php endforeach; ?>
          <div class="dropdown-divider"></div>
          <a href="admin/logout" class="dropdown-item"><?=__("Logout")?></a>
        </div>
      </li>
      <li class="nav-item"  style="display:none">
        <a class="nav-link" onclick="document.body.requestFullscreen()" href="javascript:void(0)" role="button">
          <i class="fa fa-arrows-alt"></i>
        </a>
      </li>
    </ul>
