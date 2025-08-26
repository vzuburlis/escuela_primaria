
<figure class="container lazy" data-container="*" style="margin:auto;max-width:90%;width: max-content;"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
  <img src="<?=View::thumb($data['image'], 800)?>" alt="<?=($data['alt_text'] ?? '')?>"
  style="vertical-align: middle;">
  <figcaption><?=$data['caption']?></figcaption>
</figure>
