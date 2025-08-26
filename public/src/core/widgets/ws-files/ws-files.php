<div class="row" style="max-height:400px;overflow:scroll">
<?php

if (Session::userId() == 0) {
    echo "<div class='alert alert-warning'>Contenido exclusivo por miembros</div>";
    return;
}

function img_file_type_ws($f)
{
    if (str_ends_with($f, '.txt') || str_ends_with($f, '.pdf')) {
        return 'assets/core/icons/file-text.svg';
    }
    if (str_starts_with($f, 'assets/') || str_starts_with($f, 'tmp/')) {
        return $f;
    }
    return 'lzld/thumb?media_thumb=120&src=' . $f;
}

$pattern = '~(http.*\.)(jpe?g|png|pdf|[tg]iff?|svg)~i';
$posts = DB::get("SELECT content FROM ws_post ORDER BY id DESC LIMIT 30;");
foreach ($posts as $p) {
    $m = preg_match_all($pattern, $p['content'], $matches);
    foreach ($matches[0] as $f) {
        echo '<div class="col-4 p-1"><a href="' . $f . '" target=_blank>';
        echo '<img src="' . img_file_type_ws($f) . '" style="max-width:100%;margin:auto">';
        echo '</a></div>';
    }
}
?>
</div>
