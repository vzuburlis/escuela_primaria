<?php
$fonts = Gila\Config::getArray('theme.fonts')??[];
if ($font = Gila\Config::get('theme.heading-font')) {
  $fonts[] = $font;
}
if ($font = Gila\Config::get('theme.body-font')) {
  $fonts[] = $font;
}

foreach ($fonts as $font) {
  if (!in_array($font, Gila\View::$web_safe_fonts) && !empty($font)) {
    $link = 'https://fonts.googleapis.com/css2?family='.urlencode($font);
    View::cssAsync($link);  
  }
}
