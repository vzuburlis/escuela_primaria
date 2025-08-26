<?=View::css('lib/calendar/core/main.min.css')?>
<?=View::css('lib/calendar/timegrid/main.min.css')?>
<?=View::script('lib/calendar/timegrid/main.min.js')?>
<?=View::css('lib/calendar/daygrid/main.min.css')?>
<?=View::script('lib/calendar/core/main.min.js')?>
<?=View::script('lib/calendar/interaction/main.min.js')?>
<?=View::script('lib/calendar/daygrid/main.min.js')?>
<?=View::script('lib/vue/vue.min.js');?>
<?=View::script('core/admin/vue-components.js')?>
<?=View::script('core/lang/content/' . Config::get('language') . '.js')?>
<?php
$calendarTypes = [
  'email' => [
    'emoji' => '✉','color' => 'green'
  ],
  'videocall' => ['emoji' => '📹','color' => 'lightblue'],
  'call' => ['color' => 'coral'],
  'note' => ['color' => 'lightgrey'],
  'comment' => ['emoji' => '💬','color' => 'orange']
];

$table_activity = $table . '_activity';
if ($table == 'crm_opportunity') {
    $table_activity = 'crm_activity';
}
$events = DB::getAssoc("SELECT 
(SELECT COALESCE(NULLIF(`name`,''),CONCAT('#',content_id))  FROM {$table} WHERE {$table}.id=content_id) as title,
FROM_UNIXTIME(date) as start,FROM_UNIXTIME(date+300) as end,type,
CONCAT('{$baseUrl}',content_id) as url
FROM $table_activity;");
//COALESCE(c.name,'#',t.id) as title,
//FROM_UNIXTIME(date) as start,FROM_UNIXTIME(date+300) as end,type,
//CONCAT('{$baseUrl}',content_id) as url
//FROM $table_activity t,contact c WHERE c.id=t.contact_id;");
?>
<style>#calendar{max-width:1100px;margin:auto}</style>
<script>
var calendar;
var calendarTypes = <?=json_encode($calendarTypes)?>;
document.addEventListener('DOMContentLoaded', function() {

var calendarEl = document.getElementById('calendar');

  calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'interaction', 'dayGrid' ],
    header: {
      left: 'prevYear,prev,next,nextYear today',
      center: 'title',
      right: 'dayGridMonth,dayGridWeek,dayGridDay'
    },
    defaultDate: '<?=date("Y-m-d")?>',
    initialView: 'resourceTimelineWeek',
    navLinks: true, // can click day/week names to navigate views
    editable: true,
    eventLimit: true, // allow "more" link when too many events
    events: <?=json_encode($events)?>,
    eventRender: function(o) {
      for(i in calendarTypes) if(o.event.extendedProps.type==i) {
        if(calendarTypes[i].emoji) o.el.innerHTML = "<span style='position:absolute;right:0'>"+calendarTypes[i].emoji+"</span>"+o.el.innerHTML;
        if(calendarTypes[i].color) {
          o.el.style.background = calendarTypes[i].color
          o.el.style.borderColor = calendarTypes[i].color
        }
        if(calendarTypes[i].borderColor) o.el.style.borderColor = calendarTypes[i].borderColor
        if(calendarTypes[i].textColor) o.el.style.color = calendarTypes[i].textColor
        if(calendarTypes[i].daily) {
          o.event.daily=true
          o.el.innerHTML = o.event.title;
        }
      }
    },
    //dateClick: function(i) {
    //  let _i = i
    //  g.loader()
    //  g.get('cm/edit_form/<?=$table?>?id=new', function(x) {
    //    g.loader(false)
    //    g.modal({title:g.tr("New Registry"), body:x, buttons:"update"});
    //    calendar_app = new Vue({el: '#<?=$table?>-edit-item-form'})
    //    g('#<?=$table?>-edit-item-form input[name="start"]').all[0].value = _i.dateStr+"T00:00:00"
    //  })
    //},
    eventClick: function(i){
      if (i.event.url!='' && i.event.url!==null) {
        location.href = i.event.url
        //window.open(i.event.url)
        return
      }
      g.loader()
      g.get('cm/edit_form/<?=$table?>?id='+i.event.id, function(x){
        g.loader(false)
        g.modal({title:g.tr("Edit Registry"), body:x, buttons:"update delete"});
        calendar_app = new Vue({el: '#<?=$table?>-edit-item-form'})
      })
    },
    eventDrop: function(i){
      let postData = "start="+i.event.start.toISOString()
      if(i.event.end) postData = postData+"&end="+i.event.end.toISOString()
      g.post('cm/update_rows/<?=$table?>?id='+i.event.id, postData);
    }
  });
  calendar.render();

  g.dialog.buttons.delete = {title:'Delete', class:'error', fn:function(e){
    id_name = '<?=$table?>-edit-item-form'  
    form = document.getElementById(id_name)
    let irow = form.getAttribute('data-id')
    data = new FormData()
    data.append('id',irow)
    g.ajax({method:'post',url:'cm/delete/<?=$table?>',data:data,fn:function(data) {
      data = JSON.parse(data)
      event = calendar.getEventById(data.id)
      event.remove()
    }})
    g('#gila-popup').parent().remove();
  }};

  g.dialog.buttons.update = {title:'Update', fn:function(e){
    id_name = '<?=$table?>-edit-item-form'  
    form = document.getElementById(id_name)
    form.getElementsByTagName("BUTTON")[0].click()
  }};
  

});

function <?=$table?>_action() {
  id_name = '<?=$table?>-edit-item-form'  
  form = document.getElementById(id_name)
  let irow = form.getAttribute('data-id')
  if(irow===0) irow='new'
  data = new FormData(form);
  let inputs = form.querySelectorAll('[name]')

  let _this = this
  if(irow>0) {
    url = 'cm/update_rows/<?=$table?>?id='+irow
  } else {
    url = 'cm/update_rows/<?=$table?>'
  }
  g.ajax({method:'post',url:url,data:data,fn:function(x) {
    data = JSON.parse(x)
    if(irow>0) {
      event = calendar.getEventById(data.items[0][0])
      event.setStart(data.items[0]['start'])
      event.setProp('title', data.items[0]['title'])
      event.setExtendedProp('url', data.items[0]['url'])
      event.setExtendedProp('type', data.items[0]['type'])
      event.setEnd(data.items[0]['end'])
    } else {
      calendar.addEvent({
        id: data.items[0].id,
        start: data.items[0].start,
        title: data.items[0].title,
        url: data.items[0].url,
        end: data.items[0].end,
        type: data.items[0].type
      })
    }
  }})
  g('#gila-popup').parent().remove();
};

</script>

<div id='calendar'></div>
