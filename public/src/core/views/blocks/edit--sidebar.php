
<div class="sidebar-builder" onmouseenter="componentarea_editbtn.style.display = 'none';block_toolbar.style.display = 'none';">


<div v-if="editSidebar==''" style="height:90vh;overflow:none;display:grid;grid-template-columns:68px 1fr">

  <div style="text-align:center;overflow-y:auto;border-right:1px solid lightgrey">
    <?php if ($content == 'page') { ?>
    <div id="sb_tab1" class="sb_tab" onclick="sbTab('sb_globals');this.classList.add('active');">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-world" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M3.6 9h16.8" /><path d="M3.6 15h16.8" /><path d="M11.5 3a17 17 0 0 0 0 18" /><path d="M12.5 3a17 17 0 0 1 0 18" /></svg>
      <br><small><?=__('Globals', ['es' => 'Globales'])?></small>
    </div>
    <?php } ?>
    <div id="sb_tab2" class="sb_tab" onclick="sbTab('sb_elements');this.classList.add('active');">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="6" height="5" rx="2" /><rect x="4" y="13" width="6" height="7" rx="2" /><rect x="14" y="4" width="6" height="16" rx="2" /></svg>
      <br><small><?=__('Elements', ['es' => 'Elementos'])?></small>
    </div>
    <div id="sb_tab3" class="sb_tab" onclick="sbTab('sb_uploads');this.classList.add('active');">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 -3l3 3" /><path d="M12 12l0 9" /></svg>
      <br><small><?=__('Uploads', ['es' => 'Subidas'])?></small>
    </div>

    <?php if (FS_ACCESS && $content == 'page') : ?>
    <div id="sb_tab4" class="sb_tab" onclick="sbTab('sb_elements','sb_el_form');this.classList.add('active');">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-forms" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#444444" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a3 3 0 0 0 -3 3v12a3 3 0 0 0 3 3" /><path d="M6 3a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3" /><path d="M13 7h7a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-7" /><path d="M5 7h-1a1 1 0 0 0 -1 1v8a1 1 0 0 0 1 1h1" /><path d="M17 12h.01" /><path d="M13 12h.01" /></svg>
      <br><small><?=__('Forms', ['es' => 'Formularios'])?></small>
    </div>
    <?php endif; ?>
  </div>

<?php  if ($content == 'page') {
    include_once __DIR__ . '/sb_globals.php';
}
include_once __DIR__ . '/sb_uploads.php';
include_once __DIR__ . '/sb_forms.php';
foreach (Config::getList('editor-sidebar') as $sb) {
    if (isset($sb['file'])) {
        include_once $sb['file'];
    }
}
?>

  <div v-if="edit" id="sb_elements" class="sb_content mb-4">
    <p class="sb_p my-2"><?=__('_sb_elements_p', [
    'en' => 'These are elements that you can drag and drop inside a text block',
    'es' => 'Esos son elementos que puedes arrastrar y soltar dentro de un bloque de texto',
    'el' => 'Αυτά είναι στοιχεία που μπορείς να κάνεις μεταφορά και απόθεση μέσα σε ένα μπλοκ κειμένου'
    ])?></p>
      <div v-for="(gname,g) in dummy_groups">
        <h6 class="el-header" :id="'sb_el_'+g">
          {{gname}}
        </h6>
        <div>
          <div class="dummy_components">
          <span v-for="(com,i) in dummy_components" v-if="com.removed!=true&&com.group.includes(g)&&(emailSupport==false||true==com.emailSupport)" :data-name="com.name" class="dummy_component">
            <img :src="com.image">
            <span v-html="com.label_<?=Config::lang()?>"></span>
          </span>
          </div>
        </div>
      </div>
  </div>

</div>


<div v-if="editSidebar=='typography'" id="fontpairs_sidebar" style="padding:0.8em">
  <div style="height:32px">
    <span @click="closeSideBar()" class="edit-sidebar-close" title="<?=__('Close')?>">&times;</span>
  </div>
  <span class="btn btn-outline-primary fullwidth mb-1"
  onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Typography')?>', 'fonts')">
    <?=__('Select fonts', ['es' => 'Elegir fuentes'])?>
  </span>
  <span v-if="pairFont" class="btn btn-primary fullwidth mb-1"
  @click="savePairFont()">
    <?=__('Save selection', ['es' => 'Guardar selección'])?>
  </span>
  <div v-for="(pair,i) of pairFonts" class="fontpair" @click="selectPairFont(pair)"
  :class="{selected:pair[0]==rootVar.hfont&&pair[1]==rootVar.bfont}">
    <h3 :style="{'font-family':pair[0]}">{{pair[0]}}</h3>
    <p :style="{'font-family':pair[1]}">{{pair[1]}}</p>
  </div>
</div>

<div v-if="editSidebar=='colors'" id="colors_sidebar" style="padding:0.8em">
  <div style="height:32px">
    <span @click="closeSideBar()" class="edit-sidebar-close" title="<?=__('Close')?>">&times;</span>
  </div>
  <span class="btn btn-outline-primary fullwidth mb-1"
  onclick="theme_options('<?=Config::get('theme')?>', '<?=__('Colors')?>', 'colors')">
    <?=__('Select colors', ['es' => 'Elegir colores'])?>
  </span>
  <span v-if="colorPalette" class="btn btn-primary fullwidth mb-1"
  @click="savePalette()">
    <?=__('Save selection', ['es' => 'Guardar selección'])?>
  </span>
  <div>
    <div v-for="(p,i) in colorPalettes" class="colorpalette" @click="selectColorPalette(p)"
    :class="{selected:p[0]==rootVar.color1&&p[1]==rootVar.color2&&p[2]==rootVar.color3&&p[3]==rootVar.color4}">
      <span v-for="c in p" class="colorpalette--color" :style="{backgroundColor:c}"></span>
    </div>
  </div>
</div>

<div v-if="editSidebar=='languages'" id="languages_sidebar" style="padding:0.8em">
  <div style="height:32px">
    <span @click="closeSideBar()" class="edit-sidebar-close" title="<?=__('Close')?>">&times;</span>
  </div>
  <div v-for="(lang,i) in allLanguages" :key="i">
    <label><inpuy type="checkbox" :value="i" v-model="selectedLanguages"> {{language}}</label>
  </div>
  <span class="btn btn-primary fullwidth mb-1"
  @click="updateLanguages()">
    <?=__('Save selection', ['es' => 'Guardar selección'])?>
  </span>
</div>

</div>

<script>
function sbTab(id,anchor='') {
  g('.sb_tab').removeClass('active')
  g('.sb_content').style('display','none')
  x = document.getElementById(id)
  x.style.display = 'block'
  if (id=='sb_elements') {
    setDummyComponents()
  }
  if (id=='sb_uploads') {
    appEditMenu.updateUploadedComponents()
  }
  if (id=='sb_forms') {
    appEditMenu.updateFormComponents()
  }
  if(anchor!='') {
    document.getElementById(anchor).scrollIntoView();
  }
}
<?php if ($content == 'page') { ?>
sbTab('sb_globals')
sb_tab1.classList.add('active')
<?php } else { ?>
g.loader()
var elementInt = setInterval(function() {
  if (typeof appEditMenu=='undefined') return
  if (appEditMenu.dummy_components!=[]) {
    setTimeout(() => {
      sb_tab2.classList.add('active')
      g.loader(false)
      sbTab('sb_elements')
    }, 150);
    clearInterval(elementInt)
  }
},50)
<?php } ?>

</script>


