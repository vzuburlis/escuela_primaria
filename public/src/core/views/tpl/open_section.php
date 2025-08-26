<?php

$innerPrepend = '';
$backgroundColor = null;
if (!empty($data['background-color']) && !empty($data['alfa']) && $data['alfa'] > 0) {
    $bg = $data['background-color'];
    if ($bg[0] == '#') {
        $backgroundColor = 'rgba(' . hexdec($bg[1] . $bg[2]) . ',' . hexdec($bg[3] . $bg[4]) . ',' . hexdec($bg[5] . $bg[6]) . ',' . htmlentities($data['alfa'] ?? '0') . ')';
    } else {
        $backgroundColor = 'rgba(' . $bg . ',' . htmlentities($data['alfa'] ?? '0') . ')';
    }
}

$html = View::$sectionPadding ? '<div' : '<section';
if (isset($data['id'])) {
    $html .= ' id="' . $data['id'] . '"';
}
if (isset($data['animation']) && isset($data['_type']) && $data['_type'] != 'text') {
    $html .= ' data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"';
}

$class = 'lazy';
if (!empty($data['display'])) {
    $class .= ' ' . $data['display'];
}
if (!empty($data['class'])) {
    $class .= ' ' . $data['class'];
}
$html .= ' class="' . $class . '"';
$bgsize = empty($data['background-size']) ? 'cover' : htmlentities($data['background-size']);
$style = "padding:12px 0;position:relative;background-size:$bgsize;";

if (!empty($data['bg-color'])) {
    if ($data['bg-color'][0] == '#') {
        $style .= 'background-color: ' . htmlentities($data['bg-color']) . ';';
    } else {
        $style .= 'background-color: rgb(' . htmlentities($data['bg-color']) . ');';
    }
}
if (!empty($data['attachment']) && $data['attachment'] == 'fixed') {
    $style .= 'background-attachment: fixed;';
}
if (!empty($data['padding-top'])) {
    $style .= 'padding-top:' . htmlentities($data['padding-top']) . ';';
}
if (!empty($data['padding-bottom'])) {
    $style .= 'padding-bottom:' . htmlentities($data['padding-bottom']) . ';';
}
if (!empty($data['overflow'])) {
    $areaStyle .= 'overflow:' . htmlentities($data['overflow']) . ';';
}


if (!empty($data['lines-top']) && $backgroundColor) {
    $innerPrepend .= '<svg style="pointer-events:none;top:-4em;position:absolute;left:0;z-index:1" fill="' . $backgroundColor . '" viewBox="0 0 700 90" width="100%" height="4em" preserveAspectRatio="none">' . $pathLines[$data['lines-top']] . '</svg>';
}

if (!empty($data['video'])) {
    $ext = (string) substr($data['video'], strrpos($data['video'], '.')) ?? 'webm';
    $innerPrepend .= '<div class="video-overlay-video" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;overflow:hidden">
    <video src="' . htmlentities(Config::get('base'). $data['video']) . '" type=video/' . $ext . ' style="object-fit:cover;min-height:100%;min-width:100%" autoplay loop muted playsinline>
    </video>
  </div>';
} elseif (!empty($data['background'])) {
    $style .= 'background-image:url(\'' . htmlentities(strtr($data['background'], [' ' => '%20'])) . '\');';
    $style .= 'text-align:' . htmlentities($data['align'] ?? 'center') . ';';
    if (!empty($data['positionY'])) {
        $style .= 'background-position-y:' . htmlentities($data['positionY']) . ';';
    }
    if (!empty($data['positionX'])) {
        $style .= 'background-position-x:' . htmlentities($data['positionX']) . ';';
    }
    if (!empty($data['backgroundSM'])) {
        $style .= 'background-image: image-set(';
        $style .= 'url(\'' . htmlentities(strtr($data['background'], [' ' => '%20'])) . '\') 1x,';
        $style .= 'url(\'' . htmlentities(strtr($data['backgroundSM'], [' ' => '%20'])) . '\') 2x);';
    }
    if (!empty($data['background-repeat'])) {
        $style .= 'background-repeat:' . htmlentities($data['background-repeat']) . ';';
    }
}
if (!empty($data['height']) || !empty($data['min-height'])) {
    $style .= 'min-height:' . htmlentities($data['min-height'] ?? $data['height']) . ';';
}
if (isset($data['aspect-ratio'])) {
    $style .= 'aspect-ratio:' . htmlentities($data['aspect-ratio']) . ';';
}

if ($backgroundColor) {
    $innerPrepend .= '<div style="background-color:' . $backgroundColor;
    $innerPrepend .= ';position:absolute;width:100%;height:100%;top:0;pointer-events:none"></div>';
}

$html .= ' style="' . $style . '"';
$html .= '><style>.componentarea,section>.gm-grid{position:relative}</style>' . $innerPrepend;
echo $html;
