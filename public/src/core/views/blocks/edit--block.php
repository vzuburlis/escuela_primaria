<script>
  blockOptions = [
  {
    'label': g.tr('Basic', {es:'Básicos'}),
    'group': 'param'
  },
  {
    'label': g.tr('Size', {es:'Tamaño'}),
    'group': 'size'
  },
  {
    'label': g.tr('Spacing', {es:'Espaciado'}),
    'group': 'spacing'
  },
  {
    'label': g.tr('Background', {es:'Fondo'}),
    'group': 'bg'
  },
  {
    'label': g.tr('Display', {es:'Mostrar'}),
    'group': 'display'
  },
  {
    'label': g.tr('Advanced', {es:'Avanzado'}),
    'group': 'advanced'
  },
]
</script>
<div v-if="editSidebar=='block'" class="sidebar-builder" style="padding:6px">
  <div style="display:flex;flex-direction:row;align-items: center;justify-content:space-between;margin-bottom:0.5em">
    <?=__('Block', ['es' => 'Bloque'])?>
    <div @click="closeBlockEdit()" class="float-right edit-component-close" title="<?=__('Close')?>">&times;</div>
  </div>
  <div id="block_sidebar">
    <div class="accordion accordion-flush m-0 p-0" id="accordionFlushBlock">
      <div v-for="(elo,e) in blockOptions" class="accordion-item">
        <div v-if="e==0" class="accordion-header">
          <button class="accordion-button p-1" type="button" data-bs-toggle="collapse" :data-bs-target="'#bleo'+e" aria-expanded="true">
          {{elo.label}}
          </button>
        </div>
        <div v-else class="accordion-header">
          <button class="accordion-button collapsed p-1" type="button" data-bs-toggle="collapse" :data-bs-target="'#bleo'+e" aria-expanded="false">
          {{elo.label}}
          </button>
        </div>
        
        <div :id="'bleo'+e" class="accordion-collapse collapse p-1 border" :class="{show:e==0}" data-bs-parent="#accordionFlushBlock">

          <div v-for="(opt,i) in blockData.fields" v-if="opt.group==elo.group">

            <div v-if="opt.type=='list'||opt.type=='tinymce'" class="pb-2">
              <div class="alert alert-info">
                <span class="small" @click="blockEdit()" class="btn btn-sm btn-primary w-100">
                  <?=__('To change the content click on the button', ['es' => 'Para editar el contenido haz click en button'])?>
                </span>
                <span type="button" @click="blockEdit()" class="btn btn-sm btn-primary w-100">
                  ✎ <?=__('Content', ['es' => 'Contenido'])?>
                </span>
              </div>
            </div>

            <div v-if="i=='text'||opt.type=='html'" class="pb-2">
              <span type="button" @click="editTextCode(i)" class="btn btn-sm btn-outline-primary w-100">
              ✎ HTML
              </span>
            </div>

            <div v-if="i=='mustache'" class="py-1">
              <span type="button" @click="editTextCode(i)" class="btn btn-sm btn-outline-primary w-100">
              ✎ <?=__('Template', ['es' => 'Plantilla'])?>
              </span>
            </div>

            <div v-if="opt.type=='range'" class="pb-1">
              <label>{{opt.title??i}}</label>
              <div class="range-input">
                <input type="range" :min="opt.min??0" :max="opt.max" :step="opt.step" v-model="blockData.data[i]"
                @input="updateBlockField(i, blockData.data[i])">
                <input size=2 v-model="blockData.data[i]">
                <span @click="updateBlockField(i, opt.defalut??0)">⎌</span>
              </div>
            </div>

            <div v-if="!opt.type||opt.type=='text'" class="pb-1 d-flex">
              <label>{{opt.title??i}}</label>
              <input v-model="blockData.data[i]" @input="updateBlockField(i, blockData.data[i])">
            </div>

            <div v-if="opt.type=='textarea'" class="pb-1">
              <label>{{opt.title??i}}</label>
              <textarea v-model="blockData.data[i]" @input="updateBlockField(i, blockData.data[i])" class="w-100"></textarea>
            </div>

            <div v-if="opt.type=='number'" class="pb-1 d-flex">
              <label>{{opt.title??i}}</label>
              <input type="number" v-model="blockData.data[i]" @input="updateBlockField(i, blockData.data[i])">
            </div>

            <div v-if="opt.type=='id'" class="pb-1 d-flex">
              <label>{{opt.title??i}}</label>
              <span class="d-flex w-100">
                <input v-model="blockData.data[i]">
                <span @click="updateBlockField(i, blockData.data[i])" style="margin-left:8px;cursor:pointer">💾</span>
              </span>
            </div>

            <div v-if="opt.type=='select'||opt.type=='radio'" class="py-1 d-flex">
              <label>{{opt.title??i}}</label>
              <div class="">
                <select v-model="blockData.data[i]" @change="updateBlockField(i, blockData.data[i],opt.u)">
                  <option v-for="(op,opk) in opt.options" :value="opk">{{g.tr(op)}}
                </select>
              </div>
            </div>

            <div v-if="opt.type=='color-input'" class="pb-1">
              <label>{{opt.title??i}}</label>
              <div class="d-flex align-items-center" style="display:flex">
                <div @click="cInput[opt.ci]=!cInput[opt.ci];$forceUpdate()" :style="{background:blockData.data[i][0]=='#'?blockData.data[i]:'rgb('+blockData.data[i]+')'}"
                  class="color-c" style="border:1px solid #444;">
                </div>
                <div v-if="!emailSupport" v-for="(color,key) in paletteList" @click="updateBlockField(i,color);"
                :style="{background:'rgb('+color+')'}"
                style="border-radius:0.7em;border:1px solid #444;width:1.4em;height:1.4em;color:white;margin-left:0.6em;padding:0.15em 0.35em">
                </div>
                <div @click="updateBlockField(i,'');$forceUpdate()" style="margin-left:8px;">⎌</div>
              </div>
              <color-picker v-if="cInput[opt.ci]" v-model="colorPicker" @input="updateBlockField(i,colorPicker.hex8);$forceUpdate()"
              :preset-colors='appEditMenu.paletteList'></color-picker>
            </div>

            <div v-if="opt.type=='media2'" class="py-1">
              <label>{{opt.title??i}}</label>
              <span type="button" :onclick="'open_media_gallery(\'#imB'+i+'\')'" class="p-1 bordered" style="border-style:dashed">
                <img :src="bgPlaceholder(blockData.data[i])" style="max-height:36px;max-width:60px;vertical-align:middle">
              </span>
              <span  type="button" v-if="blockData.data[i]"
              @click="updateBlockField(i,'')" class="badge bg-danger rounded-pill">
                <?=__('Remove')?>
              </span>
              <input type="hidden" v-model="blockData.data[i]" :id="'imB'+i" @input="updateBlockField(i,blockData.data[i])">
            </div>

            <div v-if="opt.type=='video'" class="py-1">
              <label><?=__('Video')?></label>
              <span type="button" onclick="open_media_gallery('#imSelBlockV')" class="badge bg-success rounded-pill">
              ▶ <?=__('Select file')?>
              </span>
              <span v-if="blockData.data[i]" type="button" 
              @click="updateBlockField(i,'')" class="badge bg-danger rounded-pill">
                <?=__('Remove')?>
              </span>
              <input :value="blockData.data[i]" type="hidden" id="imSelBlockV"
              @input="updateBlockField(i,imSelBlockV.value);">
            </div>

            <div v-if="opt.type=='checkbox'" class="py-1">
              <label @click="blockData.data[i]++;updateBlockField(i,blockData.data[i]%2);">
                <input type="checkbox" value=1 v-model="blockData.data[i]"
                class="btn btn-outline-secondary" style="width:auto;user-select:none"> {{opt.title??i}}
              </label>
            </div>

            <div v-if="opt.type=='datetime-local'" class="py-1">
              <label>{{opt.title??i}}</label>
              <input type="datetime-local" v-model="blockData.data[i]" @change="updateBlockField(i, blockData.data[i])">
            </div>

          </div>

          <div v-if="e==0" class="mt-2">
            <a v-for="l in blockData.links" :href="l.url" target="_blank" class="btn btn-primary btn-sm text-white" v-html="g.tr(l.label,l.tr)+'  &rarr;'"></a>
          </div>

        </div>

    </div>
  </div>
  <?php Event::fire('block-edit.after') ?>
</div>

