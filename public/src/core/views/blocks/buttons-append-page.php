<script>
if(g('.block-head').all.length===0) {
  g('footer').prepend("<div style='position:relative;width:100%;z-index: 1;'>\
<span class='vtour-footer-btn span-editop-btn span-editfooter-btn' style='right:1em;top:0.5em;display:block' \
onclick='theme_options(\"<?=Config::get('theme')?>\", \"<?=__('Footer')?>\",\"footer\")'\
title='<?=__('Edit Footer')?>'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");
g('footer').append("<div class='block-add-btn' style='position:absolute;top:40%;left:50%' data-pos='0'>\
<button style='width:auto'><img class='tr-icon' src='assets/core/icons/plus.svg'> "+g.tr('Add Block', {es:'Anadir Bloque'})+"</button></div>");
// g('.block-add-btn').all[0].click()
} else {
  g('footer').prepend("<div style='position:relative;width:100%;z-index: 1;'>\
<span class='block-add-btn' style='margin-top: -16px;' data-pos='"+g('.block-head').all.length+"'>\
<button title='"+g.tr('Add Block', {es:'Anadir Bloque'})+"'><img class='tr-icon' src='assets/core/icons/plus.svg'></button></span>\
<span class='vtour-footer-btn span-editop-btn span-editfooter-btn' style='right:1em;top:0.5em;display:block' \
onclick='theme_options(\"<?=Config::get('theme')?>\", \"<?=__('Footer')?>\",\"footer\")'\
title='"+g.tr('Edit', {es:'Editar'})+"'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");
}
g('body>header').append("<div style='position:relative;width:100%;z-index:99999'>\
<span class='vtour-header-btn span-editop-btn' style='right:3.5em;top:-2.5em;display:block' \
onclick='theme_options(\"<?=Config::get('theme')?>\", \"<?=__('Header')?>\",\"header\")'\
title='<?=__('Edit')?>'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");

if(typeof nav!='undefined') nav.addEventListener('click', function(e) {
  edit_mainmenu()
})
if(typeof headerlogo!='undefined') headerlogo.addEventListener('click', function(e) {
  e.stopPropagation();
  if (e.preventDefault) {
    e.preventDefault();
  }
  theme_options('<?=Config::get('theme')?>', '<?=__('Logo')?>', 'logo');
})
</script>
