<?php

$lang = Config::lang();
?>
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#" id="help-menu-toggle">
    <i class="fa fa-question-circle-o"></i>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <a href="https://pyme.one/<?=$lang?>/support" target=_blank class="dropdown-item">
      <?=__('Support', ['es' => 'Soporte'])?>
    </a>
    <!--div class="dropdown-divider"></div>
    <a href="https://gilacms.com/<?=$lang?>/webinars" target=_blank class="dropdown-item">
      <?=__('Webinars', ['es' => 'Webinars'])?>
    </a-->
    <div class="dropdown-divider"></div>
    <a href="https://www.facebook.com/groups/2552803244828314" target=_blank class="dropdown-item">
      <?=__('Ask community', ['es' => 'Comunidad'])?>
    </a>
    <div class="dropdown-divider"></div>
    <a href="mailto:support@pyme.one" target=_blank class="dropdown-item">
      <?=__('Report issue', ['es' => 'Reportar problema'])?>
    </a>
  </div>
</li>
