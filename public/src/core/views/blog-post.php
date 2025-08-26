<?php View::includeFile('header.php'); ?>
<style>
  body{display:grid;grid-template-rows:auto 1fr auto}
  ul>li>a{text-decoration:none}
</style>
<?php if (Config::get('blog.post_header') ?? Config::get('blog.post-header')) :
    ?><div
style="height:400px;background-image:url(<?=View::thumb($img ?? $post['img'], 1200)?>);background-size: cover;margin-bottom:-160px">
</div>
<?php endif; ?>

<?php if (Config::get('blog.sidebar') == 1) : ?>
<div class="row" style="max-width:1200px;margin:0 auto;">
<div class="wrapper col-lg-9" style="">
<?php else : ?>
<div class="wrapper" style="max-width:900px;margin:0 auto;">
<?php endif; ?>

<div class="wrapper" style="width:100%;margin-bottom: 30px;<?=Config::get('blog.post_header') ? 'background:white;' : ''?><?=Config::get('blog.post-header') ? 'background:white;' : ''?>">

  <article>
    <header style="text-align:center">
      <h1 style="display: block"><?=$title?></h1>
      <span class="meta" style="color:#666"><?=__('Posted on')?> <?=View::date(max(strtotime($post['created']), $post['publish_at']), 'F j, Y')?></span>
      <?php if (Config::get('blog.post_author') ?? Config::get('blog.post-author')) : ?>
        <span><?=__('by ', ['es' => 'por'])?> <a href="<?=Config::base('blog/author/' . $post['user_id'])?>"><?=$author?></a></span>
      <?php endif; ?>
      <?php if (Config::get('blog.post_categories') || Config::get('blog.post-categories')) :
            ?><p>
            <?php foreach ($categories as $c) : ?>
          <a class="btn btn-sm btn-primary" href="<?=Config::base('category/' . $c['id'])?>"><?=$c['title']?></a>
            <?php endforeach; ?>
      </p>
      <?php endif; ?>
    </header>
    <div style="max-width:700px;margin:auto" id="blocks">
      <?=$text?>
    </div>
  </article>
  <?php View::widgetArea('post.after'); ?>
  <?php if (Config::get('blog.show_related') && !isset($GLOBALS['added_related_posts'])) {
        $category = $categories[0] ? $categories[0]['id'] : 0;
        if (Config::get('blog.show_related') == 'grid') {
            echo '<div style="max-width:700px;margin:40px auto"><aside><b>' . __('Read more:', ['es' => 'Leer mas']) . '</b>';
            echo View::css('core/widgets.css');
            View::widgetBody('category-post--grid', ['n_post' => 4, 'category' => $category]);
            echo '</aside></div>';
        }
        if (Config::get('blog.show_related') == 'list') {
            echo '<div style="max-width:700px;margin:40px auto"><aside><b>' . __('Read more:', ['es' => 'Leer mas']) . '</b>';
            $args = [
            'posts' => 4, 'category' => $category, 'publish' => 1,
            'language' => Config::lang()
            ];
            $posts = Gila\Post::getPosts($args);
            foreach ($posts as $post) {
                echo '<br><a href="' . $post['url'] . '">' . $post['title'] . '</a>';
            }
            echo '</aside></div>';
        }
  } ?>


</div>
</div>

<?php if (Config::get('blog.sidebar') == 1) : ?>
<div class="col-lg-3 sidebar wrapper">
    <?=View::getWidgetArea('sidebar')?>
</div>
</div>
<?php endif; ?>

<?php View::includeFile('footer.php'); ?>
