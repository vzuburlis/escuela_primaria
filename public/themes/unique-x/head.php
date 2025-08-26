<base href="<?=Gila\Config::base()?>">
<title><?=$page_title??Gila\Config::get('title')?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if (isset(Gila\View::$canonical)) {
  echo '<link rel="canonical" href="'.Gila\View::$canonical.'" />';
}
if (Gila\Config::get('favicon')) {
  $thumb = View::thumb(Config::get('favicon'),'fav/96_',96);
  $ext = pathinfo($thumb, PATHINFO_EXTENSION);
  echo "<link rel=\"shortcut icon\" type=\"image/$ext\" sizes=96x96 href=\"$thumb\">";
  //$thumb = View::thumb(Config::get('favicon'),'fav/32_',32);
  //echo "<link rel=icon type=\"image/$ext\" sizes=32x32 href=\"$thumb\">";
}
foreach (Gila\View::$meta as $key=>$value) {
  if (substr($key,0,3)=='og:') {
    echo '<meta property="'.$key.'" content="'.htmlentities($value).'">';
  } else {
    echo '<meta name="'.$key.'" content="'.htmlentities($value).'">';
  }
}
Gila\Event::fire('head.meta');

$bundleName = '';
foreach (Gila\View::$stylesheet as $link) {
  Gila\View::$css[] = $link;
  $startLen = strlen(Gila\View::$cdn_host);
  $startStr = Gila\View::$cdn_host;
  echo '<link href="'.$link.'" rel="stylesheet">';
}
if (FS_ACCESS && !empty($bundleName)) {
  echo '<link href="min?'.$bundleName.'" rel="stylesheet">';
}

Gila\Event::fire('head');

include_once __DIR__.'/header.style.php';
