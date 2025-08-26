<?php View::script('core/web-components.js') ?>
<form method="post" action="">
  <?php Form::hiddenInput('login') ?>
  <div class="form-group mb-3">
    <input class="form-control" placeholder="E-mail" name="username" type="email" autofocus>
  </div>
  <div class="form-group mb-3">
    <input class="form-control" placeholder="<?=__('Password')?>" name="password" id="pass" type="password" value="">
    <span style="position: relative; float:right; margin-top: -30px; padding:0 8px; cursor: pointer;"
    onclick="if(pass.type=='password') {pass.type='text';this.textContent='🙈'} else {pass.type='password';this.textContent='👁'}" title="<?=__('Show password')?>">👁</span>
  </div>
  <button type="submit" class="btn btn-primary w-100"><?=__('Log In')?></button>
</form>

<br>
<p style="text-align:center">
  <a href="<?=Config::url('user/password_reset')?>" rel="nofollow"><?=__('forgot_pass')?></a>
  <?php if (Config::get('user_register') == 1) {
        echo '| <a href="' . Config::url('user/register') . '">' . __('Register') . '</a>';
  } ?>
</p>
