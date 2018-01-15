<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Outil AcidMail, Gestionnaire Mail
 *
 * @package   Acidfarm\Tool
 */
class AcidMail
{
    /**
     * Envoie un e-mail en fonction des paramètres renseignés en entrée et retourne true en cas de réussite, false
     * sinon.
     *
     * @param            $from_name  Nom de l'émetteur.
     * @param            $from_email Email de l'émetteur.
     * @param            $to_email   Email du destinataire.
     * @param            $subject    Sujet de l'e-mail.
     * @param            $body       Corps de l'e-mail.
     * @param bool|false $is_html    True si l'HTML est actif.
     * @param array      $attached   Liste des éléments attachés. ([noms]=>[chemins])
     * @param array      $stream     Liste des éléments attachés en mode stream. ([noms]=>[streams])
     * @param array      $functions  Liste de fonctions à appliquer à l'objet PHPMAILER
     *
     * @return bool
     */
    public static function send(
        $from_name,
        $from_email,
        $to_email,
        $subject,
        $body,
        $is_html = false,
        $attached = [],
        $stream = [],
        $functions = []
    ) {
        Acid::load(Acid::get('externals:phpmailer:path:autoload'));
        
        try {
            Acid::log('MAIL', json_encode([
                'from_name' => $from_name, 'from_email' => $from_email, 'to_email' => $to_email, 'subject' => $subject
            ]));
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->set('exceptions', true);
            
            if (Acid::get('email:method') === 'smtp') {
                $mail->IsSMTP();                                    // set mailer to use SMTP
                $mail->Host = Acid::get('email:smtp:host');        // specify main and backup server
                if (Acid::exists('email:smtp:port')) {
                    $mail->Port = Acid::get('email:smtp:port');
                }
                
                if (Acid::get('email:smtp:user')) {
                    $mail->SMTPAuth = true;
                    $mail->Username = Acid::get('email:smtp:user');
                    $mail->Password = Acid::get('email:smtp:pass');
                }
                
                if (Acid::get('email:smtp:secure')) {
                    $mail->SMTPSecure = Acid::get('email:smtp:secure');
                }
                
                if (Acid::get('email:smtp:debug')) {
                    $mail->SMTPDebug = 1;
                }
            } else {
                $mail->isMail();
            }
            
            $mail->From = $from_email;
            $mail->FromName = utf8_decode($from_name);
            $mail->addCustomHeader('SiteName: ' . Acid::get('url:system'));
            
            if (is_array($to_email)) {
                $nb_dest = 0;
                foreach ($to_email as $to) {
                    if ($nb_dest === 0) {
                        $mail->AddAddress($to);
                    } else {
                        $mail->AddCC($to);
                    }
                    $nb_dest++;
                }
            } else {
                $mail->AddAddress($to_email);
            }
            
            $mail->WordWrap = 80;                                // set word wrap to 80 characters
            $mail->IsHTML($is_html);                                // set email format to HTML
            
            $my_body = utf8_decode(stripslashes($body));
            
            $mail->Subject = utf8_decode(stripslashes($subject));
            $mail->Body = $my_body;
            
            if ($is_html) {
                $mail->AltBody = $mail->html2text($mail->AltBody); //fix isHTMl(true) && isMail() issues
            }
            
            foreach ($attached as $name => $elt) {
                $mail->AddAttachment($elt, utf8_decode($name));
            }
            
            foreach ($stream as $name => $elt) {
                $mail->AddStringAttachment($elt, utf8_decode($name));
            }
            
            if (!empty($functions)) {
                $keys = array_keys($functions);
                
                if (is_numeric($keys[0])) {
                    foreach ($functions as $functab) {
                        call_user_func_array([$mail, $functab['func']], $functab['args']);
                    }
                } else {
                    foreach ($functions as $func => $args) {
                        switch ($func) {
                            default :
                                call_user_func_array([$mail, $func], $args);
                                break;
                        }
                    }
                }
            }
            
            $mail->Send();
        } catch (phpmailerException $e) {
            Acid::log('mail', 'AcidMail::send - error : ' . json_encode($e->getMessage()));
            
            return false;
        }
        
        Acid::log('mail', 'AcidMail::send - success');
        
        return true;
    }
    
    /**
     * Envoie un e-mail au staff
     *
     * @param string $subject   Sujet de l'e-mail.
     * @param string $body      Corps de l'e-mail.
     * @param bool   $is_html   True si l'HTML est actif.
     * @param array  $attached  Liste des éléments attachés. ([noms]=>[chemins])
     * @param array  $stream    Liste des éléments attachés en mode stream. ([noms]=>[streams])
     * @param array  $functions liste de fonctions à appliquer à l'objet PHPMAILER
     * @param string $tpl       le fichier template à utiliser
     *
     * @return boolean
     */
    public static function sendStaff(
        $subject,
        $body,
        $is_html = true,
        $attached = [],
        $stream = [],
        $functions = [],
        $tpl = null
    ) {
        $tpl = $tpl === null ? 'mail/staff.tpl' : $tpl;
        
        $from_name = Acid::get('site:name');
        $from_email = Acid::get('site:email');
        $to_email = Acid::get('site:email');
        
        return self::send($from_name, $from_email, $to_email, $subject, $body, $is_html, $attached, $stream,
            $functions);
    }
}
