<div id="component_edit_btn" onclick="componentSideEditor()" style="margin-top:2em">
<span><img class="tr-icon" src="assets/core/icons/pencil.svg"></span>
</div>
<div id="component_pad_border" style="border-width:2px"></div>
<div id="component_pad_over" style="border-width:2px"></div>
<div id="component_pad_left" title="<?=__('Margin Left', ['es' => 'Margen izquierdo'])?>"
onmousedown="component_pad_update='left'">
<div style="width:33%;height:100%;margin-left:33%"></div>
</div>
<div id="component_pad_right" title="<?=__('Width', ['es' => 'Ancho'])?>"
onmousedown="component_pad_update='right'">
<div style="width:33%;height:100%;margin-left:33%"></div>
</div>
<div id="component_pad_top" title="<?=__('Margin Top', ['es' => 'Margen superior'])?>"
onmousedown="component_pad_update='top'">
<div style="width:100%;height:33%;margin-top:22%"></div>
</div>
<div id="component_pad_move" title="<?=__('Move', ['es' => 'Movar'])?>"
onmousedown="component_pad_update='move'">
<div style="width:50%;height:50%;border-radius:50%;background:white;border-width:2px;"></div>
</div>
<div id="component_pad_resize" title="<?=__('Resize', ['es' => 'Cambiar el tamaño'])?>"
onmousedown="component_pad_update='resize'">
<div style="width:50%;height:50%;border-radius:50%;background:white;border-width:2px;"></div>
</div>
<div id="component_dl_left" style="border-left:1px dashed var(--main-primary-color)"></div>
<div id="component_dl_right" style="border-right:1px dashed var(--main-primary-color)"></div>
<div id="component_dl_top" style="border-top:1px dashed var(--main-primary-color)"></div>
<div id="component_dl_bottom" style="border-bottom:1px dashed var(--main-primary-color)"></div>

<div id="component_pad_bottom" onmousedown="component_pad_update='bottom'">
<div style="width:100%;height:33%;margin-top:33%"></div>
</div>
<div id="component_toolbar">
<!--div v-if="componentEditor==true">
  <span @click="setNode('B')" style=""><b>B</b></span>
  <span @click="setNode('I')" style=""><i>I</i></span>
  <span @click="setNode('U')" style=""><u>U</u></span>
  <span @click="setNode('DEL')" style=""><del>S</del></span>
  <span v-if="selectedComponent.tagName!='A'" @click="setNode('A',null,{href:prompt('URL', '')})" style=""><img class="tr-icon" src="assets/core/icons/link.svg"></span>
  <span @click="setNode('MARK')" style=""><mark>M</mark></span>
  <span @click="unsetNode()" style="">T<sub>x</sub></span>
</div-->
</div>
<div id="table_toolbar">
<span @click="addTableRow()" title="<?=__('Add Row')?>"><img class='tr-icon' src='assets/core/icons/row-add.svg'></span>
<span @click="removeTableRow()" title="<?=__('Remove Last Row')?>"><img class="tr-icon" src="assets/core/icons/trash.svg"></span>
</div>
<div id="table_columnbar">
<span @click="addTableColumn()" title="<?=__('Add Table Column')?>"><img class='tr-icon' src='assets/core/icons/column-add.svg'></span>
<span @click="removeTableColumn()" title="<?=__('Remove Table Column')?>"><img class="tr-icon" src="assets/core/icons/trash.svg"></span>
</div>
<div id="componentarea_editbtn">
<span @click="editColumn()" title="<?=__('Edit Column', ['es' => 'Editar columna'])?>">
  <img class="tr-icon"  src="assets/core/icons/settings.svg">
</span>
<span @click="cloneColumn()" title="<?=__('Cuplicate column', ['es' => 'Duplicar columna'])?>"><img class='tr-icon' src='assets/core/icons/copy.svg'></span>
<span @click="removeColumn()" title="<?=__('Remove column', ['es' => 'Eliminar columna'])?>"><img class="tr-icon" src="assets/core/icons/trash.svg"></span>
<div class="frame" style="pointer-events: none;border:1px solid var(--select-color);position:fixed"></div>
</div>
<div v-for="(el,i) in selectedList" class="selected_border" :style="selected_el_border[i]"></div>

<div id='block_toolbar' class='block-toolbar'>
<div>
<span class='block-design-btn' @click="blockEdit2()" title='<?=__('Section Settigns')?>'><img class='tr-icon' src='assets/core/icons/settings.svg'></span>
<span class='block-swap-btn' @click="blockSwap()" title='<?=__('Section move up')?>'><img class='tr-icon' src='assets/core/icons/arrow-up.svg'></span>
<span class='block-swap-btn' @click="blockClone()" title='<?=__('Copy section')?>'><img class='tr-icon' src='assets/core/icons/copy.svg'></span>
<span class='block-del-btn' @click="blockDelete()" title='<?=__('Remove section')?>'><img class='tr-icon' src='assets/core/icons/trash.svg'></span>
</div>
</div>
<div id="template_toolbar">
<span @click="editTemplate()" title="<?=__('Edit Template', ['es' => 'Latte'])?>"><img class="tr-icon" src="assets/core/icons/code-moustache.svg"></span>
</div>
