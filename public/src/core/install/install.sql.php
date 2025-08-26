<?php

use Gila\TableSchema;
use Gila\DB;
use Gila\Table;

require_once 'src/core/update.php';

TableSchema::update(include 'src/core/tables/option.php');

$_user = $_POST['adm_user'];
$_email = $_POST['adm_email'];
$_pass = password_hash($_POST['adm_pass'], PASSWORD_BCRYPT);

DB::query("REPLACE INTO userrole(id,userrole,`level`) VALUES(1,'Admin',10);");
if (!DB::value("SELECT id FROM user WHERE id=1;")) {
    DB::query("INSERT INTO user(id,username,email,pass,active)
  VALUES(1,?,?,?,1);", [$_user,$_email,$_pass]);
    DB::query("INSERT INTO usermeta VALUES(1,1,'role',1);");
}

DB::query('ALTER TABLE post ADD FULLTEXT KEY `title` (`title`,`post`);');
