
cmirror=new Array()
mce_editor=new Array()
commonAttributes = ['href','src','style','width','height','frameset','frameborder','allow','type','alt','min','max','placeholder'];

g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 210,
  remove_linebreaks : false,
  document_base_url: base_url,
  verify_html: false,
  cleanup: true,
  plugins: ['code codesample table charmap image media lists link emoticons'],
  menubar: true,
  toolbar: 'format formatselect bold italic | bullist numlist outdent indent | link image media emoticons code table | alignleft aligncenter alignright alignjustify',
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },
}

g.dialog.buttons.src_code = {title:'HTML', fn:function() {
  g.loader()
  g.closeModal();
  appEditMenu.editTextCode()
}, class:'btn-outline-secondary'}

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
  g.loader()
  fm = new FormData(widget_options_form)
  values = readFromClassComponents()
  for(x in values) {
    fm.set(x, values[x])
  }
  if (typeof widget_id.value!='undefined') {
    cblock_pos = widget_id.value.split('_')[2]  
  }
 
  g('#widget-popup').parent().remove();
  document.body.style.overflowY = 'auto'
  g.ajax({url:'blocks/update?g_response=content',method:'POST',data:fm,fn:function(data) {
    g.loader(false)
    blocks_preview_reload(data)
    appEditMenu.draft = true
  }, error:function(data) {
    data = JSON.parse(data)
    g.alert(data.error, 'error')
  }})
}, class:'g-btn btn-primary'}

g.dialog.buttons.create_widget = {title:'Create',fn:function(){
  g.loader()
  widget_id = cblock_content.replace('/','_')+'_'+cblock_pos;
  g.post('blocks/create', 'id='+widget_id+'&type='+cblock_type, function(data){
    fm = new FormData(widget_options_form)
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    fm.set('widget_id',widget_id)
    g('#widget-popup').parent().remove();
    document.body.style.overflowY = 'auto'
    g.ajax({url:'blocks/update?g_response=content',method:'POST',data:fm,fn:function(data){
      g.loader(false)
      if (cblock_type!='text') {
        // some dynamic blocks need to run their js
        window.location.reload();
      } else {
        blocks_preview_reload(data)
        appEditMenu.draft = true
      }
    }})
  })
}, class:'g-btn btn-primary'}



g.dialog.buttons.delete_widget = {title:'Delete',class:'error',fn:function() {
  let el = g('#widget_options_form input[name=widget_id]').all[0]
  g('#widget-popup').parent().remove();
  document.body.style.overflowY = 'auto'
  block_del(el.value)
}, class:'g-btn btn-danger'}

function block_edit_open() {
  textarea = g('#widget_options_form textarea').first()
  if (!textarea || !textarea.innerHTML.includes('{{')) {
    app = new Vue({
      el: '#widget_options_form'
    })
  }
  transformClassComponents();
}

function block_edit_close() {
  textareas=g('.codemirror-js').all
  for(i=0;i<textareas.length;i++) {
    textareas[i].value=cmirror[i].getValue()
  }
  textareas_mce=g('.tinymce').all[0]
  if(typeof textareas_mce!='undefined') {
    textareas_mce.value=tinymce.get(mce_editor[0]).getContent()
  }
}


function block_edit(id,type,group=null) {
  href='blocks/edit?id='+id+"&type="+type;
  if(group!=null) href = href+'&group='+group
  _type = type.toUpperCase().replace('_',' ');
  if (group=='mustache'||group=='text') g.loader()
  g.get(href, function(data) {
    g.loader(false)
    buttons = 'update_widget'
    if (_type=='TEXT') buttons = 'update_widget src_code'
    if (group=='mustache'||group=='text') buttons = 'update_widget'
    g.dialog({class:'lightscreen large',id:'widget-popup',title:_type,body:data,type:'modal',buttons:buttons})
    block_edit_open()
  });
};



function block_pos(id,pos) {
  g.loader()
  g.post('blocks/pos', 'id='+id+'&pos='+pos, function(data) {
    g.loader(false)
    blocks_preview_reload(data)
    appEditMenu.draft = true
  });
}



function open_gallery_post() {
  g.post("admin/media","g_response=content&path=assets",function(gal){ 
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',class:'large',id:'media_dialog','z-index':99999})
  })
}

blocks_preview_reload = function(data) {
  if (typeof blocks == 'undefined') {
    if (typeof main == 'undefined') {
      blocks = g('body>div').first()
    } else {
      blocks = g('#main>div').first()
    }
  }
  blocks.innerHTML = data
  g.lazyLoad()
  block_heads_append()
  updateComponentsDrag()
}

block_heads_append = function() {
  g('.block-head').prepend("<div>\
<span class='span-padding-top' onmousedown=\"block_pad_relative=this;block_pad_update='top'\"></span>\
<span class='block-add-btn'><button title='"+g.tr('Add Block', {'es':'Anadir Bloque'})+"'><img class='tr-icon' src='assets/core/icons/plus.svg'></button></span>\
</div>");
  g('.block-head').append("<div style='position: relative;width: 100%;'>\
<span class='span-padding-bottom' onmousedown=\"block_pad_relative=this;block_pad_update='bottom'\"></span>\
</div>");
}

document.body.classList.add('v-lg')
function document_body_class_v(x) {
  document.body.classList.remove('v-xs')
  document.body.classList.remove('v-md')
  document.body.classList.remove('v-lg')
  document.body.classList.add('v-'+x)
  // foreach block-head
  el = document.getElementsByClassName('block-head')
  for(var i=0; i<el.length; i++) {
    dis = appEditMenu.componentGetDisplay(g(el[i]).find('section').all[0])
    if (dis=='' || dis.includes(x)) {
      el[i].style.display = 'block'
    } else {
      el[i].style.display = 'none'
    }
  }
}
function desktopView() {
  document.body.style.width = 'auto'
  if (document.getElementById('nav')) {
    document.getElementById('nav').style.setProperty('display', 'flex');
  }
  appEditMenu.view = 'lg'
  document_body_class_v('lg')
}
function tabletView() {
  document.body.style.width = '900px'
  if (document.getElementById('nav')) {
    document.getElementById('nav').style.setProperty('display', 'none', 'important');
  }
  appEditMenu.view = 'md'
  document_body_class_v('md')
}
function mobileView() {
  document.body.style.width = '330px'
  if (document.getElementById('nav')) {
    document.getElementById('nav').style.setProperty('display', 'none', 'important');
  }
  appEditMenu.view = 'xs'
  document_body_class_v('xs')
}
g.dialog.buttons.save_options = {
  title: g.tr('Save', {'es':'Guardar'}),
  fn:function() {
    let p = g.el('theme_id').value;
    let fm=new FormData(g.el('theme_options_form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'admin/themes?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x) {
      g.loader(false)
      g('.gila-darkscreen').remove();
      window.location.reload();
    }})
  },
  class:'g-btn btn-primary'
}
g.dialog.buttons.save_addonoptions = {
  title: g.tr('Save', {'es':'Guardar'}),
  fn:function() {
    let p = g.el('addon_id').value;
    let fm=new FormData(g.el('addon_options_form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'admin/packages?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x) {
      g.loader(false)
      g('.gila-darkscreen').remove();
      window.location.reload();
    }})
  },
  class:'g-btn btn-primary'
}

g.dialog.buttons.save_settings = {
  title: g.tr('Save'), fn:function() {
    let fm = new FormData(g.el('settings-form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'admin/settings?g_response=content',method:'POST',data:fm,fn:function(x){
      g.loader(false)
      g('.gila-darkscreen').remove();
      window.location.reload();
    }})
  },
  class:'g-btn btn-primary'
}

g.dialog.buttons.save_mainmenu = {
  title: g.tr('Save'), fn:function() {
    let fm=new FormData(g.el('mainmenu-form'))
    g.loader()
    g.ajax({url:'blocks/mainmenu?lang='+pageLanguage,method:'POST',data:fm,fn:function(x){
      g.loader(false)
      g('.gila-darkscreen').remove();
      window.location.reload();
    }})
  },
  class:'g-btn btn-primary'
}

function theme_options(p,title,group=null) {
  g.loader()
  if (group=='fonts') {
    appEditMenu.loadGoogleFonts()
  }

  g.post("admin/themes?g_response=content", 'options='+p+'&group='+group,function(x){
    g.loader(false)
    g.modal({title:title,body:x,buttons:'save_options',type:'modal'})
    app = new Vue({
      el: '#theme_options_form'
    })
    transformClassComponents()
  })
}

function addon_options(p,title,group=null) {
  g.loader()

  g.post("admin/packages/options?g_response=content", 'options='+p, function(x){
    g.loader(false)
    g.modal({title:title,body:x,buttons:'save_addonoptions',type:'modal'})
    app = new Vue({
      el: '#addon_options_form'
    })
    transformClassComponents()
  })
}

function website_settings() {
  g.loader()

  g.get("blocks/websettings?g_response=content", function(x){
    g.loader(false)
    g.modal({title: g.tr('Website settings',{es:'Ajustes del sitio'}),body:x,buttons:'save_settings',type:'modal'})
    app = new Vue({
      el: '#website_settings_form'
    })
    transformClassComponents()
  })
}

function edit_mainmenu() {
  g.loader()

  g.get("blocks/mainmenu?g_response=content&lang="+pageLanguage, function(x){
    g.loader(false)
    g.modal({title: g.tr('Menu',{es:'Menu'}),body:x,buttons:'save_mainmenu',type:'modal'})
    app = new Vue({
      el: '#mainmenu-form'
    })
    //transformClassComponents()
  })
}

function add_fonts() {
  html = '<div id="fonts_modal">'
  html += '<div style="position:absolute;top:0.7em;left:0.7em;">'
  html += '<span style="width:100px">{{list.length-5}} '+g.tr('Fonts added', {es:'Fuentes agregadas'})+'</span>'
  html += '<input v-model="search" placeholder="'+g.tr('Search', {es:'Buscar'})+'" style="margin-left:20px"></div>'
  html += '<div class="google-fonts">'
  for(i of appEditMenu.allgooglefonts) if(i){
    line2 = '<br>abcABC'
    if (i=='Dela Gothic One'||i=='Abril Fatface') line2=''
    html += '<div v-if="filter(\''+i+'\')" :class="{selected:included(\''+i+'\')}" style="font-family:\''+i+'\'" @click="select(\''+i+'\')">'+i+line2+'</div>'
  }
  html += '</div></div>'
  appEditMenu.loadGoogleFonts()
  g.modal({title: '',body:html,buttons:'',type:'modal'})
  addFonts = new Vue({
    el: '#fonts_modal',
    data: {
      list:googlefonts,
      search:''
    },
    methods:{
      update() {
        this.$forceUpdate()
      },
      included(f) {
        return this.list.includes(f)
      },
      filter(f) {
        if(f.includes && f.toLowerCase().includes(this.search.toLowerCase())) return true
        return false
      },
      select(f) {
        g.post('blocks/toggleFont', {font:f}, function(f){
          if ((ax = addFonts.list.indexOf(f)) !== -1) {
            addFonts.list.splice(ax, 1);
          } else {
            addFonts.list.push(f);
          }
          addFonts.update()
        })
      }
    }
  })

}

function block_design(id,type) {
  href='blocks/design?id='+id+"&type="+type;
  _type = type.toUpperCase().replace('_',' ');
  g.get(href, function(data) {
    buttons = 'update_widget'
    if (canCreatePrototype) {
      buttons += ' prototype_widget'
    }
    g.dialog({class:'lightscreen large',id:'widget-popup',title:g.tr('Block settings',{es:'Fondo'}),body:data,type:'modal',buttons:buttons})
    block_edit_open()
  });
};

let inlineTinies=g('.inline-tinymce').all
let inlineTexts=g('[data-inline]').all
let inlineTextValues=new Array(inlineTexts.length)
for(i=0; i<inlineTexts.length; i++) {
  inlineTextValues[i] = inlineTexts[i].innerHTML
}


document.addEventListener("keyup", function(e){
  args=[]
  for(i=0; i<inlineTexts.length; i++) if(inlineTextValues[i]!=inlineTexts[i].innerHTML){
    inlineTextValues[i]=inlineTexts[i].innerHTML
    key = inlineTexts[i].getAttribute('data-inline')
    args[key] = inlineTexts[i].innerHTML
  }
});



g.click('.block-add-btn', function(){
  if (this.hasAttribute('data-pos')) {
    pos = this.getAttribute('data-pos')
  } else {
    pos = this.parentNode.parentNode.getAttribute('data-pos')
  }
  blocks_app.openList();
  blocks_app.selected_pos = pos;
})

function getElementIndex (element) {
  return Array.from(element.parentNode.children).indexOf(element);
}


document.addEventListener('click', function(e){

  if (e.target.classList.contains('selected-component')==false &&
    g(e.target).findUp('.block-head').all.length!==0 &&
    g(e.target).findUp('.selected-component').all.length==0 &&
    g(e.target).findUp('#component_edit_btn').all.length==0 &&
    g(e.target).findUp('#component_toolbar').all.length==0) {
      if (e.target.style.position!='absolute' && !e.shiftKey && !e.ctrlKey) {
        appEditMenu.unsetSelectedComponent();
      }
  }

  if (g('.vc-chrome').all.length>0 &&
    g(e.target).findUp('.vc-chrome').all.length==0 &&
    e.target.classList.contains('color-c')==false) {
      g('.vc-chrome').all[0].parentNode.firstChild.click()
  }
  if (g('.vc-sketch').all.length>0 &&
    g(e.target).findUp('.vc-sketch').all.length==0 &&
    e.target.classList.contains('color-c')==false) {
      g('.vc-sketch').all[0].parentNode.firstChild.click()
  }

  if (e.target.href) {
    if (e.target.href.startsWith('javascript:')) return;
    if (e.target.classList && e.target.classList.contains('gallery-item')) {
      e.preventDefault();
      return false;
    }
    if (g(e.target).findUp('.text-block').all.length>0) {
      e.preventDefault();
      return false;
    }

    if (e.target.href.startsWith(base_url+'#')) return;
    if (e.target.href.startsWith(base_url+'blocks/display')) return;
    if (e.target.href.startsWith(base_url+'blocks/pageEditor')) return;
    x = g(e.target).findUp('.componentarea>div')
    if(typeof x.all[0]!='undefined') {
      if (e.target.classList.contains('component')==false) {
        x = prompt('URL', decodeURI(e.target.href))
        if (x!==null) e.target.href = x
      }
      e.preventDefault();
      return false
    }
  }
  if (e.target.dataset.c && e.target.dataset.c=='admin') return;
  if (e.target.target && e.target.target=='_blank') return;

  if (e.target.parentNode && e.target.parentNode.href) {
    if (e.target.parentNode.href.startsWith(base_url+'blocks/display')) return;
    if (e.target.parentNode.href.startsWith(base_url+'blocks/pageEditor')) return;
  }

  if (e.target.classList.contains('ge-eo')) {
    c = g(e.target).findUp('.component').all[0]
    if (typeof c!=='undefined') {// && c.classList.contains('selected-component')==false) {
      appEditMenu.setSelectedComponent(e.target)
    }
  }

  updateComponentToobarEditorPosition()
  appEditMenu.updateTableToolbarPosition()


  if (g(e.target).findUp('#widget_options_form').all.length==0
    && g(e.target).findUp('#gila-popup').all.length==0
    && g(e.target).findUp('#topEditMenu').all.length==0
    && g(e.target).findUp('#component_sidebar').all.length==0
    && g(e.target).findUp('#media_dialog').all.length==0) {
    if (e.target.href && e.target.parentNode && e.target.parentNode.tagName=='P') {
      alert(g.tr('Links dont work in editing', {es:'Enlaces no sirven en edición'}))
    }
    e.preventDefault();
    return false;
  }

});


appEditMenu = new Vue({
  el:"#editMenu",
  data: {
    draft: isDraft,
    content: contentTable,
    emailSupport: emailSupport,
    googlefonts: googlefonts,
    allgooglefonts: allgooglefonts,
    pairFonts: [
      ['Open Sans', 'Open Sans'],
      ['Lobster', 'Baumans'],
      ['Montserrat', 'Raleway'],
      ['Pacifico', 'Josefin Slab'],
      ['Josefin Slab', 'Oswald'],
      ['Oswald', 'Montserrat'],
      ['Quicksand', 'Noto Sans'],
      ['Russo One', 'Ubuntu'],
      ['Dosis', 'Josefin Slab'],
      ['Lobster', 'Jura'],
      ['Indie flower','Oswald']
    ],
    colorPalettes: [
      ['#EF2D56', '#EF2D56', '#2FBF71', '#333333', '#F8F8F8'],
      ['#668F80', '#A0AF84', '#2D3047', '#333333', '#F8F8F8'],
      ['#EFA8B8', '#E26D5A', '#A0AF84', '#333333', '#F8F8F8'],
      ['#E26D5A', '#333333', '#303030', '#333333', '#F8F8F8'],
      ["#971206", "#D89122", "#AE6903", "#333333", "#F8F8F8"],
      ['#011428', '#08A4BD', '#4C212A', '#333333', '#F8F8F8'],
      ['#FFA400', '#009FFD', '#2A2A72', '#232528', '#F8F8F8'],
      ["#3E8211", "#B9854B", "#201500", "#333333", "#F8F8F8"],
      ["#777CB4", "#4C8B42", "#A02758", "#333333", "#EFFFF3"],
      ["#B93009", "#A95F1B", "#B93009", "#333333", "#EFE5D3"],
      ["#78526C", "#32446C", "#282828", "#333333", "#EFE5D3"],
      ["#3880A5", "#9DB9BD", "#6D4A36", "#333333", "#F8F8F8"],
      ["#88AFCA", "#A3B79D", "#425060", "#333333", "#F8F8F8"],
      ["#2B6F75", "#013D45", "#2B6F75", "#333333", "#F8F8F8"],
      ["#971206", "#FFDF7E", "#151515", "#333333", "#F8F8F8"],
    ],
    spellcheck: useSpellcheck,
    columnGrids: ['','2c','3c','3c1c','4c','4c2c','bricks','1f','f1','first-fluid'],
    editSidebar: '',
    selectedFonts: selectedFonts,
    selectedColors: selectedColors,
    load: false,
    edit:true,
    rootVar: rootVar,
    colorPalette: null,
    pairFont: null,
    pageId: pageId,
    pageTitle: pageTitle,
    pageSlug: pageSlug,
    pagePublic: pagePublic,
    pages: [],
    dummy_components: [],
    dummy_groups: dummyGroups,
    attr_groups: attrGroups,
    upload_components: [],
    componentImages: [],
    componentFields: [],
    updated: Date.now(),
    themeId: themeId,
    previewedTheme: null,
    previewedLayout: null,
    tableBody: null,
    componentTag: null,
    componentEditor: false,
    component: null,
    componentWithEditor: null,
    selectedComponent: null,
    styleFontSize:'12px',
    colorPicker:'',
    colorPicker2:'',
    styleFontFamily:'',
    paletteList: ['var(--p1color)','var(--p2color)','var(--p3color)','var(--p4color)','var(--p5color)'],
    listStyleTypes: ['disc','square','circle','armenian','cjk-ideographic','decimal','decimal-leading-zero','georgian','hebrew','hiragana','hiragana-iroha','katakana','katakana-iroha','lower-alpha','lower-greek','lower-latin','lower-roman','upper-alpha','upper-greek','upper-latin','upper-roman','none','inherit'],
    svgSize:0,
    minHeight:0,
    cStyle: {},
    cInput: [false,false,false,false],
    cTransform: {},
    searchIcon: '',
    searchClass: '',
    styleBackgroundImage:null,
    showDrafts:false,
    selectedList: [],
    blockData: [],
    bclasses: ['border', 'rounded', 'shadow', 'bg-primary', 'bg-dark', 'text-primary', 'text-white', 'text-md-right', 'text-md-left', 'm-auto','card', 'btn-sm', 'btn-lg'],
    view: 'lg', // select by screen size
    menuTypes: [],
    actionTypes: [],
    faIcons: [],
    elOptions: [],
    cclasses: [],
    selectedMenuType: null,
    selectedActionType: null,
    options: {
      createBlockOnDrop: false,
      setBlockHeightOnDrop: false,
      fixDecimals: true,
      dragLines: true,
      minAspectRatio: '3/1',
    },
    dragLine: {
      left:null,
      right:null,
      top:null,
      bottom:null,
    },
    dragLineRel: {
      left:null,
      right:null,
      top:null,
      bottom:null,
    }
  },
  components: {
    'color-picker': VueColor.Sketch//Chrome
  },
  methods: {
    canUseElOptions(elo) {
      if (elo.css && !this.emailSupport) return false
      return true
    },
    selectPairFont(pair) {
      this.rootVar.hfont = pair[0]
      this.rootVar.bfont = pair[1]
      this.pairFont = pair
      themePairFont.innerHTML = ':root{--hfont:'+pair[0]+';--bfont:'+pair[1]+'}'
    },
    savePairFont() {
      let fm=new FormData()
      pair = this.pairFont
      fm.append('option[heading-font]', pair[0])
      fm.append('option[body-font]', pair[1])
      this.pairFont = null
      g.ajax({url:'admin/themes?g_response=content&save_options='+this.themeId,method:'POST',data:fm,fn:function(x){
      }})
    },
    selectColorPalette(p) {
      this.rootVar.color1 = p[0]
      this.rootVar.color2 = p[1]
      this.rootVar.color3 = p[2]
      this.rootVar.color4 = p[3]
      this.colorPalette = p
      inner = ':root{--main-primary-color:'+p[0]+'!important;--main-a-color:'+p[1]+'!important;--hcolor:'+p[2]+'!important;--bs-heading-color:'+p[2]+'!important;--bcolor:'+p[3]+'!important;--body-bgcolor:'+p[4]+'!important}'
      themeColorPalette.innerHTML = inner
    },
    savePalette() {
      let fm=new FormData()
      p = this.colorPalette
      fm.append('option[primary-color]', p[0])
      fm.append('option[accent-color]', p[1])
      fm.append('option[heading-color]', p[2])
      fm.append('option[body-color]', p[3])
      fm.append('option[page-background-color]', p[4])
      this.colorPalette = null
      g.ajax({url:'admin/themes?g_response=content&save_options='+this.themeId,method:'POST',data:fm,fn:function(x){
        window.location.reload()
      }})
    },
    closeSideBar() {
      if (this.colorPalette!==null || this.pairFont!==null) {
        window.location.reload()
      }
      this.editSidebar=''
    },
    updateUploadedComponents(path='') {
      g.getJSON('blocks/getUploadedComponents?path='+path, function(data){
        appEditMenu.upload_components = data.items
        setTimeout(function(){
          setDummyComponents()
        }, 100)
      })
    },
    loadGoogleFonts() {
      for(i of this.allgooglefonts) if(i){
        i = i.replace(':', ':wght@')
        i = i.replace(' ', '+')
        g.loadCSS('https://fonts.googleapis.com/css2?family='+i)
      }
    },
    discardChanges(){
      g.loader()
      g.post('blocks/discard', 'id='+content_id, function(data) {
        g.loader(false)
        appEditMenu.draft = false;
        window.location.reload()
      });
    },
    revertChanges(){
      if (!this.draft) return
      g.loader()
      g.post('blocks/revert', 'id='+content_id, function(data) {
        g.loader(false)
        appEditMenu.draft = false;
        //window.location.reload()
        blocks_preview_reload(data)
      });
    },
    listChanges(){
      //if (!this.draft) return
      g.loader()
      g.get('blocks/history/'+content_id, function(data) {
        g.loader(false)
        g.dialog({title:g.tr('Saves', {es:'Guardados'}), class:'lightscreen',body:data,type:'modal'})
      });
    },
    saveChanges(){
      g.loader()
      g.post('blocks/save', 'id='+content_id, function(data) {
        g.loader(false)
        appEditMenu.draft = false;
        g.alert(g.tr('Saved',{es:'Guardado'}), "success");
        this.pagePublic=1
      });
    },
    toggleEdit(){
      g('.block-head>div:nth-child(1)').toggleClass('hide');
      g('.block-end>div:nth-child(1)').toggleClass('hide');
      if(this.edit===true) {
        this.edit=false
      } else {
        this.edit=true
      }
    },
    sbGlobalFonts()
    {
      this.editSidebar='typography';
      if (this.selectedFonts==0) g.post('blocks/selectedFonts', [], null);
      this.selectedFonts=1;
      this.loadGoogleFonts()
    },
    sbGlobalColors()
    {
      this.editSidebar='colors';
      if (this.selectedColors==0) g.post('blocks/selectedColors', [], null);
      this.colorPalette=null;
      this.selectedColors=1;
    },
    editPageData() {
      irow = this.pageId
      href='cm/edit_form/'+this.content+'?id='+irow+'&callback=g_page_popup_update';
      g.get(href,function(data){
        g.dialog({title:g.tr('Edit Registry'), class:'lightscreen large',body:data,type:'modal',buttons:'popup_update'})
        formId = '#page-edit-item-form'
        edit_popup_app = new Vue({
          el: formId,
          data: {id:irow}
        })
        transformClassComponents()
        g(formId+' input').all[1].focus()
      })
    },
    loader(x=true) {
      g.loader(x)
      this.load = x
    },
    deleteComponent() {
      block = g(this.selectedComponent).findUp('.block-head').first()
      pos1 = block.getAttribute('data-pos');
      componentarea1 = g(appEditMenu.selectedComponent).findUp('.componentarea').all[0];
      setComponentPadDisplay('none')
      this.closeInlineEditor()
      component_toolbar_relative = null
      this.selectedComponent.remove()
      this.selectedComponent = null
      blocksUpdateText(pos1, componentarea1)
    },
    duplicateComponent() {
      duplicant = this.selectedComponent.cloneNode(true)
      duplicant.removeAttribute('contenteditable')
      if (typeof this.selectedComponent.id!='undefined') {
        var _id = this.selectedComponent.id.split('_')
        if (_id.length==2 && !isNaN(_id[1])) {
          duplicant.id = _id[0]+'_'+(parseInt(_id[1])+1)
        } else if (_id[0]!=''&&_id[0]!=null) {
          duplicant.id = _id[0]+'_1'
        }
        console.log('New ID: ',duplicant.id)
      }
      parent = this.selectedComponent.parentNode;
      if (parent.lastChild == this.selectedComponent) {
          parent.appendChild(duplicant);
      } else {
        parent.insertBefore(duplicant, this.selectedComponent.nextSibling);
      }
      changeDuplicateIDs(parent)
      blocksUpdateFields()
      updateComponentsDrag()
    },
    resetTransform(t) {
      this.elResetTransform(this.selectedComponent, t)
      for(el of this.selectedList) {
        this.elResetTransform(el, t)
      }
      this.setEditableAttributes()
    },
    elResetTransform(el, t) {
      str = el.style.transform
      list = str.split(' ')
      for(i in list) if (list[i].includes(t)) {
        list[i] = ''
      }

      el.style.transform = list.join(' ')
      this.setEditableAttributes()
    },
    resetAttribute(attr) {
      this.selectedComponent.style.att=''
      for(el of this.selectedList) {
        el.style.attr=''
      }
      this.setEditableAttributes()
    },
    componentSelectedClass(classes) {
      if(this.selectedComponent.classList)
      for(c of classes) if(this.selectedComponent.classList.contains(c)) {
        return c
      }
      return '';
    },
    componentPromptBG() {
      x = prompt('URL', decodeURI(this.bgPlaceholder(this.selectedComponent.style.backgroundImage)))
      if (x!==null) this.componentSetStyle('backgroundImage', x)
    },
    componentSelectClass(i, classes) {
      if(this.selectedComponent.classList)
      for(c of classes) {
        this.selectedComponent.classList.remove(c)
      }
      this.selectedComponent.classList.add(i)
    },
    componentSetClass(cl, keep=[]) {
      for(k of keep) if(this.selectedComponent.classList.contains(k)) {
        cl = cl+' '+k
      }
      this.selectedComponent.setAttribute('class', cl)
    },
    resetComponent() {
      this.selectedComponent.style.removeProperty('text-align')
      this.selectedComponent.style.removeProperty('padding-right')
      this.selectedComponent.style.removeProperty('padding-left')
      this.selectedComponent.style.removeProperty('padding')
      this.selectedComponent.style.removeProperty('font-family')
      this.selectedComponent.style.removeProperty('text-transform')
      this.selectedComponent.style.removeProperty('font-size')
      this.selectedComponent.style.removeProperty('font-color')
      this.selectedComponent.style.removeProperty('line-height')
      this.selectedComponent.style.removeProperty('color')
      this.selectedComponent.style.removeProperty('background')
      this.selectedComponent.style.removeProperty('margin-right')
      this.selectedComponent.style.removeProperty('margin-left')
      this.selectedComponent.style.removeProperty('margin-top')
      this.selectedComponent.style.removeProperty('transform')
      this.selectedComponent.style.removeProperty('width')
      this.selectedComponent.style.removeProperty('height')
      this.selectedComponent.style.removeProperty('transform')
      this.selectedComponent.style.removeProperty('clip-path')
      if(this.selectedComponent.style.length==0) {
        this.selectedComponent.removeAttribute('style')
      }
      this.setEditableAttributes()
      blocksUpdateFields()
      updateComponentToolbarPosition()
    },
    moveComponentRight() {
      if (this.selectedComponent.nextElementSibling) {
        el = this.selectedComponent.nextElementSibling
        el.after(this.selectedComponent)
      }
      blocksUpdateFields()
    },
    moveComponentFirst() {
      this.selectedComponent.parentNode.prepend(this.selectedComponent)
      blocksUpdateFields()
    },
    componentIsEditable() {
      if (this.selectedComponent == null) return false
      c = this.selectedComponent
      if (c.tagName!='DIV'&&this.selectedComponent.textContent!='') return true
      if (c.classList.contains('ge-eo')) return true;
      if (c.hasAttribute('ie')) return true
      if (typeof c.childNodes!='undefined') {
        children = c.childNodes;
        if (children.length>0 && children[0].tagName=='LABEL') return true
      }
      return false
    },
    componentHasText() {
      if (['P','I','TABLE','H1','H2','H3','H4','H5','H6','UL','OL','BLOCKQUOTE','LABEL'].includes(this.selectedComponent.tagName)) return true
      if(typeof this.selectedComponent.firstChild!='undefined' && this.selectedComponent.firstChild!=null)
        if(['P','LABEL','H3','H4','H5'].includes(this.selectedComponent.firstChild.nodeName)) return true;
      return false
    },
    componentHas(x) {
      if (this.selectedComponent.tagName=='DIV' &&
      this.selectedComponent.parentNode.classList.contains('componentarea')) {
        if (['text-align', 'color', 'background-color', 'padding', 'margin', 'border-radius', 'min-height', 'rotate', 'clip-path','grid-column','grid-row'].includes(x)) return true
        return false
      }
      if(x=='text-align' && this.selectedComponent.tagName=='SPAN') return false;
      if(x=='gallery' && this.componentImages.length>0) {
        return true;
      }
      if (this.selectedComponent.hasAttribute('ed-'+x)) {
        return true
      }

      if (x=='grid-row') return true
      if (x=='grid-column') return true
      if (this.componentFields.includes(x)) return true
      if (this.componentFields.includes('nomore')) return false
      // to remove the rest //return false
      if (x=='text-align') {
        if (this.selectedComponent.tagName=='DIV') return true
        if (this.selectedComponent.tagName=='SPAN') return false
        return (this.componentIsEditable()&&this.selectedComponent.tagName!=='A')
      }
      if (x=='color') return this.componentIsEditable()||this.selectedComponent.tagName=='DIV';
      if (x=='background-color') return this.componentIsEditable()||this.selectedComponent.tagName=='DIV';
      if (x=='max-width') return (['IMG', 'P'].includes(this.selectedComponent.tagName))||this.selectedComponent.classList.contains('el-divider');
      if (this.selectedComponent.classList.contains('el-divider')||this.selectedComponent.classList.contains('el-spacer')) {
        return true
      }
      if (x=='rotate') return true
      if (x=='grid-row') return true
      if (x=='padding-x') return (['A', 'DIV'].includes(this.selectedComponent.tagName))
      if (x=='padding-y') return (['A', 'DIV'].includes(this.selectedComponent.tagName))
      if (x=='border-radius') return (['IMG','A', 'DIV'].includes(this.selectedComponent.tagName))
      if (x=='bgimage') return this.selectedComponent.tagName=='DIV'
    },
    contrastRatio(c1,c2) {
      if (c2.charAt(0)=='#') {
        b = hex2rgb(c2)
        if (b==null) b = hexa2rgb(c2)
      } else {
        sep = c2.indexOf(",") > -1 ? "," : " ";
        b = c2.substr(4).split(")")[0].split(sep);
        if (c2.charAt(3)=='a') b = c2.substr(5).split(")")[0].split(sep);
      }
      if (c1.charAt(0)=='#') {
        a = hex2rgb(c1)
        if (a==null) a = hexa2rgb(c1)
      } else {
        sep = c1.indexOf(",") > -1 ? "," : " ";
        a = c1.substr(4).split(")")[0].split(sep);
        if (c1.charAt(3)=='a') a = c1.substr(5).split(")")[0].split(sep);
      }
      if (a==null || b==null) return ''
      a = rgb2hsl(a[0],a[1],a[2])
      b = rgb2hsl(b[0],b[1],b[2])
      if(a[2]>b[2]) {
        l = (a[2]+0.05)/(b[2]+0.05)
      } else {
        l = (b[2]+0.05)/(a[2]+0.05)
      }
      return l+':1'
    },
    closeInlineEditor() {
      if (this.componentWithEditor!==null) {
        this.componentWithEditor.removeAttribute('contenteditable');
        this.componentWithEditor.removeAttribute('spellcheck');
        this.componentWithEditor.removeEventListener('keydown', this.keydown, false);
        this.componentWithEditor = null
        window.getSelection().removeAllRanges()
      }
      this.componentEditor = false
    },
    openInlineEditor() {
      if (component_toolbar_relative===null) return
      if (this.componentIsEditable()) {
        this.componentEditor = true
        this.componentWithEditor = this.selectedComponent
        if(this.selectedComponent.contenteditable!=true) {
          this.selectedComponent.setAttribute('contenteditable', true);
        }
        if (this.spellcheck==false) {
          this.selectedComponent.setAttribute('spellcheck', false);
        }
        this.selectedComponent.addEventListener('keydown', this.keydown, false);
      }
    },
    unsetNode(){
      if(!this.onEditor()) return
      x = ['B','STRONG','ABBR','I','U','DEL','INS','SUB','SUB','SUP','MARK','CODE','BLOCKQUOTE','A']

      if (!x.includes(this.sel.anchorNode.parentNode.nodeName)) return
      var el = this.sel.anchorNode.parentNode
      var parent = el.parentNode;
      while( el.firstChild ) {
        parent.insertBefore(  el.firstChild, el );
      }
      parent.removeChild(el);
    },
    setNode(x,html='',obj=null) {
      if(!this.onEditor()) return
      if(this.sel.anchorNode.parentNode.nodeName==x) {
        if (x=='A') {
          el = this.sel.anchorNode.parentNode
          if(obj) for(attr in obj) el[attr] = obj[attr]
        } else {
          this.unsetNode()
        }
        return
      }

      if(this.sel=='') {
        if(html=='') {
          return
        }
        this.insertNode(x,html,true,obj)
        return
      }

      var el = document.createElement(x);
      el.innerHTML = this.sel;
      this.range.deleteContents();
      this.insert(el)
      if(obj) for(attr in obj) el[attr] = obj[attr]
    },
    promptAttr(txt, attr='') {
      v = ''
      if (attr=='href' && this.sel
      && this.sel.anchorNode
      && this.sel.anchorNode.parentNode.nodeName=='A') {
        console.log(this.sel.anchorNode.parentNode.href)
        v = this.sel.anchorNode.parentNode.href
      }
      x = prompt(txt, v)
      if (x==null||x=='') return  v
      return x
    },
    hasStyle(a) {
      if (this.selectedComponent.style[a] && this.selectedComponent.style[a]!='none') {
        return true
      }
      a = '-webkit-'+a
      if (this.selectedComponent.style[a] && this.selectedComponent.style[a]!='none') {
        return true
      }
      return false
    },
    onEditor() {
      el = this.componentWithEditor
      if (window.getSelection) {
        this.sel = window.getSelection();

        if (this.sel.rangeCount > 0 && this.sel.getRangeAt) {
          for (var i = 0; i < this.sel.rangeCount; ++i) {
            if (!this.isOrContains(this.sel.getRangeAt(i).commonAncestorContainer, el)) {
              return false;
            }
          }

          this.range = this.sel.getRangeAt(0);
          this.node2edit = false
          // clean up script,p and div tag
          all = el.getElementsByTagName("DIV");
          for (let i=all.length-1, min=-1; i>min; i--) {
            anchor = all[i]
            if(typeof anchor!=='undefined' && anchor!=el &&
                typeof anchor.parentNode!=='undefined' && anchor.parentNode!=el.parentNode) {
              while(anchor.firstChild) {
                anchor.parentNode.insertBefore(anchor.firstChild, anchor);
              }
              anchor.parentNode.removeChild(anchor);
            }
          }

          return true;
        }
      }

      return false;
    },
    isOrContains(node, container) {
      while (node) {
        if (node === container) return true;
        node = node.parentNode;
      }
      return false;
    },
    keydown(event) {
      if(!this.onEditor()) return
      if(event.keyCode==13) {
        event.preventDefault()
        parentNode = this.sel.anchorNode.parentNode
        nodeName = this.sel.anchorNode.parentNode.nodeName;
        html = this.sel.anchorNode.parentNode.innerHTML
        if(['OL','UL','PRE','CODE','A','B','I','BLOCKQUOTE'].includes(nodeName)) return;
        if(['LI','P'].includes(nodeName)) {

          if (event.shiftKey) {
            document.execCommand('insertHTML', false, '<br>');
          } else {
            let range = new Range();
            elTxt = this.sel.anchorNode.parentNode.innerText;
            start = window.getSelection().anchorOffset
            end = elTxt.length
            txt = window.getSelection().toString()
            if (start<end) {
              range.setStart(this.sel.anchorNode, start+txt.length);
              range.setEnd(this.sel.anchorNode, end);
              window.getSelection().removeAllRanges();
              window.getSelection().addRange(range);
              txt = window.getSelection().toString()
            }

            sel = window.getSelection()
            if (txt.length==0) txt = ' ';
            el = document.createElement(nodeName);
            console.log(nodeName, this.sel.anchorNode.parentNode.tagName)
            el.innerHTML = txt;
            el.setAttribute('ie','');
            el.setAttribute('contenteditable',true);

            if (parentNode.parentNode.lastChild == parentNode) {
              parentNode.parentNode.appendChild(el)
            } else {
              parentNode.parentNode.insertBefore(el, parentNode.nextSibling);
            }
            
            range.deleteContents()
            this.unsetSelectedComponent()
            el.focus()
            range.selectNodeContents(el);
            range.collapse(true); // Collapse to the start
            sel.removeAllRanges();
            sel.addRange(range);

            pos1 = g(parentNode).findUp('.block-head').all[0].getAttribute('data-pos');
            componentarea1 = g(parentNode).findUp('.componentarea').all[0];
            blocksUpdateText(pos1, componentarea1)
            updateComponentsDrag()
            this.setSelectedComponent(el)
            this.closeInlineEditor()
            this.openInlineEditor()          
          }
        };
        event.preventDefault();
      }
      updateComponentToolbarPosition()
    },
    insertNode(x,html=' ',editable=true,obj=null) {
      if(!this.onEditor()) return
      var el = document.createElement(x);
      el.innerHTML = html;
      this.insert(el)
      if(obj) for(attr in obj) el[attr] = obj[attr]
      if(editable) {
        this.range.selectNodeContents(el)
      }
    },
    insertText(html) {
      if(!this.onEditor()) return
      var el = document.createTextNode(html);
      this.insert(el)
    },
    insert(el) {
      this.range.insertNode(el);
      this.range.setStartAfter(el)
      this.sel.removeAllRanges()
      this.sel.addRange(this.range)
    },
    bgPlaceholder(x) {
      if (typeof x=='undefined' || x=='none' || x==null || x=='') return 'assets/core/photo.png'
      return x.replace(/^url\(["']?/, '').replace(/["']?\)$/, '')
    },
    componentDisplay(x) {
      this.componentRemoveClass('d-md-none')
      this.componentRemoveClass('d-lg-none')
      this.componentRemoveClass('d-none')
      this.componentRemoveClass('d-md-block')
      this.componentRemoveClass('d-lg-block')
      this.componentRemoveClass('d-md-inline-block')
      this.componentRemoveClass('d-lg-inline-block')
      if (x=='xs') {
        this.componentAddClass('d-md-none')
      }
      if (x=='xs-md') {
        this.componentAddClass('d-lg-none')
      }
      if (x=='md-lg') {
        this.componentAddClass('d-none')
        this.componentAddClass('d-md-block')
      }
      if (x=='lg') {
        this.componentAddClass('d-none')
        this.componentAddClass('d-lg-block')
      }
    },
    componentGetDisplay(el=null) {
      if (el==null) {
        el = this.selectedComponent
      }
      if (el.classList.contains('d-md-none')) {
        return 'xs'
      }
      if (el.classList.contains('d-lg-none')) {
        return 'xs-md'
      }
      if (el.classList.contains('d-md-block')||el.classList.contains('d-md-inline-block')) {
        return 'md-lg'
      }
      if (el.classList.contains('d-lg-block')||el.classList.contains('d-lg-inline-block')) {
        return 'lg'
      }
      return ''
    },
    componentToggleProp(x) {
      this.selectedComponent.toggleAttribute(x)
      b = this.selectedComponent.hasAttribute(x)
      for(el of this.selectedList) {
        el.toggleAttribute(x, b)
      }
    },
    componentSetTag(x) {
      new_list = []
      for(el of this.selectedList) {
        new_list.push(this.elSetTag(el, x))
      }
      new_el = this.elSetTag(this.selectedComponent, x)
      this.setSelectedComponent(new_el)
      this.selectedList = new_list
      blocksUpdateFields()
      updateComponentToolbarPosition()
      updateComponentsDrag()
    },
    elSetTag(prv_el, x) {
      el = document.createElement(x);
      el.innerHTML = prv_el.innerHTML
      el.style.cssText = prv_el.cssText
      for (a of prv_el.attributes) {
        el.setAttribute(a.name, a.value);
      }
      el.setAttribute('contenteditable', true);
      if (this.spellcheck==false) {
        el.setAttribute('spellcheck', false);
      }
      el.classList.add('component')
      prv_el.replaceWith(el)
      return el
    },
    componentUpdateData(x) {
      if (x=='address') {
        this.selectedComponent.src = 'https://maps.google.com/maps?width=100%&height=500&hl=en&q='+this.selectedComponent.dataset.address+'&ie=UTF8&t=p&z=16&iwloc=B&output=embed'
      }
      if (x=='ihref') {
        this.componentUpdateDataHref()
      }
      if (x.startsWith('i_')) {
        this.componentUpdateChildProp(x.substring(2))
      }
      if (x.startsWith('is_')) {
        this.componentUpdateChildStyle(x.substring(3))
      }
      if (x.startsWith('ip_')) {
        this.componentUpdateChildParam(x.substring(3))
      }

      if (x=='video_url'||x=='video-url') {
        this.componentUpdateVideoURL()
      }
      blocksUpdateFields()
    },
    componentUpdateSrcAddress() {
      this.selectedComponent.src = 'https://maps.google.com/maps?width=100%&height=500&hl=en&q='+this.selectedComponent.dataset.address+'&ie=UTF8&t=p&z=16&iwloc=B&output=embed'
      blocksUpdateFields()
    },
    componentUpdateSource(x) {
      this.selectedComponent.firstChild.src = x
      blocksUpdateFields()
    },
    componentUpdateSVG() {
      x = this.selectedComponent.dataset.svg
      g.get(x, function(data){
        if (!data.startsWith('<svg')) {
          alert(g.tr('Not an svg file', {es:'No es un archivo svg'}))
          return
        }
        osvg = g(appEditMenu.selectedComponent).find('svg').all[0]
        _h = osvg.getAttribute('height')
        _w = osvg.getAttribute('width')
        appEditMenu.selectedComponent.innerHTML = data
        svg = g(appEditMenu.selectedComponent).find('svg').all[0]
        svg.setAttribute('height', _h)
        svg.setAttribute('width', _w)
        for(el of appEditMenu.selectedList) {
          el.innerHTML = data
          svg = g(el).find('svg').all[0]
          svg.setAttribute('height', _h)
          svg.setAttribute('width', _w)
        }
        blocksUpdateFields()
      })
    },
    componentUpdateImgSrc() {
      this.elUpdateImgSrc(this.selectedComponent)
      for(el of this.selectedList) {
        this.elUpdateImgSrc(el)
      }
    },
    elUpdateImgSrc(el) {
      if (el.tagName=='IMG') return
      img = g(el).find('svg').all[0]
      img.src = el.src
    },
    componentUpdateDataHref(attr=null) {
      x = this.selectedComponent.dataset.ihref??this.selectedComponent.dataset.href
      if (this.selectedComponent.tagName=='A') {
        this.selectedComponent.href = x
        return
      }
      el = this.selectedComponent.firstChild
      if (el.tagName=='A' || attr=='callback_url') {
        el.href = x
        if (x==''||typeof x==undefined) this.selectedComponent.innerHTML = el.innerHTML
      } else {
        this.selectedComponent.innerHTML = '<a href="'+x+'">'+this.selectedComponent.innerHTML+'</a>'
      }
    },
    componentUpdateChildProp(prop) {
      x = this.selectedComponent.dataset['i_'+prop]
      el = this.selectedComponent.firstChild
      el[prop] = x
    },
    componentUpdateChildStyle(attr) {
      x = this.selectedComponent.dataset['is_'+attr]
      el = this.selectedComponent.firstChild
      el.style[attr] = x
      console.log(attr,x)
    },
    componentUpdateChildParam(param) {
      x = this.selectedComponent.dataset['ip_'+param]
      el = this.selectedComponent.firstChild
      el.src = this.updateURLParameter(el.src, param, x)
    },
    updateURLParameter(url, param, paramVal){
      var newAdditionalURL = "";
      var tempArray = url.split("?");
      var baseURL = tempArray[0];
      var additionalURL = tempArray[1];
      var temp = "";
      if (additionalURL) {
          tempArray = additionalURL.split("&");
          for (var i=0; i<tempArray.length; i++){
              if(tempArray[i].split('=')[0] != param){
                  newAdditionalURL += temp + tempArray[i];
                  temp = "&";
              }
          }
      }
  
      var rows_txt = temp + "" + param + "=" + paramVal;
      return baseURL + "?" + newAdditionalURL + rows_txt;
    },
    componentUpdateVideoURL() {
      video_url = this.selectedComponent.dataset.video_url.trim()
      slash = video_url.lastIndexOf('/')
      video_url.substring(slash + 1)
      url = new URL(video_url);
      urlParams = new URLSearchParams(url.search.slice(1));
      if(video_url.includes('//vimeo')) {
        slash = video_url.indexOf('vimeo.com/')
        v = video_url.substring(slash+10)
        this.selectedComponent.src = 'https://player.vimeo.com/video/'+v+'?byline=0&portrait=0'
      } else if(video_url.includes('open.spotify')) {
        slash = video_url.indexOf('episode/')
        v = video_url.substring(slash+8).split('?')[0]
        this.selectedComponent.src = 'https://open.spotify.com/embed/episode/'+v+'?utm_source=generator'
      } else {
        let v = urlParams.get("v")
        if(v===null) v = 'videoseries?list='+urlParams.get("list")
        if(video_url.startsWith('https://youtu.be')) v = video_url.substring(slash+1)
        this.selectedComponent.src = 'https://www.youtube.com/embed/'+v
      }
      blocksUpdateFields()
    },
    componentUpdateVimeoURL() {
      url = new URL(this.selectedComponent.dataset.video_url);
      v = video_url.lastIndexOf('/')
      this.selectedComponent.src = 'https://www.youtube.com/embed/'+v+'?byline=0&portrait=0'
      blocksUpdateFields()
    },
    componentSetAlign(v) {
      this.componentSetStyle('text-align', v)
    },
    componentSet(attr, v) {
      this.selectedComponent[attr] = v
      blocksUpdateFields()
    },
    isComponent(c) {
      if (c.classList.contains('component')) return true
      if (c.hasAttribute('ie')) return true
      if (c.classList.contains('inline-edit')) return true
      return false
    },
    componentBreadcumb() {
      list = []
      x = this.selectedComponent
      while(!g(x.parentNode).hasClass('text-block')) {
        list.unshift(x)
        x = x.parentNode
      }
      x = this.selectedComponent
      while(typeof x.children!='undefined') {
        arr = Array.from(x.children)
        if (arr.length==0) {
          break
        } else if (arr.length==1) {
          x = arr[0]
          list.push(x)
        } else {
          list.push(Array.from(x.children))
          break
        }
      }
      return list
    },
    componentToggleClass(c) {
      if (!this.selectedComponent.classList.contains(c)) {
        this.componentAddClass(c)
      } else {
        this.componentRemoveClass(c)
      }
      this.$forceUpdate()
    },
    componentAddClass(c) {
      if (c=='d-md-block' && this.selectedComponent.style.display=='inline-block') {
        c='d-md-inline-block'
      }
      if (c=='d-lg-block' && this.selectedComponent.style.display=='inline-block') {
        c='d-lg-inline-block'
      }
      this.selectedComponent.classList.add(c)

      for(el of this.selectedList) {
        el.classList.add(c)
      }
    },
    componentRemoveClass(c) {
      this.selectedComponent.classList.remove(c)
      for(el of this.selectedList) {
        el.classList.remove(c)
      }
    },
    componentAddTransform(t, v) {
      this.elAddTransform(this.selectedComponent, t, v)
      for(el of this.selectedList) {
        this.elAddTransform(el, t, v)
      }
    },
    elAddTransform(el, t, v) {
      str = el.style.transform
      list = str.split(' ')
      for(i in list) if (list[i].includes(t)) {
        list[i] = t+'('+v+')'
        el.style.transform = list.join(' ')
        return
      }
      list.push(t+'('+v+')')
      el.style.transform = list.join(' ')
    },
    componentSetStyle(prop, v, unit='') {
      this.elSetStyle(this.selectedComponent, prop, v, unit)
      for(el of this.selectedList) {
        this.elSetStyle(el, prop, v, unit)
      }
      blocksUpdateFields()
      updateComponentToolbarPosition()
    },
    updateCStyle(prop) {
      j = this.selectedComponent.style[prop]
      this.cStyle[prop] = Math.round(j.replace(/[^0-9.]/g, ''))
    },
    visibleOption(opt,o,elo) {
      if (opt.tag && opt.tag!=this.selectedComponent.tagName) return false
      pdisplay = getComputedStyle(this.selectedComponent.parentNode).display
      //if (typeof this.selectedComponent.parentNode.style.display)
      if (opt.if=='grid' && pdisplay!='grid') return false;
      if (opt.if=='absolutePos' && this.absolutePos()==false) return false;
      if (opt.if=='isColumn') return this.isColumn();
      return (!opt.field||this.componentHas(opt.field)) && (!elo.basic||o<elo.basic||elo.show_advanced);
    },
    absolutePos() {
      return this.selectedComponent.style.position=='absolute'
    },
    isColumn() {
      return this.selectedComponent.parentNode.classList.contains('componentarea')
    },
    elSetStyle(el, prop, v, unit='') {
      //var rules = document.styleSheets[0].cssRules;
      if (v==null || v=='') {
        el.style[prop] = ''
        el.style.removeProperty(prop)
        this.cStyle[prop] = ''
      } else {
        el.style[prop] = v+unit
        this.cStyle[prop] = v
        if (prop=='backgroundColor') if (el.classList.contains('btn')) {
          el.style.setProperty('background-color', v, 'important');
        }
        if (prop=='borderColor') if (el.classList.contains('btn')) {
          el.style.setProperty('border-color', v, 'important');
        }
        if (['color','border-color','background-color'].includes(prop))
        if (el.classList.contains('btn')) {
          el.style.setProperty(prop, v, 'important');
        }
      }
      if(prop=='max-width'||prop=='maxWidth') if(['P','DIV'].includes(el.tagName)) {
        if(v=='100%'&&el.tagName=='P') {
          el.style.removeProperty('max-width')
          el.style.removeProperty('display')
        } else el.style.display = 'inline-block'
        if(v=='100%'&&el.tagName=='DIV') {
          el.style.removeProperty('max-width')
          el.style.removeProperty('margin')
        } else el.style.margin = 'auto'
      }
      if (prop=='animationName'||prop=='animation-name') if(!el.style.animationDuration){
        el.style.animationDuration = '2s'
        this.cStyle.animationDuration = 2
      }
      if(prop=='background-clip'||prop=='backgroundClip') {
        if (v=='text') {
          el.style.color='transparent'
          el.style.setProperty('-webkit-background-clip','text')
        } else {
          el.style.removeProperty('-webkit-background-clip')
        }
      }
      if(prop=='mask-image'||prop=='maskImage') {
        if(v=='') {
          el.style.removeProperty('-webkit-mask-image')
          el.style.removeProperty('-webkit-mask-size')
          el.style.removeProperty('mask-size')
          el.style.removeProperty('-webkit-mask-repeat')
          el.style.removeProperty('mask-repeat')
        } else {
          el.style.setProperty('mask-size','contain')
          el.style.setProperty('mask-repeat','no-repeat')
        }
      }
      if((prop=='min-height'||prop=='minHeight')&&v=='0px') {
        el.style.removeProperty('min-height')
      }
      if(prop=='position'&&v=='absolute') {
        this.setSizePer(el)
      }
      if((prop=='height'||prop=='width') && el.childNodes) {
        cel = el.childNodes[0]
        if (typeof cel.src=='undefined') return
        query = ''
        if (cel.src.includes('?')) {
          query = cel.src.split('?')[1]
        }        
        var queryParams = new URLSearchParams(query);
        queryParams.set(prop,v.replace(/[^0-9$.,]/g, ''));
        cel.src = cel.src.split('?')[0]+'?'+queryParams.toString()
      }
    },
    setSizePer(el) {
      rect = g(el).findUp('[style*="position:relative"]').all[0].getBoundingClientRect()
      elrect = el.getBoundingClientRect()
      el.style.width = (100*(elrect.width/rect.width)).toFixed(1).toString()+'%'
      el.style.height = (100*(elrect.height/rect.height)).toFixed(1).toString()+'%'
    },
    componentUpdateSVGSize() {
      this.elUpdateSVGSize(this.selectedComponent)
      for(el of this.selectedList) {
        this.elUpdateSVGSize(el)
      }
    },
    elUpdateSVGSize(el) {
      svg = g(el).find('svg').all[0]
      svg.setAttribute('width', this.svgSize)
      svg.setAttribute('height', this.svgSize)
      updateComponentToolbarPosition()
    },
    editColumn() {
      if (this.componentArea == null) {
        this.componentArea = g(this.selectedComponent).findUp('.text-block>div').all[0]
      }
      this.setSelectedComponent(this.componentArea)
      componentarea_editbtn.style.display = 'block'
      this.updateComponentAreaToobarPosition()
    },
    editComponentColumn() {
      this.componentArea = g(this.selectedComponent).findUp('.text-block>div').all[0]
      this.setSelectedComponent(this.componentArea)
      componentarea_editbtn.style.display = 'block'
      this.$forceUpdate()
      this.updateComponentAreaToobarPosition()
    },
    updateComponentAreaToobarPosition() {
      if (this.componentArea == null) {
        componentarea_editbtn.style.display = 'none'
        return
      }
      if (block_toolbar_relative == null && this.componentArea!=this.selectedComponent) {
        componentarea_editbtn.style.display = 'none'
        return
      }
      if (this.componentArea.parentNode && this.componentArea.parentNode.dataset.hg==1) {
        componentarea_editbtn.style.display = 'none'
        return
      }
      rect = this.componentArea.getBoundingClientRect()
      componentarea_editbtn.style.left = (rect.x??rect.left)+'px' //+Math.floor(rect.width/3)
      componentarea_editbtn.style.top = (rect.y??rect.top)-6+'px'
      document.querySelector('#componentarea_editbtn>.frame').style.width = rect.width+'px'
      document.querySelector('#componentarea_editbtn>.frame').style.height = rect.height+'px'
    },
    removeColumn() {
      if (this.componentArea.parentNode.childElementCount<2) {
        g.alert(g.tr('Cannot remove the only column', {es:'No puedes eliminar la uniqua columna'}))
        return
      }
      setComponentPadDisplay('none')
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      this.componentArea.remove()
      blocksUpdateText(pos1, componentarea1)
    },
    removeTableRow() {
      if (this.tableBody.childElementCount<2) return
      this.tableBody.lastElementChild.remove()
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      blocksUpdateText(pos1, componentarea1)
      updateComponentToolbarPosition()
    },
    removeTableColumn() {
      for (let i = 0; i < this.tableBody.children.length; i++) {
        if (this.tableBody.children[i].childElementCount<2) return
        this.tableBody.children[i].lastElementChild.remove()
      }
      thead = this.tableBody.parentNode.firstChild
      if (thead.tagName=='THEAD') for (let i = 0; i < thead.children.length; i++) {
        if (thead.children[i].childElementCount<2) return
        thead.children[i].lastElementChild.remove()
      }
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      blocksUpdateText(pos1, componentarea1)
      updateComponentToolbarPosition()
    },
    addColumn(src=null) {
      if (src!=null) {
        el = src.cloneNode(true)
        this.componentArea.after(el)
      } else {
        el = document.createElement('DIV');
        this.componentArea.parentNode.append(el)
      }
      this.unsetSelectedComponent()
      updateComponentsDrag()
      previousHTML = null
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      blocksUpdateText(pos1, componentarea1)
    },
    cloneColumn() {
      if (this.componentArea==null) console.error('No column selected')
      this.addColumn(this.componentArea)
    },
    addTableRow() {
      el = this.tableBody.lastChild.cloneNode(true)
      this.tableBody.append(el)
      updateComponentsDrag()
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      blocksUpdateText(pos1, componentarea1)
      updateComponentToolbarPosition()
    },
    addTableColumn() {
      for (let i = 0; i < this.tableBody.children.length; i++) {
        el = this.tableBody.children[i].lastChild.cloneNode(true)
        this.tableBody.children[i].append(el)
      }
      thead = this.tableBody.parentNode.firstChild
      if (thead.tagName=='THEAD') for (let i = 0; i < thead.children.length; i++) {
        el = thead.children[i].lastChild.cloneNode(true)
        thead.children[i].append(el)
      }
      updateComponentsDrag()
      pos1 = g(this.componentArea).findUp('.block-head').all[0].getAttribute('data-pos');
      componentarea1 = g(this.componentArea).findUp('.componentarea').all[0];
      blocksUpdateText(pos1, componentarea1)
      updateComponentToolbarPosition()
    },
    editTextCode(field='text') {
      //if (this.componentArea == null) return
      if (block_toolbar_relative ==null) return
      pos = block_toolbar_relative.getAttribute('data-pos')
      type = block_toolbar_relative.getAttribute('data-type')
      block_edit(content_id+pos, type, field)
    },
    editTemplate() {
      if (this.componentArea == null) return
      blockHead = g(this.componentArea).findUp('.block-head')
      pos = blockHead.attr('data-pos')
      type = blockHead.attr('data-type')
      block_edit(content_id+pos, type, 'latte')
    },
    updateComponentImages() {
      this.componentImages = []
      if (this.selectedComponent!==null && ['DIV','A'].includes(this.selectedComponent.tagName)) {
        this.componentImages = g(this.selectedComponent).find('img').all
      }
    },
    addSelectedComponent(x) {
      if ((ax = this.selectedList.indexOf(x)) !== -1) {
        this.selectedList.splice(ax, 1);
      } else {
        this.selectedList.push(x);
      }
    },
    setSelectedComponent(x) {
      this.selectedList = []
      this.selectedMenuType = null
      this.selectedActionType = null
      this.closeBlockEdit()
      //setComponentPadDisplay('none')
      if (this.selectedComponent) {
        elClassListRemove(this.selectedComponent, 'selected-component')
      }
      this.selectedComponent = x
      this.componentFields = []
      for(i of this.dummy_components) if(this.selectedComponent.classList.contains('el-'+i.name)) {
        if (typeof i.fields!=='undefined') this.componentFields = i.fields;
      }
      this.updateComponentImages()
      this.cInput = [false,false,false,false]

      this.selectedComponent.classList.add('selected-component')
      this.setEditableAttributes()
      if(this.selectedComponent.tagName=='TABLE') {
        this.tableBody = g(this.selectedComponent).find('tbody').all[0]
        table_toolbar.style.display = 'flex'
        table_columnbar.style.display = 'flex'
      } else {
        table_toolbar.style.display = 'none'
        table_columnbar.style.display = 'none'
      }
      this.setComponentToolbarRelative(this.selectedComponent)
    },
    unsetSelectedComponent(x) {
      this.selectedList = []
      this.closeBlockEdit()
      setComponentPadDisplay('none')
      this.closeInlineEditor()
      if (this.selectedComponent!=null) {
        elClassListRemove(this.selectedComponent, 'selected-component')
      }
      this.tableBody = null
      this.selectedComponent = null
      component_toolbar_relative = null
      table_toolbar.style.display = 'none'
      table_columnbar.style.display = 'none'
      hideComponentsDragLines()
      setDummyComponents()
    },
    setEditableAttributes() {
      if(this.selectedComponent==null) return
      cattr = ['fontSize','borderRadius','borderWidth','lineHeight','letterSpacing','wordSpacing',
      'padding','height','width','maxHeight','maxWidth','minHeight','minWidth',
      'margin-top','margin-left','left','top',
      'animationDuration','animationDelay']
      defaultStyle = Object.assign({}, window.getComputedStyle(this.selectedComponent))
      computed = window.getComputedStyle(this.selectedComponent)
      for (i in computed) {
        if (cattr.indexOf(i)>-1) {
          this.cStyle[i] = Math.round(computed[i].replace(/[^0-9.]/g, ''))
        } else {
          this.cStyle[i] = computed[i]
        }
      }

      //this.styleRotate = computed.transform.replace(/[^0-9.]/g, '')
      trns = computed.transform.split(' ')
      ctrns = ['rotate','scale']
      this.cTransform = {}
      for (i in ctrns) for (j in trns) if (j.includes(ctrns[i])) {
        this.cTransform[ctrns[i]] = Math.round(j.replace(/[^0-9.]/g, ''))
      }

      this.svgSize = 20
      svg = g(this.selectedComponent).find('svg').all
      if (svg.length>0) {
        // bbox = this.selectedComponent.firstChild.getBBox()
        this.svgSize = svg[0].getAttribute('width')
      }


      if(computed.fontFamily.match(/"([^"]+)"/)) {
        this.cStyle['font-family'] = computed.fontFamily.match(/"([^"]+)"/)[1]
      } else this.cStyle['font-family'] = computed.fontFamily

      if (defaultStyle.styleHeight) {
        this.styleHeight = Math.round(defaultStyle.styleHeight.replace(/[^0-9.]/g, ''))
      } else this.styleHeight=300

    },
    imgSrc(src) {
      if(src.startsWith('http:') || src.startsWith('https:') || src.split('.').pop()=='svg') {
        return src;
      }
      return 'lzld/thumb?media_thumb=120&src='+src;
    },
    canMarginRight() {
      if (!component_toolbar_relative) return false
      x = ['P','H1','H2','H3','H4','H5','UL','OL','BLOCKQUOTE'];
      if (x.includes(component_toolbar_relative.tagName)) return true
      return false
    },
    canMargin() {
      return false
      if (!component_toolbar_relative) return false
      if (component_toolbar_relative.classList.contains('ge-eo')) return false
      x = ['P','H1','H2','H3','H4','H5','IMG','A','UL','OL','BLOCKQUOTE','DIV'];
      if (x.includes(component_toolbar_relative.tagName)) return true
      return false
    },
    blockEdit2() {
      if (block_toolbar_relative==null) return
      this.unsetSelectedComponent()
      pos = block_toolbar_relative.getAttribute('data-pos')
      type = block_toolbar_relative.getAttribute('data-type')
      block_toolbar_relative.classList.add('selected')
      href='blocks/blockData?id='+content_id+pos+"&type="+type;
      this.editSidebar = 'block'
      this.componentArea = null
      componentarea_editbtn.style.display = 'none'
      g.loader()
      g.getJSON(href, function(data) {
        g.loader(false)
        //if (_type=='TEXT') buttons = 'update_widget src_code'
        for (i in data.fields) if (typeof data.data[i]=='undefined') {
          data.data[i] = ''
        }
        appEditMenu.blockData = data
        appEditMenu.$forceUpdate()
      });
    },
    closeBlockEdit() {
      if (block_toolbar_relative!=null) {
        block_toolbar_relative.classList.remove('selected')
      }
      this.editSidebar = ''
    },
    updateBlockField(key, v) {
      this.blockData.data[key] = v
      _pos = block_toolbar_relative.getAttribute('data-pos')
      object = {
        id: content_id+pos,
        fields: JSON.stringify([{
          name: key, value: v, pos: parseInt(_pos),
        }])
      }
      data = new URLSearchParams(object).toString();
      g.post('blocks/updateFields', data, function(data) {
        g.loader(false)
        blocks_preview_reload(data)
        block_toolbar_relative = g('.block-head[data-pos="'+_pos+'"]').all[0]
        block_toolbar_relative.classList.add('selected')
        appEditMenu.draft = true
        setDummyComponents()
        if (object.name!='text') setTimeout(function() {
          var load_event = new Event("load"); 
          window.dispatchEvent(load_event);
        }, 200)
        //appEditMenu.$forceUpdate()
      }, function(data) {
        data = JSON.parse(data)
        g.alert(data.error, 'error')
        setDummyComponents()
      })

      if (key=='hide-grid' && v==1) {
        componentarea_editbtn.style.display = 'none'
      }
    },
    blockEdit() {
      if (block_toolbar_relative==null) return
      pos = block_toolbar_relative.getAttribute('data-pos')
      type = block_toolbar_relative.getAttribute('data-type')
      block_edit(content_id+pos, type)    
    },
    blockSettings() {
      if (block_toolbar_relative==null) return
      pos = block_toolbar_relative.getAttribute('data-pos')
      type = block_toolbar_relative.getAttribute('data-type')
      block_design(content_id+pos, type)    
    },
    blockSwap() {
      _block = block_toolbar_relative
      pos = _block.getAttribute('data-pos')-1
      id = content_id+(pos+1)
      appEditMenu.loader()
      g.post('blocks/pos', 'id='+id+'&pos='+pos, function(data) {
        appEditMenu.loader(false)
        if (_block.previousSibling) {
          _block.setAttribute('data-pos', _block.previousSibling.getAttribute('data-pos'))
          _block.previousSibling.setAttribute('data-pos', pos+1);
          _block.parentNode.insertBefore(_block, _block.previousSibling);
        }
        appEditMenu.draft = true
        _block.scrollIntoView(); 
      });
    },
    blockClone() {
      _block = block_toolbar_relative
      pos = _block.getAttribute('data-pos')
      id = content_id+pos
      appEditMenu.loader()
      g.post('blocks/clone', 'id='+id, function(data) {
        appEditMenu.loader(false)
        blocks_preview_reload(data)
        appEditMenu.draft = true
      });
    },
    blockDelete() {
      if(confirm(g.tr('Delete this block?'))==false) return
      _block = block_toolbar_relative
      pos = _block.getAttribute('data-pos')
      id = content_id+pos
      appEditMenu.loader()
      g.post('blocks/delete', 'id='+id, function(data) {
        block_toolbar.style.display = 'none'
        appEditMenu.loader(false)
        blocks_preview_reload(data)
        appEditMenu.draft = true
      });
    },
    movableComponent() {
      if(this.selectedComponent.classList.contains('ge-eo')) return false
      return true
    },
    updateTableToolbarPosition() {
      if (this.tableBody == null) return
      rect = this.tableBody.parentNode.getBoundingClientRect()
      table_toolbar.style.left = (rect.x??rect.left)-20+rect.width/2+'px'
      table_toolbar.style.top = ((rect.y??rect.top)+rect.height)+15+'px'
      table_columnbar.style.left = (rect.x??rect.left)+rect.width-15+'px'
      table_columnbar.style.top = ((rect.y??rect.top)-20+rect.height/2)+15+'px'
    },
    setComponentToolbarRelative(target) {
      if (component_toolbar_relative == target) {
        updateComponentToolbarPosition()
        return;
      }
      component_toolbar_relative = target
      setComponentPadDisplay('block')
      appEditMenu.component = target
      appEditMenu.container = null
      updateComponentToolbarPosition()
    },
    getMenuTypes() {
      g.loader()
      g.getJSON('blocks/getMenuTypes', function(data) {
        g.loader(false)
        appEditMenu.menuTypes=data.items
      })
      this.selectedMenuType='page'
      this.$forceUpdate()
    },
    getActionTypes() {
      g.loader()
      g.getJSON('blocks/getActionTypes', function(data) {
        g.loader(false)
        appEditMenu.actionTypes=data.items
      })
      this.selectedActionType='webform'
      this.$forceUpdate()
    },
    getElementOptions() {
      g.loader()
      g.getJSON('blocks/getElementOptions', function(data) {
        g.loader(false)
        appEditMenu.elOptions=data.elOptions
        appEditMenu.faIcons=data.faIcons
        appEditMenu.bclasses=data.bclasses
        appEditMenu.cclasses=data.cclasses
        appEditMenu.dummy_components=data.elements
      })
    },
  }
})


var previousHTML = null;
var previousSelectedComponent = null;
setInterval(function(){ 
  if (appEditMenu.selectedComponent) {
    if (previousSelectedComponent!=appEditMenu.selectedComponent) {
      previousSelectedComponent = appEditMenu.selectedComponent
      previousHTML = appEditMenu.selectedComponent.outerHTML
    } else if(previousHTML!=null && previousHTML!=appEditMenu.selectedComponent.outerHTML) {
      previousHTML = appEditMenu.selectedComponent.outerHTML // avoid error from next call
      blocksUpdateFields()
    }
  }
}, 1500);

lastUpdateBlock = null
function blocksUpdateFields() {
  pos1 = g(appEditMenu.selectedComponent).findUp('.block-head').all[0].getAttribute('data-pos');
  componentarea1 = g(appEditMenu.selectedComponent).findUp('.componentarea').all[0];
  blocksUpdateText(pos1, componentarea1)
}

function blocksUpdateText(pos1, componentarea1) {
  //g.loader()
  removeComponentsDrag()
  updateComponentToolbarPosition()
  name1 = componentarea1.dataset.field;
  text1 = componentarea1.innerHTML;
  text1 = text1.replace('contenteditable=\"true\"', '')
  text1 = text1.replace('contenteditable="true"', '')
  text1 = text1.replace('spellcheck="false"', '')
  text1 = text1.replace('spellcheck=\"false\"', '')
  text1 = text1.replace('contenteditable=true', '')
  text1 = text1.replace('spellcheck=false', '')
  fieldsString = JSON.stringify([{pos:pos1,name:name1,value:text1}]);
  appEditMenu.updated = Date.now()

  g.ajax({url:'blocks/updateFields?g_response=content',method:'POST',
  data:{id:content_id, fields:fieldsString},
  fn:function(data){
    appEditMenu.draft = true
    appEditMenu.updateComponentImages()
    addComponentsDrag()
    setDummyComponents()
    if (appEditMenu.selectedComponent &&
      appEditMenu.selectedComponent.parentNode.classList.contains('componentarea')) {
      // columns div need to force update
      appEditMenu.$forceUpdate()
    }
  }})
}
function blocksUpdateField(el, field, value) {
  pos1 = g(el).findUp('.block-head').all[0].getAttribute('data-pos');
  fieldsString = JSON.stringify([{pos:pos1,name:field,value:value}]);

  g.ajax({url:'blocks/updateFields?g_response=content',method:'POST',
  data:{id:content_id, fields:fieldsString},
  fn:function(data){
    console.log('Case B')
    appEditMenu.draft = true
    setDummyComponents()
  }})
}


function g_page_popup_update() {
  form = g('.gila-popup form').last()
  data = new FormData(form);
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')
  for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].table.name == t) {
    _this = rootVueGTables[i]
  }

  url = 'cm/update_rows/'+t+'?id='+id
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    data = JSON.parse(data)
    pageFrame.src = editPageUrl+'?t=page&id='+appEditMenu.pageId
    appEditMenu.pageTitle = data.items[0].title
    appEditMenu.pageSlug = data.items[0].slug
    appEditMenu.pagePublic = data.items[0].publish
  }})


  g.closeModal();
}




// https://web.dev/drag-and-drop/
var component_toolbar_relative = null
var component_pad_update = null;
var block_pad_update = null;
var block_relative = null;
var block_toolbar_relative = null;
var component_padWidth  = 18;
var component_padLength  = 24;
var component_toolbarHeight = 28;

document.addEventListener('DOMContentLoaded', (event) => {


  function handleDragStart(e) {
    if (appEditMenu.selectedComponent==e.target && e.target.style.position!='absolute') {
      e.preventDefault();
      return
    }
    appEditMenu.unsetSelectedComponent()

    if (e.target.style.position=='absolute') {
      component_pad_update='move'
      appEditMenu.setSelectedComponent(e.target)
      appEditMenu.setComponentToolbarRelative(e.target)
      e.preventDefault();
      return
    }


    this.style.opacity = 0.8;
    dragSrcEl = this;

    blockHead = g(this).findUp('.block-head')

    if(this.classList.contains('dummy_component')) if(this.dataset.name) {
      component = null
      for(i in appEditMenu.dummy_components) if(appEditMenu.dummy_components[i].name == this.dataset.name) {
        component = appEditMenu.dummy_components[i]
      }
      if (component==null) return
      if (typeof component.tag!='undefined') {
        tag = component.tag
      } else {
        tag = 'GROUP'
      }
      element = document.createElement(tag);
      if(typeof component.html!='undefined') {
        element.innerHTML = component.html;
      }
      if(typeof component.outerHTML!='undefined') {
        element.outerHTML = component.outerHTML;
      }
      for(i of commonAttributes) {
        if(typeof component[i]!='undefined') {
          element[i] = component[i];
        }
      }
      if(typeof component.props!='undefined') for(i of component.props) {
        element.setAttribute(i, '');
      }
      if(typeof component.prop_value!='undefined') for(i in component.prop_value) {
        element.setAttribute(i, component.prop_value[i]);
      }
      if(typeof component.data!='undefined') {
        keys = Object.keys(component.data)
        for(i of keys) {
          element.dataset[i] = component.data[i];
        }
      }
      if(typeof component.class!='undefined') {
        classList = component.class.split(' ')
        for(c of classList) element.classList.add(c);
      }
      element.classList.add('el-'+component.name);
      element.classList.add('component')
      dragSrcEl = element;
    } else {
      if (typeof this.dataset.tag!='undefined') {
        tag = this.dataset.tag
      } else {
        tag = 'GROUP'
      }
      element = document.createElement(tag);
      if(typeof this.dataset.html!='undefined') {
        console.log(this.dataset.html)
        element.innerHTML = this.dataset.html;
      }
      if(typeof this.dataset.style!='undefined') {
        element.style = this.dataset.style;
      }
      for(i of [...commonAttributes, 'name']) {
        if(typeof this.dataset[i]!='undefined') {
          element[i] = this.dataset[i];
        }
      }
      for(i of ['address','video_url']) {
        if(typeof this.dataset[i]!='undefined') {
          element.dataset[i] = this.dataset[i];
        }
      }
      if(typeof this.dataset.class!='undefined') {
        classList = this.dataset.class.split(' ')
        for(c of classList) element.classList.add(c);
      }
      element.classList.add('component')
      dragSrcEl = element;
    }

    dragEndEl = dragSrcEl;

    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
    e.stopPropagation();
    setComponentPadDisplay('none')
  }


function handleDragEnd(e) {
  e.stopPropagation();
  if (e.preventDefault) {
    e.preventDefault();
  }
  this.style.opacity = 1;
  hideRectOver()
  if(!dragEndEl.parentNode) return

  if (dragEndEl !== this && dragEndEl.parentNode.classList.contains('componentarea')) {
    // drop inside a componentarea
    x = dragEndEl.appendChild(dragSrcEl)
    componentarea2 = dragEndEl.parentNode;
    console.log('Drop inside componentarea')
  } else if (dragEndEl !== this
    && g(dragEndEl).findUp('.componentarea>div').all[0]) {
    // drop inside a component or in it's child
    if (!dragEndEl.parentNode.classList.contains('el-container') && !dragEndEl.parentNode.parentNode.classList.contains('componentarea')) {
      dragEndEl = g(dragEndEl).findUp('.componentarea>div>.component').all[0]
    }
    x = dragEndEl.parentNode.insertBefore(dragSrcEl, dragEndEl)
    componentarea2 = g(dragEndEl).findUp('.componentarea').all[0];
    console.log('Drop inside component')
  } else if (appEditMenu.options.createBlockOnDrop &&
    dragEndEl !== this && dragEndEl.classList.contains('block-head') && dragEndEl.style.position!='absolute') {
    // drop inside a new block
    pos = parseInt(dragEndEl.dataset.pos)
    block_create(content_id, 'text', pos, dragSrcEl.outerHTML)
    console.log('New block')
  } else {
    dragSrcEl = null
    return
  }

  if (dragSrcEl.tagName=='GROUP'||(dragSrcEl.classList && dragSrcEl.classList.contains('el-group'))) {
    var parent = dragSrcEl.parentNode;
    while(dragSrcEl.firstChild) {
      if (typeof dragSrcEl.firstChild.classList!='undefined') {
        dragSrcEl.firstChild.classList.add('component')
      }
      parent.insertBefore(dragSrcEl.firstChild, dragSrcEl);
    }
    parent.removeChild(dragSrcEl);
  }

  updateComponentsDrag()
  // update both
  removeComponentsDrag()
  blockHead = g(dragEndEl).findUp('.block-head').all[0]
  if (typeof blockHead=='undefined') {
    console.error('No block parent found on drop')
    return
  }
  if (blockHead.dataset.pos) {
    pos2 = blockHead.getAttribute('data-pos');
  } else {
    dragSrcEl = null
    return
  }

  name2 = componentarea2.dataset.field;
  text2 = componentarea2.innerHTML;
  fieldsData = [{pos:pos2,name:name2,value:text2}];

  g.loader()
  blockHead = g(this).findUp('.block-head')
  if(typeof blockHead.all[0]!='undefined' && !blockHead.all[0].isSameNode(dragEndEl)) {
    pos1 = blockHead.all[0].getAttribute('data-pos');
    componentarea1 = g(this).findUp('.componentarea').all[0];
    name1 = componentarea1.dataset.field;
    text1 = componentarea1.innerHTML;
    fieldsData = [{pos:pos1,name:name1,value:text1},{pos:pos2,name:name2,value:text2}];
  }

  // set block height if element is sticker
  block = g(dragEndEl).findUp('.block-head').first()
  if (appEditMenu.options.setBlockHeightOnDrop) {
    if (dragSrcEl.style && dragSrcEl.style.position=='absolute') {
      // set minimum height for the block
      if (appEditMenu.options.minAspectRatio) {
        mar = appEditMenu.options.minAspectRatio
        if (block.style['aspect-ratio']=='' || block.style['aspect-ratio']==null) {
          block.style['aspect-ratio'] = mar
          fieldsData.push({pos:pos2,name:'aspect-ratio',value:mar})
        }
      } else if (block.style.height=='' || block.style.height==null) {
        block.style.height='30vw'
        fieldsData.push({pos:pos2,name:'height',value:'30vw'})
      }
    }
  }

  fieldsString = JSON.stringify(fieldsData);
  g.ajax({url:'blocks/updateFields?g_response=content',method:'POST',
  data:{id:content_id, fields:fieldsString},
  fn:function(data){
    addComponentsDrag()
    g.loader(false)
    appEditMenu.draft = true
    setDummyComponents()
    g.lazyLoad()
  }})

  dragSrcEl = null;

  return false;
}

function handleDragOverBlock(e) {
  block = g(e.target).findUp('.block-head').first()
  if (dragEndEl) return
  dragEndEl = block
  updateRectOver(null, dragEndEl)
  return false
}

function updateRectOver(el,over,overElement=1) {
  if (over==null || typeof over.getBoundingClientRect=='undefined') {
    return
  }
  width = '8px'
  rect = over.getBoundingClientRect()
  component_pad_over.style.backgroundColor = 'var(--select-color)'
  component_pad_over.style.display = 'block'
  component_pad_over.style.position = 'fixed'

  component_pad_over.style.left = rect.left+'px'
  component_pad_over.style.top = rect.top+'px'
  if (overElement==0) {
    component_pad_over.style.top = (rect.top+rect.height-10)+'px'
  }
  component_pad_over.style.height = width
  component_pad_over.style.width = rect.width+'px'

  if (el!=null) {
    computed = window.getComputedStyle(el)
    if (el.style.position=='absolute') {
      if (over.style.position!='relative') {
        over = g(over).findUp('[style*="position:relative"]').all[0]
        rect = over.getBoundingClientRect()
      }
      component_pad_over.style.height = rect.height+'px'
      component_pad_over.style.width = rect.width+'px'
      component_pad_over.style.backgroundColor = 'unset'
    } else if (computed.display=='inline-block' || computed.display=='inline') {
      if (overElement==0) {
        component_pad_over.style.left = (rect.left+rect.width)+'px'
        component_pad_over.style.top = rect.top+'px'
      }
      component_pad_over.style.height = rect.height+'px'
      component_pad_over.style.width = width
    }
  }

}
function hideRectOver() {
  component_pad_over.style.display = 'none'
}

function handleDragOver(e) {
  // drag over a component
  if (dragSrcEl == null) return false;

  dragEndEl = e.target
  overEl = 1
  rect = e.target.getBoundingClientRect()
  if (window.scrollY+rect.top+rect.height/2 < e.pageY) {
    if (e.target.nextSibling) {
      dragEndEl = e.target.nextSibling
    } else {
      overEl = 0
      dragEndEl = e.target.parentNode
    }
  }
  updateRectOver(dragSrcEl, dragEndEl, overEl)

  return false;
}

function handleDragEnter(e) {
  if (dragSrcEl == null) return false;
  dragEndEl = e.target
  updateRectOver(dragSrcEl, dragEndEl, 0)
}

function handleDragLeave(e) {
  x = g(e.target).findUp('.component')
  if (x == dragEndEl) {
    dragEndEl = null
  }
}


function handleOverBlock(e) {
  if (appEditMenu.editSidebar=='block') return
  el = e.target
  if(!el.classList || !el.classList.contains('block-head')) {
    x = g(el).findUp('.block-head').all
    if(x.length>0) {
      el = x[0]
    }
  }

  if (block_toolbar_relative!=el && appEditMenu.selectedComponent!=appEditMenu.componentArea) {
    componentarea_editbtn.style.display = 'none'
  }
  block_toolbar_relative = el
  block_toolbar.style.display = 'flex'
  updateBlockToobarPosition()
  appEditMenu.updateComponentAreaToobarPosition()

  if (appEditMenu.componentArea!=null) {
    g(appEditMenu.componentArea).findUp('.block-head').all
  }
}




function updateBlockToobarPosition() {
  if (block_toolbar_relative == null) {
    block_toolbar.style.display = 'none'
    return
  }
  rect = block_toolbar_relative.getBoundingClientRect()
  block_toolbar.style.top = (rect.y??rect.top)+4+'px'
}


function handleOverComponent(e) {
  if (e.target.tagName=='IFRAME'||e.target.tagName=='AUDIO') {
    component_toolbar_relative = e.target
    if (e.target.parentNode && e.target.parentNode.classList.contains('el-embed')) {
      component_toolbar_relative = e.target.parentNode
    }
    updateComponentToobarEditorPosition()
    component_edit_btn.style.display = 'block'
  }
  return;
}



function handleOverComponentArea(e) {
  if (appEditMenu.selectedComponent!=null && appEditMenu.selectedComponent == appEditMenu.componentArea) {
    return
  }
  componentarea_editbtn.style.display = 'block'
  appEditMenu.container = null
  appEditMenu.componentArea = e.target
  //appEditMenu.unsetSelectedComponent()
  appEditMenu.updateComponentAreaToobarPosition()
}
function handleLeaveComponentArea(e) {
  appEditMenu.updateComponentAreaToobarPosition()
}


function handleClickComponent(e) {
  c = e.target
  console.log(c.tagName)
  if (c.tagName=='LABEL') {
    e.preventDefault();
    if(c.tagName=='LABEL' && c.parentNode.tagName=='DIV') {
      c=c.parentNode
    } else if(c.tagName=='SELECT' && c.parentNode.tagName=='LABEL') {
      c=c.parentNode
    } else {
      return
    }
  }
  e.stopPropagation()
  // prevent input focus
  if (c.tagName=='SELECT'||c.parentNode.tagName=='SELECT') {
    e.preventDefault();
  }
  if (c.hasAttribute('data-action')||c.hasAttribute('data-href')) {
    e.preventDefault();
  }
  if (appEditMenu.selectedComponent == c) return


  if (!appEditMenu.isComponent(c)) {
      c = g(c).findUp('.component').all[0]
    if (typeof c=='undefined' || c==null) {
      return
    } else if (appEditMenu.selectedComponent == c) {
      if (!e.shiftKey && !e.ctrlKey) {
        appEditMenu.selectedList=[]
      }
      return
    }
  }
  if (e.shiftKey || e.ctrlKey) {
    appEditMenu.addSelectedComponent(c)
    updateComponentToolbarPosition()
    return
  }
  appEditMenu.setSelectedComponent(c)
  appEditMenu.componentArea = null
  appEditMenu.updateComponentAreaToobarPosition()
  appEditMenu.closeInlineEditor()
  appEditMenu.openInlineEditor()
  updateComponentToobarEditorPosition()
  appEditMenu.updateTableToolbarPosition()
}

function handleClickComponentArea(e) {

  c = e.target
  if (appEditMenu.selectedComponent == c) return
  if (appEditMenu.selectedComponent == appEditMenu.componentArea) {
    appEditMenu.componentArea = null
    appEditMenu.unsetSelectedComponent()
  }
}

document.body.addEventListener('paste', function(e){
  if (appEditMenu.selectedComponent!=e.target) return;
  e.preventDefault();
  const clipboardData = e.clipboardData || window.clipboardData;
  const html = clipboardData.getData("text/html");
  const plainText = clipboardData.getData("text/plain");
  if (e.shiftKey) {
    plainText = pasteHtml(e, html, plainText)
  } else {
    sanitizedContent = `${plainText.replace(/\n/g, "<br>")}`;
    insertHtmlAtCaret(e.target, sanitizedContent);
    //document.execCommand("insertHTML", false, plainText);
  }
}, false);
function pasteHtml(e, html, plainText) {
  let sanitizedContent;
  if (html) {
      // Sanitize or customize HTML content
      sanitizedContent = sanitizeHtml(html);
  } else if (plainText) {
      // If only plain text is available, wrap it in a <p> tag
      sanitizedContent = `${plainText.replace(/\n/g, "<br>")}`;
  }
  if (sanitizedContent) {
      insertHtmlAtCaret(e.target, sanitizedContent);
  }
}
function sanitizeHtml(html) {
  const tempDiv = document.createElement("div");
  tempDiv.innerHTML = html;
  // Example: Strip out <script> and <style> tags
  const scripts = tempDiv.querySelectorAll("script, style");
  scripts.forEach((script) => script.remove());
  // clean tags
  const elements = tempDiv.querySelectorAll('*');
  elements.forEach(el => {
    el.removeAttribute('style');
    el.removeAttribute('class');
  });
  // Allow only <p>, <b>, <i>, <u>, <a>, <br>
  const allowedTags = ["P", "B", "I", "U", "A", "BR", "UL", "OL", "LI", "STRONG", "H5", "H4", "H3", "H2", "H1", "SUB", "SUP", "DEL"];
  Array.from(tempDiv.querySelectorAll("*")).forEach((node) => {
      if (!allowedTags.includes(node.nodeName)) {
          node.replaceWith(...node.childNodes); // Replace node with its children
      }
      if (node.nodeName=='LI') {
        const paragraphs = li.querySelectorAll(':scope > p');
        paragraphs.forEach(p => {
          p.remove();
        });
      }
  });

  // Get all <li> elements and remove p from inside
  const listItems = document.querySelectorAll('li');
  listItems.forEach(li => {
      // Get all direct child <p> elements within the current <li>
      const paragraphs = li.querySelectorAll(':scope > p');
  
      // Iterate through and remove each <p>
      paragraphs.forEach(p => {
          p.remove(); // Removes the element from the DOM
      });
  });
  
  return tempDiv.innerHTML;
}

// Function to insert sanitized HTML at the caret position
function insertHtmlAtCaret(editableElement, html) {
  const selection = window.getSelection();
  const range = selection.getRangeAt(0);

  if (range) {
      const fragment = document.createDocumentFragment();
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      while (tempDiv.firstChild) {
          fragment.appendChild(tempDiv.firstChild);
      }

      range.deleteContents();
      range.insertNode(fragment);

      // Move the cursor after the inserted content
      selection.removeAllRanges();
      if (typeof fragment.lastChild.nodeType!='undefined' && fragment.lastChild.nodeType==1) {
          const newRange = document.createRange();
          newRange.setStartAfter(fragment.lastChild);
          selection.addRange(newRange);
      }
  } else {
      // If no range is found, append the HTML at the end
      editableElement.innerHTML += html;
  }
}

let cItems = document.querySelectorAll('.componentarea>div>*');
let icItems = [...document.querySelectorAll('.inline-edit'),...document.querySelectorAll('.componentarea *[ie]')];
let caItems = document.querySelectorAll('.componentarea>div');
//let textItems = document.querySelectorAll('.componentarea');
let blockItems = document.querySelectorAll('.block-head');
let dragSrcEl = null;
setDummyComponents = function() {
  let cComponents = document.querySelectorAll('.dummy_components>*');
  cComponents.forEach(function(item) {
    if (item.classList.contains('dummy_draggable')) return
    item.classList.add('dummy_draggable');
    item.draggable = true;
    item.addEventListener('dragstart', handleDragStart, false);
    item.addEventListener('dragend', handleDragEnd, false);
  });
}
setDummyComponents()

updateComponentsDrag = function() {
  removeComponentsDrag();
  cItems = document.querySelectorAll('.componentarea>div>*');
  caItems = document.querySelectorAll('.componentarea>div');
  blockItems = document.querySelectorAll('.block-head');
  icItems = [...document.querySelectorAll('.inline-edit'),...document.querySelectorAll('.componentarea *[ie]')];
  addComponentsDrag();
}




addComponentsDrag = function() {
  icItems.forEach(function(item) {
    item.classList.add('component');
    item.addEventListener('click', handleClickComponent, false);
    //item.addEventListener('paste', handlePasteComponent, false);
  });
  cItems.forEach(function(item) {
    if (g(item).findUp('.nocomponentarea').all.length>0) return;
    item.classList.add('component');
    item.draggable = true;
    item.addEventListener('dragstart', handleDragStart, false);
    item.addEventListener('dragover', handleDragOver, false);
    item.addEventListener('dragleave', handleDragLeave, false);
    item.addEventListener('dragend', handleDragEnd, false);
    item.addEventListener('mouseover', handleOverComponent, false);
    item.addEventListener('click', handleClickComponent, false);
    //item.addEventListener('paste', handlePasteComponent, false);
  });

  if (appEditMenu.selectedComponent) {
    appEditMenu.selectedComponent.classList.add('selected-component')
  }
  
  caItems.forEach(function(item) {
    item.addEventListener('dragenter', handleDragEnter, false);
    item.addEventListener('mouseenter', handleOverComponentArea, false);
    item.addEventListener('mouseleave', handleLeaveComponentArea, false);
    item.addEventListener('click', handleClickComponentArea, false);
  });
  
  blockItems.forEach(function(item) {
    item.addEventListener('mouseover', handleOverBlock, false);
    item.addEventListener('mouseenter', handleOverBlock, false);
    item.addEventListener('dragover', handleDragOverBlock, false);
  });
}

removeComponentsDrag = function() {
  icItems.forEach(function(item) {
    elClassListRemove(item, 'component')
    elClassListRemove(item, 'selected-component')
    item.removeEventListener('click', handleClickComponent, false);
    //item.removeEventListener('paste', handlePasteComponent, false);
  });
  cItems.forEach(function(item) {
    elClassListRemove(item, 'component')
    elClassListRemove(item, 'selected-component')
    item.removeAttribute('draggable');
    item.removeEventListener('dragstart', handleDragStart, false);
    item.removeEventListener('dragover', handleDragOver, false);
    item.removeEventListener('dragleave', handleDragLeave, false);
    item.removeEventListener('dragend', handleDragEnd, false);
    item.removeEventListener('mouseover', handleOverComponent, false);
    item.removeEventListener('click', handleClickComponent, false);
    //item.removeEventListener('paste', handlePasteComponent, false);
  });
  caItems.forEach(function(item) {
    item.removeEventListener('mouseenter', handleOverComponentArea, false);
  });
  blockItems.forEach(function(item) {
    item.removeEventListener('mouseover', handleOverBlock, false);
    item.removeEventListener('mouseenter', handleOverBlock, false);
    item.removeEventListener('dragover', handleDragOverBlock, false);
  });

}




addComponentsDrag()

document.addEventListener('scroll', function(e) {
  if (component_toolbar_relative !== null) {
    updateComponentToolbarPosition()
  }
  updateBlockToobarPosition()
  appEditMenu.updateComponentAreaToobarPosition()

})




document.addEventListener('keydown', (e)=>{
  if (g('gila-darkscreen').all.length>0) {
    return
  }
  if (e.key=='Escape') {
    appEditMenu.unsetSelectedComponent()
  }

  //if (appEditMenu.selectedComponent===null && e.ctrlKey && e.key === 'z' &&
  //g('gila-darkscreen').all.length==0) {
  //  appEditMenu.revertChanges()
  //}
  if (e.key=='Delete' && appEditMenu.selectedComponent) {
    if (e.target.nodeName == 'INPUT') return;
    if (e.target.nodeName == 'TEXTAREA') return;
    if (appEditMenu.selectedComponent.hasAttribute('contenteditable') && window.getSelection().anchorNode!==null) return;
    if (appEditMenu.selectedComponent.classList.contains('ge-eo')) return;
    if (g(appEditMenu.selectedComponent).findUp('[contenteditable]').all.length>0) return;
    appEditMenu.deleteComponent()
  }
})






var prevPageX = 0;
var prevPageY = 0;

document.addEventListener('mousemove', function(e) {
  diffX = e.pageX - prevPageX
  diffY = e.pageY - prevPageY
  if (component_toolbar_relative) {
    if (component_pad_update=='right') {
      if (!component_toolbar_relative.style.width) {
        component_toolbar_relative.style.width = "100%"
      }
      rect = component_toolbar_relative.parentNode.getBoundingClientRect()
      x = (parseFloat(component_toolbar_relative.style.width.replace('%',''))/100)*rect.width + diffX
      if(x<1) x=1
      if(x>rect.width) x=rect.width
      component_toolbar_relative.style.width = (100*(x/rect.width)).toFixed(1).toString()+'%'
    }
    if (component_pad_update=='top') {
      if (!component_toolbar_relative.style.marginTop) {
        component_toolbar_relative.style.marginTop = "0"
      }
      x = parseInt(component_toolbar_relative.style.marginTop.replace('px','')) + diffY
      if(x<-200) x=-200
      component_toolbar_relative.style.marginTop = x.toString()+'px'
    }
    if (component_pad_update=='move' && component_toolbar_relative!=null) {
      if (!component_toolbar_relative.style.left && !component_toolbar_relative.style.right) {
        component_toolbar_relative.style.left = "0"
      }
      if (!component_toolbar_relative.style.top && !component_toolbar_relative.style.bottom) {
        component_toolbar_relative.style.top = "0"
      }
      relD = g(component_toolbar_relative).findUp('[style*="position:relative"]').all[0];
      if (relD==null) {
        relD = g(component_toolbar_relative).findUp('.block-head').all[0];
      }
      rect = relD.getBoundingClientRect()
      if (!component_toolbar_relative.style.left) {
        x = parseFloat(component_toolbar_relative.style.right.replace('%','')) - diffX*100/rect.width
        component_toolbar_relative.style.right =  x.toFixed(1).toString()+'%'
      } else {
        x = parseFloat(component_toolbar_relative.style.left.replace('%','')) + diffX*100/rect.width
        component_toolbar_relative.style.left =  x.toFixed(1).toString()+'%'  
      }
      if (!component_toolbar_relative.style.top) {
        x = parseFloat(component_toolbar_relative.style.bottom.replace('%','')) - diffY*100/rect.height
        component_toolbar_relative.style.bottom =  x.toFixed(1).toString()+'%'
      } else {
        x = parseFloat(component_toolbar_relative.style.top.replace('%','')) + diffY*100/rect.height
        component_toolbar_relative.style.top =  x.toFixed(1).toString()+'%'
      }
    }
    if (component_pad_update=='resize' && component_toolbar_relative!=null) {
      if (!component_toolbar_relative.style.width.endsWith('%')
      ||!component_toolbar_relative.style.height.endsWith('%')) {
        appEditMenu.setSizePer(component_toolbar_relative)
        component_toolbar_relative.style.maxWidth=''
        component_toolbar_relative.style.maxHeight=''
      }
      relD = g(component_toolbar_relative).findUp('[style*="position:relative"]').all[0];
      if (relD==null) {
        relD = g(component_toolbar_relative).findUp('.block-head').all[0];
      }
      rect = relD.getBoundingClientRect()
      x = parseFloat(component_toolbar_relative.style.width.replace('%','')) + diffX*100/rect.width
      component_toolbar_relative.style.width = x.toString()+'%'
      x = parseFloat(component_toolbar_relative.style.height.replace('%','')) + diffY*100/rect.height
      component_toolbar_relative.style.height =  x.toString()+'%'
    }

    updateComponentToolbarPosition()
  }


  if (typeof block_relative=='undefined') return

  if (block_pad_update!==null && block_relative===null) {
    block_relative = g(e.target).findUp('.block-head').find('section').all[0]
    if (typeof block_relative=='undefined') {
      block_pad_update=null
      block_relative==null
      return
    }
  }

  if (block_pad_update=='top') {
    x = parseInt(block_relative.style.paddingTop.replace(/[^0-9]/g, '')) + diffY
    if (x>-1 && x<181) {
      block_relative.style.paddingTop = x+'px'
      updateComponentToolbarPosition()
      block_pad_relative.innerText = x+'px'
      if (typeof appEditMenu.blockData.data!='undefined') {
        appEditMenu.blockData.data['padding-top'] = x+'px'
      }
    }
  }
  if (block_pad_update=='bottom') {
    x = parseInt(block_relative.style.paddingBottom.replace(/[^0-9]/g, '')) + diffY
    if (x>-1 && x<181) {
      block_relative.style.paddingBottom = x+'px'
      updateComponentToolbarPosition()
      block_pad_relative.innerText = x+'px'
      if (typeof appEditMenu.blockData.data!='undefined') {
        appEditMenu.blockData.data['padding-bottom'] = x+'px'
      }
    }
  }

  prevPageX = e.pageX
  prevPageY = e.pageY
})


});





function updateComponentToobarEditorPosition() {
  if (appEditMenu.selectedComponent) {
    rect = appEditMenu.selectedComponent.getBoundingClientRect()
    component_toolbar.style.left = (rect.x??rect.left)+rect.width/2+'px'
    component_toolbar.style.top = ((rect.y??rect.top)-component_toolbarHeight/2-12)+'px'
  }
  if (component_toolbar_relative) {
    rect = component_toolbar_relative.getBoundingClientRect()
    component_edit_btn.style.left = (rect.x??rect.left)+'px'
    component_edit_btn.style.top = ((rect.y??rect.top)-component_toolbarHeight)+'px'
  }
}

selected_el_border = []
function updateComponentToolbarPosition() {
  updateComponentToobarEditorPosition()
  if (component_toolbar_relative == null) {
    setComponentPadDisplay('none')
    return
  }
  l = component_padLength
  rect = component_toolbar_relative.getBoundingClientRect()

  component_pad_top.style.top = ((rect.y??rect.top)-component_padWidth/2)+'px'
  component_pad_top.style.left = ((rect.x??rect.left)+rect.width/2-l/2)+'px'
  component_pad_top.style.height = component_padWidth+'px'
  component_pad_top.style.width = l+'px'
  component_pad_move.style.top = ((rect.y??rect.top)-4)+'px'
  component_pad_move.style.left = ((rect.x??rect.left)-4)+'px'
  component_pad_move.style.height = l+'px'
  component_pad_move.style.width = l+'px'
  component_pad_resize.style.top = ((rect.y??rect.top)+rect.height-8)+'px'
  component_pad_resize.style.left = ((rect.x??rect.left)+rect.width-8)+'px'
  component_pad_resize.style.height = l+'px'
  component_pad_resize.style.width = l+'px'

  if (appEditMenu.selectedComponent &&
    appEditMenu.selectedComponent.style.position=='absolute') {
    component_pad_move.style.display = 'block'
    component_pad_resize.style.display = 'block'
    showComponentDragLines(rect)
  } else {
    component_pad_move.style.display = 'none'
    component_pad_resize.style.display = 'none'
    appEditMenu.dragLine = {left:null,right:null,top:null,bottom:null}
    hideComponentsDragLines()
  }

  component_pad_border.style.left = (rect.x??rect.left)+'px'
  component_pad_border.style.top = (rect.y??rect.top)+'px'
  component_pad_border.style.width = rect.width+'px'
  component_pad_border.style.height = rect.height+'px'
  component_pad_border.style.transform = ''
  if (component_toolbar_relative.style.transform) {
    cel = component_toolbar_relative
    regexp = /rotate\((.*)turn\)/g;
    array = [...cel.style.transform.matchAll(regexp)];
    if (typeof array[0]!='undefined' && typeof array[0][1]!='undefined') {
      rot = parseFloat(array[0][1])
      if (rot!=0) {
        component_pad_border.style.top =(rect.top+(rect.height-cel.offsetHeight)/2)+'px'//((rect.x??rect.left)+mLeft)+'px'
        component_pad_border.style.left =(rect.left+(rect.width-cel.offsetWidth)/2)+'px'//((rect.x??rect.left)+mLeft)+'px'
        component_pad_border.style.width = cel.offsetWidth+'px'
        component_pad_border.style.height = cel.offsetHeight+'px'
        component_pad_border.style.transform = cel.style.transform
      }
    }
  }

  appEditMenu.updateTableToolbarPosition()

  for(i in appEditMenu.selectedList) {
    rect = appEditMenu.selectedList[i].getBoundingClientRect()
    selected_el_border[i] = {
      left: (rect.x??rect.left)+'px',
      top: (rect.y??rect.top)+'px',
      width: rect.width+'px',
      height: rect.height+'px'
    }
  }

  // refresh after 1 sec
  if (appEditMenu.selectedList.length>0) setTimeout(function () {
    if (document.activeElement && document.activeElement.tagName!='INPUT') {
      console.log(document.activeElement)
      //appEditMenu.$forceUpdate()
    }
  }, 1000)
  
}

function showComponentDragLines(rect) {
  parent = appEditMenu.selectedComponent.parentNode
  if (appEditMenu.dragLines==false) {
    appEditMenu.dragLine = {left:null,right:null,top:null,bottom:null}
    hideComponentsDragLines()
    return;
  }
  children = parent.childNodes;
  relative = g(appEditMenu.selectedComponent).findUp('[style*="position:relative"]').all[0]
  x = relative.getBoundingClientRect()
  relativeRect = relative.getBoundingClientRect()
  left = x.x; topPos = x.y; right = x.x+x.width; bottom = x.y+x.height;
  min_left = Math.abs(rect.x-left);
  min_right = Math.abs(rect.x+rect.width-right);
  min_top = Math.abs(rect.y-topPos);
  min_bottom = Math.abs(rect.y+rect.height-bottom);
  appEditMenu.dragLineRel = {left:left,right:right,top:topPos,bottom:bottom}

  for(var i=0;i<children.length;i++) if(appEditMenu.selectedComponent!=children[i]) {
    if (!children[i].getBoundingClientRect) continue;
    x = children[i].getBoundingClientRect()
    // left drag line
    _left = Math.abs(rect.x-x.x)
    if (_left < min_left) {
      min_left = _left
      left = x.x
    }
    // right drag line
    _right = Math.abs(rect.x+rect.width-x.x-x.width)
    if (_right < min_right) {
      min_right = _right
      right = x.x+x.width
    }
    // top drag line
    _top = Math.abs(rect.y-x.y)
    if (_top < min_top) {
      min_top = _top
      topPos = x.y
    }
    // bottom drag line
    _bottom = Math.abs(rect.y+rect.height-x.y-x.height)
    if (_bottom < min_bottom) {
      min_bottom = _bottom
      bottom = x.y+x.height
    }
  }
  // alternative lines
  for(i=0;i<children.length;i++) if(appEditMenu.selectedComponent!=children[i]) {
    if (!children[i].getBoundingClientRect) continue;
    x = children[i].getBoundingClientRect()
    _left = Math.abs(rect.x-x.x-x.width)
    if (_left < min_left && right>x.x+x.width) {
      min_left = _left
      left = x.x+x.width
    }
    _top = Math.abs(rect.y-x.y-x.height)
    if (_top < min_top && bottom>x.y+x.height) {
      min_top = _top
      topPos = x.y+x.height
    }
  }
  // alternative lines
  for(i=0;i<children.length;i++) if(appEditMenu.selectedComponent!=children[i]) {
    if (!children[i].getBoundingClientRect) continue;
    x = children[i].getBoundingClientRect()
    _right = Math.abs(rect.x+rect.width-x.x)
    if (_right < min_right && left<x.x) {
      min_right = _right
      right = x.x
    }
    _bottom = Math.abs(rect.y+rect.height-x.y)
    if (_bottom < min_bottom && topPos<x.y) {
      min_bottom = _bottom
      bottom = x.y
    }
  }


  appEditMenu.dragLine = {left:left,right:right,top:top,bottom:bottom}
  component_dl_left.style.display = 'block'
  component_dl_left.style.left = left+'px'
  component_dl_left.style.top = relativeRect.top+'px'
  component_dl_left.style.height = relativeRect.height+'px'
  component_dl_left.style.width = 0
  component_dl_right.style.display = 'block'
  component_dl_right.style.left = right+'px'
  component_dl_right.style.top = relativeRect.top+'px'
  component_dl_right.style.height = relativeRect.height+'px'
  component_dl_right.style.width = 0
  component_dl_top.style.display = 'block'
  component_dl_top.style.left = relativeRect.left+'px'
  component_dl_top.style.top = topPos+'px'
  component_dl_top.style.width = relativeRect.width+'px'
  component_dl_top.style.height = 0
  component_dl_bottom.style.display = 'block'
  component_dl_bottom.style.left = relativeRect.left+'px'
  component_dl_bottom.style.top = bottom+'px'
  component_dl_bottom.style.width = relativeRect.width+'px'
  component_dl_bottom.style.height = 0
  if (Math.round(left)!=Math.round(rect.x) && Math.round(right)==Math.round(rect.x+rect.width) && appEditMenu.selectedComponent.style.left) {
    x = parseFloat(component_toolbar_relative.style.left.replace('%',''))
    w = (100*(rect.width/relativeRect.width))
    component_toolbar_relative.style.right =  (100-x-w).toFixed(1).toString()+'%'
    component_toolbar_relative.style.removeProperty('left')
  }
  if (Math.round(left)==Math.round(rect.left) && Math.round(right)!=Math.round(rect.right) && appEditMenu.selectedComponent.style.right) {
    x = parseFloat(component_toolbar_relative.style.right.replace('%',''))
    w = (100*(rect.width/relativeRect.width))
    component_toolbar_relative.style.left = (100-x-w).toFixed(1).toString()+'%'
    component_toolbar_relative.style.removeProperty('right')
  }
  if (Math.round(topPos)!=Math.round(rect.y) && Math.round(bottom)==Math.round(rect.y+rect.height) && appEditMenu.selectedComponent.style.top) {
    x = parseFloat(component_toolbar_relative.style.top.replace('%',''))
    h = (100*(rect.height/relativeRect.height))
    component_toolbar_relative.style.bottom =  (100-x-h).toFixed(1).toString()+'%'
    component_toolbar_relative.style.removeProperty('top')
  }
  if (Math.round(topPos)==Math.round(rect.y) && Math.round(bottom)!=Math.round(rect.y+rect.height) && appEditMenu.selectedComponent.style.bottom) {
    x = parseFloat(component_toolbar_relative.style.bottom.replace('%',''))
    h = (100*(rect.height/relativeRect.height))
    component_toolbar_relative.style.top =  (100-x-h).toFixed(1).toString()+'%'
    component_toolbar_relative.style.removeProperty('bottom')
  }
  appEditMenu.$forceUpdate()
}
function hideComponentsDragLines() {
  component_dl_left.style.display = 'none'
  component_dl_right.style.display = 'none'
  component_dl_top.style.display = 'none'
  component_dl_bottom.style.display = 'none'
}


document.addEventListener('mouseup', function(e) {
  if (component_pad_update=='move' && component_toolbar_relative!=null) {
    if (!component_toolbar_relative.style.left && !component_toolbar_relative.style.right) {
      component_toolbar_relative.style.left = "0"
    }
    if (!component_toolbar_relative.style.top && !component_toolbar_relative.style.bottom) {
      component_toolbar_relative.style.top = "0"
    }
    relD = g(component_toolbar_relative).findUp('[style*="position:relative"]').all[0];
    if (relD==null) {
      relD = g(component_toolbar_relative).findUp('.block-head').all[0];
    }
    rect = relD.getBoundingClientRect()
    if (!component_toolbar_relative.style.left) {
      x = parseFloat(component_toolbar_relative.style.right.replace('%','')) - diffX*100/rect.width
      component_toolbar_relative.style.right =  x.toFixed(1).toString()+'%'
    } else {
      x = parseFloat(component_toolbar_relative.style.left.replace('%','')) + diffX*100/rect.width
      component_toolbar_relative.style.left =  x.toFixed(1).toString()+'%'  
    }
    if (!component_toolbar_relative.style.top) {
      x = parseFloat(component_toolbar_relative.style.bottom.replace('%','')) - diffY*100/rect.height
      component_toolbar_relative.style.bottom =  x.toFixed(1).toString()+'%'
    } else {
      x = parseFloat(component_toolbar_relative.style.top.replace('%','')) + diffY*100/rect.height
      component_toolbar_relative.style.top =  x.toFixed(1).toString()+'%'
    }
    updateComponentToolbarPosition()
  }

  documentStopPadding()
})
document.body.addEventListener('mouseenter', function(e) {
  documentStopPadding()
})
function documentStopPadding() {
  if (typeof block_relative=='undefined') return

  if (component_pad_update !== null && component_toolbar_relative!==null) {
    pos1 = g(component_toolbar_relative).findUp('.block-head').all[0].getAttribute('data-pos');
    componentarea1 = g(component_toolbar_relative).findUp('.componentarea').all[0];
    blocksUpdateText(pos1, componentarea1)
  }
  component_pad_update = null
  if (block_pad_update !== null && block_relative!==null) {
    value = block_relative.style['padding-'+block_pad_update]
    blocksUpdateField(block_relative, 'padding-'+block_pad_update, value)
    block_pad_relative.innerText = ''
  }
  block_pad_update = null
  block_relative = null
}


function setComponentPadDisplay(x) {
  if (appEditMenu.canMargin()) {
    component_pad_top.style.display = x
  } else {
    component_pad_top.style.display = 'none'
  }
  if (appEditMenu.canMarginRight()) {
    component_pad_right.style.display = x
  } else {
    component_pad_right.style.display = 'none'
  }
  if (appEditMenu.selectedComponent!=null && appEditMenu.selectedComponent.style.position=='absolute') {
    component_pad_move.style.display = x
    component_pad_resize.style.display = x
  } else {
    component_pad_move.style.display = 'none'
    component_pad_resize.style.display = 'none'
  }

  component_pad_border.style.display = x

  if (component_toolbar_relative && component_toolbar_relative.tagName=='IFRAME') {
    component_edit_btn.style.display = x
  } else {
    component_edit_btn.style.display = 'none'
  }
}

function componentSideEditor () {
  if (component_toolbar_relative == null) return
  appEditMenu.setSelectedComponent(component_toolbar_relative)
}

function elClassListRemove(item, name) {
  if(typeof item.classList=='undefined') return
  item.classList.remove(name);
  if(item.classList.length==0) {
    item.removeAttribute('class')
  }
}

function rgb2hsl(r,g,b) {
  // Make r, g, and b fractions of 1
  r /= 255;
  g /= 255;
  b /= 255;

  // Find greatest and smallest channel values
  let cmin = Math.min(r,g,b),
      cmax = Math.max(r,g,b),
      delta = cmax - cmin,
      h = 0,
      s = 0,
      l = 0;
  // Calculate hue
  // No difference
  if (delta == 0)
    h = 0;
  // Red is max
  else if (cmax == r)
    h = ((g - b) / delta) % 6;
  // Green is max
  else if (cmax == g)
    h = (b - r) / delta + 2;
  // Blue is max
  else
    h = (r - g) / delta + 4;

  h = Math.round(h * 60);
    
  // Make negative hues positive behind 360°
  if (h < 0)
      h += 360;
  
  // Calculate lightness
  l = (cmax + cmin) / 2;

  // Calculate saturation
  s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
    
  // Multiply l and s by 100
  s = +(s * 100).toFixed(1);
  l = +(l * 100).toFixed(1);
  return [h,s,l]
  //return "hsl(" + h + "," + s + "%," + l + "%)";
}
function hex2rgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  if (hex.length==4) {
    result = /^#?([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})$/i.exec(hex);
    result[1]+='0'
    result[2]+='0'
    result[3]+='0'
  }
  return result ? [
    parseInt(result[1], 16),
    parseInt(result[2], 16),
    parseInt(result[3], 16)
  ] : null;
}
function hexa2rgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? [
    parseInt(result[1], 16),
    parseInt(result[2], 16),
    parseInt(result[3], 16)
  ] : null;
}

function changeDuplicateIDs(parent) {
  const allElements = parent.querySelectorAll('*');
  const idMap = new Map();
  const duplicateIds = [];
  allElements.forEach(element => {
    const id = element.id;
    if (id) { // Only consider elements with an id
      if (idMap.has(id)) {
        idMap.set(id, idMap.get(id) + 1);
      } else {
        idMap.set(id, 1);
      }
    }
  });

  idMap.forEach((count, id) => {
    if (count > 1) {
      duplicateIds.push(id);
    }
  });

  const duplicateElements = duplicateIds.flatMap(id =>
      Array.from(document.querySelectorAll(`#${id}`))
  );

  console.log('Duplicate IDs:', duplicateIds);
  console.log('Duplicate Elements:', duplicateElements);
}


// scroll body while dragging
let autoScrollInterval;
document.body.addEventListener('dragover', function (e) {
  clearInterval(autoScrollInterval);
  if (e.target.classList.contains('sidebar-builder')) return
  rect = document.body.getBoundingClientRect();
  offsetY = e.clientY - rect.top - document.documentElement.scrollTop;
  const topThreshold = topEditMenu.offsetHeight + header.offsetHeight + 50;
  const bottomThreshold = rect.height - 50;
  if (offsetY < topThreshold) {
    console.log(offsetY)
      autoScrollInterval = setInterval(() => {
        document.documentElement.scrollTop -= 60;
      }, 6);
  //} else if (offsetY > bottomThreshold) {
  //    autoScrollInterval = setInterval(() => {
  //      document.documentElement.scrollTop += 10;
  //    }, 20);
  }
});
document.body.addEventListener('dragleave', function () {
  clearInterval(autoScrollInterval);
});
document.body.addEventListener('drop', function () {
  clearInterval(autoScrollInterval);
});
