<?php

View::includeFile('user-login-header.php');?>

  <div class="gl-4 centered wrapper g-card bg-white">
    <div class="g-form">
      <div class="border-buttom-main_ text-align-center">
        <div>
          <i class="fa fa-5x fa-warning" style="color:yellow"></i>
        </div>
        <h3><?=__('activate_error2', __('activate_error1'))?></h3>
      </div>
      <a class="btn btn-success btn-block" href="<?=Config::url('')?>"><?=__('Home')?></a>
    </div>
  </div>

</body>

</html>
