<?php if (!empty(Config::get('blog.title'))) : ?>
<div style="text-align:center">
  <h2><?=Config::get('blog.title')?></h2>
</div>
<?php endif; ?>
<?php if (Config::get('blog.show_categories')) : ?>
<div class="text-center mt-3">
    <?php foreach (Gila\Post::categories() as $i=>$c) : ?>
    <a class="btn btn-sm btn-primary" href="<?=Config::base('category/' . $c['id'])?>"><?=$c['title']?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php View::includeFile('blog-list.php'); ?>
