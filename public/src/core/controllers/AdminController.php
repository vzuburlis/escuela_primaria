<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace core\controllers;

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Session;
use Gila\Router;
use Gila\Package;
use Gila\Widget;
use Gila\Response;
use Gila\Controller;
use Gila\Page;
use Gila\Theme;
use Gila\Profile;
use Gila\UserAgent;
use Gila\Menu;

class AdminController extends Controller
{
    public function __construct()
    {
        @header('X-Robots-Tag: noindex, nofollow');
        self::admin(0);
        Config::addLang('core/lang/admin/');
        View::set('page_title', 'Admin\\' . __(Router::getAction()));
    }

  /**
  * Renders admin/dashboard.php
  */
    public function indexAction()
    {
        if (Session::level() == 0) {
            $url = Config::get('user_redirect') ?? '';
            $base = Config::base($url);
            echo "<meta http-equiv='refresh' content='0;url=" . $base . "' />";
            exit;
        }
        self::accessLvl(1);
        $id = Router::getPath() ?? null;

        if ($r = Page::getByIdSlug($id)) {
            View::set('title', $r['title']);
            View::set('text', $r['page']);
            View::set('page', $r);
            if (!empty($r['language'])) {
                Config::lang($r['language']);
            }
            Config::canonical($r['slug']);
            View::meta('og:title', $r['title']);
            View::meta('og:type', 'website');
            View::meta('og:url', View::$canonical);
            View::meta('og:site_name', Config::get('title'));
            View::meta('og:description', $r['description']);
            if (empty($r['image']) && Config::get('og-image')) {
                $r['image'] = Config::get('og-image');
            }
            if (!empty($r['meta_robots'])) {
                View::meta('robots', $r['meta_robots']);
            }

            View::set('page_title', $r['title'] . ' | ' . Config::get('title'));
            if ($r['template'] == '' || $r['template'] === null) {
                View::renderFile('page.php');
            } else {
                View::renderFile('page--' . $r['template'] . '.php');
            }
            return;
        }

        if ($to = Page::redirect($id)) {
            http_response_code(301);
            header("Location: $to");
            exit;
        }

        if (Router::param('action', 1)) {
            http_response_code(404);
            View::renderAdmin('404.php');
            return;
        }
        $this->dashboard_GET();
    }

    public function dashboard_GET()
    {
        self::accessLvl(1);
        $wfolders = ['log','themes','src','tmp','assets','data'];
        foreach ($wfolders as $wf) {
            if (is_writable($wf) == false) {
                View::alert('warning', $wf . ' folder is not writable. Permissions may have to be adjusted.');
            }
        }
        if (Session::hasPrivilege('admin') && FS_ACCESS && Package::check4updates()) {
            View::alert('warning', '<a class="g-btn" href="admin/packages">' . __('_updates_available') . '</a>');
        }

        View::renderAdmin('admin/dashboard.php');
    }

  /**
  * List and edit widgets
  */
    public function widgetsAction()
    {
        self::accessLvl(1);
        if ($id = Router::param('id', 1)) {
            View::set('widget', Widget::getById($id));
            View::renderFile('admin/edit_widget.php');
            return;
        }
    }

    public function content_GET($type = null, $id = null)
    {
        if ($type == null) {
            if (Session::hasPrivilege('admin')) {
                View::renderAdmin('admin/contenttype.php');
            } else {
                http_response_code(404);
                View::renderAdmin('404.php');
            }
            return;
        }

        $src = explode('.', Config::$content[$type])[0];
        View::set('table', $type);
        View::set('tablesrc', $src);
        if ($id == null) {
            View::renderAdmin('admin/content-vue.php');
        } else {
            View::set('id', $id);
            View::renderAdmin('admin/content-edit.php');
        }
    }

    public function contentNav_GET($type, $id)
    {
        View::set('table', $type);
        View::set('id', $id);
        View::renderAdmin('admin/content-nav.php');
    }

    public function kanban_GET($type)
    {
        View::set('table', $type);
        View::renderAdmin('admin/kanban-vue.php');
    }

    public function activities_GET($type)
    {
        View::set('table', $type);
        View::set('baseUrl', 'admin/activities/' . $type . '/');
        View::renderAdmin('admin/activities-calendar.php');
    }

    public function update_widgetAction()
    {
        echo Widget::update($_POST);
    }

    public function usersAction()
    {
        self::access('admin admin_user admin_userrole admin_permissions');
        View::renderAdmin('admin/users.php');
    }

    public function package_options_GET($package)
    {
        self::access('admin');
        View::set('package_name', $package);
        View::renderAdmin('admin/package_options.php');
    }

  /**
  * List and manage installed packages
  * @photo
  */
    public function packagesAction()
    {
        self::access('admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['test'])) {
            new Package();
            return;
        }
        if (Session::userId()==1 && $activate = $_GET['activate']) if(!empty($activate)){
            Package::activate($activate);
            return;
        }

        $search = htmlentities(Router::param('search', 2));
        $tab = Router::param('tab', 1);
        $packages = [];

        if ($tab == 'new') {
            $url = 'https://pyme.one/packages/?search=' . $search;
            $url .= Config::get('test') == '1' ? '&test=1' : '';
            if (!$contents = file_get_contents($url)) {
                View::alert('error', "Could not connect to packages list. Please try later.");
            } else {
                $packages = json_decode($contents);
            }
        } else {
            $packages = Package::scan();
        }
        if (!is_array($packages)) {
            View::alert('error', "Something went wrong. Please try later.");
            $packages = [];
        }
        View::set('packages', $packages);
        View::set('search', $search);
        View::renderAdmin('admin/package-list.php');
    }

    public function newthemesAction()
    {
        self::access('admin');
        if (!FS_ACCESS) {
            return;
        }
        $packages = [];
        $search = htmlentities(Router::param('search', 2));
        if (!$contents = file_get_contents('https://pyme.one/packages/themes?search=' . $search)) {
            View::alert('error', "Could not connect to themes list. Please try later.");
        } else {
            $packages = json_decode($contents);
        }
        if (!is_array($packages)) {
            View::alert('error', "Something went wrong. Please try later.");
            $packages = [];
        }
        View::set('packages', $packages);
        View::set('search', $search);
        View::renderAdmin('admin/theme-list.php');
    }

    public function themesAction()
    {
        self::access('admin');
        new Theme();
        $packages = Theme::scan();
        View::set('packages', $packages);
        View::renderAdmin('admin/theme-list.php');
    }

    public function theme_optionsAction()
    {
        self::access('admin');
        View::renderAdmin('admin/theme-options.php');
    }

    public function settingsAction()
    {
        self::access('admin');
        View::renderAdmin('admin/settings.php');
    }

    public function loginAction()
    {
        if (Session::userId() > 0) {
            header('Location: ' . Config::get('base') . 'admin');
            return;
        }
        View::set('title', __('Log In'));
        View::renderAdmin('login.php');
    }

    public function logoutAction()
    {
        Session::destroy();
        echo "<meta http-equiv='refresh' content='0;url=" . Config::get('base') . "' />";
    }

    public function fileTags_GET()
    {
        $data = Request::validate(['path' => 'required']);
        $list = DB::getList("SELECT tag FROM user_file,file_tag WHERE user_file.id=file_id AND path=?", [
        $data['path']
        ]);
        Response::success(['tags' => $list]);
    }

    public function fileTags_POST()
    {
        self::access('admin edit_assets');
        $data = Request::validate(['path' => 'required', 'tags' => '']);
        if (is_string($data['tags'])) {
            $data['tags'] = explode(',', $data['tags']);
        }

        if ($file_id = DB::value("SELECT id FROM user_file WHERE path=?", [$data['path']])) {
            DB::query("DELETE FROM file_tag WHERE file_id=?", [$file_id]);
            foreach ($data['tags'] as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    DB::query("INSERT INTO file_tag(file_id,tag) VALUES(?,?);", [
                    $file_id, substr($tag, 0, 50)
                    ]);
                }
            }
        }
        Response::success(['message' => __('Changes saved', ['es' => 'Los cambios se guardaron'])]);
    }

    public function mediaAction()
    {
        View::renderAdmin('admin/media.php');
        if (!isset($_REQUEST['g_response'])) {
            echo '<style>.media-tabs-side{display:none}</style>';
        }
    }

    public function profileAction()
    {
        $user_id = Session::userId();
        Profile::postUpdate($user_id);
        Config::addLang('core/lang/myprofile/');
        View::set('page_title', __('My Profile'));
        View::set('twitter_account', User::meta($user_id, 'twitter_account'));
        View::set('token', User::meta($user_id, 'token'));
        View::set('user_photo', User::meta($user_id, 'photo'));
        View::set('user_id', $user_id);
        View::renderAdmin('admin/myprofile.php');
    }

    public function sessionsAction()
    {
        Config::addLang('core/lang/myprofile/');
        $user_id = Session::key('user_id');
        View::set('page_title', __('Sessions'));
        View::renderAdmin('admin/mysessions.php');
    }

    public function deviceLogoutAction()
    {
        $device = Router::request('device');
        if (User::logoutFromDevice($device)) {
            $info = [];
            $sessions = Session::findByUserId(Session::userId());
            foreach ($sessions as $key => $session) {
                $user_agent = $session['user_agent'];
                $info[$key] = UserAgent::info($user_agent);
                $info[$key]['ip'] = $session['ip_address'];
                if ($_COOKIE['GSESSIONID'] == $session['gsessionid']) {
                    $info[$key]['current'] = true;
                }
            }
            echo json_encode($info);
        } else {
            echo json_encode([
            'error' => 'Could not log you out from this device'
            ]);
        }
    }

    public function phpinfoAction()
    {
        if (!FS_ACCESS || !Session::hasPrivilege('admin dev')) {
            http_response_code(404);
            View::renderAdmin('404.php');
            return;
        }
        self::access('admin');
        View::includeFile('admin/header.php');
        phpinfo();
        View::includeFile('admin/footer.php');
    }

    public function menuAction($menu = null)
    {
        if ($menu != null) {
            if (Session::hasPrivilege('admin')) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($_POST['menu'])) {
                        Menu::setContents($menu, strip_tags($_POST['menu']));
                        echo json_encode(["msg" => __('_changes_updated')]);
                        exit;
                    }
                } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                    Menu::remove($menu);
                    Response::success(["message" => __('_changes_updated')]);
                }
            }
        }
        View::set('menu', ($menu ?? 'mainmenu'));
        View::renderAdmin('admin/menu_editor.php');
    }

    public function notificationsAction($type = null)
    {
        View::set('type', $type);
        View::renderAdmin('admin/notifications.php');
    }

    public function layoutAction($type = null)
    {
        if ($type) {
            Session::key('admin_layout', $type);
        }
        View::renderAdmin('admin/select-layout.php');
    }

    public function appsAction($type = null)
    {
        View::renderAdmin('admin/list-apps.php');
    }
}
