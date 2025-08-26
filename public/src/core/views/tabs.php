<div class="row">
  <ul class="g-nav g-tabs gs-12" id="theme-tabs"><?php
  $tab = Request::get('tab');
  $fn = $data['links'][0][1];
  $char = strpos($data['baseUrl'], '?') > 0 ? '&' : '?';
  if (isset($data['cookie_name'])) {
      if ($tab) {
          setcookie($data['cookie_name'], $tab);
      } else {
          $tab = $_COOKIE[$data['cookie_name']] ?? 0;
      }
  }
  foreach ($data['links'] as $key => $link) {
      if ($tab == $key) {
          $active = 'active';
          $fn = $link[1];
      } else {
          $active = '';
      }
      $href = $data['baseUrl'] . $char . 'tab=' . $key;
      echo '<li class="' . $active . '"><a href="' . $href . '">' . __($link[0]) . '</a></li>';
  }
    ?>
  </ul>
  <div class="tab-content gs-12">
    <div class=''><?php $fn(); ?></div>
  </div>
</div>
