<?php

/*!
 * This file is part of Gila CMS
 * Copyright 2017-25 Vasileios Zoumpourlis
 * Licensed under BSD 3-Clause License
 */
namespace Gila;

class Form
{
    private static $html;
    private static $input_type;
    public static $iclass = 'form-control';

    public static function posted($name = '*')
    {
        if (Config::get('disable_form_posted') === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        @session_start();
        $value = $_SESSION['_t' . $name] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $value !== null && $value === $_POST['formToken']) {
            if ($name !== '*') {
                unset($_SESSION['_t' . $name]);
            }
            @session_commit();
            return true;
        }
        @session_commit();
        return false;
    }

    public static function verifyToken($check, $name = '*')
    {
        @session_start();
        $value = $_SESSION['_t' . $name] ?? null;
        @session_commit();
        if ($value === $check) {
            return true;
        }
        return false;
    }

    public static function getToken($name = '*')
    {
        @session_start();
        if ($v = @$_SESSION['_t' . $name]) {
            return $v;
        }
        $gsession = substr(bin2hex(random_bytes(32)), 0, 32);
        $_SESSION['_t' . $name] = $gsession;
        @session_commit();
        return $gsession;
    }

    public static function hiddenInput($name = '*')
    {
        return '<input type="hidden" name="formToken" value="' . self::getToken($name) . '">';
    }

    public static function html($fields, $values = [], $prefix = '', $suffix = '')
    {
        self::$html = '';
        self::initInputTypes();

        foreach ($fields as $key => $op) {
            $ov = @$values[$key];
            if (!$ov) {
                if (isset($op['default'])) {
                    if (!isset($op['empty']) || $op['empty'] != true) {
                        $ov = $ov ?? $op['default'];
                    }
                }
            }
            self::$html .= self::input($prefix . $key . $suffix, $op, $ov, $key);
        }
        return self::$html;
    }

    public static function input($name, $op, $ov = '', $key = '')
    {
        self::initInputTypes();
        $type = $op['input_type'] ?? ($op['type'] ?? 'text');
        $class = 'type-' . $type;
        if (isset($op['class'])) {
            $class = ' ' . $op['class'];
        }
        $html = '<div class="' . $class . '">';
        if (isset($op['input_style'])) {
            $html = '<div class="' . $class . '" style="' . $op['input_style'] . '">';
        }
        $tilang = 'title_' . Config::lang();
        $label = ucfirst(str_replace(['-','_'], ' ', $key));
        $label = $op['label'] ?? $label;
        $label = $op['title'] ?? $label;
        $label = $op[$tilang] ?? $label;
        $label = Config::tr($label);
        if ($label == '') {
            $label = '&nbsp;';
        }
        if ($op['required'] ?? false) {
            $label .= '<span class="required-field"> *</span>';
        }

        if ($type && $type == 'hidden') {
            $value = !empty($ov) ? 'value="' . htmlspecialchars($ov) . '"' : '';
            return '<input type="hidden" name="' . $name . '" ' . $value . '>';
        }

        $_label = '';
        if (!isset($op['label']) || $op['label'] !== false) {
            $html .= '<div class="g-label">' . $label;
            $_label = '</div>';
        }

        if ($type && $type == 'check_box') {
            $req = isset($op['required']) ? ' required' : '';
            $html .= '<label><input type=checkbox name="' . $name . '" ' . ($ov == 1 ? 'checked' : '') . ' value=1 ' . $req . '>';
            $html .= ' <span>' . $label . '</span></label>';
            if (isset($op['helptext'])) {
                $helptext = $op['helptext_' . Config::lang()] ?? Config::tr($op['helptext']);
                $html .= '<br><span style="font-weight:400;font-size:90%">' . $helptext . '</span>';
            }
            return $html . $_label;
        }

        if (isset($op['helptext'])) {
            $helptext = $op['helptext_' . Config::lang()] ?? Config::tr($op['helptext']);
            $html .= '<br><span style="font-weight:400;font-size:90%">' . $helptext . '</span>';
        }
        if (isset($op['generator'])) {
            $html .= ' <span class="btn btn-sm btn-outline-secondary mb-1" onclick="gtableGenerator.' . $op['generator'] . '(\'' . $name . '\')">';
            $html .= __('Generate', ['es' => 'Generar']) . '</span>';
        }
        $html .= $_label;

        $req = (isset($op['required']) && $op['required']) ? ' required' : '';
        if (isset($op['maxlength'])) {
            $req = ' maxlength=' . $op['maxlength'];
        }
        if (isset($op['minlength'])) {
            $req = ' minlength=' . $op['minlength'];
        }
        if (isset($op['pattern'])) {
            $req = ' pattern=' . $op['pattern'];
        }
        if (isset($op['input_title'])) {
            $req = ' title=' . $op['input_title'];
        }

        if (isset(self::$input_type[$type])) {
            $html .= self::$input_type[$type]($name, $op, $ov);
        } elseif (in_array($type, ['hidden','number','date','datetime','datetime-local','time','color','password','email','range','month'])) {
            if (($type === 'datetime-local' || $type === 'datetime') && !empty($ov)) {
                $ov = date('Y-m-d\TH:i', is_numeric($ov) ? $ov : strtotime($ov));
                $html .= '<input class="' . self::$iclass . '" step=15 name="' . $name . '" type="' . $type . '"' . $req . ' value="' . htmlspecialchars($ov) . '">';
                return $html . '</div>';
            }
            if ($type === 'date' && $ov) {
                $ov = date('Y-m-d', is_numeric($ov) ? $ov : strtotime($ov));
            }
            if ($type === 'month' && $ov) {
                $ov = date('Y-m', is_numeric($ov) ? $ov : strtotime($ov));
            }
            if ($type === 'range') {
                $req .= ' min=' . ($op['min'] ?? 0) . ' max=' . ($op['max'] ?? 10) . ' step=' . ($op['step'] ?? 1);
            }
            $html .= '<input class="' . self::$iclass . '" name="' . $name . '" type="' . $type . '"' . $req . ' value="' . htmlspecialchars($ov) . '">';
        } else {
            $placeholder = isset($op['placeholder']) ? ' placeholder="' . $op['placeholder'] . '"' : '';
            $ctrlPrevent = ($op['ctrlPrevent']?? false) ? ' onkeydown="if(event.which!=\'86\' && event.which!=\'88\' && event.which!=\'67\' && event.ctrlKey) event.preventDefault()"' : '';
            $html .= '<input class="' . self::$iclass . '" name="' . $name . '" value="' . htmlspecialchars($ov) . '" ' . $ctrlPrevent . $placeholder . $req . '>';
        }

        return $html . '</div>';
    }

    public static function addInputType($index, $value)
    {
        Config::addList($index, $value);
        if (!isset(self::$input_type)) {
            self::initInputTypes();
        }
        self::$input_type[$index] = $value;
    }

    public static function initInputTypes()
    {
        if (isset(self::$input_type)) {
            return;
        }

        self::$input_type = [
        'select' => function ($name, $field, $ov) {
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            if (isset($field['disabled_option'])) {
                $html .= '<option value=""' . ($value == $ov ? ' selected' : '') . ' disabled>' . __($field['disabled_option']) . '</option>';
            }
            foreach ($field['options'] as $value => $option) {
                $html .= '<option value="' . $value . '"' . ($value == $ov ? ' selected' : '') . '>' . __($option) . '</option>';
            }
            return $html . '</select>';
        },
        'wc-select' => function ($name, $field, $ov) {
            $options = [];
            foreach ($field['options'] as $key => $label) {
                $options[] = ['id' => $key, 'label' => (string)$label];
            }
            $e = '';
            if (isset($field['placeholder'])) {
                $e .= 'placeholder="'. $field['placeholder']. '"';
            }
            $e .= 'name="' . $name . '"';
            if (!empty($ov)) {
                $e .= ' value="' . ($ov?? '') . '"';
            }
            if (!empty($field['id'])) {
                $e .= ' id="' . $field['id'] . '"';
            }
            return '<select-component '. $e. ' options=\'' . htmlentities(json_encode($options)) . '\'>';
        },
        'wc-select-multiple' => function ($name, $field, $ov) {
            foreach ($field['options'] as $key => $label) {
                $options[] = ['id' => $key, 'label' => (string)$label];
            }
            return '<select-component name="' . $name . '" options=\'' . htmlentities(json_encode($options)) . '\' multiple>';
        },
        'v-select' => function ($name, $field, $ov) {
            $x = rand(1, 100000);
            $html = '<input type="hidden" name="' . $name . '" id="vueSM' . $x . '">';
            $html .= '<v-select v-model="formValue.' . $name . '"';
            $html .= ' @input="vueSM' . $x . '.value=JSON.stringify(formValue.' . $name . ')"';
            $options = [];
            foreach ($field['options'] as $key => $label) {
                $options[] = ['code' => $key, 'label' => (string)$label];
            }
            $html .= ' :reduce="op=>op.code" :options=\'' . htmlentities(json_encode($options)) . '\' />';
            return $html;
        },
        'v-select-multiple' => function ($name, $field, $ov) {
            $x = rand(1, 100000);
            $html = '<input type="hidden" name="' . $name . '" id="vueSM' . $x . '">';
            $html .= '<v-select multiple v-model="formValue.' . $name . '"';
            $html .= ' @input="vueSM' . $x . '.value=JSON.stringify(formValue.' . $name . ')"';
            $options = [];
            foreach ($field['options'] as $key => $label) {
                $options[] = ['code' => (string)$key, 'label' => (string)$label];
            }
            $html .= ' :reduce="op=>op.code" :options=\'' . json_encode($options) . '\' />';
            return $html;
        },
        'v-select-ajax' => function ($name, $field, $ov) {
            $x = rand(1, 100000);
            $d = '[]';
            if (!empty($ov) && $ov > 0) {
                if (!empty($field['options']) && array_key_exists($ov, $field['options'])) {
                    $d = json_encode(['id' => $ov, 'name' => $field['options'][$ov]], JSON_UNESCAPED_UNICODE);
                } else {
                    $title = isset($field['table']) ? DB::value("SELECT `name` FROM {$field['table']} WHERE id=?", [$ov]) : '#' . $ov;
                    $d = json_encode(['id' => $ov, 'name' => htmlentities($title)], JSON_UNESCAPED_UNICODE);
                }
            }

            $html = '<v-select-ajax src="' . $field['src'] . '" name="' . $name . '" d=\'' . $d . '\' value="' . $ov . '" />';
            return $html;
        },
        'dual_input' => function ($name, $field, $ov) {
            $pl1 = $field['placeholders'][0] ?? 'desde';
            $pl2 = $field['placeholders'][1] ?? 'hasta';
            $a = '<input type=number placeholder="'. $pl1. '" class="' . self::$iclass . '" name="' . $name . '[gt]" value="' . htmlspecialchars($ov['gt']?? '') . '"/>';
            $b = '<input type=number placeholder="'. $pl2. '" class="' . self::$iclass . '" name="' . $name . '[lt]" value="' . htmlspecialchars($ov['lt']?? '') . '"/>';
            return '<div class=d-flex>'. $a. '&nbsp;'. $b. '</div>';
        },
        'barcode' => function ($name, $field, $ov) {
            return '<input class="' . self::$iclass . '" onkeydown="keydown=\'preventKeyDown(this.event)\'" name="' . $name . '" value="' . htmlspecialchars($ov) . '"/>';
        },
        'meta' => function ($name, $field, $ov) {
            if (@$field['meta-csv'] == true || @$field['meta_csv'] == true) {
                return '<input class="' . self::$iclass . '" placeholder="values seperated by comma" name="' . $name . '" value="' . htmlspecialchars($ov) . '"/>';
            }
            if (is_string($ov)) {
                $ov = explode(',', $ov);
            } else {
                $ov = json_decode($ov);
            }
            if (!is_array($ov)) {
                $ov = empty($ov) ? [] : [$ov];
            }
            $encoded = !is_string($ov) ? json_encode($ov ?? [], JSON_UNESCAPED_UNICODE) : $ov;
            $html = '<g-multiselect value="' . htmlspecialchars($encoded) . '"';
            return $html .= ' options="' . htmlspecialchars(json_encode($field['options'])) . '" name="' . $name . '">';
        },
        'keywords' => function ($name, $field, $ov) {
            if (is_string($ov)) {
                $ov = explode(',', $ov);
            }
            $html = '<input-keywords value="' . htmlspecialchars(json_encode($ov ?? [])) . '"';
            return $html .= ' name="' . $name . '">';
        },
        'admin_user' => function ($name, $field, $ov) {
            $getOptions = DB::get("SELECT DISTINCT user.id,`username` FROM user, usermeta WHERE vartype='role' AND user.id=user_id");
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            foreach ($getOptions as $r) {
                $html .= '<option value="' . $r[0] . '"' . ($r[0] == $ov ? ' selected' : '') . '>' . $r[1] . '</option>';
            }
            return $html . '</select>';
        },
        'role_' => function ($name, $field, $ov) {
            if (is_string($ov)) {
                $ov = explode(',', $ov);
            }
            $options = [];
            $getOptions = DB::get("SELECT `id`,`userrole` FROM userrole WHERE `level`<=" . Session::level());
            foreach ($getOptions as $op) {
                $options[] = ['id' => $op[0], 'label' => $op[1]];
            }
            return '<select-component name="' . $name . '" options=\'' . htmlentities(json_encode($options)) . '\' value="' . htmlspecialchars(json_encode($ov ?? [])) . '" multiple>';
        },
        'role' => function ($name, $field, $ov) {
            if (is_string($ov)) {
                $ov = explode(',', $ov);
            }
            $getOptions = DB::get("SELECT `id`,`userrole` FROM userrole WHERE `level`<=" . Session::level());
            foreach ($getOptions as $op) {
                $options[$op[0]] = $op[1];
            }
            $html = '<g-multiselect value="' . htmlspecialchars(json_encode($ov ?? [])) . '"';
            return $html .= 'options="' . htmlspecialchars(json_encode($options)) . '" name="' . $name . '">';
        },
        'admin_role' => function ($name, $field, $ov) {
            $getOptions = DB::get("SELECT `id`,`userrole` FROM userrole");
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            foreach ($getOptions as $r) {
                $html .= '<option value="' . $r[0] . '"' . ($r[0] == $ov ? ' selected' : '') . '>' . $r[1] . '</option>';
            }
            return $html . '</select>';
        },
        'radio' => function ($name, $field, $ov) {
            $html = '<div class="g-radio ' . self::$iclass . '" style="padding: var(--main-padding) 0; width: max-content;">';
            foreach ($field['options'] as $value => $display) {
                $id = 'radio_' . $name . '_' . $value;
                $html .= '<input name="' . $name . '" type="radio" value="' . $value . '"';
                $html .= ($value == $ov ? ' checked' : '') . ' id="' . $id . $value . '">';
                $html .= '<label for="' . $id . $value . '">' . __($display) . '</label>';
            }
            return $html . '</div>';
        },
        'animation' => function ($name, $field, $ov) {
            $options = ['' => 'None','fade-in' => 'Fade','expand' => 'Expand','move-left' => 'Left','move-right' => 'Right','move-up' => 'Up','move-down' => 'Down'];
            $field['options'] = $options;
            return self::$input_type['select']($name, $field, $ov);
        },
        'postcategory' => function ($name, $field, $ov) {
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            $res = DB::get('SELECT id,title FROM postcategory;');
            $res[] = ['demo', '--Demo--'];
            $html .= '<option value=""' . ('' == $ov ? ' selected' : '') . '>*</option>';
            foreach ($res as $r) {
                $html .= '<option value="' . $r[0] . '"' . ($r[0] == $ov ? ' selected' : '') . '>' . $r[1] . '</option>';
            }
            return $html . '</select>';
        },
        'comments' => function ($name, $field, $ov) {
            $form = isset($field['content']) ? ' form="' . $field['content'] . '-edit-item-form"' : '';
            return '<input-comments name="' . $name . '" fieldname="' . $field['fieldname'] . '" username="' . Session::key('user_name') . '" value="' . htmlspecialchars($ov ?? '[]') . '"' . $form . '>';
        },
        'media2' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            $ov = htmlspecialchars($ov);
            return '<input-media name="' . $name . '" value="' . $ov . '">';
        },
        'upload-media' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            $ov = htmlspecialchars($ov);
            $x = '';
            if (isset($field['media_size'])) {
                $x .= ' size=' . $field['media_size'];
            }
            if (isset($field['media_placeholder'])) {
                $x .= ' thumb="' . htmlspecialchars($field['media_placeholder']) . '"';
            }
            return '<input-upload-media name="' . $name . '" value="' . $ov . '"' . $x . '>';
        },
        'tree-select' => function ($name, $field, $ov) {
            return '<tree-select name="' . $name . '" value="' . $ov . '" data=' . json_encode($field['data']) . '>';
        },
        'palette' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            if (empty($ov)) {
                $ov = json_encode(end($field['palettes']));
            }
            $field['palettes'][] = json_decode($ov, true);
            $ov = htmlspecialchars($ov);
            $pal = $field['palettes'] ? htmlspecialchars(json_encode($field['palettes'])) : '';
            $labels = $field['labels'] ? htmlspecialchars(json_encode($field['labels'])) : '';
            return '<color-palette name="' . $name . '" value="' . $ov . '" palettes="' . $pal . '" labels="' . $labels . '">';
        },
        'media-gallery' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            if (!is_array(json_decode($ov, true))) {
                $ov = explode(',', $ov);
                for ($i = count($ov); $i < $field['max']; $i++) {
                    array_push($ov, '');
                }
                $ov = json_encode($ov);
            }
            $ov = htmlspecialchars($ov);
            $up = Config::get('shop.product_umedia') ? '1' : '0';
            return '<input-gallery upload=' . $up . ' name="' . $name . '" value="' . $ov . '">';
        },
        'files' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            if (!is_array(json_decode($ov, true))) {
                $ov = explode(',', $ov);
                for ($i = count($ov); $i < $field['max']; $i++) {
                    array_push($ov, '');
                }
                $ov = json_encode($ov);
            }
            $ov = htmlspecialchars($ov);
            $up = Config::get('shop.product_umedia') ? '1' : '0';
            return '<input-files upload=' . $up . ' name="' . $name . '" value="' . $ov . '">';
        },
        'media' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            $ov = htmlspecialchars($ov);
            return '<div class="g-group">
          <span class="g-btn g-group-item" onclick="open_media_gallery(\'#' . $id . '\')"> &nbsp;&#9654; </svg>
          </span>
          <span class="g-group-item"><input class="' . self::$iclass . ' fullwidth" value="' . $ov . '" id="' . $id . '" name="' . $name . '"><span>
        </span></span></div>';
        },
        'key' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            return '<div class="g-group">
          <span class="btn g-group-item" onclick="open_select_row(\'#' . $id . '\',\'' . $field['table'] . '\',\'' . $field['title'] . '\')"> 🔑 </span>
          <span class="g-group-item"><input class="fullwidth" value="' . ($ov ?? 0) . '" id="' . $id . '" name="' . $name . '" disable><span>
        </span></span></div>';
        },
        'textarea' => function ($name, $field, $ov) {
            return '<textarea class="' . self::$iclass . ' fullwidth" name="' . $name . '" rows=' . ($field['rows'] ?? 3) . ' style="resize:vertical;">' . htmlspecialchars($ov) . '</textarea>';
        },
        'codemirror' => function ($name, $field, $ov) {
            return '<textarea class="' . self::$iclass . ' fullwidth codemirror-js" name="' . $name . '">' . htmlspecialchars($ov) . '</textarea>';
        },
        'tinymce' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            return '<textarea class="' . self::$iclass . ' fullwidth tinymce" id="' . $id . '" name="' . $name . '">' . htmlspecialchars($ov) . '</textarea>';
        },
        'paragraph' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            return '<textarea class="' . self::$iclass . ' fullwidth tinymce" id="' . $id . '" name="' . $name . '">' . htmlspecialchars($ov) . '</textarea>';
        },
        'vue-editor' => function ($name, $field, $ov) {
            $id = 'm_' . str_replace(['[',']'], '_', $name);
            return '<vue-editor id="' . $id . '" name="' . $name . '" text="' . htmlentities($ov) . '"></vue-editor>';
        },
        'language' => function ($name, $field, $ov) {
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            $res = include __DIR__ . '/../lang/languages.php';
            $ov = $ov ?? Config::lang();
            $list = Config::getArray('languages') ?? [];
            foreach ($res as $key => $r) {
                if ($key == $ov || in_array($key, $list) || $key == Config::lang()) {
                    $html .= '<option value="' . $key . '"' . ($key == $ov ? ' selected' : '') . '>' . $r . '</option>';
                }
            }
            return $html . '</select>';
        },
        'checkbox' => function ($name, $field, $ov) {
            return self::$input_type['switch']($name, $field, $ov);
        },
        'check_box' => function ($name, $field, $ov) {
            $value = $field['value'] ?? 1;
            $checked = $ov == $value ? 'checked' : '';
            return "<input type=checkbox name='$name' $checked value='$value' $req>";
        },
        'switch' => function ($name, $field, $ov) {
            if ($ov == 1) {
                $checked = ['','checked'];
            } else {
                $checked = ['checked',''];
            }
            return '<div class="g-switch">
        <input name="' . $name . '" type="radio" value="0" id="chsw_' . $name . '0" ' . $checked[0] . '>
        <input name="' . $name . '" type="radio" value="1" id="chsw_' . $name . '1" ' . $checked[1] . '>
        <div class="g-slider"></div>
        </div>
        ';
        },
        'list' => function ($name, $field, $ov) {
            $fields = htmlspecialchars(json_encode($field['fields']));
            $value = is_string($ov) && json_decode($ov) ? htmlspecialchars($ov) : '[]';
            return '<input-list style="width:100%;border:1px solid var(--main-border-color);" name="' . $name . '" fieldlist="' . $fields . '" value="' . $value . '"></input-list>';
        },
        'menu' => function ($name, $field, $ov) {
            $value = json_decode($ov) ? htmlspecialchars($ov) : '[]';
            $types = json_encode(Menu::getMenuTypes(), JSON_UNESCAPED_UNICODE);
            $types = htmlspecialchars($types);
            return '<menu-editor style="width:100%;border:1px solid var(--main-border-color);" name="' . $name . '" value="' . $value . '" itemtypes="' . $types . '" ></menu-editor>';
        },
        'color-input' => function ($name, $field, $ov) {
            $value = htmlentities($ov);
            $palette = $field['palette'] ?? [];
            return '<color-input palette=\'' . htmlspecialchars(json_encode($palette)) . '\' name="' . $name . '" value="' . $value . '"></color-input>';
        },
        'iframe' => function ($name, $field, $ov) {
            $value = $ov;
            if (!empty($value)) {
                if (!file_exists($value)) {
                    return '<p class="small">' . __('File not found') . '</p>';
                } else {
                    return '<iframe src=\'' . htmlentities($value) . '\' style=\'width:100%;height:80vh\'></iframe>';
                }
            }
        },
        'activity_log' => function ($name, $field, $ov) {
            $value = htmlentities($ov);
            return '<activity-log src=\'' . $field['src'] . '\' :id=\'id\'></activity-log>';
        },
        'google-font' => function ($name, $field, $ov) {
            $options = View::$web_safe_fonts;
            $gfonts = include 'src/core/data/google_fonts.php';
            $options = array_merge($options, $gfonts);
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';

            $html = '<select name="' . $name . '" class="form-select">';
            foreach ($options as $option) {
                $html .= '<option style="font-size:130%;font-family:' . $option . '" value="' . $option . '" ' . ($option == $ov ? ' selected' : '') . '>';
                $html .= $option . '</option>';
            }
            return $html . '</select>';
        },
        'template' => function ($name, $field, $ov) {
            $html = '<select class="' . self::$iclass . ' form-select" name="' . $name . '">';
            $templates = View::getTemplates($field['template']);
            $html .= '<option value=""' . ('' == $ov ? ' selected' : '') . '>' . '[Default]' . '</option>';
            foreach ($templates as $template) {
                $html .= '<option value="' . $template . '"' . ($template == $ov ? ' selected' : '') . '>' . ucwords($template) . '</option>';
            }
            return $html . '</select>';
        }
        ];

        foreach (Config::getList('input-type') as $type => $value) {
            self::$input_type[$type] = $value;
        }
      /* CONTENT
      if($type=='content') {
      $table = $op['table'];
      $tablesrc = explode('.', Config::$content[$table])[0];
      include __DIR__.'/content.php';
      }*/
    }
}
