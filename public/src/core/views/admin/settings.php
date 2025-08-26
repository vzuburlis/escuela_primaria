<?php
$config_list = ['title' => 'Title', 'description' => 'Description', 'admin_email' => 'Admin Email'];
foreach ($_POST as $key => $value) if (is_string($value)) {
    $_POST[$key] = strip_tags($value);
}
if (isset($_POST['gila_languages'])) {
  file_put_contents('lang.txt', json_encode($_POST['gila_languages']));
  if (!is_array($_POST['gila_languages'])) {
    $_POST['gila_languages'] = [$_POST['gila_languages']];
  }
  if (!in_array(Config::lang(), $_POST['gila_languages'])) {
    $_POST['gila_languages'][] = Config::lang();
  }
  Config::set('languages', $_POST['gila_languages']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($config_list as $key => $value) {
        Config::set($key, $_POST['gila_' . $key]);
    }
    $options_list = ['timezone', 'language', 'admin_logo', 'login_logo', 'favicon', 'user_register', 'admin_theme', 'admin_palette', 'user_register', 'user_activation'];
    if (FS_ACCESS) {
      $options_list = array_merge($options_list, ['env', 'check4updates', 'maxImgWidth', 'maxImgHeight', 'utk_level', 'locale', 'default_controller']);
    }
    foreach ($options_list as $op) {
      if (isset($_POST['gila_'.$op])) {
        Config::set($op, $_POST['gila_'.$op]);
      }
    }
  
    Response::success();
    return;
}
View::script('lib/vue/vue.min.js');
View::script('core/lang/content/' . Config::get('language') . '.js');
View::script('core/admin/media.js');
View::script('core/admin/vue-components.js');
?>
<style>.g-switch{z-index:1;vertical-align: middle;}
</style>

<div class="container">
<?php View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Config::base('admin/settings')?>" class="g-form">
  <input type="hidden" name="submit-btn">
  <h2><?=__("Basic Settings")?></h2><hr>

<?php
foreach ($config_list as $key => $value) {
    if ($value[0] != '.') { ?>
  <div class="mb-2">
  <label class="label"><?=__($value)?></label>
  <div>
    <input class="form-control" name="gila_<?=$key?>" value="<?=Config::get($key)?>">
  </div>
  </div>
    <?php }
} ?>

<?php if (FS_ACCESS || Config::get('user_register') ?? true) : ?>
  <br>
    <?php echo Form::input('gila_user_register', ["type" => "switch"], Config::get('user_register'), __("New users can register")) ?>

  <br>
    <?php echo Form::input('gila_user_activation', ["type" => "select","options" => ['byemail' => __('Email activation link'),'byadmin' => __('Administration'),'auto' => __('Auto')]], Config::get('user_activation'), __("New Users activation")) ?>
<?php endif; ?>

  <div class="mb-2">
    <label class="label col-md-5"><?=__("Timezone")?></label>
    <div class="">
    <select name="gila_timezone" value="<?=Config::get('timezone')?>" class="form-control">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
        $sel = (Config::get('timezone') == $value ? 'selected' : '');
        echo '<option value="' . $value . "\" $sel>" . ucwords($value) . '</option>';
    }
    ?>
    </select></div>
  </div>

  <br><div class="mb-2">
    <label class="label"><?=__("Language")?></label>
    <select name="gila_language" value="<?=Config::get('language')?>">
    <?php
    $languages = include 'src/core/lang/languages.php';
    foreach ($languages as $k => $value) {
        $sel = (Config::get('language') == $k ? 'selected' : '');
        echo '<option value="' . $k . "\" $sel>" . ucwords($value) . '</option>';
    }
    ?>
    </select>
  </div>


  <br><div class="mb-2">
    <?php echo Form::input('gila_admin_logo', ['type' => 'media2'], Config::get('admin_logo'), __('Admin logo'));?>
  </div>

  <br><div class="mb-2">
    <?php echo Form::input('gila_login_logo', ['type' => 'media2'], Config::get('login_logo'), __('Login logo'));?>
  </div>

  <br><div class="mb-2">
    <?php echo Form::input('gila_favicon', ['type' => 'media2'], Config::get('favicon'), __('Favicon'));?>
  </div>

  <br><div class="mb-2">
<?php
if (FS_ACCESS) {
    $options = ['default' => 'Default', 'deepblue' => 'Deep Blue', 'liquidcool' => 'Liquid Cool', '-' => 'Old'];
    foreach (Config::getList('admin-themes') as $theme) {
        $options[$theme[0]] = $theme[1];
    }
    echo Form::input('gila_admin_theme', ["type" => "select","options" => $options], Config::get('admin_theme'), __("Admin Theme"));
}

if (FS_ACCESS) {
    if (Config::get('admin_palette') || Config::getList('admin-palettes')) {
        $palettes = Config::getList('admin-palettes');
        echo '<br>';
        echo Form::input('gila_admin_palette', ["type" => "palette","palettes" => $palettes], Config::get('admin_palette'), __("Admin Palette"));
    }
}

?></div>

  <br>
  <div>
    <a class="g-btn" style="min-width:unset" onclick="save_settings()"><?=__("Submit")?></a>
  </div>

  <?php
    if (FS_ACCESS) {
        include __DIR__ . '/settings-advanced.php';
    }
    ?>
</form>
</div>

<script>
var settingsApp = new Vue({
  el: '#settings-form'
})

function save_settings() {
  g.postForm('settings-form', function() {
    g.alert('<?=__('_changes_updated')?>', 'success')
  })
}
</script>
