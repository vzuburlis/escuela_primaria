<!DOCTYPE html>
<html lang="<?=Config::lang()?>">
<?php View::stylesheet('core/gila.min.css') ?>
<?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
<?php View::head()?>
<body>
<div style="margin:0" id="main">
<?=$text?>
</div>
<?php View::scriptAsync("core/gila.min.js")?>
<?php if (!FS_ACCESS) {
    View::includeFile('footer.php');
}?>
<?=View::cssAsync('lib/font-awesome/6/css/all.css')?>
</body>
</html>
