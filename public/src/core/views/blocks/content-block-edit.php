<?=View::script('core/vue-color.min.js');?>
<?=View::script('core/gila.min.js')?>
<?=View::script('lib/vue/vue.min.js');?>
<?=View::scriptAsync('core/lang/content/' . Config::get('language') . '.js')?>
<?=View::script("core/admin/content.js")?>
<?=View::script('core/admin/media.js')?>
<?=View::scriptAsync("core/admin/vue-components.js")?>
<?=View::scriptAsync("core/admin/menu-editor.js")?>
<?=View::scriptAsync('core/admin/color-input303.js');?>
<?=View::script('lib/bootstrap52/bootstrap.bundle.min.js')?>
<?=View::css('lib/bootstrap52/bootstrap.min.css')?>
<?=View::css('core/gila.min.css')?>
<?=View::css('core/admin/content.css')?>
<style>
<?php if ($content !== 'page') : ?>
.span-editfooter-btn {display:none!important}
<?php endif; ?>
.selected-component label{
  pointer-events:none;
}
</style>
<style id="themePairFont"></style>
<style id="themeColorPalette"></style>

<?php
$cid = $content . '_' . $id . '_';
$editPageUrl = Config::base() . 'blocks/pageEditor/' . $id;
$previewPageUrl = Config::base() . 'blocks/pagePreview/' . $id;
Config::loadLang('core/lang/');

$pages = [];
$contentTable = new Table($content);
$contentTitle = $contentTable->getTable()['title'] ?? __('Back');
$googlefonts = array_merge([null], View::$web_safe_fonts);

$emailSupport = 'false';
if (in_array($content, ['email','campaign','template'])) {
    $emailSupport = 'true';
} else {
    $googlefonts = array_merge($googlefonts, Config::getArray('theme.fonts') ?? []);
}

?>
<div id=pageFrameDiv >
  <div style="position:fixed;background:rgba(120,120,120,0.5);backdrop-filter: blur(10px);-webkit-backdrop-filter: blur(10px);left:0;right:0;top:0;width:100%;bottom:0;" onclick="this.parentNode.style.display='none'">
    <div style="position:absolute;cursor:pointer;color:white;font-size:3em;right:10px;top:10px;">&times;</div>
  </div>
  <iframe style="width:100%;top:10vh;height:93vh;border:0;transition:0.3s;box-shadow:black 0px 0px 5px;z-index:100" id=pageFrame ></iframe>
</div>


<script>
content_id = '<?=$cid?>';
contentTable = '<?=$content?>';
emailSupport = <?=$emailSupport?>;
isDraft = <?=($isDraft == true ? 'true' : 'false')?>;
useSpellcheck = <?=(FS_ACCESS == true || $content == 'post' ? 'true' : 'false')?>;
pages = <?=json_encode($pages)?>;
pageId = <?=$id?>;
pageTitle = '<?=addslashes($title)?>';
pagePublic = <?=$pagePublic ?? 'true'?>;
pageSlug = '<?=$pageSlug?>';
pageLanguage = '<?=Config::lang()?>';
editPageUrl = '<?=$editPageUrl?>';
canCreatePrototype = <?=(Event::get('canCreatePrototype', true) ? 'true' : 'false')?>;
themeId = '<?=Config::get('theme')?>';
selectedFonts = <?=Config::get('theme.selectedFonts') ?? 0?>;
selectedColors = <?=Config::get('theme.selectedColors') ?? 0?>;
rootVar = {
  hfont:'<?=Config::get('theme.heading-font')?>',
  bfont:'<?=Config::get('theme.body-font')?>',
  color1:'<?=Config::get('theme.primary-color')?>',
  color2:'<?=Config::get('theme.accent-color')?>',
  color3:'<?=Config::get('theme.heading-color')?>',
  color4:'<?=Config::get('theme.body-color')?>'
};
g.language = "<?=Config::get('language') ?? 'en'?>";
googlefonts = <?=json_encode($googlefonts)?>;
base_url = '<?=Config::get('base')?>';
allgooglefonts = <?=json_encode(include 'src/core/data/google_fonts.php')?>;
var dummyGroups = {
  'basic': g.tr('Basic', {es:'Básicos'}),
  'text': g.tr('Text',{es:'Texto'}),
  'media': 'Media',
  'btn': g.tr('Buttons', {es:'Botones'}),
  'sticker': g.tr('Stickers', {es:'Pegatinas'}),
  'other': g.tr('Others', {es:'Otros'}),
  'form': g.tr('Form', {es:'Formulario'}),
}
var attrGroups = {
  'basic': g.tr('Basic', {es:'Básicos'}),
  'text': g.tr('Text',{es:'Texto'}),
  'design': g.tr('Design',{es:'Diseño'}),
  'position': g.tr('Position',{es:'Posicion'}),
  'transform': g.tr('Transform',{es:'Transformar'}),
}
</script>


<div id="editMenu" style="position: fixed;background:#555;color:white;">
  <div id="topEditMenu">
    <div style="display:flex;" class="">
      <span style="cursor:pointer" onclick="<?=('location.href=\'' . $back_url . '\'' ?? 'history.back()')?>">
        <img src='assets/core/arrow-left-bbb.svg' style="cursor:pointer;height:32px">
      </span>
    </div>
    <div>
      <?php if ($content == 'page_template') : ?>
        &nbsp;<span type=button onclick="template_options(<?=$id?>)" style="display:inline-block;padding:0.5em;border-right:1px solid #333"><i class="fa fa-cogs"></i></span>
      <?php endif; ?>
      <div type=button @click="editPageData()" style="display:inline-block;padding:0.5em;border-right:1px solid #333">
        <span style="max-width:260px">{{pageTitle}}</span>&nbsp;
        <span><i class="fa fa-pencil"></i></span>
      </div>
      <?php if (isset($r['language'])) : ?>
      <div style="display:inline-block;padding:0.5em;border-right:1px solid #333">
        <span style="text-transform: uppercase;" type="button" data-bs-toggle="dropdown"
          aria-expanded="false" id="languageDropdown"><?=$r['language']?></span>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <?php
        $languages = Config::languageOptions();
        foreach ($languages as $key => $lang) {
            echo "<li><a class=\"dropdown-item text-dark\" href=\"blocks/$content/$id?lang=$key\">$lang</a></li>";
        }
        ?>
        </ul>
      </div>
      <?php endif; ?>

      <div class="vtour4" style="display:inline-block;padding:0.5em;border-right:1px solid #333">
        <span onclick="mobileView()"><img class="tr-icon" src="assets/core/icons/device-mobile.svg"></span>
        <span onclick="tabletView()"><img class="tr-icon" src="assets/core/icons/device-tablet.svg"></span>
        <span onclick="desktopView()"><img class="tr-icon" src="assets/core/icons/device-desktop.svg"></span>
      </div>

      <div style="display:inline-block;padding:0.5em;">
        <a href="<?=Config::base('support')?>" target=_blank> <i class="fa fa-info-circle"></i></a>
      </div>
    </div>

    <div>
      <div id="liveUsers" style="display: inline-block;padding: 0.2em 0.4em;vertical-align: middle;"></div>
        <div style="display:inline-block;padding:0.2em 0.4em;">
          <a type="button" :class="{disabled:!draft}" href="javascript:void(0)" @click="revertChanges()"><img class="tr-icon" src="assets/core/icons/revert.svg" title="<?=__('Undo')?>"></a>&nbsp;
          <a type="button" href="javascript:void(0)" @click="listChanges()"><img class="tr-icon" src="assets/core/icons/history.svg" title="<?=__('History')?>"></a>&nbsp;
          <a type="button" class="vtour5" :class="{disabled:!draft}" href="javascript:void(0)" @click="discardChanges()"><img class="tr-icon" src="assets/core/icons/trash-d.svg" title="<?=__('Delete Draft')?>"></a>&nbsp;
          <a type="button" class="vtour-public" target="_blank" href="blocks/preview/<?=$content?>/<?=$id?>"><img class="tr-icon" src="assets/core/icons/eye.svg" title="<?=__('Preview', ['es' => 'Prevista'])?>"></a>&nbsp;
          <button type="button" class="g-btn success vtour-public-btn" :class="{disabled:!draft}" @click="saveChanges()"><?=__('Save', ['es' => 'Guardar'])?></button>
        </div>
      </div>
    </div>

    <?php
    include_once __DIR__ . '/edit--pads.php';
    include_once __DIR__ . '/edit--el.php';
    include_once __DIR__ . '/edit--sidebar.php';
    include_once __DIR__ . '/edit--block.php';
    ?>
  </div>
  <block-editor-add-popup>
</div><!--#editMenu-->

<?=View::script('core/block-editor-add.js')?>
<?=View::script('core/block-editor.js')?>
<?=View::cssAsync('lib/font-awesome/6/css/all.css')?>

<script>
var previewPageUrl = '<?=$previewPageUrl?>';
appEditMenu.options.setBlockHeightOnDrop = true
appEditMenu.getElementOptions()

block_heads_append()
desktopView()
</script>
<?php
if ($content == 'page') {
    include __DIR__ . '/../blocks/buttons-append-page.php';
} elseif (strpos($content, 'page_template') == 0) {
    @include __DIR__ . '/../blocks/buttons-append-page_alt.php';
}
?>
<?=View::script('lib/CodeMirror/codemirror.js')?>
<?=View::script('lib/CodeMirror/javascript.js')?>
<?=View::cssAsync('lib/CodeMirror/codemirror.css')?>
<?=View::scriptAsync("lib/tinymce5/tinymce.min.js")?>
<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>

<?php Event::fire('content-block-edit') ?>
