<?php

/**
 * Récupération de la liste des arguments passés en paramètres
 *
 * @param $argv
 *
 * @return array
 */
function arguments($argv)
{
    $arguments = [];
    if ($argv) {
        foreach ($argv as $value) {
            if (strpos($value, '--') === 0) {
                $temp = explode('=', $value);
                $cur_index = substr($temp[0], strlen('--'));
                $cur_value = implode('=', array_slice($temp, 1));
                $arguments[$cur_index] = $cur_value;
            }
        }
    }
    
    return $arguments;
}

/**
 * Récupération de la liste des arguments passés en paramètres
 *
 * @param $argv
 *
 * @return array
 */
function add_default_arguments($current_values = [])
{
    $inputs = [];
    
    if ($steps = get_steps()) {
        foreach ($steps as $step) {
            $config = $step . '/config.php';
            
            //ajout de valeurs à $inputs
            if (file_exists($config)) {
                require $config;
            }
        }
    }
    
    foreach ($inputs as $vname => $vtab) {
        if (!isset($current_values[$vname]) && isset($vtab['default'])) {
            $current_values[$vname] = $vtab['default'];
        }
    }
    
    return $current_values;
}

/**
 * Retourne les steps de configuration
 *
 * @return array
 */
function get_steps()
{
    $dir = INSTALL_PATH . 'core/steps/';
    $folders = [];
    $folder = opendir($dir);
    while ($file = readdir($folder)) {
        if ($file != "." && $file != ".." && is_dir($dir . $file)) {
            $folders[] = $dir . $file;
        }
    }
    sort($folders);
    closedir($folder);
    
    return $folders;
}

/**
 * Retourne les steps de configuration
 *
 * @return array
 */
function print_steps()
{
    if ($steps = get_steps()) {
        foreach ($steps as $step) {
            $config = $step . '/config.php';
            $form = $step . '/form.php';
            if (file_exists($config)) {
                require $config;
            }
            
            if (file_exists($form)) {
                include $form;
            }
        }
    }
}

/**
 * Affiche l'erreur avec son habillage
 *
 * @return array
 */
function print_error_and_exit($error, $flag_title = 'Error')
{
    if (php_sapi_name() == "cli") {
        echo "\n ! " . $error . " !\n";
    } else {
        include INSTALL_PATH . 'core/template/head.php';
        echo '<div class="corpus"><div class="block"><h3>' . $flag_title . '</h3>' . $error . '</div></div>';
        include INSTALL_PATH . 'core/template/foot.php';
    }
    exit();
}

/**
 * Affiche l'erreur avec son habillage
 *
 * @return array
 */
function print_cli_variable_detail($key, $fields, $all_values)
{
    $conf = $fields[$key];
    $title = (isset($conf['title']) ? $conf['title'] : '');
    $desc = (isset($conf['desc']) ? $conf['desc'] : '');
    $value = isset($all_values[$key]) ? $all_values[$key] : null;
    $values = isset($conf['values']) ? $conf['values'] : (!empty($conf['bool']) ? [0 => 'off', 1 => 'on'] : []);
    
    $values_txt = '';
    if ($values) {
        $simple_print = true;
        $advanced_print = [];
        foreach ($values as $vkey => $vvalue) {
            if ($vkey != $vvalue) {
                $simple_print = false;
            }
            
            $advanced_print[] = $vkey . ' : ' . $vvalue;
            
            if ($simple_print) {
                $values_txt = '{ ' . implode(', ', array_keys($values)) . ' }' . "\n";
            } else {
                $values_txt = '{' . "\n" . implode("\n", $advanced_print) . "\n" . '}' . "\n";
            }
        }
    }
    
    return '--' . $key
           . "\n" .
           $title . ' : < ' . $value . ' >'
           . "\n"
           . ($desc ? ($desc . "\n") : '')
           . $values_txt
           . "\n";
}

/**
 * Affiche l'erreur avec son habillage
 *
 * @return array
 */
function print_cli_help()
{
    $help = '';
    $values = [];
    
    $infos = [
        'help'         => ['title' => 'Print help'],
        'quickinstall' => [
            'title' => 'Disable install assistant',
            'desc'  => 'only use default values and parameters'
        ]
    ];
    foreach ($infos as $ikey => $iconf) {
        $help .= print_cli_variable_detail($ikey, $infos, [$ikey => 'If defined']);
    }
    
    $help .= "\n\n";
    
    //For each step, show current configuration...
    if ($steps = get_steps()) {
        $inputs = [];
        
        foreach ($steps as $step) {
            $config = $step . '/config.php';
            
            if (file_exists($config)) {
                require $config;
            }
        }
        
        if ($inputs) {
            foreach ($inputs as $key => $conf) {
                $values[$key] = isset($conf['default']) ? $conf['default'] : null;
                $help .= print_cli_variable_detail($key, $inputs, $values);
            }
        }
    }
    
    return $help;
}

/**
 * Récupération de la liste des arguments passés en paramètres
 *
 * @param $argv
 *
 * @return array
 */
function form_from_input($input, $inputs)
{
    $config = isset($inputs[$input]) ? $inputs[$input] : false;
    
    if ($config) {
        $default = isset($config['default']) ? $config['default'] : '';
        $title = isset($config['title']) ? $config['title'] : '';
        $desc = isset($config['desc']) ? $config['desc'] : '';
        $warning = isset($config['warning']) ? $config['warning'] : '';
        $is_password = !empty($config['password']);
        $is_bool = !empty($config['bool']);
        $is_multi = !empty($config['multi']);
        $has_already_label = false;
        $field = '';
        
        if (!empty($config['values'])) {
            $select = $is_multi ? '' : '<select name="' . $input . '" >';
            foreach ($config['values'] as $value => $label) {
                if ($is_multi) {
                    $has_already_label = true;
                    $selected = $value == $default ? 'checked="checked"' : '';
                    $select .= '<label>' .
                               '<input
                            type="checkbox"
                            name="' . $input . '[]" 
                            value="' . htmlspecialchars($value) . '"
                        ' . $selected . '/>' .
                               ' ' . htmlspecialchars($label) . ' ' .
                               '</label>';
                } else {
                    $selected = $value == $default ? 'selected="selected"' : '';
                    $select .= '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>'
                               . htmlspecialchars($label) . '</option>';
                }
            }
            $select .= $is_multi ? '' : '</select>';
            
            $field = $select;
        } elseif ($is_bool) {
            $checked = $default ? 'checked="checked"' : '';
            $field = '<input name="' . $input . '" type="checkbox" value="1" ' . $checked . '/>';
        } else {
            $field = '<input type="' . ($is_password ? 'password' : 'text') . '" name="' . $input . '" value="'
                     . htmlspecialchars($default) . '"/>';
        }
        
        return ($has_already_label ? '' : '<label>') .
               htmlspecialchars($title) . ' : ' .
               $field .
               ($desc ? (' <i>' . htmlspecialchars($desc) . '</i>') : '') .
               ($warning ? (' - <b>' . htmlspecialchars($warning) . '</b>') : '') .
               ($has_already_label ? '' : '</label>');
    }
    
    return '';
}

/**
 * @param string $variable $ is put before
 * @param string $value    value is put between '
 * @param bool   $is_a_comment
 *
 * @return string
 */
function get_sf_line_for_string($variable, $value, $is_a_comment = false, $description = '')
{
    return get_sf_line($variable, "'" . addslashes($value) . "'", $is_a_comment, $description);
}

/**
 * @param string $variable $ is put before
 * @param mixed  $value    value is put as true if not empty, false otherwise
 * @param bool   $is_a_comment
 *
 * @return string
 */
function get_sf_line_for_bool($variable, $value, $is_a_comment = false, $description = '')
{
    return get_sf_line($variable, ($value ? 'true' : 'false'), $is_a_comment, $description);
}

/**
 * @param        $variable $ is put before
 * @param        $array    array is converted as text
 * @param bool   $is_a_comment
 * @param string $description
 *
 * @return string
 */
function get_sf_line_for_string_array($variable, $array, $is_a_comment = false, $description = '')
{
    $array_txt = 'array()';
    if ($array && is_array($array)) {
        $new_array = [];
        foreach ($array as $elt) {
            $new_array[] = "'" . addslashes($elt) . "'";
        }
        $array_txt = 'array(' . implode(',', $new_array) . ')';
    }
    
    return get_sf_line($variable, $array_txt, $is_a_comment, $description);
}

/**
 * @param string $variable
 *
 * @return string
 */
function get_sf_variable($variable)
{
    $base = '$';
    if ($variable) {
        $parse = explode(':', $variable);
        
        $base .= $parse[0];
        $parse = array_slice($parse, 1);
        if ($parse) {
            foreach ($parse as $subvariable) {
                $base .= "['$subvariable']";
            }
        }
        
        return $base;
    }
    
    return '$acid_undefined[]';
}

/**
 * @param $array
 *
 * @return string
 */
function get_sf_concat_variables($array)
{
    if ($array && is_array($array)) {
        $new_array = [];
        foreach ($array as $variable) {
            $new_array[] = get_sf_variable($variable);
        }
        
        return implode('.', $new_array);
    }
    
    return get_sf_variable($array);
}

/**
 * @param string $variable $ is put before
 * @param string $value    add ' for string
 * @param bool   $is_a_comment
 *
 * @return string
 */
function get_sf_line($variable, $value, $is_a_comment = false, $description = '')
{
    return ($is_a_comment ? '//' : '') .
           get_sf_variable($variable) . '    =   ' . $value . ';' .
           ($description ? ('//' . $description) : '') . "\n";
}

/**
 * @param $label
 *
 * @return string
 */
function get_sf_label_line($label)
{
    return "\n" . '//--' . $label . "\n";
}

/**
 * @param $label
 *
 * @return string
 */
function get_sf_comment_line($comment)
{
    return '//' . $comment . "\n";
}

/**
 * @return string
 */
function get_sf_start()
{
    return '<?php ';
}

/**
 * @return string
 */
function get_sf_stop()
{
    return "\n\n";
}

/**
 * @param $label
 *
 * @return string
 */
function get_sf_line_skip()
{
    return "\n";
}


/**
 * Retourne la valeur du tableau associée à la clé en entrée
 * Return la valeur par défaut si non défini
 *
 * @param      $key
 * @param      $tab
 * @param null $def
 *
 * @return null
 */
function get_in_tab($key,$tab,$def=null)
{
    return isset($tab[$key]) ? $tab[$key] : $def;
}

/**
 *  Génération aléatoire de mot de passe
 *
 * @return string
 */
function getRandPasswordSalt()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $nbchars = strlen($chars);
    $ident = '';
    
    $salt_size = 8;
    for ($i = 0; $i <= $salt_size; $i++) {
        $ident .= $chars[rand(0, $nbchars - 1)];
    }
    
    return $ident;
}

/**
 * Retourne la liste des thèmes disponibles à l'install
 *
 * @return string
 */
function getThemes()
{
    $themes = ['' => 'default'];
    $path = INSTALL_PATH . '../theme';
    
    if (is_dir($path)) {
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, ['.', '..', 'default'])) {
                    if (is_dir($path . '/' . $entry)) {
                        $themes[$entry] = $entry;
                    }
                }
            }
            
            closedir($handle);
        }
    }
    
    return $themes;
}

/**
 * Test si les informations de BDD sont correctes
 *
 * @param string $db_type
 * @param string $db_host
 * @param string $db_port
 * @param string $db_base
 * @param string $db_user
 * @param string $db_pass
 *
 * @return boolean
 */
function checkDataBase($db_type, $db_host, $db_port, $db_base, $db_user, $db_pass)
{
    $db = new PDO($db_type .
                  ':host=' . $db_host .
                  ';port=' . $db_port .
                  ';dbname=' . $db_base,
        $db_user,
        $db_pass
    );
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //ACCESS
    $rand = rand(1, 100);
    $res = $db->query('SELECT ' . $rand . ' as rand_value')->fetch(PDO::FETCH_ASSOC);
    
    return ($res['rand_value'] == $rand);
}

function checkEmail($from, $to, $mail_method, $mailhost, $mailport, $mailuser, $mailpass, $mailsecure)
{
    require INSTALL_PATH . '../acid/externals/PHPMailer/PHPMailerAutoload.php';
    
    $subject = "Test email using php";
    $body = "This is a test email message.";
    if ($mail_method == 'smtp') {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->set('exceptions', true);
            
            $mail->IsSMTP();
            $mail->Host = $mailhost;
            if ($mailport) {
                $mail->Port = $mailport;
            }
            
            if ($mailuser) {
                $mail->SMTPAuth = true;
                $mail->Username = $mailuser;
                $mail->Password = $mailpass;
            }
            
            if ($mailsecure) {
                $mail->SMTPSecure = $mailsecure;
            }
            
            $mail->SMTPDebug = 0;
            
            $mail->From = $from;
            $mail->FromName = utf8_decode('Mail Checker');
            $mail->AddAddress($to);
            
            $mail->Subject = utf8_decode(stripslashes($subject));
            $mail->Body = $body;
            
            return $mail->Send();
        } catch (phpmailerException $e) {
            return $e->getMessage();
        }
    } else {
        return mail($to, $subject, $body, 'From: Mail Checker <' . $from . '>' . "\r\n");
    }
    
    return false;
}