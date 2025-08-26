<script>
<?php if ($content == 'page_template') : ?>
if(g('.block-head').all.length===0) {
  g('footer').prepend("<div style='position:relative;width:100%;z-index: 1;'>\
<span class='vtour-footer-btn span-editop-btn span-editfooter-btn' style='right:1em;top:0.5em;display:block' \
onclick='template_options(\"<?=$id?>\", \"<?=__('Footer')?>\",\"footer\")'\
title='<?=__('Edit Footer')?>'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");
g('footer').append("<div class='block-add-btn' style='position:absolute;top:40%;left:50%' data-pos='0'>\
<button style='width:auto'><img class='tr-icon' src='assets/core/icons/plus.svg'> "+g.tr('Add Block', {es:'Anadir Bloque'})+"</button></div>");
} else {
  g('footer').prepend("<div style='position:relative;width:100%;z-index: 1;'>\
<span class='block-add-btn' style='margin-top: -16px;' data-pos='"+g('.block-head').all.length+"'>\
<button title='"+g.tr('Add Block', {es:'Anadir Bloque'})+"'><img class='tr-icon' src='assets/core/icons/plus.svg'></button></span>\
<span class='vtour-footer-btn span-editop-btn span-editfooter-btn' style='right:1em;top:0.5em;display:block' \
onclick='template_options(\"<?=$id?>\", \"<?=__('Footer')?>\",\"footer\")'\
title='"+g.tr('Edit', {es:'Editar'})+"'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");
}
g('body>header').append("<div style='position:relative;width:100%;z-index:99999'>\
<span class='vtour-header-btn span-editop-btn' style='right:3.5em;top:-2.5em;display:block' \
onclick='template_options(\"<?=$id?>\", \"<?=__('Header')?>\",\"header\")'\
title='<?=__('Edit')?>'><img class='tr-icon' src='assets/core/icons/pencil.svg'></span>\
</div>");
<?php endif; ?>

if(typeof headerlogo!='undefined') headerlogo.addEventListener('click', function(e) {
  e.stopPropagation();
  if (e.preventDefault) {
    e.preventDefault();
  }
  template_options(<?=$id?>, '<?=__('Logo')?>', 'logo');
  return false;
})

function template_options(id,title,group=null) {
  g.loader()

  g.post("blocks/options/<?=$content?>/"+id+"?g_response=content", {group:group}, function(x){
    g.loader(false)
    g.modal({title:title,body:x,buttons:'save_template_options',class:'large'})
    app = new Vue({
      el: '#theme_options_form'
    })
    transformClassComponents()
  })
}
g.dialog.buttons.save_template_options = {
  title: g.tr('Save', {'es':'Guardar'}),
  fn:function() {
    let p = g.el('theme_id').value;
    let fm=new FormData(g.el('theme_options_form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'blocks/saveOptions/<?=$content?>/'+p,method:'POST',data:fm,fn:function(x) {
      g.loader(false)
      g('.gila-darkscreen').remove();
      window.location.reload();
    }})
  },
  class:'g-btn btn-primary'
}
</script>
