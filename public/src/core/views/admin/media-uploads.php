<?php
if (isset($_GET['q'])) {
    View::includeFile('admin/media-uploads-files');
    return;
}

Session::key('media_search', $search);
Session::key('media_tab', 'uploads');
View::script('core/admin/media.js');
View::script('core/lang/content/' . Config::get('language') . '.js');
?>

<div id='admin-media-div'>
  <div class='fullwidth' style="gap:0.4em;display:flex;align-items:center">
<?php //if (Session::hasPrivilege('admin upload_assets')) { ?>
    <input type='file' id='upload_files'
    accept="image/*,video/*,audio/*" onchange='gallery_uupload_files()'
    multiple data-csrf="<?=Form::getToken()?>" style="position:absolute;width:0;margin-left:-400px">
    <button class="btn btn-outline-success" onclick="upload_files.click()"><?=__('Upload', ['es' => 'Subir'])?></button>
<?php //endif; ?>
    <span style="position:relative;padding:0">
      <input class='form-control w-100' style="margin:0" onchange="filter_ufiles(this.value)" placeholder="filter"/>
      <img src="assets/core/admin/filter.svg" class="img-btn" style="opacity:0.6;max-height:18px;position:absolute;margin:0.3em;right:0.3em;top:0.3em"></i>
    </span>
<?php if (Session::hasPrivilege('admin edit_assets')) { ?>
    <button class="btn btn-outline-secondary mr-1" onclick="gallery_uedit_selected()" id=muEdit disabled><?=__('Tags', ['es' => 'Etiquetas'])?></button>
    <button class="btn btn-outline-danger mr-1" onclick="gallery_udelete_selected()" id=muDelete disabled><?=__('Delete')?></button>
<?php } ?>
  </div>

<div class='g-gal wrapper gap-8px' style='background:white;' id=mediaUploadsGallery
ondrop='gallery_udrop_files(event);' ondragover='event.preventDefault();'>

<?php
View::includeFile('admin/media-uploads-files');
echo "</div>";
if ($total = Config::get('media_uploads_limit')) {
    $size = Cache::remember('fsize', 8000, function () {
        return FileManager::getUploadsSize();
    });
    $mb = round($size / (1024 * 1024), 1);
    echo '<progress value="' . $mb . '" max="' . $total . '"> ' . round(100.0 * $mb / $total) . '% </progress>';
    echo ' <span style="font-size:80%">' . round(100.0 * $mb / $total, 2) . '% ';
    echo __('from', ['es' => 'de']) . ' ' . $total . ' MB </span>';
}
echo "</div><!--admin-media-div-->";
