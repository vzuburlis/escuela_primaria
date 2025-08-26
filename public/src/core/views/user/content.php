<?php
  $src = explode('.', Config::$content[$type])[0];
  View::renderFile('head');
?>
<div class="wrapper">
<?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
<?php
  View::renderFile('admin/content-vue.php');
?>
</div>
