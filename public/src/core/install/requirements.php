<head>
  <base href="<?=Gila\Config::base()?>">
  <title>Install Gila CMS</title>
  <meta name="robots" content="noindex">
  <style><?=file_get_contents("src/core/assets/gila.min.css")?></style>
</head>
<body class="bg-lightgrey">
  <div class="gm-6 centered row" style="">
    <div class="gm-12 wrapper text-align-center">
      <h1 class="margin-0">Gila CMS Requirements</h1>
    </div>
<?php
$continue = true;

if (version_compare(phpversion(), $required_php) < 0) {
    echo "<span class='alert fullwidth'>PHP version $required_php is required.</span><br>";
    $continue = false;
} else {
    echo "<span class='alert alert-success fullwidth'>✓ PHP version is 8 or more.</span><br>";
}

foreach ($required_ext as $k => $v) {
    if (!extension_loaded($v)) {
        echo "<span class='alert fullwidth'>Extension $v is not loaded.</span>";
    }
}

if (is_writable(realpath("")) == false) {
    echo "<span class='alert fullwidth'>Folder is not writable. Permissions may have to be adjusted</span>";
    $continue = false;
}

if (function_exists("apache_get_modules")) {
    if (!in_array('mod_rewrite', apache_get_modules())) {
        echo "<span class='alert fullwidth'>mod_rewrite is not enabled.</span>";
        $continue = false;
    }
}
?>
    <p>
      Before you continue to the installation make sure you know the Database name and the user credencials. If you dont know them, ask them from your hosting provider.<br>
      Checkout the <a href="https://gila-cms.readthedocs.io/en/latest/install.html#errors" target="_blank">documentation</a>.<br>
    </p>
    <?php if ($continue) { ?>
      <div class="gl-12"><a class="g-btn gl-12" href="?install&step=1">Continue</a></div>
    <?php } ?>
  </div>
</body>
