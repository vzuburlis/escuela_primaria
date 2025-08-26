<!DOCTYPE html>
<html lang="<?=Gila\Config::lang()?>">

<head>
  <?=Gila\View::head(false)?>
  <?=Gila\View::css('core/gila.min.css')?>
  <?=Gila\View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
  <?=Gila\View::css('lib/font-awesome/css/font-awesome.min.css')?>
  <?=Gila\View::scriptAsync("core/gila.min.js")?>
  <style><?=htmlentities(Gila\Config::get('theme.css'), ENT_NOQUOTES)?>
<?php if (!in_array($_SERVER['REMOTE_ADDR'], ['localhost', '127.0.0.1', '::1'])) {
    foreach (DB::getAssoc("SELECT * FROM gilacms.editor_style WHERE active=1") as $cl) {
        echo htmlentities($cl['data'], ENT_NOQUOTES) . "\n";
    }
} ?></style>
</head>

<body style="background: var(--main-bg-color)" class="body--user-login">
