<?php

if (Acid::get('sentry:url')) {

    try {
            $client = new Raven_Client(Acid::get('sentry:url'));

            $error_handler = new Raven_ErrorHandler($client);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler();
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
