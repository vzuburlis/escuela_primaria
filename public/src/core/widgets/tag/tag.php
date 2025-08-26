<?=View::script('lib/bootstrap52/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
<div class="p-1 widget-tags">
<?php
$data['n'] ??= 12;
$res = DB::query("SELECT metavalue,COUNT(*) AS n FROM metadata
WHERE metakey='post.tag' AND (SELECT publish from post WHERE post.id=post_id)=1
GROUP BY metavalue ORDER BY n DESC LIMIT 0,{$data['n']};");

while ($r = mysqli_fetch_array($res)) {
    $tag = trim($r[0]);
    echo "<a class='btn btn-light' href='blog/tag/{$tag}'>{$tag}({$r[1]}) </a>";
}

echo '</div>';
