<?php
if (!isset($gtable)) {
    $gtable = new Table($table, Session::permissions());
}
$t = $gtable->getTable();
if (!$gtable->can('read')) {
    @http_response_code(403);
    echo '<h1>403 Error</h1>';
    echo '<h2>You cannot access this content</h2>';
    return;
}

// add table settings
$table_options_json = DB::value("SELECT `data` FROM table_options WHERE `table`=? AND user_id=?
UNION SELECT `data` FROM table_options WHERE `table`=? AND user_id=0", [$table, Session::userId(), $table]);
$table_options = json_decode($table_options_json, true);
foreach ($table_options as $i => $ts) {
    if (isset($t['fields'][$i])) {
        $t['fields'][$i]['show'] = $ts['show'] ?? ($t['fields'][$i]['show'] ?? true);
    }
}

if (isset($t['require_filters'])) {
    foreach ($t['require_filters'] as $rf => $rv) {
        $_GET[$rf] = $_GET[$rf] ?? $rv;
    }
}
View::script('core/gila.js');
View::css('core/admin/content.css');
View::css('core/admin/vue-editor.css');
View::script('lib/vue/vue.min.js');
View::script('core/admin/content.js');
View::script('core/lang/content/' . Config::lang() . '.js');
View::scriptAsync('core/admin/media.js');
View::scriptAsync('core/admin/vue-components.js');
View::scriptAsync('core/wc-select-component.js');
View::scriptAsync('core/admin/vue-editor.js');
View::script('lib/CodeMirror/codemirror.js');
View::script('lib/vue/vue-select.js');
View::scriptAsync('lib/CodeMirror/htmlmixed.js');
View::scriptAsync('lib/CodeMirror/javascript.js');
View::cssAsync('lib/CodeMirror/codemirror.css');
View::cssAsync('lib/vue/vue-select.css');
View::scriptAsync('lib/bootstrap52/bootstrap.bundle.min.js');
View::script('core/Sortable.min.js');
View::script('core/vuedraggable.umd.min.js');
?>

<style>.CodeMirror{max-height:300px;border:1px solid var(--main-border-color);width:100%}</style>
<?=View::scriptAsync("lib/tinymce5/tinymce.min.js")?>

<style>
.type-textarea label{width:100%;grid-column:1/-1}
.edit-item-form .type-tinymce{min-height:300px;min-width:300px;margin-bottom:20px}
.gila-popup.side .edit-item-form>.type-tinymce{min-height:480px}
#widget_options_form textarea{min-height:180px}
.edit-item-form>.type-tinymce,.edit-item-form>.type-list,
.edit-item-form>.type-textarea,
.edit-item-form>.type-codemirror{grid-column:1/-1}
.mce-tinymce.mce-container.mce-panel{display:inline-block}
<?php if (Config::get('post_text') ?? true) : ?>
@media only screen and (min-width:700px){
  #user-post-edit-item-form>div,
  #post-edit-item-form>div{
    grid-template-columns: 2fr 2fr 2fr 1fr 1fr 1fr!important;
    min-height:60vh;
  }
  #user-post-edit-item-form>div>div,
  #post-edit-item-form>div>div{
    grid-column:span 3;
  }
  .gila-popup #user-post-edit-item-form .type-tinymce,
  .gila-popup #post-edit-item-form .type-tinymce{grid-column:1/4;grid-row:1/20}
}
<?php endif; ?>
.tox .tox-menubar,.tox .tox-toolbar, .tox .tox-toolbar__overflow, .tox .tox-toolbar__primary{
  background-color: #f0f0f0;
}
.g-form .vs__search{
  background: inherit;
  border: inherit;
  padding: inherit;
}
.v-select {display:inline-block;min-width:180px;width:90%}
.vs__selected {max-width:300px;max-height:26px;overflow:hidden;align-items:start}
#select_row_dialog .body{padding:0}
</style>

<script>
g.language = '<?=Config::lang()?>';
</script>
<?php
foreach ($t['js'] as $js) {
    $file = Config::src() . strtr($js, ['src/' => '/']);
    echo "<script>" . file_get_contents($file) . "</script>";
}
foreach ($t['css'] as $css) {
    View::css($css);
}

// read the url query and add it in filters
$tableFilters = is_array($t['filters']) ? array_merge($t['filters'], $_GET) : $_GET;
$page = $_GET['page'] ?? 1;
unset($tableFilters['p']);
unset($tableFilters['page']);
View::widgetArea('content.' . $table);
$gtotalrows = $gtable->totalRows($tableFilters);
$growsIndexed = $gtable->getRowsIndexed($tableFilters, ['page' => $page]);
$gitems = $gtable->getRows($tableFilters, ['page' => $page]);
?>

<div id="vue-table">
  <g-table gtype="<?=$table?>" ref="gtable"
  gtable="<?=htmlspecialchars(json_encode($t))?>"
  gfilter="<?=htmlspecialchars(json_encode($tableFilters))?>"
  gfields="<?=htmlspecialchars(json_encode($gtable->fields('list')))?>"
<?php
if (!isset($t['layouts'])) : ?>
  gitems="<?=htmlspecialchars(json_encode($gitems))?>"
<?php endif; ?>
<?php
if (isset($t['filters'])) : ?>
  gfilters="&<?=htmlspecialchars(http_build_query($t['filters']))?>"
<?php endif; ?>
  permissions="<?=htmlspecialchars(json_encode(Gila\Session::permissions()))?>"
  gtotalrows="<?=$gtotalrows?>"
  base="<?=Config::base('admin/content/')?><?=$table?>"></g-table>
</div>

<?php
if (isset($t['prompt']) && $gtotalrows == 0) {
    include $t['prompt'];
    if (isset($t['prompt_no_table']) && !isset($_GET['_tl'])) {
        echo '<style>#vue-table{display:none}</style>';
    }
} ?>

<script>
Vue.component('v-select', VueSelect.VueSelect);
cmirror=new Array()
mce_editor=new Array()
var csrfToken = '<?=Form::getToken()?>'
var app = new Vue({
  el:"#vue-table"
})

g_tinymce_options.templates = <?php echo json_encode((isset($templates) ? $templates : [])); ?>;
g_tinymce_options.menubar = <?=json_encode($t['tinymce_menubar'] ?? true)?>;

base_url = "<?=Config::get('base')?>/"
g_tinymce_options.document_base_url = "<?=Config::get('base')?>"
g_tinymce_options.height = '100%'
g_tinymce_options.entity_encoding = 'raw'
g_tinymce_options.plugins = ['code codesample table charmap image media lists link emoticons paste']
g_tinymce_options.paste_auto_cleanup_on_paste = true
g_tinymce_options.paste_remove_styles = true
g_tinymce_options.paste_remove_styles_if_webkit = true
//g_tinymce_options.paste_strip_class_attributes = true

window.onpopstate = function(event) {
  console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
  location.reload()
}

</script>

