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
require_once __DIR__ . '/../../claroline/inc/claro_init_global.inc.php';

$tlabelReq = 'OAUTH';

// As we are trying to get the authorization of a particular user, it works better if this one is logged in.
if (!claro_is_user_authenticated()) {
    claro_disp_auth_form();
}

// Initialization of the OAuth Server.
require_once __DIR__ . '/lib/InitServer.php';

// Set up the request from the Globals.
$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// Submit the request to the OAuth server. In particular, the server check that the client asking access is well registered,
// all the requested parameters are presents and the scope, if any, is valid.
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// If POST is empty, we know that the user has not authorized the client yet. So we show the form.
if (empty($_POST)) {
    // Get the client name from the Storage.
    $client_name = $server->getStorage('client')->getClientDetails($request->query('client_id'))['client_name'];

    $template = new ModuleTemplate( $tlabelReq, 'authorization_form.tpl.php' );
    $template->assign('clientName', $client_name);

    ClaroBody::getInstance()->appendContent($template->render());

    echo Claroline::getDisplay()->render();
} else {
    // is_authorized is TRUE iff the user clicked "Yes" on the Authorization Form.
    $is_authorized = isset($_POST['authorized']);

    // If the client is authorized, we can process and associate a freshly created Authorization Token to the user_id,
    // and return it to the client.
    $server->handleAuthorizeRequest($request, $response, $is_authorized, claro_get_current_user_id());
    $response->send();
}
