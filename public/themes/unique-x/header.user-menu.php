<?php
if (Config::get('theme.user-menu')==0 || (Session::userId()==0 && Config::get('theme.user-menu')==2)) {
  return;
}
View::scriptAsync('lib/bootstrap52/bootstrap.bundle.min.js');
if (Session::userId()>0) {
  View::includeFile('header.user-dropdown.php');
} else {
  Config::addLang('core/lang/login/');
  echo '<li><a class="nav-link btn-user-login" href="'.Config::base('user').'">'.__('My account',['es'=>'Mi cuenta']).'</a>';
  return;
  if (Config::get('user_register')==1) {
    echo '<li><a class="nav-link btn-user-login" href="'.Config::base('user').'">'.__('Log In').'</a>';
    echo '<li><a class="btn btn-sm btn-primary btn-user-register" href="'.Config::base('user/register').'">'.__('Register').'</a>';
  } else {
    echo '<li><a class="nav-link btn-user-login" href="'.Config::base('user').'">'.__('Log In').'</a>';
  }
}
