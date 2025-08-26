<?php

use Gila\Config;
use Gila\Router;
use Gila\View;

Router::controller('admin', 'core/controllers/AdminController');
Router::controller('cm', 'core/controllers/CMController');
Router::controller('user', 'core/controllers/UserController');
Router::controller('webhook', 'core/controllers/WebhookController');
Router::controller('lzld', 'core/controllers/LZLDController');
Router::controller('fm', 'core/controllers/FMController');
Router::controller('blocks', 'core/controllers/BlocksController');

Config::$amenu = [
  'dashboard' => ['Dashboard','admin','icon' => 'dashboard','access' => '*'],
  'web' => ['Website','#','icon' => 'globe','access' => 'admin web_editor','color' => Config::get('ap.orange'),'children' => [
    ['Pages','admin/content/page','icon' => 'file','access' => 'admin web_editor'],
    ['Widgets','admin/content/widget','icon' => 'th-large','access' => 'admin web_editor'],
    ['Redirects','admin/content/redirect','icon' => 'external-link','access' => 'admin'],
  ]],
  'blog' => ['Blog','#','icon' => 'newspaper-o','access' => 'admin editor writer','color' => Config::get('ap.green'),'package' => 'blog','children' => [
    ['Posts','admin/content/post','icon' => 'pencil','access' => 'admin editor writer'],
    ['Categories','admin/content/postcategory','icon' => 'bars','access' => 'admin editor'],
  ]],
  'admin' => ['Administration','#','icon' => 'wrench','access' => 'admin','color' => Config::get('ap.dark'),'package' => 'core','children' => [
    ['Users','admin/users','icon' => 'users','access' => 'admin admin_user admin_userrole admin_permissions'],
    ['Packages','admin/packages','icon' => 'dropbox','access' => 'admin'],
    ['Settings','admin/settings','icon' => 'cogs','access' => 'admin']
  ]],
];


Config::$widget_area = ['dashboard'];

Config::content([
  'post', 'user-post', 'postcategory', 'user', 'usergroup', 'user_group',
  'userrole', 'page', 'widget', 'tableschema', 'menu',
  'redirect', 'user_notification', 'notification_type', 'option',
  'contact', 'metafield', 'metafield_option', 'template', 'contentlog',
  'user_file',
], 'core');

Config::addLang('core/lang/');

Config::addList('menu.pages', ['admin', 'Admin']);


// work on these
Gila\Form::addInputType('block-colors', function ($name, $field, $ov) {
    $html = '<div class="g-radio g-input" style="padding: var(--main-padding) 0; width: max-content;">';
    $options = ['default' => 'Default', 'dark' => 'Dark', 'black' => 'Black', 'hcolor' => 'Heading', 'primary' => 'Primary', 'acolor' => 'Assent'];
    $ov = empty($ov) ? 'default' : $ov;
    foreach ($options as $value => $display) {
        $id = 'radio_' . $name . '_' . $value;
        $html .= '<input name="' . $name . '" type="radio" value="' . $value . '"';
        $html .= ($value == $ov ? ' checked' : '') . ' id="' . $id . '">';
        $html .= '<label for="' . $id . '">' . $display . '</label>';
    }
    return $html . '</div>';
});

if (FS_ACCESS) {
    Gila\Config::include('/load.templates.php');
}
