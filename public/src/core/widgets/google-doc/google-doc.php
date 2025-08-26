
<?php
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8"?>' . file_get_contents($data['url']));
$styles = $dom->getElementsByTagName('style');
foreach (iterator_to_array($styles) as $style) {
    $style->parentNode->removeChild($style);
}
$contents = $dom->getElementById('contents');
$html = $dom->saveHTML($contents);
?>
<div class="container" style="max-width:75ch" data-container="*">
<?=$html?>
</div>

