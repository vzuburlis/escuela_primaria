<div id="app">
<?=View::renderFile($wrapped_file, $wrapped_pack)?>
</div>
<script>
app = new Vue({
  el: '#app',
  mixins: [mixins],
  data: <?=json_encode(View::$part)?>,
})

var $gila = {}
$gila.data = function(url, params, fn) {
  g.postJSON(url, params, function(response) {
    for (i in response) {
      if (typeof app[i]!='undefined') {
        app[i] = response[i]
      }
    }
  })
}

<?php include Config::src() . $wrapped_pack . '/views/' . $wrapped_file . '.js' ?>
</script>
