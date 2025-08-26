<div v-if="edit" id="sb_uploads" style="padding: 0 0.8em" class="sb_content">
    <p class="sb_p py-2" style="font-size:80%"><?=__('_sb_uploads_p', [
    'en' => 'These are elements that you can drag and drop inside a text block',
    'es' => 'Esos son elementos que puedes arrastrar y soltar dentro de un bloque de texto',
    'el' => 'Αυτά είναι στοιχεία που μπορείς να κάνεις μεταφορά και απόθεση μέσα σε ένα μπλοκ κειμένου'
    ])?></p>
    <input type="hidden" id="add_new_upload" @input="appEditMenu.updateUploadedComponents(media_image_selected_path)">
    <span class="btn btn-sm btn-primary w-100" @click="open_media_gallery('#add_new_upload')">+ {{g.tr('Image',{es:'Imagen'})}}</span>
  <div class="dummy_components">
    <span v-for="(com,i) in form_components" :data-name="com.name" class="dummy_component"
    :data-tag="com.tag" :data-html="com.html" :data-style="com.style">{{com.name}}
    </span>
  </div>

</div>
