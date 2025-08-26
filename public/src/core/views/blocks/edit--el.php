<?php $reset_txt = __('Reset value', ['es' => 'Resetear valor']) ?>
<div v-if="selectedComponent!==null" id="component_sidebar" onmouseenter="if(appEditMenu.selectedComponent!=appEditMenu.componentArea) componentarea_editbtn.style.display = 'none';block_toolbar.style.display = 'none'">
  <div class="align-center justify-content-space-between">
    <div @click="unsetSelectedComponent()" class="float-right edit-component-close" title="<?=__('Close')?>">&times;</div>
    <small v-if="componentArea!==selectedComponent" style="padding:0.3em 0">
      <span class="nav-link p-0" @click="editComponentColumn()"><?=__('DIV', ['es' => 'DIV'])?></span>
      <span v-for="c in componentBreadcumb()" :class="{'fw-bold':c==selectedComponent}" @click='setSelectedComponent(c)'> \
        <span v-if="Array.isArray(c)"><span v-for="cc in c" @click='setSelectedComponent(cc)'>&nbsp;{{cc.tagName}}</span></span>
        <span v-else>{{c.tagName}}</span>
      </span>
    </small>
    <small v-else class=nav-link><?=__('DIV', ['es' => 'DIV'])?></small>
  </div>
  <div id="component_sidebar_attr">
    
    <div class="accordion  m-0 p-0" id="accordionFlushElop">
      <div v-for="(elo,e) in elOptions" v-if="canUseElOptions(elo)" class="accordion-item">
        <div class="accordion-header">
          <button v-if="e==0" class="accordion-button p-1" type="button" data-bs-toggle="collapse" :data-bs-target="'#fleo'+e" aria-expanded="false">
          {{elo.label}}
          </button>
          <button v-else class="accordion-button collapsed p-1" type="button" data-bs-toggle="collapse" :data-bs-target="'#fleo'+e" :aria-controls="'fleo'+e">
          {{elo.label}}
          </button>
        </div>
        <div :id="'fleo'+e" class="accordion-collapse collapse p-1 border" :class="{show:e==0}" data-bs-parent="#accordionFlushElop">

          <div v-if="elo.tag=='position'" class="m-auto my-1">
            <span type="button" class="btn btn-sm btn-outline-secondary" @click="moveComponentFirst()">« <?=__('Top', ['es' => 'Primero'])?></span>
            <span type="button" class="btn btn-sm btn-outline-secondary" @click="moveComponentRight()"><?=__('Next', ['es' => 'Despues'])?> ›</span>
          </div>

          <div v-for="(opt,o) in elo.options" v-if="visibleOption(opt,o,elo)||opt.common">

            <div v-if="opt.in=='range'" class="pb-1">
              <label>{{opt.label??opt.attr}}</label>
              <div class="range-input">
                <input type="range" :min="opt.min??0" :max="opt.max" :step="opt.step??1" v-model="cStyle[opt.attr]"
                @input="componentSetStyle(opt.attr, cStyle[opt.attr], opt.u);">
                <input size=2 :value="selectedComponent.style[opt.attr]"
                @keypress="if($event.keyCode==13) selectedComponent.style[opt.attr]=$event.target.value;"
                @change="componentSetStyle(opt.attr, selectedComponent.style[opt.attr]);updateCStyle(opt.attr)">
                <span type=button @click="componentSetStyle(opt.attr,'')" class="attr-revert" title="<?=$reset_txt?>">⎌</span>
              </div>
            </div>

            <div v-if="opt.g=='style'&&opt.in=='text'" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <input :value="selectedComponent.style[opt.attr]" class="w-100"
              @keypress="if($event.keyCode==13) {selectedComponent.style[opt.attr]=$event.target.value} else g.snackbar('Press ⏎ to save value')"
              @change="updateComponentToolbarPosition()"
              :placeholder="opt.placeholder">
            </div>

            <div v-if="opt.g=='style'&&opt.in=='number'" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <input v-model="selectedComponent.style[opt.attr]" type="number"
              :placeholder="opt.placeholder" style="width:80px">
            </div>

            <div v-if="opt.g=='area'&&componentArea==selectedComponent" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <input v-model="selectedComponent.style[opt.attr]" class="w-100" @input="updateComponentToolbarPosition()">
            </div>

            <div v-if="opt.g=='style'&&opt.in=='select'&&emailSupport!==opt.noemail" class="py-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <div class="">
                <select v-model="cStyle[opt.attr]" @change="componentSetStyle(opt.attr, cStyle[opt.attr],opt.u)">
                  <option v-for="op in opt.options" :value="op">{{op}}
                </select>
              </div>
            </div>

            <div v-if="opt.g=='prop'&&opt.in=='select'" class="py-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <div class="">
                <select v-model="selectedComponent[opt.attr]">
                  <option v-for="op in opt.options" :value="op">{{op}}
                </select>
              </div>
            </div>

            <div v-if="opt.g=='options'&&componentHas('options')" class="py-1">
              <table class="table" style="width:100%">
                <tr><th colspan=3><?=__('Options')?></tr>
                <tr v-for="child in selectedComponent.children">
                  <td><input style="width:4em" placeholder="<?=__('value', ['es' => 'valor'])?>" v-model="child.value">
                  <td><input style="width:8em" placeholder="<?=__('display', ['es' => 'mostrar'])?>" v-model="child.innerText">
                  <td><span @click="child.remove();$forceUpdate()" type=button class="badge bg-danger">&times;</span>
                </tr>
                <tr>
                  <td colspan=3>
                    <span @click="selectedComponent.append(document.createElement('option'));$forceUpdate()" type=button class="badge bg-success"><?=__('Add', ['es' => 'Añadir'])?></span>
                </tr>
              </table>
            </div>

            <div v-if="opt.g=='options'&&componentHas('select')" class="py-1">
              <table class="table" style="width:100%">
                <tr><th colspan=3><?=__('Options')?></tr>
                <tr v-for="child in selectedComponent.children">
                  <td><input style="width:12em" placeholder="<?=__('value', ['es' => 'valor'])?>" v-model="child.value" @change="child.innerText=child.value">
                  <td><span @click="child.remove();$forceUpdate()" type=button class="badge bg-danger">&times;</span>
                </tr>
                <tr>
                  <td colspan=3>
                    <span @click="selectedComponent.append(document.createElement('option'));$forceUpdate()" type=button class="badge bg-success"><?=__('Add', ['es' => 'Añadir'])?></span>
                </tr>
              </table>
            </div>

            <div v-if="opt.in=='color'" class="pb-1">
              <label>{{opt.label??opt.attr}}</label>
              <div class="d-flex align-items-center" style="display:flex">
                <div @click="cInput[opt.ci]=!cInput[opt.ci];$forceUpdate()" :style="{background:cStyle[opt.attr]}"
                  class="color-c" style="border:1px solid #444;">
                </div>
                <div v-if="!emailSupport" v-for="(color,key) in paletteList" @click="componentSetStyle(opt.attr,'rgb('+color+')');"
                :style="{background:'rgb('+color+')'}"
                style="border-radius:0.7em;border:1px solid #444;width:1.4em;height:1.4em;color:white;margin-left:0.6em;padding:0.15em 0.35em">
                </div>
                <div v-if="!emailSupport" type=button @click="componentSetStyle(opt.attr,'')" style="margin-left:8px;" class="attr-revert" title="<?=$reset_txt?>">⎌</div>
              </div>
              <color-picker v-if="cInput[opt.ci]" v-model="colorPicker" @input="componentSetStyle(opt.attr,colorPicker.hex8);"
              :preset-colors='appEditMenu.paletteList'></color-picker>
            </div>

            <div v-if="opt.in=='font-family'" class="pb-1">
              <label>{{opt.label??opt.attr}}</label>
              <div class="d-flex">
                <select v-model="cStyle[opt.attr]" @change="selectedComponent.style[opt.attr]=cStyle[opt.attr]">
                  <option v-for="gf in googlefonts" :style="{'font-family':gf}" :value="gf">{{gf}}
                </select>
                <span class="ge-link" type=button style="margin-top:-4px" onclick="add_fonts()">
                  +<?=__('Add', ['es' => 'Añadir'])?>
                </span>
              </div>
            </div>

            <div v-if="opt.in=='text-tag'&&componentHasText()" class="pb-1">
              <div style="display:flex;user-select:none;justify-content:center">
                <span @click="setNode('STRONG')"><b>B</b></span>
                <span @click="setNode('I')"><i>I</i></span>
                <span @click="setNode('U')"><u>U</u></span>
                <span @click="setNode('DEL')"><del>S</del></span>
                <span v-if="selectedComponent.tagName!='A'" @click="setNode('A',null,{href:promptAttr('URL', 'href')})" style="padding:0.2em"><img class="tr-icon" src="assets/core/icons/link.svg"></span>
                <span v-if="selectedComponent.tagName!='A'" @click="setNode('A',null,{href:promptAttr('URL', 'href'),style:'font-weight:bold;color:inherit;text-decoration:underline'})" style=""><b><u>A</u></b></span>
                <span @click="setNode('MARK')"><mark style="color:inherit">M</mark></span>
                <span @click="setNode('ABBR',null,{title:prompt('Acronym', '')})"><abbr title="Abbreviation">A</abbr></span>
                <span @click="unsetNode()">T<sub>x</sub></span>
              </div>
            </div>

            <div v-if="opt.g=='tag-select'&&opt.tags[selectedComponent.tagName]" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <select class="mt-1" :value="selectedComponent.tagName" @input="componentSetTag(selectTag.value)" id="selectTag">
                <option v-for="(c,i) in opt.tags" :value="i">{{c}}</option>
              </select>
            </div>

            <div v-if="opt.g=='class-select'&&componentHas(opt.attr)" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}}</label>
              <select :value="componentSelectedClass(opt.classes)" class="mt-1" @input="componentSelectClass(selectClass.value, opt.classes)" id="selectClass">
                <option v-for="(c,i) in opt.classes" :value="i">{{c}}</option>
              </select>
            </div>

            <div v-if="opt.g=='datahref'&&(componentHas(opt.attr)||selectedComponent.dataset[opt.attr]||selectedComponent.tagName==opt.tag)" class="pb-1">
              <label>{{opt.label??opt.attr}}</label>
              <input v-if="!selectedMenuType" v-model="selectedComponent.dataset[opt.attr]" @input="componentUpdateDataHref(opt.attr)">
              <select v-if="selectedMenuType" @change="selectedMenuType=$event.target.value;$forceUpdate()" class="mt-1">
                <option v-for="(mt,i) in menuTypes" :value="i">{{mt.label}}</option>
              </select>
              <select v-for="(mt,i) in menuTypes" v-if="i==selectedMenuType" id="menuTypeSel" class="mt-1">
                <option v-for="(mto,url) in mt.options" v-if="mto" :value="url">{{mto}}</option>
              </select>
              <div class="mt-1">
                <span v-if="!selectedMenuType" @click="getMenuTypes()" type=button class="badge bg-primary"><?=__('Lookup', ['es' => 'Elegir'])?></span>
                <span v-if="selectedMenuType" @click="selectedMenuType=null;$forceUpdate()" class="btn btn-sm btn-outline-secondary"><?=__('Cancel')?></span>
                <span v-if="selectedMenuType" @click="selectedComponent.dataset[opt.attr]=menuTypeSel.value;selectedMenuType=null;componentUpdateDataHref();" class="btn btn-sm btn-success"><?=__('Select')?></span>
              </div>
            </div>

            <div v-if="opt.g=='action'&&(selectedComponent.dataset[opt.attr]||componentHas(opt.attr))" class="pb-1">
              <label>{{opt.label??opt.attr}}</label>
              <input v-if="!selectedActionType" v-model="selectedComponent.dataset[opt.attr]">
              <select v-if="selectedActionType" @change="selectedActionType=$event.target.value;$forceUpdate()" class="mt-1">
                <option v-for="(mt,i) in actionTypes" :value="i">{{mt.label}}</option>
              </select><br>
              <select v-for="(mt,i) in actionTypes" v-if="i==selectedActionType" id="actionTypesel" class="mt-1">
                <option :value="mt.url??''">- {{mt.options_label}} -</option>
                <option v-for="(mto,url) in mt.options" v-if="mto" :value="url">{{mto}}</option>
              </select>
              <div class="mt-1">
                <span v-if="!selectedActionType" @click="getActionTypes()" class="badge bg-primary"><?=__('Lookup', ['es' => 'Elegir'])?></span>
                <span v-if="selectedActionType" @click="selectedActionType=null;$forceUpdate()" class="btn btn-sm btn-outline-secondary"><?=__('Cancel')?></span>
                <span v-if="selectedActionType" @click="selectedComponent.dataset[opt.attr]=actionTypesel.value;selectedActionType=null;" class="btn btn-sm btn-success"><?=__('Select')?></span>
              </div>
            </div>

            <div v-if="opt.g=='html'&&componentHas('embed')" class="pb-1">
              <label><?=__('Embed')?></label>
              <textarea v-model="selectedComponent.innerHTML" style="height:120px" class="w-100"></textarea>
            </div>

            <div v-if="opt.g=='prop'&&(opt.tag||componentHas(opt.attr)||opt.common)" class="pb-1 d-flex">
              <label>{{opt.label??opt.attr}} {{opt.required?'*':''}}</label>
              <input v-if="opt.in=='checkbox'" :checked="selectedComponent[opt.attr]"
              type="checkbox" @click="componentToggleProp(opt.attr)">
              <input v-else v-model="selectedComponent[opt.attr]" class="w-100" :list="opt.list"
              :class="{'border border-danger':opt.required&&selectedComponent[opt.attr]==''}" :placeholder="opt.placeholder">
            </div>

            <?php include __DIR__ . '/edit--el--basic2.php' ?>

            <div v-if="opt.g=='data'&&(selectedComponent.dataset[opt.attr]||componentHas(opt.attr))" class="pb-1">
              <label v-html="opt.label??opt.attr"></label>
              <textarea v-if="opt.in=='text'" v-model="selectedComponent.dataset[opt.attr]" class="w-100"
              @input="componentUpdateData(opt.attr)"></textarea>
              <input v-else v-model="selectedComponent.dataset[opt.attr]"
              @input="componentUpdateData(opt.attr)" :list="'list-'+(opt.attr??'')">
              <datalist v-if="opt.list" :id="'list-'+opt.attr">
                <option v-for="o in opt.list" :value="o"></option>
              </datalist>
            </div>

            <div v-if="opt.g=='fa'&&selectedComponent.tagName=='I'" class="">
              <input v-model="searchIcon" placeholder="<?=__('search icon', ['es' => 'buscar icono'])?>">
              <div style="max-height:240px;overflow-y:scroll">
                <i type=button v-for="fi in faIcons" v-if="searchIcon==''||fi.includes(searchIcon)"
                @click="componentSetClass(fi+' inline-edit', ['fa-2x','fa-3x','fa-4x','fa-5x'])"
                :title="fi" class="p-1" :class="fi" style="width:32px"></i>
              </div>
            </div>

            <div v-if="opt.g=='svg-size'&&componentHas('svg-size')">
              <label>{{opt.label??opt.attr}}</label>
              <div class="range-input">
                <input type="range" min=8 max=200 step=2 v-model="svgSize" @input="componentUpdateSVGSize()">
                <input size=2 v-model="svgSize" @input="componentUpdateSVGSize()">
                <span type=button @click="resetAttribute(opt.attr)" class="attr-revert" title="<?=$reset_txt?>">⎌</span>
              </div>
            </div>

            <div v-if="opt.g=='transform'">
              <label>{{opt.label??opt.attr}}</label>
              <div class="range-input">
                <input type="range" :min="opt.min" :max="opt.max" :step="opt.step??0.01" v-model="cTransform[opt.attr]"
                @input="componentAddTransform(opt.attr,cTransform[opt.attr]+opt.u)">
                <input size=4 v-model="cTransform[opt.attr]" @input="componentAddTransform(opt.attr,cTransform[opt.attr]+opt.u)">
                <span @click="resetTransform(opt.attr)" type=button class="attr-revert" title="<?=$reset_txt?>">⎌</span>
              </div>
            </div>

            <div v-if="opt.g=='clip-path'" class="py-1 d-flex">
              <label><?=__('Clip path')?></label>
              <select v-model='selectedComponent.style.clipPath'>
                <option value="">None</option>
                <option value="circle(50% at 50% 50%)"><?=__('Circle', ['es' => 'Circulo'])?></option>
                <option value="polygon(50% 0%, 0 86%, 100% 86%)">Triangle</option>
                <option value="polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)">Pentagon</option>
                <option value="polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)">Exagon</option>
                <option value="polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)">Star</option>
                <option value="polygon(79.39% 90.45%,50.00% 80.00%,20.61% 90.45%,21.47% 59.27%,2.45% 34.55%,32.37% 25.73%,50.00% 0.00%,67.63% 25.73%,97.55% 34.55%,78.53% 59.27%);">Star2</option>
                <option value="polygon(100.00% 50.00%,77.72% 61.48%,85.36% 85.36%,61.48% 77.72%,50.00% 100.00%,38.52% 77.72%,14.64% 85.36%,22.28% 61.48%,0.00% 50.00%,22.28% 38.52%,14.64% 14.64%,38.52% 22.28%,50.00% 0.00%,61.48% 22.28%,85.36% 14.64%,77.72% 38.52%);">Spikes</option>
                <option value="polygon(0% 0%, 100% 0%, 100% 75%, 75% 75%, 75% 100%, 50% 75%, 0% 75%)">Message</option>
                <option value="polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)">Rombus</option>
                <option value="polygon(10% 25%, 35% 25%, 35% 0%, 65% 0%, 65% 25%, 90% 25%, 90% 50%, 65% 50%, 65% 100%, 35% 100%, 35% 50%, 10% 50%)">Cross</option>
                <option value="polygon(100% 0%, 75% 50%, 100% 100%, 25% 100%, 0% 50%, 25% 0%)">Left chevron</option>
                <option value="polygon(75% 0%, 100% 50%, 75% 100%, 0% 100%, 25% 50%, 0% 0%)">Right chevron</option>
              </select>
            </div>

            <div v-if="opt.g=='class'&&emailSupport==false&&cclasses" style="">
              <input v-if="!opt.prefix" v-model="searchClass" class="mb-1 w-100" placeholder="<?=__('Search style', ['es' => 'Buscar estilo'])?>" @input='$forceUpdate()'>
              <div v-if="opt.prefix" class="small mb-1 bordered px-1">
                <div v-for="(lbl,c) in cclasses" v-if="c.startsWith(opt.prefix)" @click="componentToggleClass(c)" type=button>
                  <input type=checkbox :checked="selectedComponent.classList.contains(c)" > <span>{{lbl}}</span>
                </div>
                <div v-for="(lbl,c) in opt.options" @click="componentToggleClass(c)" type=button>
                  <input type=checkbox :checked="selectedComponent.classList.contains(c)" > <span>{{lbl}}</span>
                </div>
              </div>
              <div v-else style="max-height:200px;overflow-y:scroll" class="small mb-1 bordered px-1">
                <div v-for="(lbl,c) in cclasses"
                 v-if="searchClass==''||lbl.toLowerCase().includes(searchClass.toLowerCase())"
                 @click="componentToggleClass(c)" type=button>
                  <input type=checkbox :checked="selectedComponent.classList.contains(c)" > {{lbl}}
                </div>
                <div v-for="(lbl,c) in bclasses"
                  v-if="searchClass==''||lbl.toLowerCase().includes(searchClass.toLowerCase())"
                  @click="componentToggleClass(c)" type=button>
                  <input type=checkbox :checked="selectedComponent.classList.contains(c)"> {{lbl}}
                </div>
              </div>
            </div>

            <div v-if="elo.basic&&elo.basic==o+1">
              <label >
                <input type=checkbox :checked="elo.show_advanced" @click="elo.show_advanced=!(elo.show_advanced??false);;$forceUpdate()">
                <?=__('Show more options', ['es' => 'Mostrar mas opciones'])?>
              </label>
              <hr v-if="elo.show_advanced">
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div style="text-align:right;font-size:90%" v-if="selectedComponent.tagName=='TABLE'">
    <span class='ge-link' @click="addTableRow()">  + <?=__('Row', ['es' => 'Fila'])?></span> |
    <span class='ge-link' @click="removeTableRow()">  - <?=__('Row', ['es' => 'Fila'])?></span>
  </div>
  <div style="text-align:right;font-size:90%" v-if="selectedComponent.tagName=='TABLE'">
    <span class='ge-link' @click="addTableColumn()">
          + <?=__('Column', ['es' => 'Columna'])?>
    </span> |
    <span class='ge-link' @click="removeTableColumn()">
      - <?=__('Column', ['es' => 'Columna'])?>
    </span>
  </div>

  <div style="text-align:right;font-size:90%" v-if="movableComponent()">

    <button type="button" @click="resetComponent()" class="btn btn-sm" title="<?=__('Reset values')?>">
      <span style="color:#444;font-size:17px;padding:0;margin:0;width: 17px">⎌</span>
    </button>
    <button type="button" @click="duplicateComponent()" class="btn btn-sm" title="<?=__('Duplicate', ['es' => 'Duplicar'])?>">
      <img src="assets/core/icons/copy.svg" class="tr-icon">
    </button>
    <button type="button" @click="deleteComponent()" class="btn btn-sm btn-outline-danger" title="<?=__('Remove', ['es' => 'Eliminar'])?>">
      <img src="assets/core/icons/trash.svg" class="tr-icon">
    </button>
  </div>


</div>

<datalist id="field_name">
  <option value="name">
  <option value="email">
  <option value="tel">
  <option value="message">
  <option value="range">
</datalist>
