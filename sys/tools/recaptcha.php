<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

class Recaptcha
{
    /**
     * @return mixed
     */
    public static function publickey() {
        return Conf::get('recaptcha:site_key');
    }
    
    /**
     * @return mixed
     */
    public static function privatekey() {
        return Conf::get('recaptcha:secret');
    }
    
    /**
     * @return bool
     */
    public static function isEnabled() {
        return (bool) static::publickey();
    }
    
    /**
     * @return string
     */
    public static function jsLoader() {
        return static::isEnabled() ? '<script src="https://www.google.com/recaptcha/api.js?hl='.Acid::get('lang:current').'"></script>' : '';
    }
    
    /**
     * @return string
     */
    public static function front() {
        return static::isEnabled() ? '<div class="g-recaptcha" data-sitekey="'.static::publickey().'"></div>' : '';
    }
    
    /**
     * @param null $datas
     *
     * @return bool
     */
    public static function validate($datas=null) {
        $datas = $datas===null ? $_POST : $datas;
        if (static::isEnabled()) {

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $fields = array(
                'secret' => static::privatekey(),
                'response' => urlencode(Lib::getIn('g-recaptcha-response',$datas,'')),
                'remoteip' => urlencode(Lib::getIn('REMOTE_ADDR',$_SERVER,''))
            );
    
            //url-ify the data for the POST
            $fields_string = '';
            foreach($fields as $key=>$value) {
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');
    
            //open connection
            $ch = curl_init();
    
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            //execute post
            $decode = json_decode(curl_exec($ch),true);
    
            //close connection
            curl_close($ch);
   
            return $decode['success'];
        }
        
        return true;
    }
    
}