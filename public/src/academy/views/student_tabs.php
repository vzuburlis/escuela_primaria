<?php

$links = [
  ['Estudiantes', function () {
    View::set('table', 'student');
    View::renderFile('admin/content-vue.php');
  }],
  ['Grupos', function () {
    View::set('table', 'academic_group');
    View::renderFile('admin/content-vue.php');
  }],
  ['Años académicos', function () {
    View::set('table', 'academic_year');
    View::renderFile('admin/content-vue.php');
  }],
];

$baseUrl = Config::base('admin-students');
View::part('core@tabs', ['links' => $links,'baseUrl' => $baseUrl,'cookie_name' => 'tab_academy2']);
