<?=View::css('core/gila.min.css')?>
<div class="container lazy" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?php
$category = $categories[0] ? $categories[0]['id'] : null;
$data['category'] = $data['category'] ?? $category;
$data['n_post'] = $data['n_post'] ?? 4;
$GLOBALS['added_related_posts'] = true;

foreach (
    Gila\Post::getPosts(
        ['posts' => $widget_data->n_post, 'category' => $data['category'], 'language' => Config::lang()]
    ) as $key => $r
) {
    $href = Config::url('blog/' . $r['id'] . '/' . $r['slug']);
    echo '<div class="zigzag mb-4">';
    echo '<div><a href="' . $href . '">' . View::imgLazy($r['img'], 400) . '</a></div>';
    echo '<div style="width:100%;">';
    echo '<h3 style="margin:2px"><b>' . $r['title'] . '</b></h3>';
    echo '<p>' . ($r['description'] ?? $r['post']) . '</p>';
    echo '<a href="' . $href . '">' . __('Read more') . '...</a>';
    echo '</div></div>';
}

?>
</div>

