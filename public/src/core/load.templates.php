<?php

use Gila\Config;
use Gila\DB;
use Gila\Router;
use Gila\Email;
use Gila\View;



Config::content('template', 'core/tables/template.php');
Config::amenu_child('admin', ['Templates','admin/content/template','icon' => 'code','access' => 'admin editor']);

Router::add('testTemplate/(.*)', function ($template_id) {
    if (Session::userId() == 0) {
        exit;
    }
    $data = [
    'user' => [
      'email' => 'email@example.com',
      'username' => 'Juaz Perez',
      'name' => 'Juaz Perez',
    ],
    'subdomain' => 'subdomain',
    'activate_url' => '#',
    'reset_url' => '#'
    ];
    Email::send([
    'template_id' => $template_id,
    'email' => Config::get('admin_email'),
    'data' => $data
    ]);
}, 'POST');

Router::add('previewTemplate/(.*)', function ($template_id) {
    if (Session::userId() == 0) {
        exit;
    }
    $temp = DB::getOne("SELECT * FROM `template` WHERE id=?;", [$template_id]);
    $blocks = json_decode($temp['blocks'], true);
    $html = View::blocks($blocks, 'template_' . $tid);
    View::set('text', $html);
    View::renderFile('template', 'core');
}, 'GET');
