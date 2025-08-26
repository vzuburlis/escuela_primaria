<?php View::includeFile('header.php')?>
<div style="min-height: 80vh;" id="main">
<?php
$group_id = DB::value("SELECT metavalue FROM metadata WHERE content_id=? AND metakey='page_group_id'", [$page['id']]);
if ($group_id > 0 && !Session::inGroup($group_id)) {
    echo '<h2 class="my-4 p-4 border-red text-center">Contenido disponible solo por miembros</h2>';
} else {
    echo $text;
}
?>
</div>
<?php View::includeFile('footer.php')?>
