<?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
<?=View::script('lib/vue/vue.min.js');?>
<?=View::script('core/Sortable.min.js');?>
<?=View::script('core/vuedraggable.umd.min.js');?>

<?php View::alerts()?>
<style>#main-wrapper>div{background: inherit !important;border:none}
.grid-widgets{grid-template-columns:1fr 1fr 1fr 1fr 1fr 1fr; display:grid; gap:1em}
.grid-widgets>div{border:1px solid lightgrey; padding:1em;border-radius:8px}
/*.widget-area-dashboard .widget-mysql-chart{grid-column: 1/-1;}*/
.sortable-chosen {
  opacity:0.8;
  background-color: #d0d0ff!important;
  cursor: move;
}
.selected {
  background-color: #d0d0ff!important;
}
</style>

<?php if (Session::hasPrivilege('admin')) : ?>
<div class="d-block text-align-right mb-1">
<a class="btn" href="admin/content/widget?area=dashboard"><?=__('Edit widgets', ['es' => 'Editar widgets'])?></a>
</div>
<?php endif; ?>

<div class="widget-area-dashboard wrapper d-grid " id=main>
  <draggable group="widgets" @start="startDrag" @end="endDrag" class="gs-grid" :options="{delay:300}">
  <?php View::widgetArea('dashboard'); ?>
  </draggable>
</div>

<script>
app = new Vue({
  el: '#main',
  data: {
    showWidgets: false,
    widgets: <?=json_encode(Config::getList('widgets'))?>,
    selectedID: null
  },
  methods: {
    startDrag: function(ev) {
      this.selectedID = ev.item.getAttribute('data-id')
      //ev.item.classList.add('selected')
    },
    endDrag: function(ev) {
      //ev.item.classList.remove('selected')
      steps = ev.newIndex-ev.oldIndex
      g.loader()
      g.postJSON('cm/movePos/widget?id='+this.selectedID, {steps:steps}, function() {
        g.loader(false)
      })
    },
  }
})
</script>
