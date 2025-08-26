
<?php foreach ($posts as $r) : ?>
<div class="post-review g-card">
        <?php if ($img = View::thumb($r['img'], 400)) : ?>
            <a href="<?=$r['url']?>">
            <div class="post-review-img">
            <img src="<?=$img?>" alt="">
            </div>
            </a>
        <?php endif; ?>
    <div class="post-review-body wrapper">
        <a href="<?=Config::url('blog/' . $r['id'] . '/' . urlencode($r['slug']))?>">
            <h2 class="post-title" style="margin-top:0"><?=htmlentities($r['title'])?></h2>
        </a>
        <?=strip_tags($r['description'] ?? $r['post'])?>
    </div>
</div><!--hr-->
<?php endforeach; ?>

