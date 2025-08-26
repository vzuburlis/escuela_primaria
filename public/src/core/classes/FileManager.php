<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class FileManager
{
    public static $sitepath = __DIR__;
    const ILLEGAL_CHARACTERS = ['%','&','<','>','\\','{','}','*','?','\'','"',':','`','$','@','!','=','+','|'];

    public static function copy($source, $target, $rnd = false)
    {
        $success = true;
        if ($rnd) {
            $target = self::genName($source, $target);
        }
        if (is_dir($source)) {
            $target = Config::dir($target);
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    self::copy($source . '/' . $file, $target . '/' . $file);
                }
            }
        } else {
            copy($source, $target);
        }
        return $target;
    }

    public static function move_uploaded($source, $target, $name)
    {
        $ext = pathinfo($name)['extension'] ?? $name;
        $e = explode('.', $name);
        $name = $e[0];
        $target = self::genName($source, $target) . '.' . $ext;
        if (move_uploaded_file($source, $target)) {
            if (Config::get('use_webp') == 1 && strtolower($ext) == 'png') {
                $mw = Config::get('maxImgWidth') ?? 800;
                $mh = Config::get('maxImgHeight') ?? 800;
                $src = $target;
                $target = substr($target, 0, -3) . 'webp';
                Image::makeThumb($src, $target, (int)$mw, (int)$mh, 32);
                unlink($src);
            }
            if ($exif = exif_read_data($target)) {
                file_put_contents('data/' . strtr($target, ['/' => '_']) . '.json', json_encode($exif, JSON_PRETTY_PRINT));
            }
            DB::query("INSERT INTO user_file(user_id,path,size) VALUES(?,?,?)", [
            Session::userId(), $target, filesize($target)
            ]);
            DB::query("INSERT INTO file_tag(file_id,tag) VALUES(?,?);", [
            DB::$insert_id, substr($name, 0, 50)
            ]);
            return $target;
        }
        return null;
    }


    public static function genName($src, $target)
    {
        $pathinfo = pathinfo($src);
        $ext = '';
        if (isset($pathinfo['extension'])) {
            $ext = '.' . $pathinfo['extension'];
        }
        do {
            $basename = substr(bin2hex(random_bytes(30)), 0, 30);
            $file = strtr($target . '/' . $basename . $ext, ['//' => '/']);
        } while (strlen($basename) < 30 || file_exists($file));
        return $file;
    }

    public static function delete($target)
    {
        if (empty($target)) {
            return;
        }
        if (is_dir($target)) {
            self::cleanDir($target);
            @rmdir($target);
        } else {
            @unlink($target);
            DB::query("DELETE FROM user_file WHERE path=?", [$target]);
        }
        Cache::set('fsize', function () {
            return self::getUploadsSize();
        });
    }

    public static function cleanDir($target, $before = null)
    {
        $files = scandir($target);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if ($before == null || filemtime($target . '/' . $file) < time()- $before) {
                    self::delete($target . '/' . $file);
                }
            }
        }
    }

    public static function isImage($path)
    {
        if ($pinf = pathinfo($path)) {
            if ($ext = @$pinf['extension']) {
                return self::imageExtension($ext);
            }
        }
        return false;
    }

    public static function imageExtension($ext)
    {
        return in_array($ext, ['gif','png','jpg','jpeg','jfif','webp','tiff','tif','svg','jxl']);
    }

    public static function allowedFileType($path)
    {
        $filetypes = [
        'txt','json','css','pdf','twig','csv','tsv','log',
        'png','jpg','jpeg','gif','webp','ico','jfif',
        'avi','webm','mp4','mkv','ogg','mp3',
        ];
        if (is_dir($path)) {
            return true;
        }
        if (Config::get('allow_filetypes') && Session::hasPrivilege('admin')) {
            $filetypes = merge_array($filetypes, Config::get('allow_filetypes'));
        }
        if (Config::get('allow_svg')) {
            $filetypes[] = 'svg';
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, $filetypes)) {
            return true;
        }
        return false;
    }

    public static function allowedPath($path, $read = false)
    {
        $allowedPaths = ['data/public','tmp','assets'];
        if (Session::hasPrivilege('admin')) {
            $allowedPaths[] = 'log';
        }
        if (Config::get('media_uploads')) {
            $allowedPaths[] = Config::get('media_uploads');
        }

        if (!is_dir($path)) {
            $path = pathinfo($path)['dirname'];
        }
        if (
            $read && (strpos($path, 'src/') === 0 || strpos($path, 'themes/') === 0 || strpos($path, 'tmp/') === 0
            || strpos($path, 'assets/') === 0) && strpos($path, 'assets/uploads') !== 0
        ) {
            $allowedPaths = ['src', 'themes', 'assets', 'tmp'];
            $path = substr(realpath($path), strlen(realpath('.')) + 1);
        } else {
            if (!empty(SITE_PATH) && strpos($path, 'sites/') !== 0) {
                $path = SITE_PATH . DIRECTORY_SEPARATOR . $path;
            }
            $path = substr(realpath($path), strlen(realpath(self::$sitepath)) + 1);
        }

        foreach ($allowedPaths as $allowed) {
            if (
                substr($path, 0, strlen($allowed) + 1) === $allowed . DIRECTORY_SEPARATOR ||
                $path === $allowed
            ) {
                return true;
            }
        }

        return false;
    }

    public static function getUploadsSize()
    {
        $path = Config::get('media_uploads') ?? 'assets/uploads';
        $upsize = DB::value("SELECT SUM(size) FROM user_file;") ?? 0;
        $total = self::getDirectorySize($path) + $upsize;
        return $total;
    }

    public static function getDirectorySize($path)
    {
        $bytestotal = 0;
        if (self::allowedPath($path, true) && file_exists($path)) {
            if (!empty(SITE_PATH) && strpos($path, 'sites/') !== 0) {
                $path = SITE_PATH . '/' . $path;
            }
            $path = realpath($path);
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                try {
                    $bytestotal += filesize($file->getRealPath());//$file->getSize();
                } catch (Exception $e) {
                  //TODO  $e->getMessage();
                }
            }
        }
        return $bytestotal;
    }

    public static function uploadError($id)
    {
        $phpFileUploadErrors = [
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
        ];
        return $phpFileUploadErrors[$id] ?? $id;
    }

    public static function publicPath($path)
    {
        return $path;
        return (Config::get('public_path') ?? '') . SITE_PATH . $path;
    }

    public static function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } elseif (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}
