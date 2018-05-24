<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur d'Ajax
 *
 * @package   Acidfarm\Controller
 */
class AjaxController
{
    public function sample()
    {
        $results = ['elts' => [1, 2, 4, 9]];
        
        //Error sample with data only
        if (empty($_GET['success'])) {
            Ajax::setSuccess(false);
            Conf::addToContent(Ajax::returnJsonData($results));
        } //Success sample with data and preformatted content
        else {
            $content = '<p>Sample content with js </p>' .
                       '<script type="text/javascript">' .
                       'alert(1);' .
                       '</script>';
            
            Ajax::setSuccess(true);
            Conf::addToContent(
                Ajax::returnJson(
                    $content,
                    'Sample Title',
                    $results)
            );
        }
    }
    
    /**
     * 404
     */
    public function call404()
    {
        Rest::status404();
    }
}
