
<div class="container" data-container="*"
style="text-align:center;display:grid;grid-template-columns:repeat(auto-fill, minmax(580px,1fr))">
<?php
foreach (json_decode($data['videos'], true) as $vid) {
    $data['url'] = $vid[1];
    echo '<div><h3>' . htmlentities($vid[0]) . '</h3>';

    if (strpos($data['url'], '//vimeo.com')) {
        $code = end(explode('/', $data['url'])); ?>
  <iframe src="https://player.vimeo.com/video/<?=$code?>?byline=0&portrait=0" width="560" height="315" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
        <?php
    } else {
        parse_str(parse_url($data['url'])['query'], $vars);
        if (isset($vars['list'])) { ?>
  <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=<?=htmlentities($vars['list'])?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php
        } else { ?>
  <iframe width="560" height="315" src="https://www.youtube.com/embed/<?=htmlentities($vars['v'])?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php
        }
    }

    echo '</div>';
}
?>
</div>

