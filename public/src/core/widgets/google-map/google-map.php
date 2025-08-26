<iframe id="ifr-google" width="100%" height="500" style="border:0" loading="lazy" allowfullscreen
style="vertical-align: middle;" src="https://www.google.com/maps/embed/v1/place?q=<?=urlencode($data['address'])?>&key="<?=Config::get('google_map_key')?>
scrolling="no" marginheight="0" marginwidth="0" width="100%" height="<?=($data['height'] ?? '300')?>px" frameborder="0"></iframe>
