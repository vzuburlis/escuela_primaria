</div>

<footer>
<div class="container">
  <?=Gila\View::getWidgetArea('footer')?>
</div>
<div class="container pt-2" style="font-size:85%;<?=(Gila\Config::get('theme.footer-seperator')==1?'border-top:1px var(--footer-color) solid':'')?>">
<div class="row d-block">
  <div class="col-md-4 text-center text-md-start">
    <?=Gila\Config::get('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
<?php
$footerLinks = Gila\Config::getArray('theme.footer-links')??[];
foreach($footerLinks as $link) {
  echo ' | <a href="'.Gila\Config::base($link[1]).'">'.Gila\Config::tr($link[0]).'</a>';
}
?>
  </div>
  <?php Gila\Event::fire('footer.after') ?>
</div>
</div>
</footer>

<?php
include __DIR__.'/footer.fonts.php';
?>
<style><?=htmlentities(Gila\Config::get('theme.css'), ENT_NOQUOTES)?>
<?php if(isset(Config::$content['editor_style'])) foreach(DB::getAssoc("SELECT * FROM editor_style WHERE active=1") as $cl){
  echo htmlentities($cl['data'], ENT_NOQUOTES)."\n";
} ?>
</style>

<?=Gila\View::script('core/gila.min.js');?>
<?=Gila\View::script('lib/bootstrap52/bootstrap.bundle.min.js');?>
<script>
window.onscroll = function() {resizeHeader()}
function resizeHeader() {
  if (document.body.scrollTop > 0 || document.documentElement.scrollTop > 0) {
    document.getElementById("header").classList.add('header__slim');
  } else {
    document.getElementById("header").classList.remove('header__slim');
  }
}
resizeHeader()
</script>

<?=View::cssAsync('lib/font-awesome/6/css/all.css')?>
<?php if (Config::get('theme.wa.active')==1) if (empty(Config::get('theme.wa.tochat_key'))):
  $prompt = Config::get('theme.wa.txt')? '&txt='.Config::get('theme.wa.txt'): ''; ?>
  <div id="wame" style="position:fixed;<?=(Config::get('theme.wa.pos')=='left'?'left':'right')?>:24px;bottom:48px;z-index:10000;">
    <span type=button class="d-md-none" onclick="if (typeof fbq === 'function') fbq('track', 'Contact');location.href='https:\/\/api.whatsapp.com/send?phone=<?=htmlentities(Config::get('theme.wa.phone'))?><?=$prompt?>'">
      <img src="assets/themes/unique-x/whatsapp-icon-logo.png" alt="" style="width:50px">
    </span>
    <span type=button class="d-none d-md-block" onclick="if (typeof fbq === 'function') fbq('track', 'Contact');location.href='https:\/\/api.whatsapp.com/send?phone=<?=htmlentities(Config::get('theme.wa.phone'))?><?=$prompt?>'">
      <img src="assets/themes/unique-x/whatsapp-icon-logo.png" alt="" style="width:50px">
      <span class="d-none d-lg-block" style="font-size:36px"><?=Config::get('theme.wa.label')?></span>
    </span>
  </div>
<?php else : ?>
  <script defer src="https://widget.tochat.be/bundle.js?key=<?=Config::get('theme.wa.tochat_key')?>"></script>
<?php endif; ?>

</body>
</html>
