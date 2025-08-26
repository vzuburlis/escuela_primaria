<?php

namespace core\controllers;

use Gila\Page;
use Gila\Post;
use Gila\Config;
use Gila\View;
use Gila\Event;
use Gila\Router;
use Gila\Session;
use Gila\Response;
use Gila\Request;
use Gila\PageBlocks;
use Gila\Http;
use Gila\Widget;
use Gila\Menu;
use Gila\DB;
use Gila\Form;
use Gila\HttpPost;
use Gila\HtmlInput;
use Gila\Controller;
use Gila\Table;
use Gila\User;

class BlocksController extends Controller
{
    private static $draft = false;

    public function __construct()
    {
        @header('X-Robots-Tag: noindex');
        if (Session::userId() == 0) {
            Response::error(403);
        }
        Config::addLang('core/lang/admin/');
        Config::addLang('core/lang/editor/');
    }

    public function indexAction()
    {
        $table = Router::request('t') ?? Router::param('t', 1);
        $id = Router::param('id', 2);
        if (!$table || !$id) {
            View::renderAdmin('404.php');
            return;
        }
        $widgets = self::readBlocks($table, $id);
        $title = DB::value("SELECT title FROM $table WHERE id=?;", [$id]);
        View::set('contentType', $table);
        View::set('id', $id);
        View::set('isDraft', self::$draft);
        View::set('widgets', $widgets);
        View::set('title', $title);
        View::renderAdmin('admin/content-block.php', 'core');
    }

    public function editAction()
    {
        if ($id = Router::param('id', 2)) {
            $idArray = self::getIdArray($id);
            View::set('widget_id', $id);
            View::set('group', $_GET['group'] ?? null);
            View::set('contentType', $idArray[0]);
            View::set('id', $idArray[1]);
            View::set('type', $_GET['type']);
            View::set('pos', $idArray[2]);
            View::set('widgets', self::readBlocks($idArray[0], $idArray[1]));
            View::renderFile('admin/edit_block.php', 'core');
        }
    }

    public function blockData_GET()
    {
        if ($id = Router::param('id', 2)) {
            $idArray = self::getIdArray($id);
            View::set('widget_id', $id);
            View::set('group', $_GET['group'] ?? null);
            View::set('contentType', $idArray[0]);
            View::set('id', $idArray[1]);
            View::set('type', $_GET['type']);
            View::set('pos', $idArray[2]);
            View::set('widgets', self::readBlocks($idArray[0], $idArray[1]));
            View::renderFile('admin/block_data.php', 'core');
        }
    }


    public function mainmenuAction()
    {
        Config::lang($_GET['lang']);
        View::renderFile('admin/mainmenu.php', 'core');
    }


    public function updateFields_POST()
    {
        Request::load();
        $id = $_POST['id'] ?? null;
        if (empty($id)) {
            Response::error('No content id provided');
        }
        $idArray = self::getIdArray($id);
        $content = $idArray[0];
        $id = (int)$idArray[1];
        self::can_access($content, $id);

        $update = false;
        $blockFields = include('src/core/models/block-fields.php');

        $widgets = self::readBlocks($content, $id);
        if (is_string($_POST['fields'])) {
            $_POST['fields'] = json_decode($_POST['fields'], true);
        }

        foreach ($_POST['fields'] as $field) {
          // create or update the unique block
            if ($content == 'page' && $field['name'] == 'id') {
                $pos = $field['pos'];
                $uid = $field['value'];
                $widget_data = $widgets[$pos];
                if (DB::value("SELECT id FROM block WHERE uid=?", [$widget_data['id']]) > 0) {
                    if (!empty($widget_data['id']) && $widget_data['id'] !== $uid) {
                        if (DB::value("SELECT id FROM block WHERE uid=?", [$uid])) {
                            Response::error('This block ID is on use');
                        }
                    }
                    if ($widgets[$pos]['id'] === $uid) {
                        DB::query("UPDATE block SET `data`=? WHERE uid=?;", [json_encode($widget_data, JSON_UNESCAPED_UNICODE), $widget_data['id']]);
                    } else {
                  //
                        DB::query("UPDATE block SET instances=instances+1 WHERE uid=?;", [$uid]);
                    }
                } else {
                    if (DB::value("SELECT id FROM block WHERE uid=?", [$uid]) > 0) {
                        Response::error('This block ID is already in use (you can remove it from /admin/content/block)');
                    } else {
                        DB::query("INSERT INTO block(data,uid) VALUES(?,?);", [json_encode($widget_data, JSON_UNESCAPED_UNICODE), $uid]);
                    }
                }
            }

            if ($type = $widgets[$field['pos']]['_type']) {
                $fields = Widget::getFields($type);
                if (isset($fields[$field['name']]) || isset($blockFields[$field['name']])) {
                    $field_data = $field['value'];
                    $allowed = $fields[$field['name']]['allow_tags'] ?? null;
                    if ($allowed === null && $field['name'] !== 'text') {
                        $allowed = false;
                    }
                    $field_data = HtmlInput::purify($field_data, $allowed);
                    $widgets[$field['pos']][$field['name']] = $field_data;
                }
            }
            $update = true;
        }
        if ($update) {
            if ($content == 'page' && !empty($widgets[$field['pos']]['id'])) {
                $data = $widgets[$field['pos']];
                DB::query("UPDATE block SET data=? WHERE uid=?;", [json_encode($data, JSON_UNESCAPED_UNICODE), $data['id']]);
            }
            self::updateBlocks($content, $id, $widgets);
            Event::fire('blocks-html', [$widgets, $content, $id, true]);
            echo View::blocks($widgets, $content . '_' . $id . '_', true);
        }
    }

    public function update_POST()
    {
        $id = $_POST['widget_id'];
        $idArray = self::getIdArray($id);
        $content = $idArray[0];
        $id = (int)$idArray[1];
        self::can_access($content, $id);
        $pos = (int)$idArray[2];
        $widgets = self::readBlocks($content, $id);
        if ($type = $widgets[$pos]['_type']) {
            $fields = Widget::getFields($type);
            $widget_data = $widgets[$pos] ?? [];
            $post_data = $_POST['option'] ?? [];

            foreach ($post_data as $key => $value) {
                $allowed = $fields[$key]['allow_tags'] ?? false;
                $purify = $fields[$key]['purify'] ?? true;
                if ($purify === true) {
                    $widget_data[$key] = HtmlInput::purify($post_data[$key], $allowed);
                } else {
                    $widget_data[$key] = $post_data[$key];
                }
            }

          // create or update the unique block
            if ($content == 'page' && !empty($widget_data['id'])) {
                if (DB::value("SELECT id FROM block WHERE uid=?", [$widget_data['id']]) > 0) {
                    if (!empty($widgets[$pos]['id']) && $widgets[$pos]['id'] !== $widget_data['id']) {
                        if (DB::value("SELECT id FROM block WHERE uid=?", [$uid])) {
                            Response::error('This block ID is on use');
                        }
                    }
                    if ($widgets[$pos]['id'] === $widget_data['id']) {
                        DB::query("UPDATE block SET `data`=? WHERE uid=?;", [json_encode($widget_data, JSON_UNESCAPED_UNICODE), $widget_data['id']]);
                    } else {
                      // Response::error('This block ID is already in use (you can remove it from /admin/content/block)');
                        DB::query("UPDATE block SET instances=instances+1 WHERE uid=?;", [$widget_data['id']]);
                    }
                } else {
                    DB::query("INSERT INTO block(data,uid) VALUES(?,?);", [json_encode($widget_data, JSON_UNESCAPED_UNICODE), $widget_data['id']]);
                }
            }

            $widget_data['_type'] = $type;
            $widgets[$pos] = $widget_data;
            self::updateBlocks($content, $id, $widgets);
            Event::fire('blocks-html', [$widgets, $content, $id, true]);
            echo View::blocks($widgets, $content . '_' . $id . '_', true);
        }
    }

    public function posAction()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = (int)$idArray[1];
        self::can_access($content, $id);
        $pos = (int)$idArray[2];
        $newpos = (int)$_POST['pos'];
        $widgets = self::readBlocks($content, $id);

        if ($newpos < 0 || $newpos > count($widgets) - 1) {
            echo View::blocks($widgets, $content . '_' . $id . '_', true);
            return;
        }

        for ($i = $pos; $i != $newpos; $i += $newpos <=> $pos) {
          // swap blocks
            $nexti = $i + ($newpos <=> $pos);
            $tmp = $widgets[$i];
            $widgets[$i] = $widgets[$nexti];
            $widgets[$nexti] = $tmp;
            $nextrid = $content . '_' . $id . '_' . $nexti;
            $wfile = public_path(TMP_PATH . '/stacked-wdgt' . $rid . '.jpg');
            $nextwfile = public_path(TMP_PATH . '/stacked-wdgt' . $nextrid . '.jpg');
            if (file_exists($wfile)) {
                rename($wfile . '.json', public_path(TMP_PATH . '/tmp_wgtjpgjson'));
                rename($wfile, public_path(TMP_PATH . '/tmp_wgtjpg'));
            }
            if (file_exists($nextwfile)) {
                rename($nextwfile . '.json', $wfile . '.json');
                rename($nextwfile, $wfile);
            }
            if (file_exists(public_path(TMP_PATH . '/tmp_wgtjpg'))) {
                rename(public_path(TMP_PATH . '/tmp_wgtjpgjson'), $nextwfile . '.json');
                rename(public_path(TMP_PATH . '/tmp_wgtjpg'), $nextwfile);
            }
        }

        self::updateBlocks($content, $id, $widgets);
        echo View::blocks($widgets, $content . '_' . $id . '_', true);
    }


    public function create_POST()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $pos = (int)$idArray[2];
        $widgets = self::readBlocks($content, $id) ?? [];
        $new = ['_type' => $_POST['type']];
        $type = strtr($_POST['type'], ['/' => '','\\' => '','.' => '']);
      // add a unique block
        if ($type[0] == '#') {
            $bid = substr($type, 1);
            if ($block = DB::value("SELECT `data` FROM block WHERE uid=?", [$bid])) {
                $new = json_decode($block, true);
            } else {
                Response::error('No saved block found with this ID', 200);
            }
        } else {
            if (!isset(Config::$widget[$type])) {
                $wa = explode('@', $type);
                $widgetFile = Config::src() . '/' . ($wa[1] ?? 'core') . '/widgets/' . $wa[0] . '/widget.php';
                if (file_exists($widgetFile)) {
                    $widgetData = include $widgetFile;
                } else {
                    $res = new HttpPost('https://pyme.one/addons/template_data/' . $type . '?key=' . Config::get('license') ?? '', [], ['method' => 'GET']);
                    if ($data = $res->json()) {
                        $widgetData = json_decode($data->blocks, true)[0];
                        $new = $widgetData;
                    }
                }
            } else {
                $widgetFile = Config::src() . '/' . Config::$widget[$type] . '/widget.php';
                $widgetData = include $widgetFile;
            }
            $fields = $widgetData['fields'] ?? $widgetData;
            foreach ($fields as $key => $field) {
                if (isset($field['default'])) {
                    $new[$key] = $field['default'];
                }
                if ($key == 'text' && $type == 'text' && !empty($_POST['html'])) {
                    $new['text'] = $_POST['html'];
                }
            }
        }
        array_splice($widgets, $pos, 0, [$new]);
        self::updateBlocks($content, $id, $widgets);
        echo View::blocks($widgets, $content . '_' . $id . '_', true);
    }

    public function delete_POST()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $pos = (int)$idArray[2];
        $widgets = self::readBlocks($content, $id);
        array_splice($widgets, $pos, 1);
        self::updateBlocks($content, $id, $widgets);
      // delete unique block
        if ($content == 'page' && !empty($widgets[$pos]['id'])) {
            $uid = $widgets[$pos]['id'];
            DB::query("UPDATE block SET instances=instances-1 WHERE instances>0 AND uid=?", [$uid]);
            if (DB::value("SELECT instances FROM block WHERE uid=?", [$uid]) < 1) {
                DB::query("DELETE FROM block WHERE uid=?", [$uid]);
            }
        }
        echo View::blocks($widgets, $content . '_' . $id . '_', true);
    }

    public function clone_POST()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $pos = (int)$idArray[2];
        $widgets = self::readBlocks($content, $id);
        if (isset($widgets[$pos]['id'])) {
          // dont copy ID when block is cloned
            unset($widgets[$pos]['id']);
        }
        $_widgets = array_merge(array_slice($widgets, 0, $pos), [$widgets[$pos]], array_slice($widgets, $pos));
        self::updateBlocks($content, $id, $_widgets);
        echo View::blocks($_widgets, $content . '_' . $id . '_', true);
    }

    public function pageAction($id = null)
    {
        if ($id == null) {
            $r = Page::getByIdSlug($id, false);
            $id = $r['id'] ?? 1;
            header('Location: ' . Config::base() . 'blocks/page/' . $id);
            exit;
        }
        self::editorAction('page', $id);
    }

    public function pageEditorAction($id)
    {
        self::editorAction('page', $id);
    }

    public function editorAction($table, $id)
    {
        if (isset($_GET['lang'])) {
            self::change_language($table, $id);
        }
        View::set('id', $id);
        View::stylesheet('core/gila.min.css');

        Config::loadOptions();
        $r = DB::table($table)->getWith('id', $id);
        $content = DB::table($table)->name() ?? null;
        if (!$content || !$r) {
            View::renderFile('404.php');
            exit;
        }

        $blocks = self::readBlocks($content, $r['id']);
        if (isset($r['options'])) {
            Page::addThemeOptions(json_decode($r['options'], true));
        }
        if ($table == 'page_template') {
            View::$menu['mainmenu'] = [
            'type' => 'menu','children' => [
            ['type' => 'link', 'url' => '#', 'title' => 'Blog'],
            ['type' => 'link', 'url' => '#', 'title' => __('Shop')],
            ['type' => 'link', 'url' => '#', 'title' => __('Contact')],
            ]
            ];
        }

        View::set('row', $r);
        View::set('title', $r['title']);
        View::set('id', $r['id']);
        View::set('text', View::blocks($blocks, 'page_' . $r['id'] . '_', true));
        Menu::$editableLinks = 'blocks/pageEditor';
        View::stylesheet('core/block-editor.css');
        $pagePublic = true;
        if ($content === 'page' || substr($content, 0, 5) == 'page_') {
            $pagePublic = $r['publish'] ?? $r['active'];
            $template = Router::request('g_preview_template', $r['template']);
            Config::lang($r['language']);
            if (empty($template)) {
                View::set('back_url', Config::base('admin/content/page'));
                if (View::getViewFile('editor--' . $content)) {
                    View::renderFile('editor--' . $content);
                } else {
                    View::render('page.php');
                }
            } else {
                View::renderFile('page--' . $template . '.php');
            }
        } elseif ($content == 'post' || $content == 'user-post') {
            View::set('img', $r['image'] ?? '');
            View::render('blog-post.php');
        } elseif (View::getViewFile('editor--' . $content)) {
            View::renderFile('editor--' . $content);
        } else {
            View::renderFile('editor.php', 'core');
        }
        $isDraft = self::$draft;
        $title = $r['title'];
        $pageSlug = $r['slug'];
        $back_url = $_SERVER['HTTP_REFERER'] ?? Config::base('admin');
        if (isset(Config::$content[$table])) {
            $back_url = Config::base('admin/content/' . $table);
        }
        include __DIR__ . '/../views/blocks/content-block-edit.php';
    }

    public function getLocalizedId_GET($table, $id)
    {
        $gtable = new Table($table);
        if ($_GET['lang']!=DB::value("SELECT `language` FROM $table WHERE id=?", [$id])) {
            Response::success(['id'=>$id]);
        }
        $localized_id = DB::getMeta($table.'.localized_id', $id);
        if ($localized_id) {
            $_id = DB::value("SELECT $table.id,slug FROM $table,metadata
            WHERE ($table.id=? OR (content_id=$table.id AND metakey=? AND metavalue=?)) AND `language`=?",
            [$localized_id, $table.'.localized_id', $localized_id, $_GET['lang']]);
        }
        Response::success(['id'=>$_id ?? null]);
    }

    public static function change_language($table, $id)
    {
        $gtable = new Table($table);
        $localized_id = DB::getMeta($table.'.localized_id', $id);
        if ($localized_id) {
            $_id = DB::value("SELECT $table.id,slug FROM $table,metadata
            WHERE ($table.id=? OR (content_id=$table.id AND metakey=? AND metavalue=?)) AND `language`=?",
            [$localized_id, $table.'.localized_id', $localized_id, $_GET['lang']]);
        }
        if ($_id) {
            header('Location: /blocks/editor/'.$table.'/'.$_id);
        } else if ($_GET['lang']==DB::value("SELECT `language` FROM $table WHERE id=?", [$id])) {
            header('Location: /blocks/editor/'.$table.'/'.$id);
        } else {
            $fields = $gtable->fields('clone');
            $fields[] = 'slug';
            $res = $gtable->getOne(['where' => [$gtable->id() => $id], 'select' => $fields]);
            $_id = $gtable->createRow($res);
            DB::query("INSERT INTO  metadata(metavalue,metakey,content_id) VALUES(?,?,?)",  [$id, $table.'.localized_id', $_id]);
            DB::query("INSERT INTO  metadata(metavalue,metakey,content_id) VALUES(?,?,?)",  [$id, $table.'.localized_id', $id]);
            Event::fire('translate_row', ['table'=>'page', 'id'=>$_id]);
            header('Location: /blocks/editor/'.$table.'/'.$_id);
        }
    }

    public function editor2Action($content, $id)
    {
        View::set('content', $content);
        View::set('id', $id);
        include __DIR__ . '/../views/blocks/editor.php';
    }

    public function pagePreview_GET($id)
    {
        $content = 'page';

        Config::loadOptions();
        if ($content == "page" && $r = Page::getByIdSlug($id, false)) {
            Config::lang($r['language']);
            $blocks = self::readBlocks($content, $r['id']);
            View::set('title', $r['title']);
            View::set('text', View::blocks($blocks, 'page' . $r['id'], true));
            Menu::$editableLinks = 'blocks/pageEditor';
            $template = Router::request('g_preview_template', $r['template']);
            if (empty($template)) {
                View::render('page.php');
            } else {
                View::renderFile('page--' . $template . '.php');
            }
        } else {
            View::renderFile('404.php');
        }
    }

    public function saveAction()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $widgets = self::readBlocks($content, $id);
        self::saveBlocks($content, $id, $widgets);
        Event::fire('Blocks::save', ['content' => $content, 'id' => $id]);
    }

    public static function can_access($content, $id)
    {
        if (!Session::hasPrivilege('admin editor')) {
            $gtable = new Table($content);
            if ($user_col = $gtable->getTable()['filter_owner']) {
                if (Session::userId() != DB::value("SELECT $user_col FROM $content WHERE id=?", [$id])) {
                    Response::error("Invalid access", 403);
                }
            }
        }
    }

    public function discardAction()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $row = DB::query(
            "DELETE FROM blockslog WHERE content=? AND content_id=? AND draft=1;",
            [$content, $id]
        );
    }

    public function revertAction()
    {
        $rid = $_POST['id'];
        $idArray = self::getIdArray($rid);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        DB::query(
            "DELETE FROM blockslog WHERE content=? AND content_id=? AND draft=1
      ORDER BY id DESC LIMIT 1;",
            [$content, $id]
        );
        $widgets = self::readBlocks($content, $id) ?? [];
        echo View::blocks($widgets, $content . '_' . $id . '_', true);
    }

    public function getPrototypes_GET($type = null)
    {
        $content_blocks = PageBlocks::getPrototypes($type);
        $blockGroups = PageBlocks::getGroups($content_blocks, $type);
        Response::success([
        'blocks' => $content_blocks,
        'blockgroups' => $blockGroups,
        ]);
    }

    public static function readBlocks($content, $content_id)
    {
        if (isset($_GET['log_id'])) {
            $row = DB::getOne(
                "SELECT * FROM blockslog WHERE content=? AND content_id=? AND id=?;",
                [$content, $content_id,$_GET['log_id']]
            );
            if ($row) {
                  DB::query(
                      "INSERT INTO blockslog(content,content_id,`blocks`,draft,created,user_id) VALUES(?,?,?,1,?,?);",
                      [$row['content'], $content_id, $row['blocks'], time(), Session::userId()]
                  );
                  $row['id'] = DB::$insert_id;
            }
            header('Location: ' . Config::base('blocks/' . $content . '/' . $content_id));
        } else {
          // get last log
            $row = DB::getOne(
                "SELECT * FROM blockslog WHERE content=? AND content_id=? AND draft=1
       ORDER BY id DESC LIMIT 1;",
                [$content, $content_id]
            ); // LOCK IN SHARE MODE
        }
        if ($row) {
            $json = $row['blocks'];
            self::$draft = true;
        } else {
            $content = DB::res($content);
            $json = DB::value("SELECT blocks FROM `$content` WHERE id=?;", [$content_id]);
            self::$draft = false;
        }
        $tmpLang = Config::lang();
        ;
        if (DB::value("SHOW COLUMNS FROM `$content` LIKE 'language'")) {
            $lang = DB::value("SELECT language FROM `$content` WHERE id=?;", [$content_id]);
            Config::lang($lang);
        }

        $blocks = json_decode($json, true) ?? [];
        foreach ($blocks as $i => $block) {
          // read the unique block
            if ($content == 'page' && !empty($block['id'])) {
                if ($data = DB::value("SELECT data FROM block WHERE uid=?", $block['id'])) {
                    $blocks[$i] = json_decode($data, true);
                } else {
                  //error_log('Block with ID='.$block['id'].' not found in DB ', 3, 'log/error.log');
                }
            }
            if (strpos($block['text'], '<div') !== 0) {
                $blocks[$i]['text'] = '<div>' . $blocks[$i]['text'] . '</div>';
            }
        }
        Config::lang($tmpLang);
        return $blocks;
    }

    public static function updateBlocks($content, $id, $blocks)
    {
        self::saveDraft($content, $id, $blocks);
    }

    public static function saveBlocks($content, $id, $blocks)
    {
        $return = [];
        foreach ($blocks as $w) {
            if ($w !== null) {
                $return[] = $w;
            }
        }
        DB::query("UPDATE $content SET `blocks`=? WHERE id=?;", [json_encode($return), $id]);
        PageBlocks::cleanup($content, $id); // clean up old drafts
        Config::setMt($content);
        $draftFile = LOG_PATH . '/blocks/' . $content . $id . '.json';
        self::unDraft($content, $id);
    }

    public static function saveDraft($content, $content_id, $blocks)
    {
        $return = [];
        foreach ($blocks as $w) {
            if ($w !== null) {
                $return[] = $w;
            }
        }
        if (Config::get('blocklog_history') == 0) {
            $id = DB::value("SELECT id FROM blockslog WHERE content=? AND content_id=? AND draft=1
      ORDER BY id DESC LIMIT 1;", [$content, $content_id]);
        } else {
            $id = DB::value("SELECT id FROM blockslog WHERE content=? AND content_id=? AND draft=1
      AND created>UNIX_TIMESTAMP()-5 ORDER BY id DESC LIMIT 1;", [$content, $content_id]);
        }
        DB::query('LOCK TABLE blockslog WRITE;');
        if ($id === null) {
            DB::query(
                "INSERT INTO blockslog(content,content_id,`blocks`,draft,created,user_id) VALUES(?,?,?,1,?,?);",
                [$content, $content_id, json_encode($return), time(), Session::userId()]
            );
        } else {
            DB::query(
                "UPDATE blockslog SET content=?, `blocks`=?, draft=1,created=?, user_id=? WHERE id=?;",
                [$content, json_encode($return), time(), Session::userId(), $id]
            );
        }
        DB::query('UNLOCK TABLES;');
      // after 3 minutes, let 1 draft every 5 minutes
        $ids = DB::query("DELETE FROM blockslog
    WHERE content=? AND content_id=? AND draft=1
    AND created<UNIX_TIMESTAMP()-180
    AND id NOT IN(SELECT id FROM blockslog
      WHERE content=? AND content_id=?
      AND created>UNIX_TIMESTAMP()-3600*3
      GROUP BY CAST(created/300 AS INT)
    );", [$content, $content_id,$content, $content_id]);
    }

    public static function unDraft($content, $content_id)
    {
        $id = DB::value("SELECT MAX(id) FROM blockslog WHERE content=? AND content_id=?", [$content, $content_id]);
        DB::query("UPDATE blockslog SET draft=0 WHERE id=?;", [$id]);
        DB::query("DELETE FROM blockslog
    WHERE content=? AND content_id=? AND draft=1", [$content, $content_id]);
        DB::query("OPTIMIZE TABLE blockslog");
    }

    public static function getIdArray($id)
    {
        $idArray = explode('_', $id);
        if (!is_numeric($idArray[1])) {
            $idArray[0] .= '_' . $idArray[1];
            $idArray[1] = $idArray[2];
            $idArray[2] = $idArray[3] ?? '';
        }
        return $idArray;
    }


    public function toggleFontAction()
    {
        $font = htmlentities($_POST['font']);
        $fonts = Config::getArray('theme.fonts') ?? [];
        if (in_array($font, $fonts)) {
            $key = array_search($font, $fonts);
            unset($fonts[$key]);
        } else {
            $fonts[] = $font;
        }
        Config::set('theme.fonts', $fonts);
        echo $font;
    }

    public function websettings_GET()
    {
        self::access('admin');
        View::renderFile('admin/website-settings.php', 'core');
    }

    public function previewBlock_GET($type)
    {
        $data = $_GET;
        $widgetData = [];

        if (isset($_GET['id']) && $data = DB::value("SELECT data FROM block WHERE uid=?", [$_GET['id']])) {
            $data = json_decode($data, true);
        } else {
            $widgetFile = Widget::path($type);
            if (!file_exists($widgetFile)) {
                if (FS_ACCESS) {
                  $wd = DB::value("SELECT data FROM prototype WHERE id=?", [explode('--', $type)[1]]);
                  $type = explode('--', $type)[0];
                  $widgetData = json_decode($wd, true);
                } else {
                    $type = explode('--', $type)[0];
                    $widgetFile = Widget::path($type);
                    if (file_exists($widgetFile)) {
                        $widgetData = include $widgetFile;
                    }
                }
            } else {
                $widgetData = include $widgetFile;
            }
            $fields = $widgetData['fields'] ?? $widgetData;
            foreach ($fields as $key => $field) {
                if (empty($data[$key])) {
                    if (isset($field['default'])) {
                        $data[$key] = $field['default'];
                    }
                    if ($key == 'text' && $type == 'text' && $html = $_POST['html']) {
                        $data['text'] = $html;
                    }
                }
            }
        }
        include View::getViewFile('head.php');
        echo '<body style="background:' . (Config::get('theme.page-background-color') ?? 'unset') . '">';
        View::widgetBody($type, $data);
        echo '</body>';
    }

    public function history_GET($content_id)
    {
        $idArray = self::getIdArray($content_id);
        $content = $idArray[0];
        $id = $idArray[1];
        self::can_access($content, $id);
        $drafts = DB::get("SELECT blockslog.id, blockslog.created, draft, username
    FROM blockslog,user WHERE
    content=? AND content_id=? AND user.id=user_id ORDER BY created DESC;", [$content, $id]);
        echo '<ul class=list-group>';
        foreach ($drafts as $d) :
            $url = Config::base('blocks/' . $content . '/' . $id . '?log_id=' . $d['id']);
            $draft = $d['draft'] == 1 ? __(' (draft)', ['es' => ' (borrador)']) : '';
            echo '<li class=list-group-item><a href="' . $url . '" class="dropdown-item">' . date('Y-m-d H:m:i', $d['created']) . ' @ ' . $d['username'] . $draft . '</a></li>';
        endforeach;
        echo '</ul>';
    }

    public function selectedFonts_POST()
    {
        Config::set('theme.selectedFonts', 1);
    }

    public function selectedColors_POST()
    {
        Config::set('theme.selectedColors', 1);
    }

    public function getUploadedComponents_GET()
    {
        [$files, $total] = User::pageFiles();
        $el = [];
        $path = $_GET['path'] ?? null;
        if (!empty($path)) {
            $style = 'height:250px;width:100%;overflow:hidden;background-image:url(' . $path . ');background-size:cover;background-position:center;';
            $el[] = [
            'tag' => 'DIV',
            'style' => $style,
            'image' => $path,
            'class' => "m-auto",
            'data' => ['ihref' => ''],
            'fields' => ['bgimage','ihref'],
            ];
        }
        foreach ($files as $f) {
            if (FileManager::isImage($f['path']) && $path != $f['path']) {
                      $style = 'height:250px;width:100%;overflow:hidden;background-image:url(' . $f['path'] . ');background-size:cover;background-position:center;';
                      $el[] = [
                        'tag' => 'DIV',
                        'style' => $style,
                        'image' => $f['path'],
                        'class' => "m-auto",
                        'data' => ['ihref' => ''],
                        'fields' => ['bgimage','ihref'],
                      ];
            }
        }
        Response::success(['items' => $el]);
    }

    public function getFormComponents()
    {
        $el = [];
        if (DB::value("SHOW TABLES LIKE 'webform_field'") != 'webform_field') {
            return $el;
        }
        $webforms = DB::getAssoc("SELECT * FROM webform WHERE id IN(SELECT DISTINCT webform_id FROM webform_field)");
        foreach ($webforms as $webform) {
            $fields = DB::table('webform_field')->get(['webform_id'=> $webform['id']]);
            $html = '';
            foreach ($fields as $i => $field) {
                $name = $field['key'] ?? 'webform' . $data['widget_id'] . '_' . $i;
                $array = explode(',', $field['options'] ?? '');
                $options = array_combine($array, $array);
                $required = true;
                if (isset($field['required']) && $field['required'] == 0) {
                    $required = false;
                }
                $html .= Form::input($name, ['type' => $field['type'], 'label' => $field['name'], 'required' => $required, 'options' => $options]);
                $html .= '<span data-action="webform/submit?webform_id=' . $webform['id'] . '" data-msg="" data-callback_url="" data-webform_id="' . $webform['id'] . '" data-url_params="" class="btn btn-primary btn-submit component">' . __('Submit') . '</span>';
            }
            $style = 'height:250px;width:100%;overflow:hidden;background-image:url(' . $f['path'] . ');background-size:cover;background-position:center;';
            $el[] = [
            'name' => 'el-form' . $webform['id'],
            'tag' => 'DIV',
          //'html'=> $html,
          //'class'=>'el-group',
            'class' => 'lazy',
            'data' => ['load' => 'lzld/webform/' . $webform['id']],
            'group' => ['form'],
            'image' => "assets/core/component/forms.svg",
            'label_en' => $webform['name'],
            'label_es' => $webform['name'],
            ];
        }
        return $el;
    }

    public function preview_GET($table, $id)
    {
        if (Session::userId() > 0) {
            $temp = DB::getOne("SELECT * FROM $table WHERE id=?;", [$id]);
        } elseif ($table == 'page_template') {
            $temp = DB::getOne("SELECT * FROM $table WHERE id=? AND active=1;", [$id]);
        } else {
            Response::error();
        }
        Config::$option['act_as_visitor'] =  true;

        $blocks = self::readBlocks($table, $id);

        $html = View::blocks($blocks, $table . '_' . $id);
        if (isset($r['options'])) {
            Page::addThemeOptions(json_decode($r['options'], true));
        }
        if ($table == 'page_template') {
            View::$menu['mainmenu'] = [
            'type' => 'menu','children' => [
            ['type' => 'link', 'url' => '#', 'title' => 'Blog'],
            ['type' => 'link', 'url' => '#', 'title' => __('Shop')],
            ['type' => 'link', 'url' => '#', 'title' => __('Contact')],
            ]
            ];
        }

        View::set('text', $html);
        View::set('id', $id);
        if ($table == 'page') {
            if ($temp['language']) {
                Config::lang($temp['language']);
            }
            if (empty($temp['template'])) {
                View::set('back_url', Config::base('admin/content/page'));
                View::render('page.php');
            } else {
                View::renderFile('page--' . $temp['template'] . '.php');
            }
        } elseif ($table == 'post') {
            View::set('img', $r['image'] ?? '');
            View::render('blog-post.php');
        } elseif (View::getViewFile('editor--' . $table)) {
            View::renderFile('editor--' . $table);
        } else {
            View::renderFile('admin/template-preview', 'app');
        }
    }

    public function getMenuTypes_GET()
    {
        if (!Session::hasPrivilege('editor writer admin')) {
            return Response::success(['items' => []]);
        }
        $menuTypes = Menu::getMenuTypes();
        foreach ($menuTypes as $i => $mt) {
            $menuTypes[$i]['options'] = [];
            foreach ($mt['options'] as $j => $mtn) {
                if ($mtn != null) {
                    if (is_numeric($j)) {
                        $x = Menu::convert(['id' => $j, 'name' => $mtn, 'type' => $i]);
                        $menuTypes[$i]['options'][$x['url']] = $x['name'];
                    } else {
                        $menuTypes[$i]['options'][$j] = $mtn;
                    }
                }
            }
        }
        Response::success(['items' => $menuTypes]);
    }

    public function getActionTypes_GET()
    {
        if (!Session::hasPrivilege('editor writer admin')) {
            return Response::success(['items' => []]);
        }
        $webforms = DB::getOptions("SELECT CONCAT('webform/submit?webform_id=',id),`name` FROM `webform`;");
        $lists = DB::getOptions("SELECT CONCAT('crm/addEmail?list=',id),`name` FROM `contact_list` WHERE active=1;");
        $deals = DB::getOptions("SELECT CONCAT('crm/addDeal?owner_id=',user.id),username FROM user,usermeta WHERE vartype='role' AND user.id=user_id AND active=1;");
        foreach (Config::getList('menu.pages') as $p) {
            $pages[$p[0]] = $p[1];
        }
        $actionTypes = [
        'webform' => [
        'label' => Config::tr('Webform submit', ['es' => 'Enviar formulario']),
        'options' => $webforms,
        'url' => 'webform/submit',
        'options_label' => Config::tr('Select form', ['es' => 'Elegir formulario']),
        ],
        'deal' => [
        'label' => Config::tr('Create deal', ['es' => 'Crear oportunidad']),
        'options' => $deals,
        'url' => 'crm/addDeal',
        'options_label' => Config::tr('Responsible', ['es' => 'Responsable']),
        ],
        ];
        foreach ($menuTypes as $i => $mt) {
            $menuTypes[$i]['options'] = [];
            foreach ($mt['options'] as $j => $mtn) {
                if ($mtn != null) {
                    if (is_numeric($j)) {
                        $x = Menu::convert(['id' => $j, 'name' => $mtn, 'type' => $i]);
                        $menuTypes[$i]['options'][$x['url']] = $x['name'];
                    } else {
                        $menuTypes[$i]['options'][$j] = $mtn;
                    }
                }
            }
        }
        Response::success(['items' => $actionTypes]);
    }

    public function getElementOptions_GET()
    {
        if (!Config::get('get_elements_nocache')) {
            //header('Cache-Control: max-age=1800, must-revalidate');
        }
        $elOptions = include 'src/core/data/elementOptions.php';
        $faIcons = include 'src/core/data/fa6icons.php' ?? [];
        $bclasses = include 'src/core/data/basicClasses.php' ?? [];

        $res = new HttpPost('https://pyme.one/addons/editor_entities?lang=' . Config::lang() . '&key=' . Config::get('license') ?? '', [], ['method' => 'GET']);

        $aclasses = [];
        $elements = [];
        if ($data = $res->json()) {
            $aclasses = (array)$data->styles;
            $elements = (array)$data->elements;
        }
        if (Config::inPackages('web-form')) {
            $elements = array_merge($elements, self::getFormComponents());
        }
        $response = [
        'elOptions' => $elOptions,
        'faIcons' => $faIcons,
        'bclasses' => $aclasses+$bclasses,
        'cclasses' => PageBlocks::getCustomClasses(),
        'elements' => $elements,
        ];
        Response::success($response);
    }

    public function contentNew_GET($type)
    {
        View::set('table', $type);
        View::renderAdmin('admin/content-templates.php', 'core');
    }

    public function contentCreate_GET($type, $template_id = 0)
    {
        $table = $type . '_template';
        $res = new HttpPost('https://pyme.one/addons/page_template?id=' . $template_id . '&key=' . Config::get('license'), [], ['method' => 'GET']);
        if ($res->json()) {
            $tmp = (array)$res->json()->data;
        }

        $id = PageBlocks::create($type, $tmp);
        if ($type == 'page') {
            PageBlocks::replaceContent('page', $id);
        }

        header('Location: ' . Config::base('blocks/editor/' . $type . '/' . $id));
        exit;
    }
}
