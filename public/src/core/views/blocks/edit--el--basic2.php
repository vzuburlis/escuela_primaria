<div v-if="opt.g=='audio'&&selectedComponent.classList&&selectedComponent.classList.contains('el-audio')" class="py-1">
  <label><?=__('Source')?></label>
  <span type="button" onclick="open_media_gallery('#imSelComsrc')" class="badge bg-success rounded-pill">
  ▶ <?=__('Select file')?>
  </span>
  <input :value="selectedComponent.firstChild.src" type="hidden" id="imSelComsrc"
  @input="componentUpdateSource(imSelComsrc.value);">
</div>


<div v-if="opt.g=='img-src'&&(componentHas('img-src')||selectedComponent.tagName=='IMG')" class="py-1">
<label>{{opt.label}}</label>
<span type="button" onclick="open_media_gallery('#imSelComimg')" class="p-1 bordered" style="border-style:dashed">
  <img :src="selectedComponent.src" style="max-height:40px;max-width:40px;vertical-align:middle">
</span>
<input v-model="selectedComponent.src" type="hidden" id="imSelComimg" @change="componentUpdateImgSrc()">
</div>

<div v-if="opt.g=='svg'&&componentHas('svg')" class="py-1">
<label>{{opt.label}}</label>
<button type="button" onclick="open_media_gallery('#imSelComsvg')" class="btn btn-outline-secondary" style="width:100%;text-align:center">
  <img :src="selectedComponent.dataset.svg" style="max-height:40px;max-width:40px;vertical-align:middle">
</button>
<input v-model="selectedComponent.dataset.svg" type="hidden" id="imSelComsvg" @input="componentUpdateSVG();">
</div>


<div v-if="opt.g=='bgimage'&&(!opt.childTag||(selectedComponent.firstChild!=null&&opt.childTag==selectedComponent.firstChild.tagName))" class="py-1">
<label>{{opt.label}}</label>
<span type="button" onclick="open_media_gallery('#imSelCbg')" class="p-1 bordered" style="border-style:dashed">
  <img :src="bgPlaceholder(selectedComponent.style.backgroundImage)" style="max-height:40px;max-width:40px;vertical-align:middle">
</span>
<span v-if="hasStyle(opt.attr)" type="button" class="badge bg-danger rounded-pill"
@click="componentSetStyle(opt.attr,'');blocksUpdateFields()">
  <?=__('Remove')?>
</span>
<span type="button" class="badge bg-info rounded-pill"
@click="componentPromptBG();blocksUpdateFields()">
  <?=__('URL')?>
</span>
<input type="hidden" id="imSelCbg"
@input="componentSetStyle(opt.attr, 'url(\''+$event.target.value+'\')');">
</div>

<div v-if="opt.g=='maskimage'" class="py-1">
<label>{{opt.label}}</label>
<span type="button" :onclick="'open_media_gallery(\'#imB'+i+'\')'" class="p-1 bordered" style="border-style:dashed">
  <img :src="bgPlaceholder(selectedComponent.style.maskImage??selectedComponent.style.webkitMaskImage)" style="max-height:38px;height:38px;max-width:60px;vertical-align:middle">
</span>
<span v-if="hasStyle('mask-image')" type="button" class="badge bg-danger rounded-pill"
@click="componentSetStyle('mask-image','');blocksUpdateFields()">
  <?=__('Remove')?>
</span>
<input type="hidden" :id="'imB'+i"
@input="componentSetStyle('mask-image','url(\''+$event.target.value+'\')');">
</div>


<div v-if="opt.g=='view-display'" class="py-1">
<div class="btn-group" role="group" aria-label="Basic example">
<button type="button" class="btn btn-sm btn-outline-secondary d-flex"
:class="{'btn-secondary':componentGetDisplay()==''}" @click="componentDisplay('')">
  <img src="assets/core/icons/device-desktop.svg" class="tr-icon">
  <img src="assets/core/icons/device-mobile.svg" class="tr-icon">
  <img src="assets/core/icons/device-tablet.svg" class="tr-icon">
</button>
<button type="button" class="btn btn-sm btn-outline-secondary d-flex"
:disabled="view!='xs'" :class="{'btn-secondary':componentGetDisplay()=='xs'}" @click="componentDisplay('xs')">
  <img src="assets/core/icons/device-mobile.svg" class="tr-icon">
</button>
<button type="button" class="btn btn-sm btn-outline-secondary d-flex"
:disabled="view=='lg'" :class="{'btn-secondary':componentGetDisplay()=='xs-md'}" @click="componentDisplay('xs-md')">
  <img src="assets/core/icons/device-mobile.svg" class="tr-icon">
  <img src="assets/core/icons/device-tablet.svg" class="tr-icon">
</button>
<button type="button" class="btn btn-sm btn-outline-secondary d-flex"
:disabled="view=='xs'" :class="{'btn-secondary':componentGetDisplay()=='md-lg'}" @click="componentDisplay('md-lg')">
  <img src="assets/core/icons/device-tablet.svg" class="tr-icon">
  <img src="assets/core/icons/device-desktop.svg" class="tr-icon">
</button>
<button type="button" class="btn btn-sm btn-outline-secondary d-flex"
:disabled="view!='lg'" :class="{'btn-secondary':componentGetDisplay()=='lg'}" @click="componentDisplay('lg')">
  <img src="assets/core/icons/device-desktop.svg" class="tr-icon">
</button>
</div>
</div>



<div v-if="opt.g=='gallery'&&componentHas('gallery')" class="py-1">
  <label v-if="componentImages.length>1"><?=__('Images')?></label>
  <label v-if="componentImages.length==1"><?=__('Image')?></label>
  <div v-for="(image,i) in componentImages">
    <span type="button" :onclick="'open_media_gallery(\'#imSelComimg'+i+'\')'" class="p-1 bordered" style="border-style:dashed">
      <img v-if="image.src" :src="image.src" style="max-height:40px;max-width:40px;vertical-align:middle">
    </span>
    <input v-model="image.src" type="hidden" :id="'imSelComimg'+i" @change="blocksUpdateFields()">
  </div>
</div>

<div v-if="opt.g=='ul'&&selectedComponent.tagName=='UL'" class="py-1">
  <label><?=__('List Style Type')?></label>
  <select v-model='selectedComponent.style.listStyleType'>
    <option v-for="type of listStyleTypes" :value="type">{{type}}</option>  
  </select>
  <label><?=__('Image')?></label>
  <span type="button" onclick="open_media_gallery('#imSelComli')" class="p-1 bordered" style="border-style:dashed">
    <img :src="bgPlaceholder(selectedComponent.style.listStyleImage)" style="max-height:36px;max-width:60px;vertical-align:middle">
  </span>
  <span v-if="hasStyle('listStyleImage')" type="button"
@click="componentSetStyle('listStyleImage','');blocksUpdateFields()" class="badge bg-danger rounded-pill">
    <?=__('Remove')?>
  </span>
  <input :value="selectedComponent.style.listStyleImage??''" type="hidden" id="imSelComli"
  @input="componentSetStyle('listStyleImage','url('+imSelComli.value+')');">
</div>

<div v-if="opt.g=='extra-basic'" class="py-1">
  <?php Event::fire('blocks-edit-el-basic') ?>
</div>
