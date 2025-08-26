<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace core\controllers;

use Gila\Config;
use Gila\View;
use Gila\Menu;
use Gila\Widget;
use Gila\Session;
use Gila\Request;
use Gila\Response;
use Gila\Controller;
use Gila\Image;
use Gila\FileManager;
use Gila\Form;

class LZLDController extends Controller
{
    public function indexAction()
    {
    }

    public function widgetAction($id)
    {
        global $widget_data;
        @header('X-Robots-Tag: noindex, nofollow');
        if (isset($_GET['id']) && is_numeric($id)) {
            $id = $_GET['id'];
        }
        if (!is_numeric($id)) {
            View::widgetBody($id, Request::get());
            return;
        }
        $widget = Widget::getById($id);

        if ($widget) {
            if ($widget->active == 1) {
                $widget_data = json_decode($widget->data);
                @$widget_data->widget_id = $id;
                View::widgetBody($widget->widget, $widget_data);
            } else {
                echo "Widget #$id not found";
            }
        }
    }

    public function webform_GET($id)
    {
        $fields = DB::table('webform_field')->getAssoc(['webform_id'=> $id]);
        $html = '';
        foreach ($fields as $i => $field) {
            $name = $field['key'] ?? 'webform' . $id . '_' . $i;
            $array = explode(',', $field['options'] ?? '');
            $options = array_combine($array, $array);
            $required = true;
            if (isset($field['required']) && $field['required'] == 0) {
                $required = false;
            }
            $html .= Form::input($name, ['type' => $field['type'], 'label' => $field['name'], 'required' => $required, 'options' => $options]);
        }
        $html .= '<span data-action="webform/submit?webform_id=' . $id . '" data-msg="" data-callback_url="" data-webform_id="' . $id . '" data-url_params="" class="btn btn-primary btn-submit my-3">' . __('Submit') . '</span>';
        echo $html;
    }

    public function widget_areaAction($area)
    {
        View::widgetArea($area);
    }

    public function thumb_GET()
    {
        if (Session::level() === 0) {
            Response::code(403);
        }
        $size = $_GET['media_thumb'] ?? ($_GET['size'] ?? 100);
        View::$cdn_host = '';
        $file = View::thumb($_GET['src'], (int)$size);
        Image::readfile($file);
    }

    public function item_thumb_GET($table, $id)
    {
        if (Session::level() === 0) {
            Response::code(403);
        }
        header('Cache-Control: max-age=300');
        $img = DB::table($table)->getOne(['where' => ['id' => $id]]);
        View::$cdn_host = '';
        $file = View::thumb($img['image'] ?? 'assets/core/camera.svg', 100);
        Image::readfile($file);
    }

    public function widget_image_GET($widget)
    {
        @header("Content-type: text/css");
        @header('Cache-Control: max-age=84600, must-revalidate');
        [$w, $p] = Widget::explode($widget);
        $file1 = "src/$p/widgets/$w/$w.png";
        $file2 = 'assets/block-previews/' . $k . '.png';
        Image::readfile($file1);
    }

    public function maskAction()
    {
        $src = View::getThumbName($_GET['src'], 'mask');
        if (!FileManager::allowedPath($src, true)) {
            return;
        }
        if (!file_exists($src)) {
            $im = imageCreateFromPNG($_GET['src']);
            if ($im && imageFilter($im, IMG_FILTER_BRIGHTNESS, 0)) {
                  imagepng($im, $src);
                  imagedestroy($im);
            }
        }
        View::$cdn_host = '';
        Image::readfile($src);
    }

    public function amenuAction()
    {
        echo Menu::getHtml(Config::$amenu, $_GET['base'] ?? 'admin');
    }

    public function notificationSetReadAction()
    {
        UserNotification::setRead($_POST['id']);
    }

    public function css_GET()
    {
        @header("Content-type: text/css");
        @header('Cache-Control: max-age=3600, must-revalidate');
        $css = htmlentities(Config::get('theme.css'), ENT_NOQUOTES);
        echo strtr($css, ['&gt;' => '>']);
        if (isset(Config::$content['editor_style'])) {
            foreach (DB::getAssoc("SELECT * FROM editor_style WHERE active=1") as $cl) {
                echo htmlentities($cl['data'], ENT_NOQUOTES) . "\n";
            }
        }
    }
}
