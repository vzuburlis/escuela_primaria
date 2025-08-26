<?php

[$files, $total] = User::pageFiles();

foreach ($files as $file) {
    $type = 'file';
    if (FileManager::isImage($file['path'])) {
        $type = 'image';
    }
    $vidx = ['avi','webm','mp4','mkv','mp3'];
    if ($pinf = pathinfo($file['path'])) {
        if ($ext = @$pinf['extension']) {
            if (in_array(strtolower($ext), $vidx)) {
                $type = 'video';
            }
        }
    }
    $filepath = $file['path'];
    $tags = $file['tags'];
    if ($type == 'image') {
        if ($ext == 'webp') {
            $img = '<img src="' . htmlspecialchars($filepath) . '">';
            echo '<div draggable=false data-id=' . $file['id'] . ' data-path="' . $filepath . '" title="' . htmlentities($tags) . '" class="gal-path gal-' . $type . '">' . $img . '</div>';
        } else {
            $img = '<img src="' . View::thumb($filepath, 100) . '">';
            echo '<div draggable=false data-id=' . $file['id'] . ' data-path="' . $filepath . '" title="' . htmlentities($tags) . '" class="gal-path gal-' . $type . '">' . $img . '</div>';
        }
    }
    if ($type == 'video') {
        $img = '<img src="assets/core/admin/movie.svg">';
        echo '<div draggable=false data-id=' . $file['id'] . ' data-path="' . $filepath . '" title="' . htmlentities($tags) . '" class="gal-path gal-image">' . $img . '</div>';
    }
    if ($type == 'file') {
        $img = '<img src="assets/core/admin/file.svg">';
        echo '<div draggable=false data-id=' . $file['id'] . ' data-path="' . $filepath . '" title="' . htmlentities($tags) . '" class="gal-path gal-' . $type . '" style="opacity:0.4">' . $img . '</div>';
    }
}

if ($total > $offset + $ppp) {
    $page = Request::get('page') ?? 1;
    $page = $page + 1;
    $params = '\'' . ($_GET['q'] ?? '') . '\',' . $page;
    echo '<div class="btn btn-sm btn-outline-secondary" onclick="this.remove();filter_ufiles(' . $params . ')" style="grid-column: 1 / -1"> load more</div>';
}
