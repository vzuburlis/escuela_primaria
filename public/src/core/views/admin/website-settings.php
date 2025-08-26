<?php
View::script('lib/vue/vue.min.js');
View::script('core/lang/content/' . Config::get('language') . '.js');
View::script('core/admin/media.js');
View::script('core/admin/vue-components.js');
?>
<style>.g-switch{z-index:1;vertical-align: middle;}
#settings-form>div>*{min-width:30%;display:block}</style>

<div class="gm-12">
<?php View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Config::base('admin/settings')?>" class="g-form">
  <input type="hidden" name="submit-btn">

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__('Title')?></label>
  <input class="g-input" name="gila_title" value="<?=Config::get('title')?>" class="gm-4" />
  </div>

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__('Description')?></label>
  <input class="g-input" name="gila_description" value="<?=Config::get('description')?>" class="gm-4" />
  </div>

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__('Email')?></label>
  <input class="g-input" name="gila_email" value="<?=Config::get('admin_email')?>" class="gm-4" />
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Default language", ['es'=>'Idioma predeterminado'])?></label><select name="gila_language" value="<?=Config::get('language')?>" class="gm-4">
    <?php
    $languageFile = 'src/core/lang/languages.en.php';
    if (Config::lang()=='es') {
      $languageFile = 'src/core/lang/languages.es.php';
    }
    $languages = include $languageFile;
    $language_icon = include 'src/core/lang/language_icon.php';
    foreach ($languages as $k => $value) {
        $sel = (Config::get('language') == $k ? 'selected' : '');
        echo '<option value="' . $k . "\" $sel>" . ucwords($value) . '</option>';
    }
    ?>
    </select>
<?php if (Website::canUse('web')): ?>
  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Languages", ['es'=>'Idiomas'])?></label>
    <div>
    <?php
    $pageLanguages = Config::getArray('languages');
    foreach ($languages as $k => $value) {
        $sel = (in_array($k, $pageLanguages) ? 'checked' : '');
        echo "<label class='p-1 m-1 bordered bg-grey' style='font-size:75%'>";
        echo '<input name="gila_languages[]" style="vertical-align:middle" type="checkbox" value="' . $k . "\" $sel> ";
        // echo " <img class='flag-icon' style='vertical-align:baseline' src='https://flagcdn.com/w20/{$language_icon[$k]}.png' alt=''> " .
        echo ucwords($value);
        echo "</label>";
    }
    ?>
    <div>
  </div>
<?php endif; ?>

  <?php Event::fire('website-settings.after') ?>

</form>
</div>
