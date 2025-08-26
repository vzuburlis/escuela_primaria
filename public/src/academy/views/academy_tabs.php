<?php

$links = [
  ['Asignaturas', function () {
    View::set('table', 'academic_subject');
    View::renderFile('admin/content-vue.php');
  }],
//  ['Periodos', function () {
//    View::set('table', 'academic_period');
//    View::renderFile('admin/content-vue.php');
//  }],
//  ['Grados', function () {
//    View::set('table', 'academic_grade');
//    View::renderFile('admin/content-vue.php');
//  }],
];

$baseUrl = Config::base('admin-academy');
View::part('core@tabs', ['links' => $links,'baseUrl' => $baseUrl,'cookie_name' => 'tab_academy']);
