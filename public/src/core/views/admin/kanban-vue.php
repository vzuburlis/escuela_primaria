<?php
$_GET['pipeline_id'] = $_GET['pipeline_id'] ?? 1;
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
if (isset($t['require_filters'])) {
    foreach ($t['require_filters'] as $rf => $rv) {
        $_GET[$rf] = $_GET[$rf] ?? $rv;
    }
}
$href_prefix = $t['kanban_href'] ?? 'crm/deal/';
View::script('core/gila.js');
View::css('core/admin/content.css');
View::css('core/admin/vue-editor.css');
View::script('lib/vue/vue.min.js');
View::script('core/admin/content.js');
View::script('core/lang/content/' . Config::lang() . '.js');
View::scriptAsync('core/admin/media.js');
View::scriptAsync('core/admin/vue-components.js');
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
.type-textarea label{width:100%}
.edit-item-form>.type-tinymce{min-height:300px;margin-bottom:20px}
.edit-item-form>.type-tinymce,.edit-item-form>.type-list{grid-column:1/-1}
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
unset($tableFilters['p']);
unset($tableFilters['page']);
View::widgetArea('content.' . $table);
$gtotalrows = $gtable->totalRows($tableFilters);
$grows = $gtable->getRows($tableFilters, ['limit' => false, 'stage_id' > 0]);
$stages = DB::getAssoc("SELECT * FROM crm_stage WHERE pipeline_id=? ORDER BY pos ASC;", [$_GET['pipeline_id'] ?? 1], 'id');
?>

<div class="d-flex justify-content-space-between gap-2 rounded-2 p-2" id="vue-kanban" style="background:#efefef;">
  <div v-for="(opt,grp) in table.fields[table.groups[0]].options" v-if="grp>0"
  style="min-width:220px;width:300px;padding:8px" class="p-2 d-flex flex-column gap-1">
    <b>{{opt}}</b>
    <small v-if="table.fields.price">
      ${{totalValue(grp)}}  
      <small v-if="stages[grp] && total>0 && parseInt(stages[grp].win_percentage)<100" class="text-secondary">
        &nbsp;({{parseInt(stages[grp].win_percentage)}}% de ${{totalValue(grp,false)}})
      </small>
    </small>
    <!--div :style="{borderTop:'4px solid '+optionColor(table.fields[table.groups[0]],grp)}"></div-->
    <draggable group="opt" :list="cards[grp]" @end="endDrag"
    style="min-height:60vh;height:100%" :data-value="grp">
    <div v-for="item in cards[grp]">
      <div class="card p-2 mb-2" :key="item.id" type="button" @click="location.href='<?=$href_prefix?>'+item.id">
        <div>
          <img :src="cardImage(item)" style="width:32px;height:32px;float:right;border-radius:50%" :title="item.owner_id?.username">
          <b>{{item.contact_name}}</b><br>
          <span>{{item.name}}</span>
        </div>
        <small class="d-flex gap-1 mt-2 align-item-center">
          <span v-for="f in table.kanban_list" class="text-secondary" v-html="display_cell(item,f)"></span>
          <span>
        </small>
        <!--div v-if="table.kanban_list" class="mt-1">
          <div class="d-block mt-1" v-for="fkey in table.kanban_list" v-html="display_cell(item,fkey)"></div>
        </div-->
        <!--div v-if="table.kanban_data" class="mt-1">
          <div v-for="field in table.kanban_data" class="mt-1"><b>{{table.fields[field].title}}: </b>
            <span class="d-inline-block" v-html="display_cell(item,field)"></span>
          </div>
        </div-->
        <!--div v-if="table.kanban_flex" @click="$event.stopPropagation()" class="d-flex align-items-center gap-1 mt-1">
          <span class="d-inline-block" v-for="fkey in table.kanban_flex" v-html="display_cell(item,fkey)"></span>
        </div-->
        <!--div v-if="table.commands" class="dropdown" @click="$event.stopPropagation()">
          <span class="g-icon-btn com-btn float-right" data-bs-toggle="dropdown" aria-expanded=false> ⋮ </span>
          <!ul class="dropdown-menu">
            <li v-for="(com,icom) in table.commands" v-if="$parent.canUse(com)" class="p-1" role="button"
            @click="$parent.command(com,item.id)" v-html="$parent.command_label(com, true)"></li>
          </ul>
        </div>
      </div-->
    </div>
    </draggable>
  </div>
</div>


<script>
Vue.component('v-select', VueSelect.VueSelect);
cmirror=new Array()
mce_editor=new Array()
var csrfToken = '<?=Form::getToken()?>'
var app = new Vue({
  el:"#vue-kanban",
  data: function() {
    table = <?=json_encode($t)?>,
    cards = {}
    for(o in table.fields[table.groups[0]].options) {
      cards[o] = []
    }
    return {
      cards: cards,
      table: table,
      items: <?=json_encode($grows)?>,
      name: '<?=$table?>',
      stages: <?=json_encode($stages)?>,
    }
  },
  watch: {
    items: function(items) {
      this.updateCards(items)
    }
  },
  methods: {
    optionColor: function(field,val){
    if(typeof field.option_colors!='undefined') {
        return field.option_colors[val]
      } else return 'grey'
    },
    updateCards: function(items) {
      field = this.table.groups[0]
      for(o in this.table.fields[field].options) if(o>0) {
        this.cards[o] = []
        for(i in this.items) if (this.items[i][field]==o) {
          this.cards[o].push(this.items[i])
        }
      }
      console.log(this.cards)
    },
    tool_label: function(tool) {
      if(typeof gtableTool[tool]=='undefined') return _e(tool)
      return _e(gtableTool[tool].label)
    },
    cardImage: function(item) {
      if (item.owner_id && item.owner_id.photo) return item.owner_id.photo
      return 'assets/core/default-user.png'
    },
    endDrag: function(ev) {
      field = this.table.groups[0]
      for(o in this.cards) {
        for(i in this.cards[o]) if (this.cards[o][i][field]!=o) {
          this.update_cell(this.cards[o][i],field,o)
        }
      }
    },
    totalValue: function(o,p=true) {
      total = 0.0
      for(i in this.cards[o]) total+=parseFloat(this.cards[o][i].price)
      if (p && this.stages[o] && parseFloat(this.stages[o].win_percentage)>0) {
        total = total*(parseFloat(this.stages[o].win_percentage)/100)
      }
      return total
    },
    update_cell: function(item, fkey, v) {
      if (typeof item==='object') {
        id = item.id
      } else {
        id = this.data.rows[item][0]
      }
      if (typeof fkey==='number') {
        fkey = this.data.fields[fkey]
      }

      let _this = this
      url = 'cm/update_rows/'+this.name+'?id='+id
      data = new FormData()
      data.append(fkey, v)
      g.ajax({method:'post',url:url,data:data,type:'json',fn:function(data) {
        _this.update_row(data.items[0]);
      }})
    },
    runtool: function(tool,e) {
      if(tool==0) return;
      this.query=this.filters;
      for(fkey in this.filter) {
        if(this.filter[fkey]!=='') this.query += '&'+fkey+'='+this.filter[fkey]
      }
      gtableTool[tool].fn(this)
      e.preventDefault()
    },
    canUseTool: function(com) {
      if(gtableTool[com] && gtableTool[com].permission && this.permissions) {
        for(p of this.table.permissions[gtableTool[com].permission]) {
          if(this.permissions.includes(p)) return true
        }
        return false
      }
      return true
    },
  }
})
app.updateCards()


g_tinymce_options.templates = <?php echo json_encode((isset($templates) ? $templates : [])); ?>;

base_url = "<?=Config::get('base')?>/"
g_tinymce_options.document_base_url = "<?=Config::get('base')?>"
g_tinymce_options.height = '100%'
g_tinymce_options.entity_encoding = 'raw'
g_tinymce_options.plugins = ['code codesample table charmap image media lists link emoticons paste']
g_tinymce_options.paste_auto_cleanup_on_paste = true
g_tinymce_options.paste_remove_styles = true
g_tinymce_options.paste_remove_styles_if_webkit = true

window.onpopstate = function(event) {
  console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
  location.reload()
}



function display_cell(item,fkey) {
      if (typeof item=='undefined') console.log('item undefined')
      if (typeof fkey==='number') {
        fkey = this.data.fields[fkey]
      }

      cv = item[fkey]
      field = this.table.fields[fkey]
      if (typeof field=='undefined') console.log(fkey)
      displayValue = cv

      if(field.alt) if(!cv) {
        cv = field.alt
        displayValue = '<span style="opacity:0.66">'+cv+'</span>'
      }
      if (typeof this.table.fields[fkey].eval != "undefined") {
        return eval(this.table.fields[fkey].eval)
      }

      if(typeof gtableFieldDisplay[fkey]!='undefined') {
        return gtableFieldDisplay[fkey](item);
      }

      // Display type
      if (typeof field.display_type != "undefined") {
        displayType = field.display_type;
      } else if (typeof field.input_type != "undefined") {
        displayType = field.input_type;
      } else if (typeof field['input-type'] != "undefined") {
        displayType = field['input-type'];
      } else {
        displayType = field.type;
      }

      if(typeof gtableDisplayType[displayType]!='undefined') {
        return gtableDisplayType[displayType](item, fkey);
      }

      if(displayType=='checkbox') {
        style = ''
        if (typeof field.toggle_values!='undefined') style='cursor:pointer'
        if(cv==1) {
          return '<i style="color:green;'+style+'" class="fa fa-check fa-2x"></i>'
        } else {
          return '<i style="color:red;'+style+'" class="fa fa-remove fa-2x"></i>'
        }
      }

      if(displayType=='color') {
        if (typeof field.option_colors != "undefined") if(cv!==null) {
          displayValue = field.option_colors[cv]
        } else displayValue = 'white'
        return '<svg viewBox="0 0 40 40" style="width:28px;vertical-align: middle;">\
        <circle stroke="lightgrey" stroke-width=1 fill="'+displayValue+'" r="15" cx="20" cy="20"/>\
        </svg>'
      }

      if(displayType=='date' && !isNaN(cv)) {
        return g.getDate(cv)
      }
      if(displayType=='datetime' && !isNaN(cv)) {
        return g.getDateTime(cv)
      }


      if(displayType=='money') {
        lf = field.number_format??'es-MX'
        currency = field.currency??'MXN'
        displayValue = new Intl.NumberFormat(lf, { style: 'currency', currency: currency }).format(displayValue);
        return '<div style="text-align:right">'+displayValue+'</div>';
      }

      if(displayType=='media') if(cv!=null && cv.length>0) {
        src = 'lzld/thumb?src='+cv+'&media_thumb=100'
        if (cv.startsWith('https:') || cv.startsWith('http:')) {
          src = cv
        }
        if (typeof field.display_width!='undefined') {
          src = 'lzld/thumb?src='+cv+'&media_thumb='+field.display_width
          return '<img src="'+src+'" style="margin:auto;max-height:200px;"></img>'
        }
        if (this.layout=='grid') {
          if (src!=cv) src = 'lzld/thumb?src='+cv+'&media_thumb=0'
          return '<img src="'+src+'" style="margin:auto;max-height:140px;"></img>'
        }
        return '<img src="'+src+'" style="max-height:35px;max-width:50px;"></img>'
      } else {
        if (field.media_placeholder) {
          if (this.layout=='grid') {
            return '<img src="'+field.media_placeholder+'" style="margin:auto;max-height:140px;max-width:200px;opacity:0.5"></img>'
          }  
          return '<img src="'+field.media_placeholder+'" style="max-height:35px;max-width:50px;opacity:0.5"></img>'
        }
        return '';
      }

      if(displayType=='number') {
        return '<div style="text-align:right">'+displayValue+'</div>';
      }

      if(displayType=='radial-bar') {
        displayValue=parseFloat(displayValue).toFixed(2)
        if (isNaN(displayValue)) return
        pcValue = parseInt(displayValue*100)
        if(field.display_percentage) displayValue=pcValue+'%'
        return '<div style="text-align:center;width:100%"><svg viewBox="0 0 40 40" style="width:28px;vertical-align: middle;">\
        <circle stroke="lightgrey" stroke-width="8" fill="transparent" r="15" cx="20" cy="20"/>\
        <path d="M21 4 a 15 15 0 0 1 0 30 a 15 15 0 0 1 0 -30"\
          fill="none"\ stroke="var(--main-a-color)";\ stroke-width="8";\
          stroke-dasharray="'+pcValue+', 100" />\
      </svg> <span style="vertical-align: middle;">'+displayValue+'</span></div>'
      }

      if(displayType=='text' & item.text && item.text.length>100) {
        return item.text.substring(0, 97)+'...';
      }


      if (typeof field.options != "undefined") if(cv!==null) {
        if (typeof field.options[cv] != "undefined") {
          if(field.option_colors && field.option_colors[cv]) {
            html = '<span class="g-badge" style="background:'+field.option_colors[cv]+'">'+field.options[cv]+'</span>';
            return html
          }
          return field.options[cv]
        }
        let resp = ''
        if(typeof cv=='string') {
          let csv = cv.split(',')
          for(i=0;i<csv.length;i++)  if(typeof field.options[csv[i]] != "undefined") {
            resp += field.options[csv[i]]+'<br>'
          } else resp += csv[i]+'<br>'
        }
        return resp
      }

      if(field.inline_edit) {
        if (displayValue==null) displayValue=''
        return '<div contenteditable="true" data-field="'+fkey+'">'+displayValue+'</div>';
      }
      if (displayValue && typeof displayValue=='string' && displayValue.length>0) {
        displayValue = displayValue.replace(/ /g, '\u00a0')
      }
      return displayValue
    }
</script>

