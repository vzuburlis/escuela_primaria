<?php

$academic_year_id = $_GET['academic_year_id'] ?? DB::value("SELECT MAX(id) FROM academic_year;");

$table = [
    'name' => 'academic_comments',
    'title' => 'Observaciones',
    'pagination' => 25,
    'id' => 'id',
    'tools' => ['add_popup','csv'],
    'commands' => ['edit_popup','delete'],
    'lang' => 'core/lang/admin/',
    'unix_times' => true,
    'search_boxes' => ['academic_year_id','group_id','period_id','subject_id'],
    'filters' => [
      'academic_year_id' => $academic_year_id,
    ],
    'permissions' => [
      'read' => true,//['admin'],
      'create' => ['admin'],
      'update' => ['admin'],
      'delete' => ['admin']
    ],
    'fields' => [
      'id' => [
        'title' => 'Level',
        'show' => false,
        'edit' => true,
        'create' => false,
      ],
      'grade_level' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Grado',
        'show' => false,
      ],
      'usergroup' => [
        'title' => 'Groups',
        'create' => false,
        'options' => [],
        'qcolumn' => "(SELECT GROUP_CONCAT(group_id) FROM user_group WHERE user_group.user_id=academic_comments.user_id)",
        'join_table' => ['user_group','user_id', 'group_id'],
        'qoptions' => ["id","usergroup","usergroup"],
        'width' => '15%',
        'join_list_fields' => null,
        'input_style' => 'grid-column:span 2',
        'href' => 'this.command(\'select_groups\',{id})',
      ],
      //'subject_id'=>[
      //  'qtype'=> 'INT UNSIGNED DEFAULT 0',
      //  'title'=>'Materia',
      //],
      'period_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Periodo',
      ],
      'user_id' => [
        'qtype' => 'INT UNSIGNED',
        'title' => 'Estudinte',
      ],
      'comments' => [
        'qtype' => 'TEXT',
      ],
    ],
];

return $table;
