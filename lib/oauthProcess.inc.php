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
$claro_loginRequested = true;

require get_module_path('OAUTH') . '/lib/InitServer.php';

if ($server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    $_uid = $token['user_id'];
    $uidReset = true;

    $claro_loginRequested = true;
    $claro_loginSucceeded = true;
} else {
    $_uid = null;
    $claro_loginSucceeded = false;
}
?>