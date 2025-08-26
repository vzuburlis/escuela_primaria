<section>

<div>
<?php
// style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px, 1fr));"
if (!@class_exists('blog')) {
    if (file_exists("src/blog/controllers/BlogController.php")) {
        include_once "src/blog/controllers/BlogController.php";
        new blog\controllers\BlogController();
    } else {
        return;
    }
}

$widget_data->n_post = $widget_data->n_post ?? 4;
$widget_data->category = $widget_data->category ?? null;

foreach (
    Gila\Post::getPosts(
        ['posts' => $widget_data->n_post, 'category' => $widget_data->category, 'language' => $data['language'] ?? Config::lang()]
    ) as $key => $r
) {
    $href = Config::base('blog/' . $r['id'] . '/' . $r['slug']);
    echo "<div style='min-width:250px;display:inline-block;padding:8px;vertical-align:top'>";
    echo "<a href='$href'>";
    if ($img = View::thumb($r['img'], 400)) {
        echo "<img style='max-height:300px;max-width:90%;margin:auto' src='$img'>";
    }
    echo "</a><div><a style='font-size:150%;' href='$href'>{$r['title']}</a>";
    echo "<br>" . ($r['description']);
    echo "</div></div>";
}

?>
</div>
</section>
