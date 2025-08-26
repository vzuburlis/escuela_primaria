rootVueGTables = []
edit_popup_app = null
table_page_loading = null

Vue.component('g-table', {
  template: '<div class="g-table">\
    <div v-if="edititem" class="edititem">\
      <span v-if="edititem>0 || edititem==\'new\'" class="btn" @click="edititem=0"><i class="fa fa-chevron-left" aria-hidden="true"></i></span> \
      <label class="g-label" v-html="table.title"></label>\
      <form :id="name+\'-edit-item-form\'" class="g-form" v-html="edit_html">\
      </form>\
      <div>\
        <a v-if="edititem==\'new\'" class="btn btn-primary" @click="update()" v-html="word(\'Create\')"></a>\
        <a v-else class="btn btn-primary" @click="update()" v-html="word(\'Update\')"></a>\
        <a class="btn btn-white" @click="edititem=false" v-html="word(\'Cancel\')"></a>\
      </div>\
    </div>\
    <div v-for="(child,childkey) in table.children" v-if="edititem>0 && edititem!=\'new\' && child.list.length>0">\
     <g-table :gtype="childkey"\
     gchild=1 :gtable="JSON.stringify(child.table)" :gfields="JSON.stringify(child.list)"\
     :gfilters="\'&amp;\'+child.parent_id+\'=\'+edititem">\
    </g-table></div>\
\
<div v-if="edititem==0" class="w-100">\
<div class="mb-1">\
  <div class="g-table-title" v-html="table.title"></div>\
  <div v-if="layout==\'table\' && table.filter_pills" class="d-inline-block">\
    <ul class="nav">\
      <li v-for="pill in table.filter_pills" class="nav-item">\
        <span v-if="pill.filters==filters" class="nav-link text-black text-bold">{{pill.label}}</span>\
        <a v-else class="nav-link text-secondary" href="javascript:void(0)" @click="runFilters(pill.filters)">{{pill.label}}</a>\
      </li>\
    </ul></>\
  </div>\
  <div class="d-inline mx-1">\
    <div v-if="table.layouts" class="d-inline-block">\
      <button v-for="l in table.layouts" type="button" class="btn com-btn" :class="{\'opacity-50\':layout!=l}" @click="setLayout(l)"><img :src="\'assets/core/icons/\'+l+\'.svg\'"></button>\
    </div>\
    <span v-if="table.tools">\
      <span v-for="(tool,itool) in table.tools" v-if="canUseTool(tool)" @click="runtool(tool,$event)"\
      class="btn btn-sm" :class="{\'btn-primary\':itool==0,\'btn-outline-secondary\':itool>0}" style="margin-right:6px;" v-html="tool_label(tool)"></span>\
    </span>\
    <span v-if="table.bulk_actions && selected_rows.length>0 && table.bulk_actions.length>0">\
<div class="dropdown show d-inline">\
  <a class="btn btn-outline-primary btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
    {{selected_rows.length}} {{g.tr(\'selected\',{es:\'elegido(s)\'})}}\
  </a>\
  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">\
    <a v-for="(iaction,itool) in table.bulk_actions" v-if="canUseTool(iaction)"\
    @click="runtool(iaction,$event)" class="dropdown-item py-2" href="#" v-html="tool_label(iaction)"></a>\
  </div>\
</div>\
    </span>\
  </div>\
</div>\
<div v-if="layout!=\'kanban\' && child!=1" class="mb-2" style="min-height:40px">\
  <div v-if="table[\'search_box\']" class="g-searchbox">\
    <input v-model="search" class="form-control"\
    @keydown="preventKeyDown($event)"\
    @keyup="if($event.which == \'8\' || $event.keyCode) if($event.which!==\'13\') runsearch()"\
    :autofocus="table[\'search_box_autofocus\']"\
    @keypress="if($event.which == \'13\') runsearch(true)" value="" type="text" style="padding-right:28px">\
      <svg @click="runsearch(true)" height="24" width="24" style="position:absolute;right:0.3em;top:0.6em;cursor:pointer" viewBox="0 0 28 28"><circle cx="12" cy="12" r="8" stroke="#929292" stroke-width="3" fill="none"></circle><line x1="17" y1="17" x2="24" y2="24" style="stroke:#929292;stroke-width:3"></line></svg>\
  </div>\
  <div v-if="table.group" style="position:relative;display:inline-block" class="g-searchbox">\
    <select v-model="group" class="form-select" @change="runsearch(true)">\
      <option value="">{{g.tr("Group by",{es:"Agrupar"})}}</option>\
      <option v-for="g in table.group" :value="g">{{field_label(g)}}</option>\
    </select>\
  </div>\
  <div v-if="totalRows>0" class="d-none my-2 d-lg-flex pagination float-right align-items-baseline">\
    <div>\
      <span>{{startIndex+1}}-{{Math.min(totalRows-startIndex, startIndex+table.pagination)}} {{g.tr("of",{es:"de"})}} {{totalRows}}</span>\
      <b v-if="page>1" type="button" @click="gotoPage(page-1)">〈</b>\
      <b v-else class="opacity-50">〈</b>\
      <b v-if="page<totalPages()" type="button" @click="gotoPage(page+1)">〉</b>\
      <b v-else class="opacity-50">〉</b>\
    </div>\
    <div v-if="data.fields.length>5">\
      &nbsp;<span data-bs-toggle="dropdown" type="button"><i class="fa fa-cogs"></i></span>\
      <div class="dropdown-menu p-0"><ul class="list-group" style="max-height:300px;overflow:auto">\
        <li v-for="fkey in data.fields" class="list-group-item">\
        <label><input type="checkbox" v-model="table.fields[fkey].show" @change="updateColumnOptions()"> {{field_label(fkey)}}</label></li>\
      </ul>\
      <span class="btn btn-sm btn-outline-secondary m-1" @click="defaultColumnOptions()">{{g.tr("Reset options",{es:"Resetear opciones"})}}</span>\
      </div>\
    </div>\
  </div>\
  <span v-if="table[\'search_boxes\']" class="btn btn-primary d-lg-none" @click="showFilters=!showFilters"><i class="fa fa-filter"></span>\
  <div v-if="table[\'search_boxes\']" v-for="sb in table[\'search_boxes\']" class="g-searchbox">\
    <div v-if="displaySearchBox(sb)">\
    <v-select v-if="table.fields[sb].voptions" v-model="filter[sb]" :options="table.fields[sb].voptions" \
      label="text" :reduce="item => item.id" :placeholder="g.tr(\'Select\',{es:\'Elige\'})+\' \'+field_label(sb)" @input="runsearch(true,sb)"/>\
    <select v-else-if="table.fields[sb].options" v-model="filter[sb]" class="form-select" @change="runsearch(true,sb)">\
      <option value="" selected>{{g.tr(\'Select\',{es:\'Elige\'})}} {{field_label(sb)}}</option>\
      <option v-for="(opt,iopt) in table.fields[sb].options" :value="iopt">{{opt}}</option>\
    </select>\
    <div v-else-if="table.fields[sb].type==\'date\'" style="position:relative;display:inline-block">\
      <input class="form-control" v-model="filter[sb]" @change="runsearch(true)" type="date">\
    </div>\
    <div v-else style="position:relative;display:inline-block">\
      <input class="form-control" v-model="filter[sb]" @keyup="if($event.which == \'8\' || $event.keyCode) if($event.which !== \'13\') runsearch()"\
      @keypress="if($event.which == \'13\') runsearch(true)" value="" type="text">\
      <div v-else style="position:relative;display:inline-block">\
        <svg height="24" width="24" style="position:absolute;right:8px;top:8px" viewBox="0 0 28 28"><circle cx="12" cy="12" r="8" stroke="#929292" stroke-width="3" fill="none"></circle><line x1="17" y1="17" x2="24" y2="24" style="stroke:#929292;stroke-width:3"></line></svg>\
      </div>\
    </div>\
    </div>\
  </div>\
</div>\
    <g-kanban v-if="layout==\'kanban\'" :table="table" :items="items"></g-kanban>\
    <g-gridline v-if="layout==\'gridline\'" :table="table" :items="items"></g-gridline>\
    <g-grid v-if="layout==\'grid\'" :table="table" :items="items"></g-grid>\
    <div v-if="layout==\'table\' &&(edititem==0||child==1)">\
      <table ref="table" class="table table-striped bg-white rounded-2 bordered" cur-page="1"  group-by="" style="position:relative;">\
      <thead class="">\
      <tr>\
        <th v-if="table.bulk_actions" style="width:28px;" @click="toggleSelectAll()">\
          <i :class="checkboxClassBulk()" aria-hidden="true"></i>\
        </th>\
        <th v-if="table.sort"></th>\
        <th v-if="table.index_rows">#</th>\
        <th v-for="ifield in data.fields" :col="ifield" class="sorting" @click="orderBy(ifield)"\
          v-if="showField(ifield)" :style="thStyle(ifield)">\
          <span v-html="field_label(ifield)"></span>\
          <i v-if="order[ifield]==\'ASC\'" class="fa fa-chevron-down" :col="ifield"></i>\
          <i v-if="order[ifield]==\'DESC\'" class="fa fa-chevron-up" :col="ifield"></i>\
        </th>\
        <th v-if="table.commands">\
        </th>\
      </tr>\
    </thead>\
    <tbody>\
    <draggable group="opt" :list="items" :options="{handle:\'.handle\'}"  @end="endRowDrag"  @start="startRowDrag" style="display:contents">\
      <tr v-for="(item,irow) in items" :row-id="irow" :class="{selected:selectedRow(item.id)}">\
        <td v-if="table.bulk_actions" @click="select_row(item.id, irow, $event)">\
          <i :class="checkboxClass(item.id)"></i>\
        </td>\
        <td v-if="table.sort">\
          <i class="fa fa-arrows handle" style="cursor:move"></i>\
        </td>\
        <td v-if="table.index_rows">{{table.pagination*(page-1)+irow+1}}</td>\
        <td v-for="fkey in data.fields" v-if="showField(fkey)"\
        :col="fkey" :value="item[fkey]" :class="tdClass(fkey)" :style="tdStyle(fkey)"\
        @keydown="inlineDataUpdate(irow, fkey)" @click="clicked_cell(irow,fkey)">\
          <g-table-cell v-if="!group" :field="table.fields[fkey]" :fkey="fkey" :item="item" :value="item[fkey]"/>\
          <div v-else v-html="display_cell(item,fkey)+display_total(item,fkey)" class="d-flex align-items-center"></div>\
          <div v-if="table.qactions && table.qactions[fkey]" class="qactions">\
            <span v-for="(com,icom) in table.qactions[fkey]" v-if="canUse(com)" @click="command(com,item.id)" v-html="command_label(com,true)"></span>\
          </div>\
        </td>\
        <td v-if="table.commands" class="td-com">\
          <span v-for="(com,icom) in table.commands" v-if="canUse(com)"\
          @click="command(com,item.id)" class="g-icon-btn com-btn" v-html="command_label(com)"></span>\
        </td>\
      </tr>\
      <tr v-if="items.length==0">\
        <td v-if="table.empty_heading" colspan="100" style="text-align:center" v-html="table.empty_heading"></td>\
        <td v-else colspan="100" class="text-center p-4">{{txtNoResults()}}</td>\
      </tr>\
    </draggable>\
    </tbody>\
    <tfoot v-if="totalPages()>1">\
      <tr>\
        <td colspan="100" class="py-0">\
          <ul class="pagination g-pagination g-table-pagination">\
            <li v-for="p in pagination()" :class="(p==page?\'active\':\'\')" @click="gotoPage(p)" v-html="p"></li>\
          </ul>\
        </td>\
      </tr>\
    </tfoot>\
    </table>\
    </div>\
  </div>\
  ',
  props: ['gtype','gtable','gfields','gfilters','gfilter','gchild','gitems','gtotalrows','permissions','base'],
  data: function(){ 
    if(!this.permissions) {
      this.permissions=null
    }
    if(!this.gfilter) {
      this.gfilter='[]'
    }
    table = JSON.parse(this.gtable)
    for(i in table.fields) {
      if(typeof table.fields[i].options!=='undefined'
      && Object.keys(table.fields[i].options).length>8) {
        o = Object.entries(table.fields[i].options);
        table.fields[i].voptions = []
        for(j in o) {
          table.fields[i].voptions.push({id:o[j][0], text:o[j][1]})
        }
      }
      if(typeof table.fields[i].show=='undefined') {
        table.fields[i].show = true
      }
    }
    filter = JSON.parse(this.gfilter)
    if (typeof table.search_boxes!='undefined') for(i in table.search_boxes) {
      if (typeof filter[table.search_boxes[i]]=='undefined') {
        filter[table.search_boxes[i]] = ''
      }
    }

    return {
    name: this.gtype,
    table: table,
    permissions: JSON.parse(this.permissions),
    data:{
      fields: JSON.parse(this.gfields),
      rows:[],
    },
    totalRows:0,
    startIndex:0,
    items: [],
    filters: this.gfilters,
    filter: filter,
    query: '',
    selected_rows: [],
    order: [],
    group: null,
    edititem:0,
    edit_html:"",
    search:"",
    page:1,
    type: this.gtype,
    child: this.gchild,
    bulk_selected: 0,
    updateRows: [],
    inlineEdit: false,
    intervalUpdate: null,
    irowSelected: null,
    basePath: this.base ?? null,
    indexRow: 0,
    keysPressed: 0,
    layout: table.layout??'table',
    delayLoaderShow: false,
    delayLoader: null,
    loadTimeRequest: 0,
    lastLoadTimeRequest: 0,
    loadTimeUpdate: 0,
    showFilters: true,
  }},
  updated: function() {
    if(this.edititem==0) return;
    transformClassComponents()
  },
  methods: {
    setLayout: function(l) {
      if (l=='kanban') {
        this.filters=''
        this.search=''
        this.group=null
        this.order=[]
      }
      this.layout = l
      this.load_page({page:1,filters:''})
      //window.location.href = 'admin/content/'+this.table.name
    },
    countRow: function() {
      this.indexRow++
      return this.indexRow
    },
    load_page: function(a={}) {
      let _this = this
      if(a.page) this.page=a.page
      if(a.order) this.order=a.order
      if(a.group) this.group=a.group
      if(a.filters) this.filters=a.filters
      // if(a.loader && a.loader==true && this.delayLoaderShow!=true) g(this.$refs.table).loader()
      if(typeof this.filters=='undefined') this.filters=''
      order = ''
      for (x in this.order) {
        order = order+'&orderby['+x+']='+this.order[x]
      }
      search = this.search ? '&search='+encodeURIComponent(this.search): '';
      group = this.group ? '&groupby='+this.group: '';
      for(fkey in this.filter) {
        if (fkey=='search') continue
        if(this.filter[fkey]!==''&&this.filter[fkey]!==null&&this.filter[fkey]!=='*') if(typeof this.filter[fkey]=='object'){
          for (const [k, v] of Object.entries(this.filter[fkey])) {
            search += '&'+fkey+'['+k+']='+v
          }
        } else {
          search += '&'+fkey+'='+this.filter[fkey]
        }
      }


      this.loadTimeRequest = new Date().getTime();
      params = '&page='+this.page+this.filters+order+group+search
      if (this.layout=='kanban') {
        params = '&limit=10000'+this.filters+order+group+search
      }
      //console.log(this.lastLoadTimeRequest+'>'+this.loadTimeRequest+'-'+this.table.cache_time*1000)
      if (typeof this.table.cache_time!='undefined' && this.lastLoadTimeRequest>this.loadTimeRequest-this.table.cache_time*1000) {
        url = 'cm/list_rows/'+this.name+'?_tm='+this.lastLoadTimeRequest+params
      } else {
        url = 'cm/list_rows/'+this.name+'?_tm='+this.loadTimeRequest+params
        this.lastLoadTimeRequest = this.loadTimeRequest;
      }

      g.getJSON(url,
      function(data) {
        g.loader(false)
        if (data.error) {
          g.alert(data.error, 'error')
        }
        if (data._tm < _this.loadTimeUpdate) return
        _this.loadTimeUpdate = data._tm
        _this.totalRows=data.totalRows
        _this.startIndex=data.startIndex
        _this.items=data.items
        if(typeof g.lazyLoad!='undefined') {
          setTimeout(function(){g.lazyLoad();}, 150);
        }
      }, function(data) {
        g.loader(false)
        if (data.error) {
          g.alert(data.error, 'error')
        }
      })
    },
    select_row: function(rowId, irow=null, event=null) {
      var index = this.selected_rows.indexOf(rowId)
      if(index === -1) {
        this.selected_rows.push(rowId);
      } else {
        this.selected_rows.splice(index, 1);
      }

      if (event && event.shiftKey && this.irowSelected) {
        step = Math.sign(this.irowSelected - irow)
        for(i=irow+step; i!=this.irowSelected+step; i+=step) {
          row_id = this.items[i].id
          index2 = this.selected_rows.indexOf(row_id)
          if(index === -1) {
            if(index2===-1) {
              this.selected_rows.push(row_id);
            }
          } else {
            if(index2>-1) {
              this.selected_rows.splice(index2, 1);
            }
          }
        }
      }

      this.bulk_selected = -1;
      if(this.selected_rows.length==0) {
        this.bulk_selected = 0;
      }
      this.irowSelected = irow
    },
    updateColumnOptions: function(def=false) {
      g.postJSON('cm/updateOptions/'+this.name, {data:this.table.fields,default:def}, function() {})
    },
    defaultColumnOptions: function() {
      g.postJSON('cm/removeOptions/'+this.name, {}, function() {
        location.reload();
      })
    },
    toggleSelectAll: function() {
      this.selected_rows = [];
      if(this.bulk_selected == 0) {
        this.bulk_selected = 1;
        for(i in this.items) {
          this.selected_rows.push(this.items[i].id);
        }
      } else {
        this.bulk_selected = 0;
      }
    },
    command: function(com, irow) {
      gtableCommand[com].fn(this,irow)
    },
    startRowDrag: function(ev) {
      i = ev.item.getAttribute('row-id')
      this.selectedID = this.items[i].id
      ev.item.classList.add("selected");
    },
    endRowDrag: function(ev) {
      steps = ev.newIndex-ev.oldIndex
      ev.item.classList.remove("selected");
      g.loader()
      g.postJSON('cm/movePos/'+this.name+'?id='+this.selectedID, {steps:steps}, function() {
        g.loader(false)
      })
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
    tool_label: function(tool) {
      if(typeof gtableTool[tool]=='undefined') return _e(tool)
      return _e(gtableTool[tool].label)
    },
    field_label: function(ifield) {
      if(typeof this.table.fields[ifield].title=='undefined') return ifield
      if(this.table.fields[ifield].title=='') return ifield
      return this.table.fields[ifield].title
    },
    command_label: function(com, label=false) {
      if (typeof gtableCommand[com]=='undefined') return '';
      if (label || this.table.action_display=='label') {
        if(typeof gtableCommand[com].label=='undefined') return '<a>'+_e(com)+'</a>'
        return '<a>'+_e(gtableCommand[com].label)+'</a>';
      }
      return '<i class="fa fa-2x fa-'+gtableCommand[com].fa+'" title="'+_e(gtableCommand[com].label)+'"></i>'
    },
    canUse: function(com) {
      if(gtableCommand[com] && gtableCommand[com].permission && this.permissions) {
        if (this.table.permissions[gtableCommand[com].permission]==true) return true
        for(p of this.table.permissions[gtableCommand[com].permission]) {
          if(this.permissions.includes(p)) return true
        }
        return false
      }
      return true
    },
    canUseTool: function(com) {
      if(gtableTool[com] && gtableTool[com].permission && this.permissions) {
        if (this.table.permissions[gtableTool[com].permission]==true) return true
        for(p of this.table.permissions[gtableTool[com].permission]) {
          if(this.permissions.includes(p)) return true
        }
        return false
      }
      return true
    },
    runsearch: function(pushState = false, field = null) {
      if(pushState==true) {
        this.gotoPage(1)
        if (field && this.require_filters && this.require_filters[field]) {
          q = ''
          for (i in this.require_filters) {
            q += '&'+i+'='+(this.filter[i]??this.require_filters[i])
          }
          location.href = 'admin/content/'+this.name+'?'+q
        }
      } else {
        this.keysPressed++
        setTimeout(function(table) {
          table.keysPressed--
          if (table.keysPressed==0) {
            table.page = 1
            table.load_page({loader:true})
          }
        }, 350, this)
      }
    },
    runFilters: function(filters) {
      this.filters = filters
      this.load_page()
    },
    displaySearchBox: function(key) {
      if (this.showFilters==false) {
        return false;
      }
      if (typeof this.table.fields[key]=='undefined') {
        console.error('Search box '+key+' does not exist')
        return false
      }
      if (typeof this.table.fields[key].conditions!='undefined') {
        for (i in this.table.fields[key].conditions) {
          if (this.filter[i]==this.table.fields[key].conditions[i]) return true;
        }
        return false;
      }
      return true;
    },
    gotoPage: function(p) {
      this.page = p
      this.pushState()
      this.load_page()
    },
    pushState: function() {
      if(this.basePath) {
        search = this.search ? '&search='+encodeURIComponent(this.search): '';
        for(fkey in this.filter) {
          if (fkey=='search') continue
          if(this.filter[fkey]!==''&&this.filter[fkey]!==null) if(typeof this.filter[fkey]=='object'){
            for (const [k, v] of Object.entries(this.filter[fkey])) {
              search += '&'+fkey+'['+k+']='+v
            }
          } else {
            search += '&'+fkey+'='+this.filter[fkey]
          }
        }
        order = ''
        history.pushState({path:this.basePath,search:search}, _e('Content'), this.basePath+'?page='+this.page+order+search)
      }
    },
    update: function(){
      let irow = this.edititem
      id_name = this.name+'-edit-item-form'
      
      form = document.getElementById(id_name)
      data = new FormData(form);
      values = readFromClassComponents()
      for(x in values) {
        data.set(x, values[x])
      }

      let _this = this
      if(irow=='new') {
        url = 'cm/update_rows/'+this.name
        if(typeof _this.filters!='undefined') {
          url = url+'?'+_this.filters
        }
      } else {
        url = 'cm/update_rows/'+this.name+'?id='+irow
      }
      g.loader()
      g.ajax({method:'post', url:url, data:data, fn:function(data) {
        g.loader(false)
        data = JSON.parse(data)
        if (data.error) {
          g.alert(data.error, 'error')
          return
        }
        if(irow=='new') {
          _this.items.unshift(data.items[0])
          if (_this.sort!='undefined') {
            _this.load_page()
          }
          if(typeof _this.table.children!='undefined') {
            _this.edititem = data.items[0].id
          }
        } else {
          _this.update_rows(data)
        }
      }, error:function(data) {
        g.loader(false)
        data = JSON.parse(data)
        if (data.error) {
          g.alert(data.error, 'error')
        }
      }})

      if(irow=='new' && typeof this.table.children!='undefined') {
        return
      }
      this.edititem = false
    },
    toggle_value: function(irow,fkey,v1=0,v2=1) {
      let _this = this
      url = 'cm/update_rows/'+this.name+'?id='+this.items[irow].id
      if(this.items[irow][fkey]==v1) v=v2; else v=v1;
      data = new FormData()
      data.append(fkey, v)
      g.ajax({method:'post',url:url,data:data,fn:function(data) {
        _this.items[irow][fkey] = v
        _this.$forceUpdate()
      }})
    },
    update_rows: function(data) {
      for(j=0;j<data.items.length;j++) {
        this.update_row(data.items[j])
      }
      this.$forceUpdate()
      for(i=0; i<rootVueGTables.length; i++) if (rootVueGTables[i].name!=this.name) {
        rootVueGTables[i].load_page()
      }
    },
    update_row: function(item) {
      for(i=0; i<this.items.length; i++) if(this.items[i].id == item.id) {
        this.items[i] = item;
        break
      }
    },
    clicked_cell: function(irow,fkey){
      field = this.table.fields[fkey]

      if (typeof field.href != 'undefined') {
        let id = this.items[irow].id
        x = field.href.replace('{id}', id).replace('{i}', irow)
        eval(x)
      }

      if (this.canUse('edit')==false) return
      if (typeof field.toggle_values != "undefined") {
        this.toggle_value(irow,fkey,field.toggle_values[0],field.toggle_values[1])
      }
    },
    href_cell: function(field, irow) {
      return field.href.replace('{id}', irow)
    },
    display_total: function(item,fkey) {
      if (fkey==this.group && item._total) return '('+item._total+')'
      return ''
    },
    display_cell: function(item,fkey) {
      cv = item[fkey]
      field = this.table.fields[fkey]
      displayValue = cv

      if(field.alt) if(!cv) {
        cv = field.alt
        displayValue = '<span style="opacity:0.66">'+cv+'</span>'
      }
      if (typeof this.table.fields[fkey].eval != "undefined") {
        return eval(this.table.fields[fkey].eval)
      }

      if(typeof gtableFieldDisplay[fkey]!='undefined') {
        return gtableFieldDisplay[fkey](item,this);
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

      if (isNaN(cv) && displayType=='date') {
        cv = Date.parse(cv)/1000;
      }
      if (isNaN(cv) && displayType=='datetime') {
        cv = Date.parse(cv)/1000;
      }
      if(displayType=='date' && !isNaN(cv)) {
        return g.getDate(cv)
      }
      if(displayType=='datetime' && !isNaN(cv)) {
        return g.getDateTime(cv)
      }
      if(displayType=='month') {
        if (isNaN(cv)) {
          cv = Date.parse(cv+'-02')/1000;
          if (isNaN(cv)) return ''
        }
        return g.getDate(cv, 'M Y')
      }


      if(displayType=='money') {
        lf = field.number_format??'es-MX'
        currency = field.currency??'MXN'
        displayValue = new Intl.NumberFormat(lf, { style: 'currency', currency: currency }).format(displayValue);
        return '<div style="text-align:right">'+displayValue+'</div>';
      }

      if(displayType=='media') if(cv!=null && cv.length>0) {
        if (cv.substring(cv.indexOf(".")+1)!='jfif') {
          src = 'lzld/thumb?src='+cv+'&media_thumb=100'
        }
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

      if(displayType=='progress-bar') {
        if (typeof field.max!='undefined') {
          x = displayValue*80/field.max
          return '<div class="progress-bar-container" style="height:15px;width:80px;background-color: #e0e0e0">\
                    <div class="progress-bar" style="height:100%;width:'+x+'px;background-color:rgb(71, 97, 212)"></div>\
                  </div>';
        }
        displayValue=parseFloat(displayValue).toFixed(2)
        if (isNaN(displayValue)) return
        pcValue = parseInt(displayValue*100)
        displayValue = ''
        if(field.display_percentage) displayValue=pcValue+'%'
        return '<span><progress value="'+pcValue+'" max=100"></progress> '+displayValue+'</span>'
      }

      if(displayType=='text' & item.text && item.text.length>100) {
        return item.text.substring(0, 97)+'...';
      }


      if (typeof field.options != "undefined") if(cv!==null) {
        if (typeof field.options[cv] != "undefined") {
          if(field.option_colors && field.option_colors[cv]) {
            txt = field.options[cv]??'N/A'
            html = '<span class="g-badge" style="background:'+field.option_colors[cv]+'">'+txt+'</span>';
            return html
          }
          if(field.option_images && field.option_images[cv]) {
            html = '<img style="height:30px;max-width:45px;" src="'+field.option_images[cv]+'" title="'+field.options[cv]+'">';
            return html
          }
          html = field.options[cv]
          if (typeof field.href!='undefined') html += ' →'
          return html
        }
        let resp = ''
        if(typeof cv=='string') {
          let csv = cv.split(',')
          for(i=0;i<csv.length;i++) {
            if(i>0)  resp += '<br>'
            if(typeof field.options[csv[i]] != "undefined") {
              resp += field.options[csv[i]]
            } else resp += csv[i]
          }
          if (typeof field.href!='undefined') resp += ' →'
        }
        return resp
      } else {
        if (typeof field.href!='undefined') return '&nbsp→'
      }

      if(field.inline_edit) {
        if (displayValue==null) displayValue=field.default??''
        return '<div contenteditable="true" data-field="'+fkey+'">'+displayValue+'</div>';
      }
      //if (displayValue && typeof displayValue=='string' && displayValue.length>0) {
      //  displayValue = displayValue.replace(/ /g, '\u00a0')
      //}
      return displayValue
    },
    showField: function(field) {
      if(typeof this.table.fields[field]=='undefined') return false
      if(typeof this.table.fields[field].show=='undefined') return true
      return this.table.fields[field].show
    },
    word: function(word){
      return _e(word)
    },
    totalPages: function(){
      return Math.ceil(this.totalRows/this.table.pagination)
    },
    pagination: function(){
      let a = []
      let total =this.totalPages()+1
      for(i=1;i<4;i++) if(i<total) if(a.indexOf(i)==-1) a.push(i);
      for(i=this.page-3;i<this.page+3;i++) if(i>0 && i<total) if(a.indexOf(i)==-1) a.push(i);
      for(i=total-3;i<total;i++) if(i>0) if(a.indexOf(i)==-1) a.push(i);
      return a
    },
    orderBy: function(key){
      if(this.order[key]=='DESC') order='ASC'; else order='DESC';
      this.order = []
      this.order[key] = order
      this.page = 1
      this.load_page({loader:true})
    },
    sortiChar: function(key){
      if(this.order[key]=='ASC') return '⮝'
      if(this.order[key]=='DESC') return '⮟'
      return '';
    },
    thStyle: function(key){
      style = '';
      if(this.table.fields[key].width) {
        style += 'width:'+this.table.fields[key].width+';'
      } else {
        style += 'width:min-content;'
      }
      if(this.table.fields[key].type && ['number','money'].includes(this.table.fields[key].type)) {
        style += 'text-align:right;'
      }
      if(this.table.fields[key].th_style) {
        style += this.table.fields[key].th_style
      }
      return style;
    },
    tdStyle: function(key){
      style = '';
      field = this.table.fields[key]
      if (typeof field.href != 'undefined') {
        style = 'cursor:pointer;'
      }
      if(this.table.fields[key].td_style) {
        style = this.table.fields[key].td_style
      }
      return style;
    },
    tdClass: function(key){
      style = key;
      if(this.table.fields[key].td_class) {
        style = key+' '+this.table.fields[key].td_class
      }
      return style;
    },
    checkboxClass: function(irow){
      cl = ''
      if(this.selectedRow(irow)) cl='fa-check-square-o'; else cl='fa-square-o';
      return 'fa tr_checkbox '+cl;//☐☑☒
    },
    selectedRow: function(irow){
      return this.selected_rows.indexOf(irow)>-1;
    },
    checkboxClassBulk: function(){
      cl = 'fa-square-o'
      if(this.bulk_selected>0) cl='fa-check-square-o';
      if(this.bulk_selected<0) cl='fa-minus-square-o';
      return 'fa bulk_checkbox '+cl;
    },
    inlineDataUpdate: function(irow, fkey){
      if(!this.table.fields[fkey].inline_edit) return;
      this.updateRows.push(irow);
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
        _this.update_rows(data)
      }})
    },
    preventKeyDown: function(e) {
      if (e.ctrlKey && e.shiftKey) return
      if (e.ctrlKey && e.which!='86' && e.which!='88' && e.which!='67') e.preventDefault()
    },
    optionColor: function(field,val) {
      if(typeof field.option_colors!='undefined') {
        return field.option_colors[val]
      } else return 'grey'
    },
    txtNoResults: function() {
      if (this.search=='' && this.filters=='') {
        return g.tr('There is no registry created', {es:'No hay registros todavia'})
      }
      return g.tr('No results found', {es:'No se encontraeron resultados'})
    }
  },
  mounted: function() {
    if (document.body.clientWidth<992) {
      this.showFilters=false
    }
    if(this.gtotalrows) {
      this.totalRows = parseInt(this.gtotalrows)
    }
    if(this.gitems) {
      this.items = JSON.parse(this.gitems)
      if(typeof g.lazyLoad!='undefined') {
        setTimeout(function(){g.lazyLoad();}, 150);
      }
    }
    if(this.items.length==0||this.layout!='table') {
      this.page=1;
      this.load_page()
    }
    rootVueGTables.push(this)

    this.intervalUpdate = setInterval(function(_this) {
      for(i=0; i<_this.updateRows.length; i++) {
        irow = _this.updateRows[i]
        id = _this.items[irow].id;
        data = {}
        tr = g('tr[row-id="'+irow+'"] [contenteditable="true"]').all
        for(j=0; j<tr.length; j++) {
          field = tr[j].getAttribute('data-field')
          value = tr[j].innerHTML
          data[field] = value
        }

        url = 'cm/update_rows/'+_this.name+'?id='+id
        g.ajax({method:'post',url:url,data:data,fn:function(data) {
          tr = g('tr[row-id="'+irow+'"] td').all
          for(j=0; j<tr.length; j++) {
            tr[j].animate([{background:'lightgreen'},{background:'inherit'}], {duration:600})
          }
        }})

      }
      _this.updateRows = [];
    }, 3000, this);

    params = new URLSearchParams(window.location.search)
    if (params.has('_cmd') && params.has('_id')) {
      if (params.has('t') && params.get('t')!=this.name) return
      this.command(params.get('_cmd'), params.get('_id'))
    } 
    if (params.has('_tl')) {
      if (params.has('t') && params.get('t')!=this.name) return
      this.runtool(params.get('_tl'), this.$event)
    } 
  }
})


Vue.component('g-kanban', {
  template: `<div class="d-flex gap-3">
  <div v-for="(opt,grp) in table.fields[table.groups[0]].options"
  style="min-width:200px;max-width:240px;background:#e8e8e8;padding:8px" class="p-2 d-flex flex-column gap-2"
  :style="{borderTop:'4px solid '+$parent.optionColor(table.fields[this.table.groups[0]],grp)}">
    <b>{{opt}}</b>
    <draggable group="opt" :list="cards[grp]" @end="endDrag"
    style="min-height:60vh;height:100%" :data-value="grp">
    <div v-for="item in cards[grp]">
      <div class="card p-2 mb-2" :key="item.id" @click="$parent.command('edit_side',item.id)">

        <div v-if="table.kanban_list" class="mt-1">
          <div class="d-block mt-1" v-for="fkey in table.kanban_list" v-html="$parent.display_cell(item,fkey)"></div>
        </div>
        <div v-if="table.kanban_data" class="mt-1">
          <div v-for="field in table.kanban_data" class="mt-1"><b>{{table.fields[field].title}}: </b>
            <span class="d-inline-block" v-html="$parent.display_cell(item,field)"></span>
          </div>
        </div>
        <div v-if="table.kanban_flex" @click="$event.stopPropagation()" class="d-flex align-items-center gap-1 mt-1">
          <span class="d-inline-block" v-for="fkey in table.kanban_flex" v-html="$parent.display_cell(item,fkey)"></span>
        </div>
        <div v-if="table.commands" class="dropdown" @click="$event.stopPropagation()">
          <span class="g-icon-btn com-btn float-right" data-bs-toggle="dropdown" aria-expanded=false> ⋮ </span>
          <ul class="dropdown-menu">
            <li v-for="(com,icom) in table.commands" v-if="$parent.canUse(com)" class="p-1" role="button"
            @click="$parent.command(com,item.id)" v-html="$parent.command_label(com, true)"></li>
          </ul>
        </div>
      </div>
    </div>
    </draggable>
  </div>
</div>`,
  props: ['table','items'],
  data: function() {
    cards = {}
    for(o in this.table.fields[this.table.groups[0]].options) {
      cards[o] = []
    }
    return {
      cards: cards
    }
  },
  watch: {
    items: function(items) {
      this.updateCards(items)
    }
  },
  methods:{
    updateCards: function(items) {
      field = this.table.groups[0]
      for(o in this.table.fields[this.table.groups[0]].options) {
        this.cards[o] = []
        for(i in items) if (items[i][field]==o) {
          this.cards[o].push(items[i])
        }
      }
    },
    endDrag: function(ev) {
      field = this.table.groups[0]
      for(o in this.cards) {
        for(i in this.cards[o]) if (this.cards[o][i][field]!=o) {
          this.$parent.update_cell(this.cards[o][i],field,o)
        }
      }
    },
  }
});

Vue.component('g-gridline', {
  template: '<div class="d-grid gap-2">\
  <draggable group="opt" :list="items" :options="{handle:\'.handle\'}"  @end="endRowDrag"  @start="startRowDrag" style="display:contents">\
    <div v-for="item in items" class="bordered p-1">\
      <div v-if="table.grid_list" @click="$event.stopPropagation()" class="d-flex align-items-center gap-1">\
        <span v-if="table.sort">\
          <i class="fa fa-arrows handle" style="cursor:move"></i>\
        </span>\
        <span class="d-inline-block" v-for="(fkey,k) in table.grid_list" v-html="$parent.display_cell(item,fkey)"\
        :style="{width:table.grid_list_w[k]}"></span>\
        <div v-if="table.commands" class="dropdown" @click="$event.stopPropagation()">\
          <span class="g-icon-btn com-btn float-right" data-bs-toggle="dropdown" aria-expanded=false> <b>⋮</b> </span>\
          <ul class="dropdown-menu">\
            <li v-for="(com,icom) in table.commands" v-if="$parent.canUse(com)" class="p-1" role="button"\
            @click="$parent.command(com,item.id)" v-html="$parent.command_label(com, true)"></li>\
          </ul>\
        </div>\
      </div>\
    </div>\
  </draggable>\
</div>',
  props: ['table','items'],
  data: {
    selectedID: null
  },
  methods:{
    startRowDrag: function(ev) {
      i = ev.item.getAttribute('row-id')
      this.selectedID = this.items[i].id
    },
    endRowDrag: function(ev) {
      steps = ev.newIndex-ev.oldIndex
      g.loader()
      g.postJSON('cm/movePos/'+$parent.name+'?id='+this.selectedID, {steps:steps}, function() {
        g.loader(false)
      })
    },
  }
});

Vue.component('g-grid', {
  template: '<div class="d-grid gap-2" style="grid-template-columns:repeat(auto-fit, 200px)">\
    <div v-for="item in items" class="p-1">\
      <div v-if="table.grid_list" @click="$event.stopPropagation()" class="d-grid align-items-center gap-1">\
        <span class="d-inline-block" v-for="(fkey,k) in table.grid_list" v-html="$parent.display_cell(item,fkey)"\
        ></span>\
        <div v-if="table.commands" class="dropdown" @click="$event.stopPropagation()">\
          <div v-if="table.grid_flex" @click="$event.stopPropagation()" class="d-flex align-items-center gap-1" style="position:absolute">\
            <span class="d-inline-block" v-for="(fkey,k) in table.grid_flex" v-html="$parent.display_cell(item,fkey)"\
            ></span>\
          </div>\
          <span class="g-icon-btn com-btn float-right" data-bs-toggle="dropdown" aria-expanded=false> <b>⋮</b> </span>\
          <ul class="dropdown-menu">\
            <li v-for="(com,icom) in table.commands" v-if="$parent.canUse(com)" class="p-1" role="button"\
            @click="$parent.command(com,item.id)" v-html="$parent.command_label(com, true)"></li>\
          </ul>\
        </div>\
      </div>\
    </div>\
</div>',
  props: ['table','items'],
  data: {},
  methods:{
  }
});

Vue.component('g-table-cell', {
  template: '<div><div v-if="field.option_edit">\
  <div class="dropdown"><span class="g-badge dropdown-toggle" data-bs-toggle="dropdown" aria-expanded=false :style="{background:$parent.$parent.optionColor(field,value)}">{{field.options[value]??"N/A"}}</span>\
  <ul class="p-0 dropdown-menu"><div class="list-group">\
    <div v-for="(opt,i) in field.option_selectable??field.options" class="list-group-item list-group-item-action" type="button" @click="$parent.$parent.update_cell(item,fkey,i)">{{opt}}</div>\
  </div></ul></div>\
</div>\
<div v-else v-html="$parent.$parent.display_cell(item,fkey)"></div>\
</div>',
  props: ['field','value','item','fkey'],
})


function transformClassComponents() {
  let textareas
  textareas=g('.codemirror-js').all
  cmirror=[]
  for(i=0;i<textareas.length;i++) {
    x=textareas[i].name
    cmirror[x]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
  }

  textareas=g('.tinymce').all
  mce_editor=[]
  if(tinymce!=='undefined') tinymce.remove() //remove all tinymce editors
  for(i=0;i<textareas.length;i++) {
    mce_editor[i] = {id: textareas[i].id, name: textareas[i].name};
    mce_editor[i].settings = JSON.parse(JSON.stringify(g_tinymce_options));
    mce_editor[i].settings.selector = '[name='+textareas[i].name.replace('[','\\[').replace(']','\\]')+']'
    mce_editor[i].settings.file_picker_callback = function(cb, value, meta) {
      input_filename = cb;
      open_gallery_post();
    }
    tinymce.init(mce_editor[i].settings)
  }

  if(typeof $ != 'undefined' && typeof $.fn.select2 != 'undefined') $(".select2").select2();
}

function readFromClassComponents() {
  let values = new Array()
  for (x in mce_editor)  {
    values[mce_editor[x].name] = tinymce.get(mce_editor[x].id).getContent()
  }
  textareas=g('.codemirror-js').all
  for (x in cmirror) {
    values[x] = cmirror[x].getValue()
  }
  return values
}


gtableCommand = Array()
gtableTool = Array()
gtableFieldDisplay = Array()
gtableDisplayType = Array()
gtableGenerator = Array()

gtableCommand.edit = {
  fa: 'pencil',
  label: 'Edit',
  fn: function(table,irow){
    let _this = table
    _this.edititem = irow
    _this.edit_html = "Loading..."
    g.loader()
    g.get('cm/edit_form/'+_this.name+'?id='+irow,function(data){
      _this.edit_html = data
      g.loader(false)
      app.$forceUpdate()
    })
  }
}
gtableCommand.edit_page = {
  fa: 'pencil',
  label: 'Edit',
  fn: function(table,irow){
    window.location.href = 'admin/content/'+table.name+'/'+irow
  }
}
var edit_popup_app={}
gtableCommand.edit_popup = {
  fa: 'pencil',
  label: 'Edit',
  fn: function(table,irow) { edit_popup_dialog(table,irow) },
}
gtableCommand.edit_popupsm = {
  fa: 'pencil',
  label: 'Edit',
  fn: function(table,irow) { edit_popup_dialog(table,irow,'small') },
}
function edit_popup_dialog(table,irow, cl='large') {
  href='cm/edit_form/'+table.name+'?id='+irow+'&callback=g_form_popup_update';
  g.get(href,function(response){
    g.dialog({title:g.tr('Edit Registry'), class:cl,body:response,type:'modal',buttons:'popup_update'})
    formId = '#'+table.name+'-edit-item-form'
    textarea = g('#gila-popup textarea').first()
    formValues = []
    if (typeof g(formId).all[0].dataset.values!='undefined') {
      formValues = JSON.parse(g(formId).all[0].dataset.values)
    }
    if (!textarea || !textarea.innerHTML.includes('{{')) {
      edit_popup_app = new Vue({
        el: formId,
        data: {id:irow,formValue:formValues}
      })
    }
    transformClassComponents()
    g(formId+' input').all[1].focus()
  },function(data){
    data = JSON.parse(data)
    g.alert(data.error)
  })
}

gtableCommand.edit_side = {
  fa: 'pencil',
  label: 'Edit',
  fn: function(table,irow) {
    href='cm/edit_form/'+table.name+'?id='+irow+'&callback=g_form_popup_update';
    g.loader()
    g.get(href,function(response){
      g.loader(false)
      g.dialog({title:g.tr('Edit Registry'), class:'lightscreen large side',body:response,type:'modal',buttons:'popup_update'})
      formId = '#'+table.name+'-edit-item-form'
      textarea = g('#gila-popup textarea').first()
      formValues = []
      if (typeof g(formId).all[0].dataset.values!='undefined') {
        formValues = JSON.parse(g(formId).all[0].dataset.values)
      }
      if (!textarea || !textarea.innerHTML.includes('{{')) {
        edit_popup_app = new Vue({
          el: formId,
          data: {id:irow,formValue:formValues}
        })
      }
      transformClassComponents()
      g(formId+' input').all[1].focus()
    },function(data){
      g.loader(false)
      data = JSON.parse(data)
      g.alert(data.error)
    })
  }
}

gtableCommand.edit_seo = {
  fa: "search",
  label: "SEO",
  permission: "update",
  fn: function(table,irow) {
    gtableCommand.edit_side.fn(table,irow)
  }
}

gtableCommand.edit_blocks = {
  fa: 'paint-brush',
  label: 'Edit',
  permission: 'update',
  fn: function(table,id){
    window.location.href = 'blocks/editor/'+table.name+'/'+id
  }
}
gtableCommand.clone = {
  fa: "copy",
  label: "Clone",
  permission: 'create',
  fn: function(table,id) {
    let _this
    _this = table
    _this.edit_html = "Loading..."
    g.post('cm/insert_row/'+_this.name, 'id='+id+'&formToken='+csrfToken, function(data){
      data = JSON.parse(data)
      _this.items.unshift(data.items[0])
    })
  }
}
gtableCommand.delete = {
  fa: "trash-o",
  label: _e("Delete"),
  permission: 'delete',
  fn: function(table,id) {
    let _this = table
    let _id = id
    data = new FormData()
    data.append('id',id)
    if(typeof csrfToken!='undefined') {
      data.append('formToken',csrfToken)
    }
    if(confirm(_e("Delete registry?"))) g.ajax({
      url: 'cm/delete?t='+_this.name,
      data: data,
      method: 'post',
      fn: function(data) {
        data = JSON.parse(data)
        if (data.success===false) {
          g.alert(data.error, 'error')
          return
        }
        for(i=0;i<_this.items.length;i++) if(_this.items[i].id == _id) {
          _this.items.splice(i,1)
        }
        //_this.$forceUpdate()
      }
    });
  }
}

gtableCommand.email = {
  fa: 'envelope',
  label: g.tr('Email',{es:'Enviar correo'}),
  fn: function(table,id){
    let _this = table
    g.loader()
    url = 'cm/email_form/'+table.name+'/'+id
    g.get(url, function(data) {
      g.loader(false)
      g.dialog({title:g.tr('Send email', {es:'Enviar correo'}), class:'lightscreen large',body:data,type:'modal',buttons:'popup_email'})
        transformClassComponents()
    })
  }
}

gtableCommand.whatsapp = {
  fa: 'whatsapp',
  label: 'Whatsapp',
  fn: function(table,id) {
    for(i=0;i<table.items.length;i++) if(table.items[i].id == id) {
      //x = table.data.fields.indexOf('contact_phone')
      phone = table.items[i].contact_phone
      if (phone=='' || phone===null) {
        //x = table.data.fields.indexOf('company_id')
        phone = table.items[i].company_id.phone??''
      }
    }
    if (phone=='' || phone===null) {
      g.alert('Empty phone')
    } else {
      phone = phone.replace(/\s/g, '')
      phone = phone.replace('+','')
      window.open("https://web.whatsapp.com/send?phone="+phone);
    }
  }
}

gtableDisplayType.user_photo = function(rv) {
  f = 'owner_id'
  if (typeof rv[f]=='undefined') {
    f = 'user_id'
    if (typeof rv[f]=='undefined') {
      f = 'assignee_id'
    }
  }
  if (rv[f]===null) return ''
  photo = rv[f].photo
  title = rv[f].username
  id = rv[f].id

  if(photo==null || photo.length==0) {
    let letter = ""
    if(typeof title!=='undefined' && title.length>0) {
      letter = title.toUpperCase()[0];
    }
    let color = ['red', 'lightseagreen', 'green', 'hotpink', 'darkorange', 'brown', 'blueviolet'][id%7]
    return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%;'+
    'width:32px;height:32px;background:'+color+'; color: white;\
    font-size: 20px; padding:6px; text-align: center; font-family: Arial;" title="'+title+'">'+letter+'</div>'
  }
  if (!photo.startsWith('https:') && !photo.startsWith('http:')) {
    photo = 'lzld/thumb?src='+photo+'&media_thumb=60'
  }
  return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%; '+
  'width:32px;height:32px;background:url('+photo+'); '+
  'background-position: center; background-size:cover;" title="'+title+'"></div>'
}


selectEmailTemplate = function(key) {
  if (key=='') return
  txt = formpopuptmp.querySelector('option[value="'+key+'"]').dataset.txt
  tinymce.get('formpopupmessage').setContent(txt)
  formpopupsubject.value = formpopuptmp.querySelector('option[value="'+key+'"]').dataset.subject
  formpopupmessage.value = txt
}


g.dialog.buttons.popup_update = {title:_e('Update'), fn:function(btn){
  form = g('.gila-popup form').last()
  form.getElementsByTagName("BUTTON")[0].click()
}};
g.dialog.buttons.popup_add = {title:_e('Create'), fn:function(btn){
  form = g('.gila-popup form').last()
  form.getElementsByTagName("BUTTON")[0].click()
}};
g.dialog.buttons.popup_invite = {title:_e('Invite',{es:'Invitar'}), fn:function(btn){
  form = g('.gila-popup form').last()
  form.getElementsByTagName("BUTTON")[0].click()
}};
g.dialog.buttons.popup_email = {title:_e('Send'), fn:function(btn){
  form = g('.gila-popup form').last()
  data = new FormData(form);
  data.append('message', tinymce.get('formpopupmessage').getContent())
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')

  g.ajax({method:'post',url:'cm/email_form/'+t+'/'+id,data:data,fn:function(data) {
    data = JSON.parse(data)
    g.closeModal();
    if (typeof data.error!='undefined') {
      g.alert(data.error, 'error')
    } else {
      g.alert(g.tr('Email sent', {es:'Correo enviado'}), 'success')
    }
  }})
}};

var g_form_popup_update_reload = false;
var g_form_popup_selected_field = null;

function g_form_popup_select_field(key) {
  g('.bulk-edit').style('display','none');
  g('.bulk-edit-'+key).style('display','block');
  g_form_popup_selected_field = key
}

function g_form_popup_update() {
  form = g('.gila-popup form').last()
  data = new FormData(form);
  if (g_form_popup_selected_field!==null) {
    tmp = data.get(g_form_popup_selected_field)
    data = new FormData()
    data.append(g_form_popup_selected_field, tmp)
    g_form_popup_selected_field = null
  }
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')
  for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].name == t) {
    _this = rootVueGTables[i]
  }

  url = 'cm/update_rows/'+t
  if(id=='new'||id==0) {
    if(typeof _this.filters!='undefined') {
      url += '?'+_this.filters
    }
  } else {
    url += '?id='+id
  }

  g.loader()
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    g.loader('false')
    if (g_form_popup_update_reload) {
      location.reload()
    }
    data = JSON.parse(data)
    if (data.error) {
      g.alert(data.error, 'error')
      return
    }
    if(id=='new' || id==0) {
      _this.items.unshift(data.items[0])
      if (_this.sort!='undefined') {
        _this.load_page()
      }
      edit_popup_app.id = _this.items[0].id
      if(typeof _this.table.children!='undefined' && _this.table.children!=null && _this.table.children.length>0) setTimeout(function(){
        document.getElementById("edit_popup_child").scrollIntoView();
        g("button[data-id='popup_add']").remove()
      }, 100)
      for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].name != _this.name) {
        rootVueGTables[i].load_page()
      }
    } else {
      if (typeof _this.update_rows == 'undefined') {
        _this.update_rows(data)
      }
      if (_this.layout=='kanban') {
        g.loader()
        _this.load_page()
      }
    }
  }})

  if((id=='new'||id==0) && typeof _this.table.children!='undefined') {
    return
  }

  g.closeModal();
} 

function g_form_popup_invite() {
  g_form_popup_update()
} 


gtableTool.edit = {
  fa: "pencil", label: _e("Edit"),
  permission: 'update',
  fn: function(table) {
    let _this = table
    ids = table.selected_rows.join()
    url = 'cm/edit_bulk/'+_this.name+'?id='+ids+'&callback=g_form_popup_update'
    g.get(url, function(data){
      g.dialog({title:g.tr('Edit Registry'), class:'lightscreen small',body:data,type:'modal',buttons:'popup_update'})
      formId = '#'+table.name+'-edit-item-form'

      edit_popup_app = new Vue({
        el: formId,
        data: {id:ids,formValue:[]}
      })
      transformClassComponents()
      if(g(formId+' input').all.length>1) {
        g(formId+' input').all[1].focus()  
      }
    })
  }
}
gtableTool.add = {
  fa: "plus", label: _e("Create"),
  permission: 'create',
  fn: function(table) {
    let _this = table
    _this.edititem = 'new'
    _this.edit_html = "Loading..."
    if(typeof _this.filters=='undefined') _this.filters=''
    g.get('cm/edit_form/'+_this.name+_this.filters, function(data){
      _this.edit_html = data
    })
  }
}
gtableTool.add_row = {
  fa: 'plus',
  label: _e('Create'),
  permission: 'create',
  fn: function(table) {
    let _this
    _this = table
    _this.edit_html = _e("Loading")+'...'
    g.post('cm/insert_row/'+_this.name, _this.query,function(data){
      data = JSON.parse(data)
      if(typeof _this.items=='undefined') {
        _this.items = [data.items[0]]
      } else _this.items.unshift(data.items[0])
    })
  }
}

gtableTool.include_popup = {
  fa: 'plus',
  label: _e('Add', {es:'Agregar'}),
  permission: 'create',
  fn: function(table) { add_popup_dialog(table) },
}
gtableTool.add_popup = {
  fa: 'plus',
  label: _e('Create'),
  permission: 'create',
  fn: function(table) { add_popup_dialog(table) },
}
gtableTool.add_popupsm = {
  fa: 'plus',
  label: _e('Create'),
  permission: 'create',
  fn: function(table) { add_popup_dialog(table, 'small') },
}
function add_popup_dialog(table,cl='large') {
  if(typeof table.filters=='undefined') table.filters=''
  href = 'cm/edit_form/'+table.name+'?callback=g_form_popup_update'+table.filters;
  g.get(href, function(data){
    g.dialog({title:g.tr('New Registry'), class:cl,body:data,type:'modal',buttons:'popup_add'})
    formId = '#'+table.name+'-edit-item-form'
    textarea = g('#gila-popup textarea').first()
    formValues = []
    if (typeof g(formId).all[0].dataset.values!='undefined') {
      formValues = JSON.parse(g(formId).all[0].dataset.values)
    }
    if (!textarea || !textarea.innerHTML.includes('{{')) {
      edit_popup_app = new Vue({
        el: formId,
        data: {id:0,formValue:formValues}
      })
    }
    transformClassComponents()
    g(formId+' input').all[1].focus()
  },function(data){
    data = JSON.parse(data)
    g.alert(data.error)
  })
}
gtableTool.csv = {
  fa: 'arrow-down', label: 'Csv',
  fn: function(table) {
    window.location.href = 'cm/csv/'+table.name+'?'+table.query;
  }
}
gtableTool.log_selected = {
  fa: 'arrow-down', label: 'Log',
  fn: function(table) {
    console.log(table.selected_rows);
  }
}
gtableTool.delete = {
  fa: 'arrow-down',
  label: _e('Delete'),
  permission: 'delete',
  fn: function(table) {
    let _this = table
    if(confirm(_e("Delete registries?"))) g.ajax({
      url: 'cm/delete?t='+_this.name,
      data: {id:table.selected_rows.join()},
      method: 'post',
      fn: function(data) {
        _this.selected_rows = []
        _this.load_page()
      }
    });
  }
}
gtableTool.delete_all = {
  fa: 'arrow-down',
  label: _e('Delete all'),
  permission: 'delete',
  fn: function(table) {
    let _this = table
    if(confirm(_e("Delete all old registries?"))) g.ajax({
      url: 'cm/delete_all?t='+_this.name,
      method: 'post',
      fn: function() {
        _this.load_page()
      }
    });
  }
}
gtableTool.merge = {
  fa: 'arrow-down',
  label: _e('Merge'),
  permission: 'delete',
  fn: function(table) {
    let _this = table
    if(confirm(_e("Merge registries?"))) g.ajax({
      url: 'cm/merge?t='+_this.name,
      data: {id:table.selected_rows.join()},
      method: 'post',
      fn: function(data) {
        if (data.error) {
          g.alert(data.error, 'warning')
        } else {
          _this.selected_rows = []
          _this.load_page()
        }
      }
    });
  }
}

gtableTool.uploadcsv = {
  fa: 'arrow-up',
  permission: 'create',
  label: _e('Upload')+' CSV',
  fn: function(table) {
    bodyMsg = '<div class="p-4"><h3>1. '+_e('_uploadcsv_step1')+'</h3>'
    bodyMsg += " <a href='cm/get_empty_csv/"+table.name+"'>"+_e('Download')+'</a>'
    bodyMsg += '<h3>2. '+_e('_uploadcsv_step2')+'</h3>'
    bodyMsg += "<br><input type='file' id='g_file_to_upload' data-table='"+table.name+"'>"
    bodyMsg += '<h3>3. '+_e('_uploadcsv_step3')+'</h3>'
    bodyMsg += " <span class='btn btn-primary' onclick='upload_csv_file()'>"+_e('Upload')+'</span> </div>'
    g.dialog({title:_e("Upload")+' CSV', body:bodyMsg, buttons:'',type:'modal', class:'large', id:'upload_csv_dialog'})
  }
}
gtableTool.upload_csv = gtableTool.uploadcsv
gtableTool.addfrom = {
  fa: 'plus', label: _e('New from'),
  fn: function(table) {
    let _table
    _table = table.table
    g.post('cm/select_row/'+_table.tool.addfrom[0],
      'list='+_table.tool.addfrom[1]+'&formToken='+csrfToken, function(gal){
      g.dialog({title:_e("Select"),body:gal,buttons:'select_row_source',type:'modal',class:'large',id:'select_row_dialog'})
      app.table_to_insert = _table.name
    })
  }
}
gtableTool.approve = {
  fa: 'check', label: _e('Approve'),
  fn: function(table) {
    if(typeof table.table.approve=='undefined') {
      alert('table[approve] is not set')
      return
    }
    let _this = table
    data = {}
    data[table.table.approve[0]] = table.table.approve[1]
    g.ajax({
      url: 'cm/update_rows?t='+_this.name+'&id='+table.selected_rows.join(),
      data: data,
      method: 'post',
      fn: function(data) {
        _this.selected_rows = []
        _this.load_page()
      }
    });
  }
}

g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 300,
  remove_linebreaks : false,
  document_base_url: ".",
  verify_html: false,
  cleanup: true,
  plugins: ['code codesample table charmap image media lists link emoticons paste'],
  menubar: true,
  //entity_encoding: 'raw',
  toolbar: 'formatselect bold italic | bullist numlist outdent indent | link image table | alignleft aligncenter alignright alignjustify',
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },
}

// Translation
function _e(phrase)
{
  if(typeof lang_array!='undefined') if(typeof lang_array[phrase]!='undefined') return lang_array[phrase];
  return phrase;
}



g.dialog.buttons.select_path = {
  title:'Select',fn: function(){
    let v = g('#selected-path').attr('value')
    if(v!=null) g('[name=p_img]').attr('value', base_url+v)
    g.closeModal('media_dialog');
  }
}
g.dialog.buttons.select_path_post = {
  title:'Select', fn: function() {
    let v = g('#selected-path').attr('value')
    if(v!=null) {
      if (v.startsWith('https:') || v.startsWith('http:')) {
        input_filename(v);
      } else {
        input_filename(base_url+v);
      }
    }
    g.closeModal('media_dialog');
  }
}
g.dialog.buttons.select_row_source = {
  title:'Select', fn: function() {
    let v = g('tr.selected>.id').attr('value')
    el = g(input_select_row).all[0]
    el.value = v
    g('#select_row_dialog').parent().remove();
  }
}

function open_gallery_post() {
  g.post('admin/media','g_response=content&formToken='+csrfToken,function(gal){ 
    g.dialog({title:'Media gallery',body:gal,buttons:'select_path_post',type:'modal',class:'large',id:'media_dialog','z-index':99999})
  })
}
var open_select_row_clicked = false
function open_select_row(rid,table,name) {
  input_select_row = rid;
  if(open_select_row_clicked) return;
  open_select_row_clicked = true;

  g.loader()
  g.post('cm/select_row/'+table,'',function(gal){
    open_select_row_clicked = false;
    g.loader(false)
    g.dialog({title:_e(name),body:gal,buttons:'select_row_source',type:'modal',id:'select_row_dialog',class:'large'})
    divId = '#gtable_select_row'
    select_popup_app = new Vue({
      el: divId,
      data: {}
    })
    transformClassComponents()
  })
}

function upload_csv_file() {
  let fm = new FormData()
  fm.append('file', g.el('g_file_to_upload').files[0]);
  table = g.el('g_file_to_upload').getAttribute('data-table');
  g.loader()
  url = 'cm/upload_csv/'+table+'?'+app.$refs.gtable.query
  g.ajax({url:url, method:'POST', data:fm, type:'json', fn:function(data){
    g.loader(false)
    g.alert(data)
    if (data.success==true) {
      app.$refs.gtable.load_page()
      g.closeModal('upload_csv_dialog')
    }
  }})
}

g.click(".select-row",function(){
  g('.select-row').removeClass('g-selected');
  g(this).addClass('g-selected');
  g('#selected-row').attr('value',this.getAttribute('data-id'))
})
