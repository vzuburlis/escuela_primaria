<style>
  #nav>ul>li>a, .g-nav-toggler {color:<?=Config::get('theme.menu-color')??'#333'?>}
  #nav>ul>li{font-family:var(--bfont)}
  <?php
  $backgroundColor = 'unset';
  $headerBackgroundColor = Config::get('theme.header-background-color')??'unset';
  $footerBackgroundColor = Config::get('theme.footer-background-color')??'#333';
  $footerColor = Config::get('theme.footer-color')??'inherit';
  $backgroundColor = Config::get('theme.page-background-color')??'unset';
  $menuColor = Config::get('theme.menu-color')??'var(--main-a-color)';
  $accentColor = Config::get('theme.accent-color') ?? '#207AB7';
  function toRGB($c) {
    if($c[0]=='#') {
      if (isset($c[4])) {
        return hexdec($c[1].$c[2]).','.hexdec($c[3].$c[4]).','.hexdec($c[5].$c[6]);
      }
      return hexdec($c[1].$c[1]).','.hexdec($c[2].$c[2]).','.hexdec($c[3].$c[3]);
    }
    return $c;
  }
  $pbga = Config::get('theme.page-background-alfa')??0;
  if ($backgroundColor!='unset' && $pbga<1) {
    $bg = $backgroundColor;
    if ($bg[0]=='#') {
      $backgroundColor = 'rgba('.hexdec($bg[1].$bg[2]).','.hexdec($bg[3].$bg[4]).','.hexdec($bg[5].$bg[6]).','.htmlentities($pbga??'0').')';
    } else {
      $backgroundColor = 'rgba('.$bg.','.htmlentities($pbga??'0').')';
    }
  }
  
  if(!FS_ACCESS) {
    echo '#nav>ul>li.active>a, #nav>ul>li>a:hover{color:var(--menu-color2)}';
    echo '#nav>ul>li>a.btn, #nav>ul>li.active>a.btn, #nav>ul>li>a.btn:hover{color:#fff}';
}
  if (Config::get('theme.header-position')=='sticky') {
    echo 'body section {scroll-margin-top: 60px}';
  }
  ?>
  :root{
    --title-color:<?=Config::get('theme.title-color')??'var(--hcolor)'?>;
    --menu-color:<?=$menuColor?>;
    --menu-color2:<?=Config::get('theme.menu-color2')??$menuColor?>;
    --menu-color3:<?=Config::get('theme.menu-color3')??$menuColor?>;
    --header-bgcolor:<?=($headerBackgroundColor??'unset')?>;
    --footer-bgcolor:<?=($footerBackgroundColor??'unset')?>;
    --footer-color:<?=($footerColor??'unset')?>;
    --body-bgcolor:<?=($backgroundColor??'unset')?>;
    --header-bgcolor2:<?=(Config::get('theme.header-background-color2')??$headerBackgroundColor)?>;
    --hfont:<?=Config::get('theme.heading-font', '\'Roboto Condensed\'')?>, sans-serif;
    --bfont:<?=Config::get('theme.body-font')??'\'Roboto\''?>, sans-serif;
    --bcolor:<?=Config::get('theme.body-color')??'#333333'?>!important;
    --hcolor:<?=Config::get('theme.heading-color')??'#ED6D1C'?>!important;
    --bs-heading-color:<?=Config::get('theme.heading-color')??'#ED6D1C'?>!important;
    --bs-body-color:<?=Config::get('theme.body-color')??'#333333'?>!important;
    --main-primary-color:<?=Config::get('theme.primary-color')??'#207AB7'?>!important;
    --main-a-color:<?=$accentColor?>!important;
    --p1color:<?=toRGB(Config::get('theme.primary-color')??'#207AB7')?>;
    --p2color:<?=toRGB($accentColor)?>;
    --p3color:<?=toRGB(Config::get('theme.heading-color')??'#ED6D1C')?>;
    --p4color:<?=toRGB(Config::get('theme.body-color')??'#333333')?>;
    --p5color:<?=toRGB(Config::get('theme.page-background-color')??'#333333')?>;
    --bs-primary-rgb:<?=toRGB(Config::get('theme.primary-color')??'#207AB7')?>!important;
    --bs-btn-color:<?=Config::get('theme.btn-color')??Config::get('theme.primary-color')?>!important;
    --bs-btn-hover-bg:<?=Config::get('theme.btn-color')??'var(--main-primary-color)'?>!important;
    --main-btn-color:<?=Config::get('theme.btn-color')??'var(--main-primary-color)'?>;
    <?php if(Config::get('theme.btn-font-size')): ?>
    --bs-btn-font-size:<?=Config::get('theme.btn-font-size')?>px;
    --bs-btn-padding-y:<?=Config::get('theme.btn-padding')?>px;
    --bs-btn-padding-x:<?=Config::get('theme.btn-padding')?>px;
    --bs-btn-border-radius:<?=Config::get('theme.btn-border-radius')?>px;
<?php endif; ?>
  }
  h1 * {
    font-family: var(--hfont);
  }
  *:focus{
    outline:auto!important;
  }
  .header-title {
    font-family: var(--hfont);
    font-size: 2em;
    margin:0.3em 0;
  }
  @media (min-width:400px) {
    .header-title {
      font-size:2.5em; margin: 0;
    }
  }
  h1,h2,h3,h4,h5,h6,button,.btn,.g-btn, .header-title {
    font-family: var(--hfont);
  }

  .btn-primary, .btn-primary:hover, .btn-primary:focus{
    background-color:var(--main-btn-color)!important;
    border-color:var(--main-btn-color)!important;
  }
  .btn-outline-primary, .btn-outline-primary:hover, .btn-outline-primary:focus{
    color:var(--main-btn-color)!important;
    border-color:var(--main-btn-color)!important;
    background-color:inherit!important;
  }
  /*.btn-primary {
    --bs-btn-bg: var(--main-primary-color);
    --bs-btn-border-color: var(--main-primary-color);
    --bs-btn-hover-bg: var(--main-primary-color);
    --bs-btn-hover-border-color: var(--main-primary-color);
    --bs-btn-focus-shadow-rgb: var(--main-primary-color);
    --bs-btn-active-bg: var(--main-primary-color);
    --bs-btn-active-border-color: var(--main-primary-color);
    --bs-btn-disabled-bg: var(--main-primary-color);
    --bs-btn-disabled-border-color: var(--main-primary-color);
  }*/
  body>header{
    background-color:var(--header-bgcolor)
  }
  body>header.header__slim{
    background-color:var(--header-bgcolor)
  }
  body>header .title{
    color:var(--title-color)
  }
  body .post-description{
    color:var(--bcolor)
  }
  body>footer{
    background-color:var(--footer-bgcolor);
    color:var(--footer-color);
  }
  body>footer a, body>footer a:hover{
    color:var(--footer-color);
  }
  .only-sr{
    position: absolute;
    left:-10000px;
    top:auto;
    opacity:0;
    background:white;
    color:#333;
  }
  .only-focus:focus{
    left:0.5em;top:0.5em;
    opacity:1;
  }
<?php if($dist=Config::get('theme.btn-distance')) if($dist>0): ?>
  .btn-row{
    margin-top:<?=$dist?>px;
    margin-right:<?=$dist?>px;
  }
<?php endif; ?>

</style>
