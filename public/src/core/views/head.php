<base href="<?=Gila\Config::base()?>">
<title><?=$page_title ?? Gila\Config::get('title')?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
foreach (Gila\View::$meta as $key => $value) {
    echo '<meta name="' . $key . '" content="' . htmlentities($value) . '">';
}
Gila\Event::fire('head.meta');
foreach (Gila\View::$stylesheet as $link) {
    Gila\View::$css[] = $link;
    echo '<link href="' . $link . '" rel="stylesheet">';
}
Gila\Event::fire('head');
if (isset(Gila\View::$canonical)) {
    echo '<link rel="canonical" href="' . Gila\View::$canonical . '">';
}
if (Gila\Config::get('favicon')) {
    $thumb = View::thumb(Config::get('favicon'), 'fav/32_', 32);
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . $thumb . '">';
}
