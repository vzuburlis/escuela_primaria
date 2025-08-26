<?php View::includeFile('user-login-header.php');?>

  <?php View::alerts()?>
  <div class="w-100 h-100 mt-4 d-flex justify-content-center align-items-center">
    <div class="col-md-5 p-3">
      <div class="text-center">
        <div>
          <img src="<?=View::thumb(Config::get('login_logo') ?? 'assets/gila-logo.png')?>" style="max-height:4em">
        </div>
        <h3><?=__('Register')?></h3>
      </div>

      <form method="post" id="form" action="<?=Config::url('user/register?submited')?>" class="g-form" enctype="multipart/form-data">
        <?=Gila\Form::hiddenInput('register')?>
        <label><?=__('Fullname')?></label>
        <div class="form-group">
          <input class="form-control fullwidth" name="name" autofocus required>
        </div>
        <?php Gila\Event::fire('register.form')?>
        <label><?=__('Email')?></label>
        <div class="form-group">
          <input class="form-control fullwidth" name="email" type="email" required>
        </div>
        <label><?=__('Password')?></label>
        <div class="form-group mb-3">
          <input class="form-control fullwidth" name="password" type="password" value="" required>
        </div>
        <?php Gila\Event::fire('recaptcha.form')?>
        <?php if (Gila\Event::fire('recaptcha.btn') == false) {
            View::part('core@form_btn');
        } ?>
      </form>
      <p style="text-align:center">
        <?=__('Already have an account?', ['es' => '¿Tienes una cuenta?'])?>
        <a href="<?=Config::url('user')?>" rel="nofollow"><?=__('Log In')?></a>
      </p>
      <p style="text-align:center">
        <?=__('_register_agree_text')?>
      </p>
    </div>
  </div>

</body>

</html>
