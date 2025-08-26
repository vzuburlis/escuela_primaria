<a class='text-decoration-none' href="<?=($logo_href??Config::base())?>" style="min-width:40px">
<?php
if(!empty(Config::get('theme.logo'))) {
  ?><img src="<?=View::thumb(Config::get('theme.logo'), 300)?>" style="max-height:<?=Config::get('theme.logo-size')??'120px'?>" class="header-logo my-0" alt=""><?php
  if (!empty(Config::get('theme.title'))) {
    ?><span class="header-title title"><?=Config::get('theme.title')?></span><?php
  }
 } else {
  ?><span class="header-title title"><?=Config::get('theme.title')??Config::get('title')?></span><?php
}
?>
</a>
