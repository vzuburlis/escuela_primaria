<?php
$posts = Gila\Post::getPosts(
    ['posts' => $data['items-to-show'], 'category' => $data['category'] ?? 0, 'language' => Config::lang()]
);
$posts2 = [];
?>
<?=View::script('lib/bootstrap52/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap52/bootstrap.min.css')?>
<div id="MyCarousel" class="carousel slide my-5 carousel-dark <?= ($data['carousel-full-width'] == '1') ? 'position-absolute w-100 start-0' : '' ?>" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php if ($posts) {
        foreach (($posts) as $i => $post) {
            $posts2[] = $post;
            ?>
        <button type="button" data-bs-target="#MyCarousel" data-bs-slide-to="<?=$i?>" <?= $i == 0 ? 'class="active"' : '' ?> aria-current="true" aria-label="Slide <?=$i + 1?>"></button>
        <?php }
    }?>
  </div>
  <div class="carousel-inner">
    <?php if (isset($posts2)) {
        foreach (($posts2) as $i => $post) { ?>
        <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
          <div class="d-block w-100" style="height:<?=(isset($data['carousel-size']) ? intval($data['carousel-size']) * 10 : 50) . 'vh'?>; background-image: url(<?=htmlentities(View::thumb($post['img'], 400))?>); background-repeat: no-repeat; background-size:cover;"> </div>
          <div class="carousel-caption d-none d-md-block <?=( isset($data['text-align']) ? 'text-' . $data['text-align'] : '') ?>"  style="height: <?=( isset($data['vertical-align']) ? (($data['vertical-align']) * 10) . '%' : '') ?>">
              <?php if (isset($post['title'])) { ?>
              <h2 class="h2" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $post['title'] ?></h2>
              <?php } ?>
              <?php if (isset($post['description'])) { ?>
              <p class="h4" style="color:<?=$data['color-text'] ?? 'white'?>"><?= $post['description'] ?></p>
              <?php } ?>
            <a class="btn btn-success" style="font-size:<?= (isset($data['button-font-size']) ? $data['button-font-size'] . 'px' : '24px') ?>" href="<?= $post['id'] ?>">See more</a>
          </div>
        </div>
        <?php }
    }?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#MyCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#MyCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<?php if ($data['carousel-full-width'] == '1') { ?>
  <div style="height:<?=(isset($data['carousel-size']) ? intval($data['carousel-size']) * 10 + 10 : 60) . 'vh'?>" class="position-relative mx-5"></div>
<?php } ?>
