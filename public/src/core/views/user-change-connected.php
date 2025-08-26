<?php

View::includeFile('user-login-header.php');?>

  <div class="gl-4 centered">
    <div class="g-form wrapper g-card bg-white">
      <div class="border-buttom-main_ text-align-center">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="80" height="80" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ED6D1C" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10" />
          </svg>
        </div>
        <h3><?=strtr(__('reset_pass_connected'), ['{user_email}' => Session::key('user_email')])?></h3>
      </div>
      <a class="btn btn-secondary btn-block" href="<?=Config::base()?>"><?=__('Home')?></a>
    </div>
  </div>

</body>

</html>
