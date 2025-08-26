<section>
<?=View::css('core/widgets.css')?>
<div>
<?php

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
    echo "<div><a style='font-size:150%;' href='$href'>{$r['title']}</a>";
    echo "<p>" . ($r['description']) . '</p>';
    echo "</div>";
}

?>
</div>
</section>
