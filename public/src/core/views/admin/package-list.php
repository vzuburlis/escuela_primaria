<style>
.addon-i{opacity:0.2;margin-bottom:8px}.logo-2x{width:33px;height:33px;margin-bottom:8px}
.card i{margin-top:8px;margin-left:8px;}
.g-badge {
  vertical-align: top;
}
</style>
<?php
View::script('lib/vue/vue.min.js');
View::script('core/admin/content.js');
View::script('lib/CodeMirror/codemirror.js');
View::scriptAsync('lib/CodeMirror/htmlmixed.js');
View::scriptAsync('lib/CodeMirror/javascript.js');
View::cssAsync('lib/CodeMirror/codemirror.css');
View::scriptAsync("lib/tinymce5/tinymce.min.js");

$dir = Config::src() . '/';
$table = '';
$pn = 0;

function dl_btn($param, $class, $text)
{
    return "<a onclick='addon_download(\"$param\")' class='btn btn-sm btn-$class'>$text</a>";
}

if (FS_ACCESS && Package::check4updates()) {
    $upgrated = 0;
    $upgrateList = json_decode(file_get_contents(LOG_PATH . '/packages2update.json'), true);
    $upgrateN = count($upgrateList);

    foreach ($upgrateList as $newp => $newv) {
        if (is_string($newp)) {
            if (isset($packages[$newp])) {
                if (version_compare($newv, $packages[$newp]->version) == 1) {
                    $logo = $dir . "$newp/logo.png";
                    $alert = "<img src='lzld/thumb?src=$logo' style='width:40px;float:left'>&nbsp;&nbsp;<b>";
                    $alert .= $packages[$newp]->title . '</b> ' . __("is_available_on_version");
                    $alert .= " $newv &nbsp;&nbsp; " . dl_btn($packages[$newp]->package, 'warning', __('Upgrade'));
                    $alert .= '&nbsp;&nbsp;<a href="https://gilacms.com/addons/package/';
                    $alert .= $packages[$newp]->package . '" class="btn btn-info" target="_blank">' . __('Info') . '</a>';
                    View::alert('success', $alert);
                } else {
                    $upgrated++;
                }
            } else {
                $upgrateN--;
            }
        }
    }
    if ($upgrateN === $upgrated) {
        unlink(LOG_PATH . '/packages2update.json');
    }
}

if (FS_ACCESS) {
    $links = [
    ['Downloaded','admin/packages'],
    ['Newest','admin/packages/new']
    ]; ?>
  <ul class="g-nav g-tabs gs-12" id="addon-tabs"><?php
    foreach ($links as $link) {
        $active = (Router::path() == $link[1] ? 'active' : '');
        echo '<li class="' . $active . '"><a href="' . Config::url($link[1]) . '">' . __($link[0]) . '</a></li>';
    } ?>
    <form method="get" class="inline-flex" style="float:right" action="<?=Config::base('admin/packages/new')?>">
      <input name='search' class="form-control" value="<?=(isset($search) ? $search : '')?>">
      &nbsp;
      <button class="btn btn-primary" type='submit'><?=__('Search')?></button>
    </form>
  </ul>
    <?php
}
?>
<div class="container" id='packages-list'><?=View::alerts()?>
  <div class="row w-100">

<?php
foreach ($packages as $pkey => $p) {
    if ($p->package != 'core' || Config::get('env') == 'dev') {
        if (isset($p->lang)) {
            Config::addLang($p->lang);
        }
        $planAllows = (!isset($p->require_app) || Event::get('site_permission', true, $p->require_app));
        $premiumIcon = !isset($p->require_app) ? '' : Event::get('premium_icon', '', $p->require_app);

      // Border color
        if (file_exists($dir . $p->package)) {
            if (in_array($p->package, Config::getArray('packages'))) {
                $border = "border-success";
            } else {
                $border = "text-muted";
            }
        } else {
            $border = "border-2 border-danger";
        }
        echo "<div class='p-2 col-md-6'><div class='card p-3 h-100 text-dark border $border'>";

      // Logo
        echo '<div class="position-absolute">';
        if (file_exists($dir . "{$p->package}/logo.png")) {
            echo '<img class="fa fa-2x logo-2x" src="' . "lzld/thumb?src=src/{$p->package}/logo.png" . '"/>';
        } elseif (file_exists($dir . "{$p->package}/logo.svg")) {
            echo '<img class="fa fa-2x logo-2x" src="' . "lzld/thumb?src=src/{$p->package}/logo.svg" . '"/>';
        } elseif (isset($p->logo) && $p->logo != '') {
            echo '<img class="fa fa-2x logo-2x" src="' . ($p->logo) . '" />';
        } else {
            echo '<i class="fa fa-2x fa-dropbox"></i>';
        }
        echo '</div>';

      // Title & version
        $title = $p->title ?? $p->package;
        $vtxt = 'Version: ' . ($p->version ?? '');
        echo '<div style="margin-left:40px"><b title="' . $vtxt . '">' . $title . '</b> ' . $premiumIcon;

      // Description
        $desc = 'description_' . Config::lang();
        $desc = $p->$desc ?? ($p->description ?? '');
        echo '<br><small class="mb-3">' . $desc . '</small><br>';

      // Requirements
        echo (@$p->author ? '<span class="small d-block"><i class="fa fa-user"></i> ' . $p->author . '</span><br>' : '');
        if (isset($p->require)) {
            $rlist = [];
            foreach ($p->require as $req => $ver) {
                if ($packages[$req]) {
                    if ($req != 'core') {
                        $rlist[] = $packages[$req]->name;
                    }
                } elseif (FS_ACCESS) {
                    $rlist[] = '<span class=text-danger title="Install with composer">' . $req . "($ver)</span>";
                }
            }
            if (!empty($rlist)) {
                echo "<div class='small mb-2'>" . __('Requires', ['es' => 'Requiere']) . ": ";
                echo implode(', ', $rlist);
                echo "</div>";
            }
        }

      // Buttons
        if (file_exists($dir . $p->package)) {
            if (in_array($p->package, Config::getArray('packages')) || $p->package == 'core') {
                if (FS_ACCESS && Config::get('env') == 'dev') {
                    echo " <span onclick='addon_activate(\"{$p->package}\")' type=button class='float-right'><i class='fa fa-refresh'></i></span>";
                }
                if ($p->package != 'core') {
                    echo " <a onclick='addon_deactivate(\"{$p->package}\")' class='btn btn-sm btn-outline-danger float-right' title='Refresh'>" . __('Deactivate') . "</a>";
                }
            } else {
                if ($p->package != 'core') {
                    if ($planAllows) {
                        echo " <a onclick='addon_activate(\"{$p->package}\")' class='btn btn-sm btn-outline-success float-right'>" . __('Activate', ['es' => 'Activar']) . "</a>";
                    } else {
                      //echo " <button onclick='gist.chat(\"navigate\", \"newConversation\")' class='btn btn-sm btn-outline-success float-right'>".__('Ask to try', ['Contactanos'])."</button>";
                    }
                }
            }
            if (!empty($p->options) && $p->options != (object)[] && $planAllows) {
                echo " <span class='btn btn-sm btn-outline-secondary' onclick='addon_options(\"{$p->package}\")' type=button>" . __('Options', ['es' => 'Opciones']) . "</span>";
            }
            if (FS_ACCESS) {
                @$current_version = json_decode(file_get_contents($dir . $p->package . '/package.json'))->version;
                if ($current_version && version_compare($p->version, $current_version) > 0) {
                    echo ' ' . dl_btn($p->package, 'warning', __('Upgrade'));
                }
            }

            echo (@$p->url ? '<a class="text-dark" href="' . $p->url . '" target="_blank"><i class="fa fa-link"></i></a>' : '');
            echo (isset($p->contact) ? ' <a href="mailto:' . $p->contact . '"><i class="fa fa-envelope-o"</i></a> ' : '');
        } else {
            if (FS_ACCESS) {
                echo dl_btn($p->package, 'success', __('Download'));
            }
        }
        echo '</div></div></div>';
        $pn++;
    }
}

?>

  </div>
</div>

<?=View::script('core/admin/media.js')?>
<?=View::script('lib/vue/vue.min.js');?>
<?=View::script('core/admin/vue-components.js');?>

<script>
g.language = '<?=Config::lang()?>';
function addon_activate(p){
  g.loader()
  g.post('admin/packages?g_response=content', 'activate='+p, function(data) {
    g.loader(false)
    response = JSON.parse(data)
    if(response.success==true) {
      if (response.reload) {
        g.alert("<?=__('_package_activated')?>",'success','location.reload(true)');
      } else {
        g.alert("<?=__('_package_activated')?>",'success');
      }
    } else {
      g.alert(response.error, 'warning');
    }
  }
)};

function addon_deactivate(p){
  g.loader()
  g.post('admin/packages?g_response=content','deactivate='+p,function(x){
    if (response.reload) {
      g.alert("<?=__('_package_deactivated')?>",'notice','location.reload(true)');
    } else {
      g.alert("<?=__('_package_deactivated')?>",'notice');
    }
    g.loader(false)
  }
)};

function addon_download(p) {
  g.loader()
  g.post('admin/packages?g_response=content', 'download='+p, function(data){
    g.loader(false)
    response = JSON.parse(data)
    if(response.success==true) {
      g.alert("<?=__('_package_downloaded')?>",'success');
    } else {
      g.alert("<?=__('_package_not_downloaded')?>",'warning');
    }
  }
)};

g.dialog.buttons.save_options = {
  title: '<?=__('Save')?>',fn:function(){
    let p = g.el('addon_id').value;
    let fm=new FormData(g.el('addon_options_form'))
    values = readFromClassComponents()
    for(x in values) {
      fm.set(x, values[x])
    }
    g.loader()
    g.ajax({url:'admin/packages?g_response=content&save_options='+p,method:'POST',data:fm,fn:function(x){
      g('.gila-darkscreen').remove();
      g.loader(false)
    }})
  }
}

function addon_options(p) {
  g.loader()
  g.post("admin/packages?g_response=content",'options='+p,function(x){
    g.loader(false)
    g.modal({title:'<?=__('Options')?>',body:x,buttons:'save_options'})
    app = new Vue({
      el: '#addon_options_form'
    })
    transformClassComponents()
  })
}

var packageListApp = new Vue({
  el: '#packages-list',
  data: {
    selectedPackage: null
  },
  methods: {
    selectPackage: function(x) {
      this.selectedPackage = x
      g.post('admin/packages','html='+x, function(html){
        g.modal({body:html,class:'large'})
      })
    }
  }
});
</script>
<?=View::cssAsync('lib/font-awesome/6/css/all.css')?>
