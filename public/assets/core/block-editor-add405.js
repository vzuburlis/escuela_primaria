var blocks_app = null;

Vue.component('block-editor-add-popup', {
  template: `<div>
  <div id="add_block" v-if="add_block" ref="blocks_popup" style="grid-template-columns:1fr;">
    <div style="position:fixed;left:0;right:0;top:0;bottom:0;background:rgba(0,0,0,0.5);z-index:-100" @click="closeList()"></div>
    <div style="text-align:center;background:white;grid-column: span 2;">
      <img src="assets/core/admin/close.svg" class="add-block-x" style="width:2em" @click="closeList()">
      <ul v-if="content!='page_user'" class="g-tabs">
        <li :class="{active:tab==0}" v-if="!emailSupport" @click="bTab(0);"><a href='javascript:void(0)'>Plantillas</a></li>
        <li :class="{active:tab==1}" v-if="!emailSupport" @click="bTab(1);"><a href='javascript:void(0)'>Creador IA</a></li>
        <li :class="{active:tab==2}" @click="bTab(2);"><a href='javascript:void(0)'>Dinamicos</a></li>
        <li :class="{active:tab==3}" @click="bTab(3);"><a href='javascript:void(0)'>Globales</a></li>
      </ul>

    </div>

    <div v-if="tab==0||tab==2||tab==3" class="add-block-div">
      <div>
        <input type="text" class="g-input" style="max-width:360px;margin-top:10px" v-model="search" @input="filter()" :placeholder="g.tr('Search')">
      </div>
      <div class="add-block-grid">
        <div v-for="(b,bk) in blocks" v-if="blockInTab(b,tab)" style="max-height:166px">
          <div class="add-block-btn" :class="{'add-block-btn__active':b.active==1}" style="pointer-events:none">
            <span class="small d-block text-align-left">
              <b v-if="b.id" class="text-dark">🆔 {{b.id}}</b>
              <span v-if="!b.name.startsWith('text')" class=text-secondary>{{b.name}}</span>
            </span>
            <img v-if="b.preview" @click="createBlock(bk)"
            :src="b.preview" class="preview border" :title="b.name" style="width:100%;pointer-events:all">
            <div v-else style="pointer-events:none">
              <div style="transform:scale(.33);margin-left:-50%;margin-top:-50%;pointer-events: all;position:relative">
                <div @click="createBlock(bk)"
                style="position:absolute;width:900px;height:100%"></div>
                <iframe :src="'blocks/previewBlock/'+b.name+'?id='+b.id"
                class="border border-dark" style="width:900px;height:450px" scrolling="no"></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="p-4 bg-white">
      <label class="text-dark mt-2">Palabras clabe/Descripcion de seccion</label>
      <textarea class="form-control mb-2" v-model="block_prompt"></textarea>
      <div><span class="btn btn-primary" @click="generateBlock()">Generar</span></div>
    </div>

  </div>
  
</div>
`,
  props: ['src'],
  data: function() {
    return {
      add_block: false,
      selected_pos: 0,
      blocks: [],
      blockgroups: [],
      selectedGroup: contentTable,
      tab: 0,
      content: contentTable,
      block_prompt: '',
      search: '',
    }
  },
  mounted: function() {
    blocks_app = this
    if (emailSupport) this.tab=2
  },
  methods:{
    createBlock(type) {
      this.closeList()
      block_create(content_id, type, this.selected_pos)
    },
    generateBlock() {
      if (this.block_prompt.length<10) {
        g.alert(g.tr('Add more description', {es:'La descripcion es muy corta'}))
        return
      }
      g.loader()
      this.closeList()
      g.postJSON('blocks/ai_coder', {subject:this.block_prompt}, function(data) {
        postData = {id:content_id+blocks_app.selected_pos, type:'text', html:data.html}
        g.post('blocks/create', postData, function(data){
          g.loader(false)
          blocks_preview_reload(data)
          appEditMenu.draft = true
        })
      }, function(data){
        g.alert(data.error)
        g.loader(false)
      })
    },
    bTab(i) {
      this.tab=i
    },
    blockInTab(b, ti) {
      if (b.hide==true) return false
      if (ti==0 && b.group=='text') return true
      if (ti==3 && b.group=='saved') return true
      if (ti==2 && b.group!='text' && b.group!='saved') return true
      return false
    },
    openList() {
      this.add_block = true
      g.loader()
      g.get('blocks/getPrototypes/'+this.content, function(data){
        g.loader(false)
        data =JSON.parse(data)
        blocks_app.blocks = data.blocks
        blocks_app.blockgroups = data.blockgroups
      })
    },
    closeList() {
      this.add_block = false
      // document.body.style.overflowY = 'auto'
    },
    removePrototype(b) {
      g.loader()
      this.add_block = false
      delete this.blocks[b.name]
      g.post('blocks/removePrototype', 'id='+b.name, function(data) {
        g.loader(false)
        g.alert("Block prototype is removed!", "success")
      });
    },
    filter: function() {
      for(i in this.blocks) {
        this.blocks[i].hide = true
        if(this.search=='' || this.blocks[i].name.toLowerCase().includes(this.search.toLowerCase())) {
          this.blocks[i].hide = false
        }
      }
      this.$forceUpdate()
    }
  },
})



function block_add(pos) {
  // load here the prototypes
  g.loader()
  g.get('blocks/getPrototypes/'+blocks_app.content, function(data){
    g.loader(false)
    data = JSON.parse(data)
    blocks_app.blocks = data.blocks
    blocks_app.blockgroups = data.blockgroups
    blocks_app.add_block = true;
    blocks_app.selected_pos = pos;
    blocks_app.$forceUpdate()
  })
}

function block_create(content_id,type,pos,html=null) {
  if (content_id.endsWith('_')) content_id = content_id.slice(0, -1)
  href='blocks/edit?id='+content_id+'_new&type='+type;
  _type = type.toUpperCase().replace('_',' ');
  cblock_content=content_id
  cblock_type=type
  cblock_pos=pos
  if (type.startsWith('text')||type.startsWith('#')) {
    g.loader()
    widget_id = cblock_content.replace('/','_')+'_'+cblock_pos;
    postData = {id:widget_id.replace('__','_'), type:cblock_type}
    if (html!==null) postData.html = html
    g.post('blocks/create', postData, function(data){
      g.loader(false)
      blocks_preview_reload(data)
      appEditMenu.draft = true
    })  
  } else {
    g.get(href, function(data) { //title:_type,
      g.dialog({class:'lightscreen large',id:'widget-popup',body:data,type:'modal',buttons:'create_widget'})
      block_edit_open()
    });
  }
}
