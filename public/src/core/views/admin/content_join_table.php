<?php

$callback = Request::get('callback') ?? $t . '_action';
$data_field = Request::get('field');
$field = $gtable->getTable()['fields'][Request::get('field')] ?? null;
$join_table = $field['join_table'] ?? null;
$otable = $field['qoptions'] ?? null;
if (!$join_table) {
    Response::code(404);
}
$jtable = DB::table($join_table[0]);
if (!$jtable) {
    Response::code(404);
}
$tableB = DB::table($otable[2]);
$html = '';
$id = Router::param("id", 2);
$id = (int)$id;
$select = $field['join_list_fields'] ?? [];
$selectB = [];
foreach ($select as $fkey) {
    if ($tableB->getTable()['fields'][$fkey] ?? false) {
        $selectB[] = $fkey;
    }
}
$options = $tableB->get(['select' => array_merge([$otable[0],$otable[1]], $selectB)]);
$items = $jtable->get(['where' => [$join_table[1] => $id], 'select' => array_merge([$join_table[2]], $select)]);
//select usergroup.id,user_id,coalesce(user_group.expire_at,usergroup.expire_at)  from usergroup
//left outer  join user_group on group_id=usergroup.id  and user_id=1
foreach ($options as $i => $op) {
    foreach ($items as $j => $it) {
        if ($op[$otable[0]] == $it[$join_table[2]]) {
            foreach ($select as $fkey) {
                if (!isset($op[$fkey]) || !empty($it[$fkey])) {
                    $options[$i][$fkey] = $it[$fkey] ?? '';
                }
            }
        }
    }
}

$html .= '<form id="' . $t . '-edit-join-list" data-table="' . $t . '" data-id="' . $id . '"data-field="' . $data_field . '" class="notranslate"';
$html .= ' action="javascript:' . $callback . '()" data-values=\'' . htmlentities(json_encode($fieldValues, JSON_UNESCAPED_UNICODE)) . '\'>';
$html .= '<button style="position:absolute;top:-1000px"></button>';
$html .= '<div class="edit-list-form w-100"><table class="table">';
$html .= '<tr><th><th>' . __($tableB->getTable()['title'] ?? 'Name');
foreach ($select as $fkey) {
    $html .= '<th>' . $jtable->getTable()['fields'][$fkey]['title'] ?? ucfirst($fkey);
}
$html .= '</tr>';
foreach ($options as $i => $op) {
    $html .= '<tr style="vertical-align:middle">';
    $checked = '';
    foreach ($items as $j => $it) {
        if ($op[$otable[0]] == $it[$join_table[2]]) {
            $checked = 'checked';
            break;
        }
    }
    $html .= '<td><input type=checkbox name="selected[' . $op['id'] . ']" ' . $checked . ' value=1>';
    $html .= '<td>' . $op[$otable[1]];
    foreach ($select as $col) {
        $val = $op[$col];
        $type = $jtable->getTable()['fields'][$col]['input_type'] ?? 'text';
        if ($type == 'date') {
            if (!empty($val)) {
                $val = date('Y-m-d', is_numeric($val) ? $val : strtotime($val));
            }
            $html .= '<td><div class=d-flex><input class=form-control placeholder="N/A" type=date name="' . $col . '[' . $op['id'] . ']" value="' . $val . '">';
            $html .= '<span onclick="this.previousElementSibling.value=null"> <i class="fa fa-undo mt-1 p-2" type=button aria-hidden="true"></i></span></div>';
        } else {
            $html .= '<td><input class=form-control name="' . $col . '[' . $op['id'] . ']" value="' . $val . '">';
        }
    }
    $html .= '</tr>';
}
$html .= '</table></div>';
echo $html;
