<?php

spl_autoload_register(function ($class) {
    global $classMap;
    if (isset($classMap[$class])) {
        require_once $classMap[$class];
        return true;
    }
    $class = strtr($class, ['\\' => DIRECTORY_SEPARATOR, '__' => '-']);
    if (file_exists(__DIR__ . '/classes/' . $class . '.php')) {
        require_once __DIR__ . '/classes/' . $class . '.php';
        class_alias('Gila\\' . $class, $class);
        return true;
    } elseif (file_exists('src/' . $class . '.php')) {
        require_once 'src/' . $class . '.php';
        return true;
    }
});


$classMap = [
  'Gila\\Cache' => __DIR__ . '/classes/Cache.php',
  'Gila\\Controller' => __DIR__ . '/classes/Controller.php',
  'Gila\\DB' => __DIR__ . '/classes/DB.php',
  'Gila\\Email' => __DIR__ . '/classes/Email.php',
  'Gila\\Event' => __DIR__ . '/classes/Event.php',
  'Gila\\FileManager' => __DIR__ . '/classes/FileManager.php',
  'Gila\\Form' => __DIR__ . '/classes/Form.php',
  'Gila\\Table' => __DIR__ . '/classes/Table.php',
  'Gila\\TableSchema' => __DIR__ . '/classes/TableSchema.php',
  'Gila\\Image' => __DIR__ . '/classes/Image.php',
  'Gila\\Logger' => __DIR__ . '/classes/Logger.php',
  'Gila\\Log' => __DIR__ . '/classes/Log.php',
  'Gila\\Menu' => __DIR__ . '/classes/Menu.php',
  'Gila\\MenuItemTypes' => __DIR__ . '/classes/MenuItemTypes.php',
  'Gila\\Package' => __DIR__ . '/classes/Package.php',
  'Gila\\Request' => __DIR__ . '/classes/Request.php',
  'Gila\\Response' => __DIR__ . '/classes/Response.php',
  'Gila\\Router' => __DIR__ . '/classes/Router.php',
  'Gila\\Session' => __DIR__ . '/classes/Session.php',
  'Gila\\Slugify' => __DIR__ . '/classes/Slugify.php',
  'Gila\\Sendmail' => __DIR__ . '/classes/Sendmail.php',
  'Gila\\Theme' => __DIR__ . '/classes/Theme.php',
  'Gila\\View' => __DIR__ . '/classes/View.php',
  'Gila\\HttpPost' => __DIR__ . '/classes/HttpPost.php',
  'Gila\\Http' => __DIR__ . '/classes/Http.php',
  'Gila\\HtmlInput' => __DIR__ . '/classes/HtmlInput.php',
  'Gila\\User' => __DIR__ . '/classes/User.php',
  'Gila\\UserData' => __DIR__ . '/classes/UserData.php',
  'Gila\\Widget' => __DIR__ . '/classes/Widget.php',
  'Gila\\Page' => __DIR__ . '/classes/Page.php',
  'Gila\\PageBlocks' => __DIR__ . '/classes/PageBlocks.php',
  'Gila\\Profile' => __DIR__ . '/classes/Profile.php',
  'Gila\\Post' => __DIR__ . '/classes/Post.php',
  'Gila\\Config' => __DIR__ . '/classes/Config.php',
  'Gila\\UserAgent' => __DIR__ . '/classes/UserAgent.php',
  'Gila\\UserNotification' => __DIR__ . '/classes/UserNotification.php',
];

if (file_exists('vendor/autoload.php')) {
    include_once 'vendor/autoload.php';
}
