<?php

View::script('lib/vue/vue.min.js');
View::script('core/admin/content.js');
View::script('lib/CodeMirror/codemirror.js');
View::script('lib/CodeMirror/htmlmixed.js');
View::script('lib/CodeMirror/javascript.js');
View::cssAsync('lib/CodeMirror/codemirror.css');
View::script("lib/tinymce5/tinymce.min.js");
Gila\Package::options($package_name);
?>
<div class="container">
  <span class="btn btn-primary" onclick="save_options()"><?=__('Save', ['es' => 'Guardar'])?>
</div>

<style>
  .type-tinymce{grid-column:1/-1}
</style>
<script>
app = new Vue({
  el: '#addon_options_form'
})
transformClassComponents()

function save_options() {
  let p = g.el('addon_id').value;
    let fm=new FormData(g.el('addon_options_form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'admin/packages?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
      g('.gila-darkscreen').remove();
      g.loader(false)
      g.success('Changes saved successfully', {es:'Los cambios se guardaron exitosamente'})
    }})
}
</script>
