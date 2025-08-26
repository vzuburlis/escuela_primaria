<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
$configfile = 'config.php';
include_once __DIR__ . "/../classes/DB.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['adm_email', 'base_url', 'db_name','db_user'];
    foreach ($keys as $key) {
        if ($_POST[$key] != strip_tags($_POST[$key])) {
            echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>";
            echo "Tags are not allowed in field: " . htmlentities($_POST[$key]);
            include __DIR__ . "/install.form.php";
            return;
        }
    }

    $host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = $_POST['db_name'];
    $_base_url = $_POST['base_url'];

    $_lc = substr($_base_url, -1);
    if ($_lc != '/' && $_lc != '\\') {
        $_base_url .= '/';
    }

    $link = @mysqli_connect($host, $db_user, $db_pass, $db_name);
    if (!$link) {
        echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>";
        echo "#" . mysqli_connect_errno() . ": " . mysqli_connect_error();
        if (mysqli_connect_errno() == 2002) {
            echo "<br>(check that database hostname is correct)";
        }
        if (mysqli_connect_errno() == 1698) {
            echo "<br>(check that database user is correct)";
        }
        if (mysqli_connect_errno() == 1044) {
            echo "<br>(check that database name is correct and user has been granted all the privileges)";
        }
        if (mysqli_connect_errno() == 1045) {
            echo "<br>(check that database user's password is correct)";
        }
        echo "</div>";
    } else {
        // create config.php
        $filedata = file_get_contents('config.default.php');
        $GLOBALS['config']['db'] = [
          'host' => $host,
          'user' => $db_user,
          'pass' => $db_pass,
          'name' => $db_name
        ];
        DB::set($GLOBALS['config']['db']);
        include __DIR__ . "/install.sql.php";
        // preinstall post,page,widgets
        $wtext1 = '{"text":"<ol><li><a href=\\\"admin\\\/content\\\/postcategory\\\">Create Categories<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/page\\\">Edit About Page<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/post\\\">Add Posts<\\\/a><\\\/li><li><a href=\\\"admin\\\/settings\\\">Set Basic Settings<\\\/a><\\\/li><\\\/ol>"}';
        $wtext2 = '{"text":"<ul><li><a href=\\\\\"https:\\\/\\\/gila-cms.readthedocs.io\\\" target=\\\"_blank\\\">Documentation<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/www.facebook.com\\\/gilacms\\\/\\\\\" target=\\\\\"_blank\\\\\">Facebook Page<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/github.com\\\/GilaCMS\\\/gila\\\\\" target=\\\\\"_blank\\\\\">Github Repo<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/tinyletter.com\\\/gilacms\\\\\">Dev Newsletter<\\\/a><\\\/li><\\\/ul>"}';
        $wtext3 = '{"text":"<p>We want to hear from you!<br>Send us your questions and thoughts at <a href=\\\"mailto:contact@gilacms.com\\\">contact@gilacms.com<\\\/a><\\\/p>"}';
        DB::query("INSERT INTO widget(id,widget,title,area,active,pos,data)
    VALUES(1,'core-counters','','dashboard',1,1,'[]'),
    (2,'paragraph','Start Blogging','dashboard',1,2,'" . $wtext1 . "'),
    (3,'paragraph','Links','dashboard',1,3,'" . $wtext2 . "'),
    (4,'paragraph','Feedback','dashboard',1,4,'" . $wtext3 . "');");

        $GLOBALS['config']['permissions'] = [
        1 => [
        0 => 'admin',
        1 => 'admin_user',
        2 => 'admin_userrole'
        ]
        ];
        $GLOBALS['config']['trusted_domains'] = [$_SERVER['HTTP_HOST']];
        $GLOBALS['config']['packages'] = ['blog'];
        $GLOBALS['config']['base'] = $_base_url;
        $GLOBALS['config']['theme'] = 'gila-blog';
        $GLOBALS['config']['title'] = 'Gila CMS';
        $GLOBALS['config']['description'] = 'An awesome website!';
        $GLOBALS['config']['default_controller'] = 'blog';
        $GLOBALS['config']['timezone'] = 'America/Mexico_City';
        $GLOBALS['config']['env'] = 'pro';
        $GLOBALS['config']['check4updates'] = 1;
        $GLOBALS['config']['language'] = 'en';
        $GLOBALS['config']['admin_email'] = $_POST['adm_email'];
        $GLOBALS['config']['media_uploads'] = 'data/uploads';
        $GLOBALS['config']['rewrite'] = 1;
        $GLOBALS['config']['use_webp'] = function_exists("imagewebp") ? 1 : 0;
        $GLOBALS['config']['utk_level'] = 10;
        $GLOBALS['config']['admin_theme'] = 'default';
        $GLOBALS['config']['admin_logo'] = 'assets/gila-logo.png';
        $GLOBALS['config']['favicon'] = 'assets/favicon.png';

        $filedata = "<?php\n\n\$GLOBALS['config'] = " . var_export($GLOBALS['config'], true) . ";";
        file_put_contents($configfile, $filedata);
        Gila\Package::copyAssets('core');
        Gila\Theme::copyAssets('gila-blog');
        Gila\Theme::copyAssets('gila-mag');
        Gila\Config::dir(LOG_PATH . '/stats');
        Gila\Config::dir(LOG_PATH . '/debug');
        Gila\Config::dir(LOG_PATH . '/cacheItem');
        @unlink(LOG_PATH . '/load.php');
        include __DIR__ . "/installed.php";
        exit;
    }
}

include __DIR__ . "/install.form.php";
