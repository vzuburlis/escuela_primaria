function gItem(x) {
  this.all = x;
}


g = function(x) {
  if (typeof x !== 'undefined') {
    if(typeof x === 'object') return new gItem([x]);
    all = document.querySelectorAll(x);
    return new gItem(all);
  }
}

g.language = document.documentElement.lang;
g.click_q = new Array();
g.swipe_q = {up:[], down:[], left:[], right:[]};
g.url_params = new URLSearchParams(window.location.search);
g.postAlertMessage = null;
g.el = function(id) {
  return document.getElementById(id);
}

// Prototypes


gItem.prototype.html = function(html) {
  if (typeof html === 'undefined') return this.all[0].innerHTML
  for(let value of this.all) {
    value.innerHTML = html
  }
  return this
}

gItem.prototype.attr = function(attr,val) {
  if (typeof val === 'undefined') return this.all[0].getAttribute(attr)
  for(let value of this.all) {
    value.setAttribute(attr, val)
  }
  return this
}

gItem.prototype.style = function(attr,val) {
  if (typeof val === 'undefined') return this.style[attr]
  for(let value of this.all) {
    value.style[attr] = val
  }
  return this
}

gItem.prototype.remove = function() {
  for(let value of this.all) {
    value.parentElement.removeChild(value)
  }
}

gItem.prototype.findUp = function(q) {
  var x = document.querySelectorAll(q);
  _this = this;
  while (this.all[0]) {
    this.all[0] = this.all[0].parentNode;
    for (xi = 0; xi < x.length; xi++) {
      if(this.all[0] && this.all[0].isSameNode(x[xi])) return this;
    }
  }
  _this.all=[]
  return _this;
}

gItem.prototype.last = function() {
  return this.all[this.all.length-1];
}
gItem.prototype.first = function() {
  if(this.all.length===0) return null;
  return this.all[0];
}

gItem.prototype.parent = function() {
  if(this.all.length===0) return this;
  this.all = [this.all[0].parentNode];
  return this;
};
gItem.prototype.children = function() {
  var n=this.all[0].firstChild;
  this.all = [];
  for ( ; n; n = n.nextSibling )
     if ( n.nodeType == 1) this.all.push( n );
  return this;
};
gItem.prototype.find = function(x) {
  if(typeof this.all[0]!='undefined') this.all = this.all[0].querySelectorAll(x);
  return this;
};

gItem.prototype.append = function(html,data,data_timeout) {
  for(let value of this.all) {
    let template = document.createElement('template');
    template.innerHTML = html;
    value.appendChild(template.content.firstChild)

    if (typeof data !== 'undefined') {
      for(let attr in data) template.content.firstChild[attr] = data[attr];
		}

    if (typeof data_timeout !== 'undefined') setTimeout(function () {
      for(let attr in data_timeout) {
        template.content.firstChild[attr] = data_timeout[attr]
      }
    }, 100)
  }
  return this
}
gItem.prototype.prepend = function(html,data) {
  for(let value of this.all) {
    let template = document.createElement('template');
    template.innerHTML = html;
    value.insertBefore(template.content.firstChild,value.childNodes[0])

    if (typeof data !== 'undefined') {
      for(let attr in data) template.content.firstChild[attr] = data[attr];
    }
  }
  return this
}


gItem.prototype.addClass = function(x) {
  for(let value of this.all){
    value.classList.add(x)
  }
}

gItem.prototype.removeClass = function(x) {
  for(let value of this.all){
    value.classList.remove(x)
  }
}
gItem.prototype.toggleClass = function(x) {
  for(let value of this.all){
    value.classList.toggle(x);
  }
}
gItem.prototype.hasClass = function(x) {
  for(let value of this.all){
    if(value.classList.contains(x)) return true;
  }
  return false;
}

gItem.prototype.loader = function(set=true) {
  if(set==true) {
    for(let value of this.all){
      g(value).append('<div class=\'gila-darkscreen gila-loader\'><img src="assets/core/ajax-loader.gif" class="centered"></div>');
    }
  } else {
      g('.gila-loader').remove()
  }
}

gItem.prototype.fullscreen = function() {
  var el = this.all[0]
  if (el.requestFullscreen) {
      el.requestFullscreen();
  } else if (el.webkitRequestFullscreen) {
      el.webkitRequestFullscreen();
  } else if (el.msRequestFullscreen) {
      el.msRequestFullscreen();
  }
}


g.click = function(query,fn) {
  g.click_q[query] = fn;
}
g.swipe = function(query, direction, fn) {
  g.swipe_q[direction][query] = fn;
}

g.run_queries = function(el, queries) {
  for(let i in queries) if(isNaN(i)) {
    var x = document.querySelectorAll(i);
    for (xi of x) {
      do {
        if(el.isSameNode(xi)) {
          el.fn = queries[i].bind(el);
          el.fn()
        }
      } while(el=el.parentNode);
    }
  }
}

g.sx = null;
g.sy = null;
function eventUnify(e) { return e.changedTouches ? e.changedTouches[0] : e };
g.swipestart = function(e) {
  g.sx = eventUnify(e).clientX;
  g.sy = eventUnify(e).clientY;
  g.target = eventUnify(e).target
}
g.swipeend = function(e) {
  if(g.sx!==null || g.sy!==null) {
    let dx = eventUnify(e).clientX - g.sx;
    let dy = eventUnify(e).clientY - g.sy;
    g.sx = null
    g.sy = null
    if(Math.abs(dy)<10 && Math.abs(dx)<10) return

    if(Math.abs(dy)>Math.abs(dx)) {
      if(dy>0) g.run_queries(g.target, g.swipe_q.up);
      if(dy<0) g.run_queries(g.target, g.swipe_q.down);
    } else {
      if(dx>0) g.run_queries(g.target, g.swipe_q.right);
      if(dx<0) g.run_queries(g.target, g.swipe_q.left);
    }
  }
};
document.onclick = function(e) {
  if (e.target.classList.contains('btn-submit')) {
    g.postBtn(e.target)
  }
  for(let i in g.click_q) if(isNaN(i)) {
    var x = document.querySelectorAll(i);
    for (xi of x) {
      if(e.target.isSameNode(xi)) {
        e.target.fn = g.click_q[i].bind(e.target);
        e.target.fn()
      } else {
        el= e.target;
        while(el=el.parentNode) {
          if(el.isSameNode(xi)) {
            el.fn = g.click_q[i].bind(el);
            el.fn()
          }
        }
      }
    }
  }
};

document.addEventListener('touchstart', g.swipestart, false);
document.addEventListener('touchend', g.swipeend, false);


gItem.prototype.load = function(path) {
  var xhttp = new XMLHttpRequest();
  var _g = this;
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      for(let value of _g.all) value.innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", path, true);
  xhttp.send();
}


g.ajax = function(args) {
  var xhttp = new XMLHttpRequest();

  xhttp.open(args.method, args.url, true);
  _fn = args.fn
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4) {
      response = this.responseText
      if(typeof args.type != 'undefined' && args.type == 'json') {
        if (response=='') {
          response = [];
        } else {
          try {
            response = JSON.parse(this.responseText);
          } catch(e) {
            console.error(e)
          }
        }
      }
      if (this.status > 199 && this.status < 300) {
        if(args.fn) args.fn(response);
      } else {
        if(!args.error) {
          args.error = function(response) {
            g.loader(false)
            if (response.error) {
              g.alert(response.error, 'error')
            } else {
              console.error(response)
            }
          }
        }
        args.error(response);
      }
    }
  };

  if(typeof args.data==='object' && args.data.constructor.name=='Object') {
    if (args.type=='json') {
      args.data = JSON.stringify(args.data)  
      args.header = "application/json"
    } else {
      args.header = "application/x-www-form-urlencoded"
      args.data = Object.keys(args.data).map((key) => {
        return encodeURIComponent(key) + '=' + encodeURIComponent(args.data[key])
      }).join('&');
    }
  }
  if(typeof args.header!='undefined') {
    xhttp.setRequestHeader("Content-type", args.header);
  }
  if(typeof args.headers!='undefined') for(i in args.headers) {
    xhttp.setRequestHeader(i, args.headers[i]);
  }
  xhttp.send(args.data);
}

g.post = function(path,params,fn,error=null) {
  g.ajax({url:path,method:'POST',header:"application/x-www-form-urlencoded",data:params,fn:fn,error:error})
}
g.postForm = function(formId,fn,error=null) {
  let el = document.getElementById(formId);
  let fm = new FormData(el);
  g.ajax({url:el.action,method:'POST',data:fm,fn:fn,error:error})
}
g.validateForm = function(el) {
  if (typeof el=='string') {
    el = document.getElementById(el)
  }
  const re = /\S+@\S+\.\S+/
  errors = []
  elements = el.querySelectorAll("[name]")
  elements.forEach(function (element, index, listObj) {
    if (typeof element.checkValidity!='undefined') {
      element.reportValidity()
      return;
    }
    ename = element.name
    label = element.name
    if (element.parentNode && element.parentNode.tagName=='LABEL') {
      label = element.parentNode.innerText
    }
    if (element.required && (element.value==''||element.value==null)) {
      errors.push({
        label:label,
        name:ename,
        message: g.tr('This field is required',{es:'El campo esta requerido'})
      })
    }
    if (element.type=='email' && !re.test(element.value)) {
      errors.push({
        label:label,
        name:ename,
        message: g.tr('Not an email format',{es:'No es formato de correo'})
      })
    }
  })
  return errors
}
g.reportValidity = function(el) {
  if (typeof el=='string') {
    el = document.getElementById(el)
  }
  const re = /\S+@\S+\.\S+/
  response = true
  elements = el.querySelectorAll("[name]")
  elements.forEach(function (element, index, listObj) {
    if (typeof element.reportValidity!='undefined') {
      if (element.reportValidity()==false) {
        response = false
      }
    }
  })
  return response
}
g.postBtn = function(el) {
  postBtn_div = g(el).findUp('section').first();
  if (postBtn_div===null) {
    postBtn_div = g(el).findUp('div.container').first();
    if (postBtn_div===null) {
      g.alert("Button should be inside a section or container", 'error')
    }
  }
  let inputElements = postBtn_div.querySelectorAll("[name]");
  let fm = {}
  for (input of inputElements){
    fm[input.name] = input.value
  }
  let form = g(el).findUp('form').first();
  if (form && typeof form.checkValidity!='undefined') {
    err = g.reportValidity(form)
  } else {
    err = g.reportValidity(postBtn_div)
  }
  if (err==false) {
    return
  }
  if (err.length>0) {
    if (form && typeof form.checkValidity!='undefined' && !form.checkValidity()) {
      form.reportValidity()
    } else {
      g.alert(err[0].label+': '+err[0].message, 'error')
    }
    return
  }

  if (form) {
    //return
  }

  // add url params in submission
  g.url_params = new URLSearchParams(window.location.search);
  if (el.dataset.url_params) for(param of el.dataset.url_params.split(',')) {
    if (!fm[param] && g.url_params.get(param)) {
      fm[param] = g.url_params.get(param)
    }
  }
  // empty input fields
  for (input of inputElements) if (typeof input.type=='undefined' || input.type!='hidden') {
    input.value = input.getAttribute('data-default')??''
  }

  if (el.dataset.msg && el.dataset.msg!='') {
    g.postAlertMessage = el.dataset.msg
  }

  if (el.dataset.callback_url && el.dataset.callback_url!='') {
    g.postRedirect(el.dataset.action, fm, el.dataset.callback_url)
  } else {
    g.postAlert(el.dataset.action, fm)
  }
}

g.get = function(path,fn,error=null){
  g.ajax({url:path,method:'GET',fn:fn,error:error})
}

g.postJSON = function(path,data,fn=null,error=null){
  g.ajax({url:path,method:'POST',header:'application/json',data:data,fn:fn,type:'json',error:error})
}

g.postAlert = function(path, data={}, callback=null){
  _callback = callback
  g.ajax({url:path,method:'POST',header:'application/json',data:data,type:'json',fn:function(data) {
    if (data.success==true) {
      if (typeof data.message=='undefined') {
        data.message = g.tr('Success',{es:'Exito'})
        if (g.postAlertMessage!=null) {
          data.message = g.postAlertMessage
        }
      }
      g.alert(data.message, 'success', _callback)
    } else {
      g.alert(data.error, 'error')
    }
  },error:function(data){
    g.alert(data.error, 'error')
  }})
}

g.postRedirect = function(path, data={}, url){
  let _url = url
  g.ajax({url:path,method:'POST',header:'application/json',data:data,type:'json',fn:function(data) {
    if (data.success==true) {
      if (!_url.startsWith('https://')) {
        _url = window.location.origin+_url
      }
      location.href = _url
    } else {
      g.alert(data.error, 'error')
    }
  },error:function(data){
    g.alert(data.error, 'error')
  }})
}

g.getJSON = function(path,fn,error=null){
  g.ajax({url:path,method:'GET',fn:fn,error:error,header:'application/json',type:'json'})
}

g.popup = function(html,col){
  if (typeof col != 'undefined') {
    g.dialog({body:html,class:col});
  } else g.dialog({body:html});
}

g.modal = function(p){
  p.type='modal'
  g.dialog(p)
}

g.dialog = function(p){
  var default_params = {class:'',escape:true,body:'',title:'',foot:'',img:'',buttons:'ok',type:'',id:'gila-popup'};
  for(i in default_params) if(typeof p[i] == 'undefined') p[i] = default_params[i];
  closeModal = 'g.closeModal()'
  if(typeof p.id!='undefined' && p.id!='gila-popup') {
    closeModal = 'g.closeModal(\''+p.id+'\')'
  }
  closebtn = '<span class="closebtn" style="font-size:32px;padding:2px 6px;line-height:1" onclick="'+closeModal+'">×</span>';
  if(p.type=='modal') {
    document.body.style.overflowY = 'hidden'
  }

  if(p.title!='') p.title='<div class="title">'+p.title+'</div>'; else p.title='<div>&nbsp;</div>';

  p.class = g.dialogClass+' '+p.class;
  buttons='';
  if(typeof p.buttons!='undefined' && p.buttons!='') for(btni of p.buttons.split(' ')) {
    if(typeof g.dialog.buttons[btni] == 'undefined') {
      console.log('Dialog Button '+btni+' not defined');
    } else {
      btn = g.dialog.buttons[btni];
      if(typeof btn.class=='undefined') btn.class='btn-primary';
      if(typeof btn.fn=='undefined') btn.fn='';
      buttons+='<button data-id="'+btni+'" class="btn '+btn.class+'" onclick="g.dialog.buttons.'+btni+'.fn(this)">'+g.tr(btn.title)+'</button> ';
    }
  }
  p.foot=buttons+p.foot;
  if(p.foot!='') p.foot='<div class="foot mt-2">'+p.foot+'</div>';
  if(p.escape == false) {
    closebtn=''
  }

  if(p.body!='') p.body='<div class="body">'+p.body+'</div>';

  append='<div id=\''+p.id+'\' class="'+p.class+' gila-popup">'+closebtn+p.title+p.img+p.body+p.foot+'</div>';
  if(p.type=='modal'||p.type=='alert') {
    dsbg = ''
    if (!p.callback) {
      dsbg = '<div style="position:absolute;left:0;top:0;right:0;bottom:0" onclick="'+closeModal+'"></div>';
    }
    if(p['z-index'] != 'undefined') zindex = 'style="z-index:'+p['z-index']+'"'; else zindex='';
    g(document.body).append('<div class=\'gila-darkscreen\''+zindex+'>'+dsbg+append+'</div>');
  } else g(document.body).append(append)
}

g.closeModal = function(x=null) {
  if(x==null) {
    x = g('.gila-popup').last()
    if(x) x.parentNode.remove();
  } else {
    x = document.getElementById(x)
  }
  if(g(x).parent().hasClass('gila-darkscreen')) {
    x.parentNode.remove();
  } else {
    x.remove();
  }
  if(g('.gila-popup').all.length===0 && document.body!=null) {
    document.body.style.overflowY = 'auto'
  }
}

g.dialog.buttons = [];
g.dialog.buttons.ok = {title:'Ok',fn:function(e){
  g.closeModal()
}};
g.dialog.buttons.confirm_yes = {title:'Yes',fn:function(e){
  g.closeModal()
}};
g.dialog.buttons.confirm_no = {title:'No',fn:function(e){
  g.closeModal()
}};

g.iconsVersion='2'
g.icon = {
  '2': {
    success: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="60" height="60" viewBox="0 0 24 24" stroke-width="3" stroke="#00b341" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>',
    error: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="60" height="60" viewBox="0 0 24 24" stroke-width="3" stroke="#ff2825" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>',
    warning: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="60" height="60" viewBox="0 0 24 24" stroke-width="3" stroke="#ff9300" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>',
    notice: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="60" height="60" viewBox="0 0 24 24" stroke-width="3" stroke="#00abfb" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>',
  },
  '1': {
    success: "<i class=\'fa fa-check-circle-o fa-5x\' aria-hidden=\'true\' style=\'color:green\'></i><br>",
    error: "<i class=\'fa fa-times-circle-o fa-5x\' aria-hidden=\'true\' style=\'color:red\'></i><br>",
    warning: "<i class=\'fa fa-exclamation-triangle fa-5x\' aria-hidden=\'true\' style=\'color:yellow\'></i><br>",
    notice: "<i class=\'fa fa-exclamation-circle fa-5x\' aria-hidden=\'true\' style=\'color:blue\'></i><br>",
  }
}
g.dialogClass='bg-white'
g.template={}

g.success = function(html, callback=null) {
  g.alert(html, 'success', callback)
}

g.confirm = function(question, callback, type='warning') {
  g_prompt_callback = callback
  g.dialog({title:question, type:type, buttons:'confirm_yes confirm_yes'})
}

g.alert = function(html, type, callback=null) {
  if (typeof html=='undefined') {
    console.error('Alert text is undefined')
    return
  }
  foot=''; buttons='ok'; icon='';
  btntext = 'OK'
  if (typeof html=='object') {
    if (html.success==true) {
      if (typeof html.message!='undefined') {
        _h = '<h3>'+html.message+'</h3>'
        console.log(html)
        if (typeof html.subtext!='undefined') {
          _h += '<div>'+html.subtext+'</div>'
        }
        if (typeof html.btntext!='undefined') {
          btntext = html.btntext
        }
        html = _h
      } else {
        html = g.tr('Success')
      }
      type = 'success'
    } else {
      html = html.error
      type = 'error'
    }
  }
  if (typeof type !== 'undefined') {
    icon = g.icon[g.iconsVersion][type]
  }
  if (html && !html.startsWith('<')) {
    html = '<h2 style="color:var(--hfont)">'+html+'</h2>'
  }
  if (callback !== null) {
    foot = "<a class='btn btn-primary' onclick='g(\"#gila-darkscreen\").remove();"+callback+"'>"+btntext+"</a>";
    buttons='';
    g.modal({body:icon+html,class:'small text-align-center',escape:false,buttons:buttons,foot:foot,type:'alert',callback:callback});
  } else {
    g.dialog({body:icon+html,class:'small text-align-center',escape:false,buttons:buttons,foot:foot,type:'alert'});
  }
}

g.snackbar = function(html) {
  if(typeof gSnackbar!=='undefined') {
    gSnackbar.remove();
    clearInterval(gSnackbarInterval);
  }
  g(document.body).append('<div class="g-snackbar show" style="z-index:999999" id="gSnackbar">'+html+'</div>');
  gSnackbarInterval = setInterval(function(){
    gSnackbar.className = gSnackbar.className.replace("show", "");
    clearInterval(gSnackbarInterval);
  }, 3000);
}
g.copyCB = function(el) {
  msg = g.tr('Copied to clipboard', {es:'Copiado al portapapeles'})
  el.select();
  document.execCommand('copy');
  g.snackbar(msg)
}

var g_requiredGroup = new Array();
var g_baseUrl = "res/";
if(typeof requiredRes == 'undefined') {
  requiredRes = new Array();
}

g.require = function(res, callback = function(){ return }) {

  if(Array.isArray(res)) {
    var group_n = g_requiredGroup.length;
    g_requiredGroup[group_n] = { loaded:0, fn:callback };
    var gcall = "if(g_requiredGroup["+group_n+"].loaded == "+res.length+"){ g_requiredGroup["+group_n+"].fn(); alert('ok'); }else g_requiredGroup["+group_n+"].loaded++;";
    for(r=0; r<res.length; r++) {
      g.require( res[r],function(){ gila_group_callback(res.length,group_n); });
    };

    return;
  }

  var rRes = requiredRes[res];

  if(typeof rRes == 'undefined') {
    console.warn(res+" is not defined in require.js");
    requiredRes[res]={wjs:res};
    rRes = requiredRes[res];
  }

  if(rRes.loaded == true) {
    callback();
    return;
  }

  if(rRes.dep) {
    g.require(rRes.dep, function(){
      if(rRes.css) g.loadCSS(g_baseUrl+rRes.css);
      if(rRes.js) g.loadJS(rRes,callback);
      if(rRes.wjs) g.loadJS(rRes,callback);
    });
  }else{
    if(rRes.css) g.loadCSS(g_baseUrl+rRes.css);
    if(rRes.js) g.loadJS(rRes,callback);
    if(rRes.wjs) g.loadJS(rRes,callback);
  }
}

g.loadJS = function(res, callback = function(){return }) {

  if(typeof res.wjs == 'undefined') url = g_baseUrl+res.js; else url = res.wjs;

  var script = document.createElement("script")

  if(res.loaded == true){
    callback();
    return;
  }else{
    if(typeof res.callbacks == 'undefined') res.callbacks = new Array();
    res.callbacks.push(callback);
  }

  script.onload = function(){
    res.loaded = true;
    for(i in res.callbacks) res.callbacks[i]();
  };

  script.src = url;
  if(res.dev == 1) script.src += "?"+Math.random();

  if( res.loading != true ) {
    document.getElementsByTagName("head")[0].appendChild(script);
    console.log(url+" loaded");
    res.loading = true;
  }
}

g.loadCSS = function(url) {
  var fileref = document.createElement("link");
  fileref.setAttribute("rel", "stylesheet");
  fileref.setAttribute("type", "text/css");
  fileref.setAttribute("href", url);
  document.getElementsByTagName("head")[0].appendChild(fileref);
}

g.loader = function(set=true) {
  if(set==true) {
    g(document.body).append('<div class=\'gila-darkscreen gila-loader\'><img src="assets/core/ajax-loader.gif" class="centered"></div>');
  } else {
    g('.gila-loader').remove()
  }
}

g.tr = function(x, alt=null) {
  if(typeof lang_array!='undefined' && lang_array[x]) return lang_array[x]
  if(alt!==null && typeof alt[g.language]!='undefined') return alt[g.language]
  return x
}


g.months = {
  en: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
  es: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dic'],
}
g.getDate = function(time,format='M d Y') {
  if (time==0||time==null) return ''
  a = new Date(time * 1000);
  year = a.getFullYear();
  month = a.getMonth();
  date = a.getDate();
  lang = 'en';
  if (typeof g.months[g.language]!='undefined') lang = g.language
  if (format=='M Y') return g.months[lang][month]+' '+year
  return g.months[lang][month]+' '+date+' '+year
}
g.getDateTime = function(time,nl=false) {
  if (time==0||time==null) return ''
  a = new Date(time * 1000);
  year = a.getFullYear();
  month = a.getMonth();
  date = a.getDate();
  h = a.getHours();
  m = a.getMinutes();
  lang = 'en';
  sp = ', ';
  if (nl==true) sp='<br>'
  if (typeof g.months[g.language]!='undefined') lang = g.language
  return g.months[lang][month]+' '+date+' '+year+sp+('0'+h).slice(-2)+':'+('0'+m).slice(-2)
}

g.lzldElements = []

g.lazyLoad = function () {
  let imgs;
  imgs = document.getElementsByClassName('lazy');

  for(i=0; i<imgs.length; i++) if (!g.lzldElements.includes(imgs[i])) {
    let el,r;
    el = imgs[i]
    r = el.getBoundingClientRect();
	  if (r.bottom > 0 && r.right > 0 &&
	      r.top < (window.innerHeight || document.documentElement.clientHeight) &&
	      r.left < (window.innerWidth || document.documentElement.clientWidth) ) {
      g.lzldElements.push(el)

      if (el.getAttribute('data-src')) {
        el.src = imgs[i].getAttribute('data-src');
	    }
      if (el.getAttribute('data-bg')) {
	      el.style.background = imgs[i].getAttribute('data-bg');
	    }
      if (el.getAttribute('data-image')) {
	      el.style.backgroundImage = 'url('+imgs[i].getAttribute('data-image')+')';
	    }
      if (el.getAttribute('data-animation')) {
	      el.style.animation = imgs[i].getAttribute('data-animation');
	    }
      if (el.getAttribute('data-load')) {
        let xhttp = new XMLHttpRequest();
        let _el = el;
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            _el.innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", el.getAttribute('data-load'), true);
        xhttp.send();
      }
      if (el.getAttribute('data-load-more')) {
        let xhttp = new XMLHttpRequest();
        let _el = el;
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            _el.parentNode.innerHTML += this.responseText;
          }
        };
        xhttp.open("GET", el.getAttribute('data-load'), true);
        xhttp.send();
        el.remove();
      }
      if (el.getAttribute('data-counter')) {
        el.dataset.count = 0.0
        if (typeof lazyCounterInterval=='undefined') lazyCounterInterval = setInterval(function(){
          counters = document.querySelectorAll('[data-count]')
          for (j = 0; j < counters.length; ++j) {
            cj = counters[j]
            if (cj.hasAttribute('data-step')) {
              count = parseFloat(cj.dataset.count)+parseFloat(cj.dataset.step)
              current = Math.ceil(count)
              cj.innerHTML = cj.dataset.prefix+current+cj.dataset.suffix
              cj.dataset.count = count
              if (parseFloat(current) > parseFloat(cj.dataset.countmax)) {
                cj.innerHTML = cj.dataset.prefix+cj.dataset.countmax+cj.dataset.suffix
                cj.removeAttribute('data-countmax')
                cj.removeAttribute('data-step')
                cj.removeAttribute('data-count')
              }
            } else {
              ms = 2000
              if (cj.dataset.counter>0) ms = 2000/parseFloat(cj.dataset.counter)
              cj.dataset.count = 0.0
              mr = cj.innerHTML.match(/^(\D*)(.*?)(\D*)$/)
              if (!isNaN(mr[2])) {
                cj.dataset.countmax = mr[2]
                cj.dataset.prefix = mr[1]
                cj.dataset.suffix = mr[3]
                cj.dataset.step = parseFloat(mr[2])/(ms/40)
              }
            }
            if (counters.length==0) {
              clearInterval(lazyCounterInterval)
              console.log('lazyCounterInterval stopped')
            }
          }
        }, 40);
	      el.style.backgroundImage = 'url('+imgs[i].getAttribute('data-image')+')';
	    }
	  }
  }

  inp = document.getElementsByClassName('param-prefix');
  for(i=0; i<inp.length; i++) if (!g.lzldElements.includes(inp[i])) {
    g.lzldElements.push(inp[i])
	  inp[i].value = g.url_params.get(inp[i].getAttribute('name'));
    inp[i].setAttribute('data-default', inp[i].value);
  }

}

gila_group_callback = function(n,group_n) {
  g_requiredGroup[group_n].loaded++;
  if(g_requiredGroup[group_n].loaded == n){
    g_requiredGroup[group_n].fn();
    console.log(g_requiredGroup[group_n].loaded+'!');
  } else {
    console.log(g_requiredGroup[group_n].loaded);
  }
}

g.click('ul.g-nav>li>a', function(){
  this.parentNode.classList.toggle('open')
  others = g('ul.g-nav>li').all
  for(let other of others) if(other.tagName=='LI' && other!==this.parentNode){
    other.classList.remove('open')
  }
  g('ul.g-nav>li>ul>li').removeClass('open')
  g('.g-nav-mobile').removeClass('display')
})
g.click('ul.g-nav>li>ul>li>a', function(){
  this.parentNode.classList.toggle('open')
  others = g('ul.g-nav>li>ul>li').all
  for(let other of others) if(other.tagName=='LI' && other!==this.parentNode){
    other.classList.remove('open')
  }
  g('.g-nav-mobile').removeClass('display')
})

g.preventKeyDown = function(e) {
  if (e.ctrlKey && e.shiftKey) e.preventDefault()
  if (e.ctrlKey && e.which!='86' && e.which!='88' && e.which!='67') e.preventDefault()
}

setTimeout(function() { g.lazyLoad(); }, 10);
window.onload = function() {
  setTimeout(function() { g.lazyLoad(); }, 100);
}
window.onload = function() {
  setTimeout(function() { g.lazyLoad(); }, 1500);
}
window.addEventListener('scroll', g.lazyLoad);
window.addEventListener('touchmove', g.lazyLoad); //touch devices
