<div class="row" style="max-height:400px;overflow:scroll">
<?php

if (Session::userId() == 0) {
    echo "<div class='alert alert-warning'>Contenido exclusivo por miembros</div>";
    return;
}

function img_file_type($f)
{
    if (str_ends_with($f, '.txt') || str_ends_with($f, '.pdf')) {
        return 'assets/core/icons/file-text.svg';
    }
    if (str_starts_with($f, 'assets/') || str_starts_with($f, 'tmp/')) {
        return $f;
    }
    return 'lzld/thumb?media_thumb=120&src=' . $f;
}

if ($group_id = Request::get('group_id')) {
    $q = "SELECT DISTINCT user_id FROM usermeta WHERE vartype='group' AND  `value`=" . ((int)$group_id);
} else {
    $list = implode(',', User::metaList(Session::userId(), 'group'));
    $q = "SELECT DISTINCT user_id FROM usermeta WHERE vartype='group' AND `value` IN($list)";
}
$files = DB::get("SELECT * FROM user_file WHERE user_id IN($q) ORDER BY used_at DESC LIMIT 60;");

foreach ($files as $f) {
    echo '<div class="col-4 p-1"><a href="' . $f['path'] . '" target=_blank>';
    echo '<img src="' . img_file_type($f['path']) . '"  style="max-width:100%;margin:auto">';
    echo '</a></div>';
}
?>
</div>
