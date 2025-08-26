
<?=View::css('core/gila.min.css')?>
<div class="gm-grid container lazy cards-grid" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>

<?php
foreach (json_decode($data['cards'], true) as $key => $card) {
    echo '<div class="g-card bg-white" style="width:100%;max-width:400px;text-align:' . htmlentities($data['align'] ?? 'center') . '">';
    if ($card[0]) {
        echo '<div class="g-card-image" style="display:inline-block;width:100%">';
        echo View::imgLazy($card[0], 400);
        echo '</div>';
    }
    echo '<div class="wrapper"><h3 style="margin:2px">' . $card[1] . '</h3><p>' . $card[2] . '</p>';
    if ($data['link_text'] && $card[3]) {
        echo '<a class="g-btn" href="' . $card[3] . '">' . $data['link_text'] . '</a>';
    }
    echo '</div></div>';
}
?>
</div>

