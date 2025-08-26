
<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<?=View::css('core/widgets.css')?>
<style>
.widget-social-icons-circle li a i:before{
  border-radius:50%;
}
.widget-social-icons-none li a i:before{
  color:inherit!important;
  background:none!important;
}
.widget-social-icons-invert li a i:before{
  background:none!important;
}
</style>
<div class="container lazy" style="padding:0;text-align:<?=($data['align'] ?? 'inherit')?>"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
  <ul data-container="*" class="widget-social-icons widget-social-icons-<?=($data['style'] ?? 'square')?>">
  <?php

  $social_ = ['facebook','twitter','google','linkedin','pinterest','youtube','instagram','tiktok','newsletter','medium','tumblr','github','codepen','twitch','slack','stack-overflow','vk','rss','soundcloud'];
  $social_icons = ['newsletter' => 'envelope'];

  foreach ($social_ as $s) {
      $sURL = $s . '-URL';
      if (isset($data[$sURL]) && !empty($data[$sURL])) {
          if ($s == 'tiktok') {
              View::cssAsync('lib/font-awesome/6/css/all.css');
              echo "<li class=\"social-{$s}\"><a href=\"" . htmlentities($data[$sURL]) . "\" target=\"_blank\"><i class='fa-brands fa-tiktok' aria-hidden='true'></i></a></li>";
          } elseif ($data[$sURL] != '') {
              $icon = $social_icons[$s] ?? $s;
              echo "<li class=\"social-{$s}\"><a href=\"" . htmlentities($data[$sURL]) . "\" target=\"_blank\"><i class='fa fa-{$icon}' aria-hidden='true'></i></a></li>";
          }
      }
  }

    ?>
  </ul>
</div>
