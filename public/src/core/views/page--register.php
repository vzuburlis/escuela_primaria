<?php View::includeFile('user-login-header.php');?>

<div class="w-100 h-100 row justify-content-center align-items-center m-0">
  <div id="main" class="col-md-6">
    <?=$text?>
  </div>
  <div class="col-md-6 p-3 bg-white text-dark justify-content-center d-flex">
    <?=View::alerts()?>
    <div class="d-flex flex-column gap-1 align-items-center" style="max-width:360px">
    <?php if (Gila\Event::fire('login.btn')) : ?>
      <div class="my-3 text-align-center position-relative w-100 opacity-75"><div class="position-absolute w-100" style="z-index:-1;border-top:1px solid currentColor;top:50%"></div><span class=" px-2" style="font-size:14px;background: var(--main-bg-color);"><?=__('OR', ['es' => 'O'])?></span></div>
      <span class="btn btn-outline-primary w-100">Registrar con correo</span>
    <?php else : ?>
        <?php View::includeFile('register-form.php');?>
    <?php endif; ?>
    </div>
  </div>
</div>

</body>

</html>
