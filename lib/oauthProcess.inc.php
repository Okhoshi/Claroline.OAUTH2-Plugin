<?php
/**
 * CLAROLINE
 *
 * @version 0.1
 *
 * @copyright (c) 2014 Quentin Devos
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package OAUTH
 *
 * @author Quentin Devos <q.devos@student.uclouvain.be>
 *
 */

require_once get_module_path('OAUTH') . '/lib/InitServer.php';

// Check that the provided Access Token is valid.
if ($server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    // Retrieve informations from the token and log in the corresponding user.
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    $GLOBALS['_uid'] = $token['user_id'];
    $GLOBALS['uidReset'] = true;

    $claro_loginRequested = true;
    $claro_loginSucceeded = true;
} else {
    $GLOBALS['_uid'] = null;
    $claro_loginSucceeded = false;
    $claro_loginRequested = false;
}