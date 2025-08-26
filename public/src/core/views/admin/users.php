<?php

$links = [];
if (Session::hasPrivilege('admin admin_user')) {
    $links[] = ['Users', function () {
        $type = 'user';
        $src = explode('.', Config::$content[$type])[0];
        View::set('table', $type);
        View::set('tablesrc', $src);
        View::renderFile('admin/content-vue.php');
    }];
}

if (Session::hasPrivilege('admin admin_user')) {
    $links[] = ['Groups', function () {
        $type = 'usergroup';
        $src = explode('.', Config::$content[$type])[0];
        View::set('table', $type);
        View::set('tablesrc', $src);
        View::renderFile('admin/content-vue.php');
    }];
}

if (Config::get('user_group') == 1) {
    if (Session::hasPrivilege('admin admin_user')) {
        if (!isset($_GET['tab']) && isset($_GET['usergroup'])) {
            $_GET['tab'] = count($links);
            $_GET['group_id'] = $_GET['usergroup'];
        }
        $_m = Config::tr('Memberships', ['es' => 'Membresias']);
        $links[] = [$_m, function () {
            $type = 'user_group';
            $src = explode('.', Config::$content[$type])[0];
            View::set('table', $type);
            View::set('tablesrc', $src);
            View::renderFile('admin/content-vue.php');
        }];
    }
}

if (Session::hasPrivilege('admin admin_userrole')) {
    $links[] = ['Roles', function () {
        $type = 'userrole';
        $src = explode('.', Config::$content[$type])[0];
        View::set('table', $type);
        View::set('tablesrc', $src);
        View::renderFile('admin/content-vue.php');
    }];
}

if (Session::hasPrivilege('admin admin_permissions')) {
    $links[] = ['Permissions',function () {
        View::renderFile('admin/permissions.php');
    }];
}

$fn = function () {
    http_response_code(404);
    View::renderFile('404.php');
};
View::part('core@tabs', ['links' => $links,'baseUrl' => 'admin/users','cookie_name' => 'admin_tab_users']);
