<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tests
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

if (Acid::get('sentry:url')) {

    try {
            $client = new Raven_Client(Acid::get('sentry:url'));

            $sentry_report_level = ( (Acid::exists('sentry:report_level') ? Acid::get('sentry:report_level') : -1 ) );

            $error_handler = new Raven_ErrorHandler($client);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler(true, $sentry_report_level);
            $error_handler->registerShutdownFunction();

            $client->user_context(User::curUser()->getVals());
            $client->extra_context(array('php_version'=>phpversion(),'session'=>AcidSession::getInstance()->data));

    } catch (Exception $e) {
        trigger_error(
                'Unable to work with Raven_Client due to sentry url :'.Acid::get('sentry:url'). "\n" .
                "Exception reçue : ".$e->getMessage()."\n"
        );
    }

}
