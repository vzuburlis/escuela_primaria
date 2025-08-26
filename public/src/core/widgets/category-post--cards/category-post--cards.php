
<?=View::css('core/widgets.css')?>
<?=View::css('core/gila.min.css')?>
<div class="gm-grid container lazy" style="display:grid; grid-gap:1em; grid-template-columns:repeat(auto-fit, minmax(260px,1fr));
justify-items:center;margin-bottom:1em;overflow: auto;padding: 2em 2px;"
data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?php
$data['n_post'] = $data['n_post'] ?? 4;
$data['category'] = $data['category'] ?? null;

foreach (
    Gila\Post::getPosts(
        ['posts' => $data['n_post'], 'category' => $data['category'], 'language' => $data['language'] ?? Config::lang()]
    ) as $key => $r
) {
    $href = Config::url('blog/' . $r['id'] . '/' . $r['slug']);
    echo '<a  class="text-decoration-none" href="' . $href . '">';
    echo '<div class="g-card bg-white" style="width:100%;max-width:300px;height:100%">';
    if ($r['img']) {
        echo '<div class="g-card-image" style="display:inline-block;width:100%">';
        echo View::imgLazy($r['img'], 400);
        echo '</div>';
    }
    echo '<div class="wrapper" style="text-align:' . htmlentities($data['align'] ?? 'center') . '">';
    echo '<h3 style="margin:2px">' . $r['title'] . '</h3>';
    echo '<p style="color:rgba(0,0,0,0.66);margin:auto;font-size:90%">' . date('F j, Y', strtotime($r['created'])) . '</p>';
    echo '<p style="color:#333;">' . ($r['description'] ?? $r['post']) . '</p>';
    echo '</div></div></a>';
}

?>
</div>

