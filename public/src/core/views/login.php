<?php View::includeFile('user-login-header.php');?>

  <div class="vh-100 h-100 d-flex justify-content-center align-items-center">
    <div class="col-sm-5 p-3 g-card bg-white text-dark rounded">
      <div class="text-center">
        <div>
          <img src="<?=View::thumb(Config::get('login_logo') ?? 'assets/gila-logo.png')?>" style="max-height:4em">
        </div>
        <h3><?=__('Log In')?></h3>
      </div>
      <?=View::alerts()?>
  <?php
    if (Session::waitForLogin() == 0) : ?>
    <div class="d-flex flex-column gap-1 align-items-center">
        <?php if (Gila\Event::fire('login.btn')) : ?>
      <div class="my-2 text-align-center position-relative w-100 opacity-75"><div class="position-absolute w-100" style="z-index:-1;border-top:1px solid currentColor;top:50%"></div><span class=" px-2" style="font-size:14px;background: var(--main-bg-color);"><?=__('OR', ['es' => 'O'])?></span></div>
            <?php if (isset($_POST['username']) && isset($_POST['password'])) : ?>
                <?php View::part('core@form_login') ?>
            <?php else : ?>
        <button type="button" class="btn btn-outline-primary" style="width:320px" onclick="hiddenLoginFrom.style.display='block';this.style.display='none'"><?=__('Login with email & password', ['es' => 'Iniciar con email y contraseña'])?></button>
        <div id="hiddenLoginFrom" style="display:none;width:320px">
                <?php View::part('core@form_login') ?>
        </div>
            <?php endif; ?>
        <?php else : ?>
            <?php View::part('core@form_login') ?>
        <?php endif; ?>
    </div>

        <?php
    endif; ?>

  </div>

</body>

</html>
