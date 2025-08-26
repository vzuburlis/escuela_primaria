<?php

namespace Gila;

class Webform
{
    public static $to_email;

    public static function submit($id, $data)
    {
        $response_id = self::saveResponse($id, $data);
        foreach ($data as $key => $value) {
            if (empty($data['email'])) {
                if (in_array(strtolower($key), ['email', 'correo', 'mail'])) {
                    $data['email'] = $value;
                }
            }
        }
        self::notify($id, $data, $response_id);
        self::subscribe($id, $data, $response_id);
        self::welcomeEmail($id, $data, $response_id);
    }

    public static function saveResponse($id, $data)
    {
        if (empty($data['email']) && !empty($data['Email'])) {
            $data['email'] = $data['Email'];
        }
        DB::query("INSERT INTO webform_response(webform_id, `user_id`, ip, `data`) VALUES(?,?,?,?)", [
        $id, Session::userId(), $_SERVER['REMOTE_ADDR'] ?? '', json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
        $response_id = DB::$insert_id;
        $fields = DB::getOptions("SELECT `name`, id FROM webform_field WHERE webform_id=?", [$id]);
        $fields = array_merge($fields, DB::getOptions("SELECT `key`, id FROM webform_field WHERE webform_id=?", [$id]));
        foreach ($data as $f => $v) {
            if (!empty($v)) {
                if (count(DB::get("SHOW TABLES LIKE 'webform_data'"))> 0 && isset($fields[$f])) {
                    DB::query("INSERT INTO webform_data(response_id, field_id, `data`) VALUES(?,?,?)", [
                        $response_id, $fields[$f], $v
                    ]);
                }
            }
        }
        return DB::$insert_id;
    }

    public static function notify($id, $data, $response_id)
    {
        $postFields = [];
        $line1 = Config::tr('You have received a new form submission via', ['es'=> 'Ha recibido un nuevo envío de formulario a través de']);
        $line2 = Config::tr('Here are the details:', ['es'=> 'Aquí están los detalles:']);
        $message = $line1. ' '. Config::get('title'). "\n\n". $line2. "\n\n";
        Config::addLang('core/lang/form_labels/');
        foreach ($data as $i => $v) {
            $postFields[] = $i;
            $message .= Config::tr($i) . ":\n";
            $message .= $data[$i] . "\n\n";
        }

        Config::lang(Config::get('language'));
        $ftext = Config::tr('You can see all form submissions in this link', [
        'es' => 'Puedes ver todos los envíos de formularios en este enlace'
        ]);
        $sub = Config::tr('New Form Submission Received', ['es' => 'Nuevo envío de formulario recibido']);
        if (isset(self::$to_email)) {
            Email::send(['message' => $message, 'subject' => $sub, 'email' => self::$to_email]);
        }

        // log by email
        if ($form_email = DB::value("SELECT user.email FROM user,webform WHERE user.id=user_id AND webform.id=?", [$id])) {
            Email::send(['message' => $message, 'subject' => $sub, 'email' => $form_email]);
        }
        if (Config::get('web-form.email') !== 0) {
            $message .= "\n" . $ftext . "\n" . Config::base('admin/content/webform_response') . "\n";
            Email::send(['message' => $message, 'subject' => $sub]);
        }
    }

    public static function subscribe($id, $data, $response_id)
    {
        // create contact
        $list = DB::value("SELECT contact_list FROM webform WHERE id=?", [$id]) ?? 0;
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && $list > 0) {
            $data['list_id'] = $list;
            $contact_id = crm\models\CRM::addContact($data);
            DB::query("INSERT INTO contactmeta(`contact_id`,`metakey`,`metavalue`) VALUES (?,?,?)", [$contact_id,"webform_response_id",$response_id]);
        }
    }

    public static function welcomeEmail($id, $data, $response_id)
    {
        $wemail = DB::value("SELECT welcome_email FROM webform WHERE id=?", [$id]);
        if ($wemail > 0) {
            Email::send([
            'template_id' => $wemail,
            'template_table' => 'email_template',
            'email' => $data['email'],
            ]);
        }
    }

    public static function submitFromPost(int $id, $fields)
    {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $_POST[$field] ?? '';
        }
        Gila\Log::debug('Webform #90');
        self::submit($id, $data);
    }

    public static function getFields(int $id)
    {
        $fields = DB::get("SELECT `type`,`name`,`key`,options,`required`,rules FROM webform_field WHERE webform_id=?", [$id]);
        if (empty($fields) && DB::value("SHOW COLUMNS FROM webform LIKE 'fields';")) {
            $fields = DB::value("SELECT fields FROM webform WHERE id=?", [$id]);
            return json_decode($fields ?? '[]');
        }
        return $fields;
    }

    public static function getResponse(int $id)
    {
        $table = new Table('webform_response', Session::permissions());
        $webform = new Table('webform', Session::permissions());
        if (!$table->can('read')) {
            return '*No access to read*';
        }
        $row = $table->getRow(['id' => $id]);
        $fields = self::getFields($row['webform_id']);
        $values = json_decode($row['data'], true);
        $responses = self::fillResponses($values, $fields);
        $row['data'] = $responses;
        return json_encode($row);
    }

    public static function fillResponses($values, $fields, $limit = 0)
    {
        $responses = [];
        foreach ($values as $i => $v) {
            if ($limit == 0 || count($responses) < $limit) {
                if (is_array($v)) {
                    if (isset($fields[$v['label']])) {
                        $ind = $fields[$v['label']];
                    } else {
                        $ind = explode('_', $v['label'])[1] ?? $i;
                    }
                    $label = $fields[$ind]['name'] ?? $v['label'];
                    $responses[] = ['label' => $label, 'value' => $v['value']];
                    continue;
                }
                $label = $fields[$i]['name'] ?? $i;
                if (str_ends_with($label, '_ID')) {
                    $tbl = substr($i, 0, -3);
                    $r = DB::getOne("SELECT * FROM $tbl WHERE id=?", [$v]) ?? [];
                    $title = $r['name'] ?? $r['title'];
                    $v = $v . '. ' . $title;
                }
                $responses[] = ['label' => $label, 'value' => $v];
            }
        }
        return $responses;
    }
}
