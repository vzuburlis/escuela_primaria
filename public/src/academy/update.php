<?php

Gila\Config::content('academic_subject', 'academy/tables/academic_subject.php');
new TableSchema('academic_subject');

Gila\Config::content('academic_period', 'academy/tables/academic_period.php');
new TableSchema('academic_period');

Gila\Config::content('academic_year', 'academy/tables/academic_year.php');
new TableSchema('academic_year');

Gila\Config::content('academic_group', 'academy/tables/group.php');
new TableSchema('academic_group');

Gila\Config::content('academic_grade', 'academy/tables/academic_grade.php');
new TableSchema('academic_grade');

Gila\Config::content('academic_comments', 'academy/tables/academic_comments.php');
new TableSchema('academic_comments');

DB::query("ALTER TABLE `academic_grade` ADD UNIQUE IF NOT EXISTS `unique_index`(grade_level,user_id,subject_id,period_id);");
DB::query("ALTER TABLE `academic_comments` ADD UNIQUE IF NOT EXISTS `unique_index`(grade_level,user_id,period_id);");
//Gila\Config::content('user_grade', 'academy/tables/user_grade.php');
//new TableSchema('user_grade');
