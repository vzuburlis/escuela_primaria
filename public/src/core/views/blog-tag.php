
<style>
.blog-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:8px}
.post-review{background:white;border:1px solid #ddd;margin-bottom: 20px;}
.post-review-img{text-align:center}
.post-review-img>img{max-width:100%; height:auto; margin-bottom:1em;max-height:200px}
.post-title{display:block}
.pagination{grid-column: 1 / -1;}
.g-pagination li {
  list-style-type: none;
  border: 1px solid #ccc;
  display: inline-block;
}
.g-pagination a {
  padding: 6px 8px;
  color: var(--main-a-color);
  border: 0;
  font: 15px Arial;
  display: inline-block;
  cursor: pointer;
  text-align: center;
}
.g-pagination .active a {
  color: white;
  background: var(--main-a-color);
}
</style>
<!-- Posts -->
<div class="wrapper" style="max-width:1170px;margin:0 auto;">
  <h2>#<?=$tag?></h2>
    <div class="blog-list wrapper">
    <?php foreach ($posts as $r) { ?>
    <div class="post-review g-card">
            <?php
            if ($img = View::thumb($r['img'], 400)) {
                echo '<div class="post-review-img">';
                echo '<img src="' . $img . '" alt="">';
                echo '</div>';
            }
            ?>
        <div class="post-review-body wrapper">
            <a href="<?=Config::url('blog/' . $r['id'] . '/' . $r['slug'])?>">
                <h2 class="post-title" style="margin-top:0"><?=htmlentities($r['title'])?></h2>
            </a>
            <?=strip_tags($r['description'] ?? $r['post'])?>
        </div>
    </div><!--hr-->
    <?php } ?>
    <!-- Pagination -->
    <?php View::renderFile('pagination.php')?>
    </div>
    <!--div class="gm-4 sidebar wrapper">
      <?=View::getWidgetArea('sidebar')?>
    </div-->
</div>
