
<?=View::css('core/gila.min.css')?>
<div class="gm-grid container lazy cards-grid" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?php
$data['n_post'] = $data['n_post'] ?? 4;
$data['category'] = $data['category'] ?? null;

foreach (
    Gila\Post::getPosts(
        ['posts' => $data['n_post'], 'category' => $data['category'], 'language' => Config::lang()]
    ) as $key => $r
) {
    $href = Config::url('blog/' . $r['id'] . '/' . $r['slug']);
    echo '<a class="text-decoration-none" href="' . $href . '">';
    echo '<div style="width:100%;max-width:300px;">';
    if ($r['img']) {
        echo '<div class="g-card-image" style="display:inline-block;width:100%">';
        echo View::imgLazy($r['img'], 400);
        echo '</div>';
    }
    echo '<div style="text-align:' . htmlentities($data['align'] ?? 'center') . '">';
    echo '<h3 style="margin:2px"><b>' . $r['title'] . '</b></h3>';
    echo '<p style="opacity:0.66;margin:auto;font-size:90%">' . date('F j, Y', strtotime($r['created'])) . '</p>';
    echo '<p class="post-description">' . ($r['description'] ?? $r['post']) . '</p>';
    echo '</div></div></a>';
}

?>
</div>

