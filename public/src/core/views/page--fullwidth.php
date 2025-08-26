<?php View::includeFile('header.php')?>
<style>
header.absolute{
  box-shadow: none;
  position: absolute;
  width:100%;
  z-index: 10;
  background-image:none;
}
</style>
<div style="min-height: 80vh;" id="main">
<?=$text?>
</div>
<?=View::widgetArea('page.footer')?>
<?php View::includeFile('footer.php')?>
