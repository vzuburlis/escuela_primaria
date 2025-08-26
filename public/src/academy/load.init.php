<?php

Gila\Config::content([
  'academic_subject','academic_period','student','teacher','parent',
  'academic_grade','grade_book','academic_year','academic_comments',
], 'academy');
Gila\Config::content('academic_group', 'academy/tables/group.php');

Gila\Config::amenu('academic', [Config::tr('Subjects', ['es' => 'Asignaturas']), 'admin-academy','icon' => 'book','access' => 'admin edit_subjects']);
Gila\Config::amenu('students', [Config::tr('Students', ['es' => 'Estudiantes']), 'admin-students','icon' => 'graduation-cap','access' => 'admin edit_students']);
Gila\Config::amenu('teachers', [Config::tr('Teachers & staff', ['es' => 'Maestros y personal']), 'admin/content/teacher','icon' => 'user','access' => 'admin edit_teachers']);
Gila\Config::amenu('parents', [Config::tr('Parents', ['es' => 'Padres y tutores']), 'admin/content/parent','icon' => 'users','access' => 'admin edit_parents']);

Gila\Config::amenu('student_grades', [Config::tr('Gradebook', ['es' => 'Calificaciones']), 'enter_grades','icon' => 'book','access' => 'admin enter_grades teacher']);
Gila\Config::amenu('student_grades2', [Config::tr('Grade report', ['es' => 'Reportes de grados']), 'student_grades','icon' => 'book','access' => 'admin parent']);

Gila\Config::amenu_child('edu_content', [Config::tr('Subjects', ['es' => 'Asignaturas']),'admin-academy','icon' => 'book','access' => 'admin edit_course edit_topic']);
Gila\Config::amenu_child('edu_content', [Config::tr('Estudiantes'),'admin-students','icon' => 'graduation-cap','access' => 'admin edit_course edit_topic']);
Gila\Config::amenu_child('edu_content', [Config::tr('Maestros'),'admin/content/teacher','icon' => 'user','access' => 'admin edit_course edit_topic']);

Config::let('academy.academic_year_id', 1);

Router::get('admin-academy', function () {
    View::renderAdmin('academy_tabs', 'academy');
});
Router::get('admin-students', function () {
    View::renderAdmin('student_tabs', 'academy');
});
Router::get('enter_grades', function () {
    View::renderAdmin('enter_grades', 'academy');
});
Router::get('student_grades', function () {
    $cards = DB::getAssoc("SELECT username, grade_level, usergroup.id, user.id,
  academic_year.name AS academic_year,
  CONCAT('student_grades/',user.id,'/',grade_level) AS href
  FROM user,academic_year, user_group,usergroup,usermeta
  WHERE user_group.user_id=user.id AND academic_year.id=academic_year_id
  AND usergroup.id=group_id
  AND vartype='user_tutee' AND usermeta.user_id=? AND usermeta.value=user.id
  ORDER BY academic_year.id DESC", [Session::userId()]);
    View::set('cards', $cards);
    View::renderAdmin('grade_cards', 'academy');
});
Router::get('student_grades/(.*)/(.*)', function ($user_id, $grade_level) {
    $parents = DB::getList("SELECT user.id FROM user,usermeta
  WHERE vartype='user_tutee' AND usermeta.user_id=user.id AND usermeta.value=?", [$user_id]);
    if (!in_array(Session::userId(), $parents) && !Session::hasPrivilege('admin teacher')) {
        View::renderFile('403.php');
        Response::code(403);
    }
    View::set('student', DB::getOne("SELECT * FROM user WHERE id=?", [$user_id]));
    View::set('grade_level', $grade_level);
    $group = DB::getOne("SELECT usergroup,usergroup.id FROM user_group,usergroup WHERE usergroup.id=group_id AND user_id=? AND usergroup.grade_level=?", [$user_id, $grade_level]);
    View::set('group', $group ?? 0);
    View::renderAdmin('grade_card', 'academy');
});
Router::get('student_ogrades/(.*)/(.*)/(.*)', function ($user_id, $grade_level, $curp) {
    @header('X-Robots-Tag: noindex, nofollow');
    $user_curp = DB::value("SELECT usermeta.value FROM usermeta
  WHERE vartype='user.curp' AND usermeta.user_id=?", [$user_id]);
    if ($user_curp != $curp) {
        View::renderFile('403.php');
        Response::code(403);
    }
    View::set('student', DB::getOne("SELECT * FROM user WHERE id=?", [$user_id]));
    View::set('grade_level', $grade_level);
    $group = DB::getOne("SELECT usergroup,usergroup.id FROM user_group,usergroup WHERE usergroup.id=group_id AND user_id=? AND usergroup.grade_level=?", [$user_id, $grade_level]);
    View::set('group', $group ?? 0);
    View::render('grade_card', 'academy');
});
Router::get('student_ogrades', function () {
    @header('X-Robots-Tag: noindex, nofollow');
    $curp = $_GET['curp'] ?? '';
    if (!empty($curp)) {
        $id = DB::value("SELECT user.id FROM user,usermeta
    WHERE usermeta.user_id=user.id AND usermeta.value=?", [$curp]);
        if ($id == null) {
            View::alert('warning', 'No hay usuario con ese CURP');
            View::render('grade_curp', 'academy');
            return;
        }
    } else {
        View::render('grade_curp', 'academy');
        return;
    }
    if (Config::get('academy.student_ogrades') == 2) {
        $_id = DB::value("SELECT id FROM usermeta
    WHERE vartype='user.pin' AND user_id=? AND `value`=?", [$id, $_GET['pin']]);
        if ($_id == null) {
            View::alert('warning', 'Incorrecto PIN');
            View::render('grade_curp', 'academy');
            return;
        }
    }
    $cards = DB::getAssoc("SELECT username, grade_level, usergroup.id, user.id,
  academic_year.name AS academic_year,
  CONCAT('student_ogrades/',user.id,'/',grade_level,'/',usermeta.value) AS href
  FROM user,academic_year, user_group,usergroup,usermeta
  WHERE user_group.user_id=user.id AND academic_year.id=academic_year_id
  AND usergroup.id=group_id
  AND vartype='user.curp' AND usermeta.user_id=user.id AND usermeta.value=?
  ORDER BY academic_year.id DESC", [$curp]);
    if (empty($cards)) {
        View::alert('warning', 'No hay boletos todavia por el estudiante');
    }
    View::set('cards', $cards);
    View::render('grade_cards', 'academy');
});


Router::get('student_grades', function () {
    View::renderAdmin('grade_cards', 'academy');
});
Router::get('fillPins', function () {
    $user = DB::table('user')->getRows(['userrole' => Config::get('academy.student_role')], ['limit' => false]);
    foreach ($user as $u) {
        User::meta($u['id'], 'user.pin', rand(1000, 9999));
    }
});

Router::get('academy/get_enter_grades/(.*)/(.*)', function ($group_id, $period_id) {
    $items = DB::getAssoc("SELECT user.* FROM user,user_group ug WHERE ug.group_id=? AND user_id=user.id", [$group_id]);
    $grade_level = DB::value("SELECT grade_level FROM usergroup WHERE id=?", [$group_id]);
    foreach ($items as $i => $item) {
        $items[$i]['grade'] = DB::getOptions("SELECT subject_id,grade FROM academic_grade WHERE grade_level=? AND period_id=? AND user_id=?", [$grade_level, $period_id, $item['id']]);
        $items[$i]['comments'] = DB::value("SELECT comments FROM academic_comments WHERE grade_level=? AND period_id=? AND user_id=?", [$grade_level, $period_id, $item['id']]);
    }
    Response::success(['students' => $items]);
});
Router::post('academy/save_enter_grades/(.*)/(.*)', function ($group_id, $period_id) {
    $data = Request::post();
    $grade_level = DB::value("SELECT grade_level FROM usergroup WHERE id=?", [$group_id]);
    foreach ($data['students'] as $i => $student) {
        foreach ($student['grade'] as $subject_id => $grade) {
            if ($grade != null) {
                DB::query("REPLACE INTO academic_grade(grade,subject_id,grade_level,period_id,user_id) VALUES(?,?,?,?,?)", [$grade, $subject_id, $grade_level, $period_id, $student['id']]);
            } elseif ($period_id == 0) {
                DB::query("DELETE FROM academic_grade WHERE subject_id=? AND grade_level=? AND period_id=0 AND user_id=?", [$subject_id, $grade_level, $student['id']]);
            }
        }
        DB::query("REPLACE INTO academic_comments(comments,grade_level,period_id,user_id) VALUES(?,?,?,?)", [$student['comments'], $grade_level, $period_id, $student['id']]);
    }

    Response::success();
});

function get_academic_periods($year_id)
{
    $items = [];
    $periods = DB::value("SELECT periods FROM academic_year WHERE id=?", [$year_id]) ?? 3;
    for ($i = 0; $i < $periods; $i++) {
        $items[] = ['name' => __('Period', ['es' => 'Periodo']) . ' ' . ($i + 1), 'id' => ($i + 1)];
    }
    return $items;
}
