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
$tlabelReq = 'OAUTH';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if (!claro_is_user_authenticated()) {
    claro_disp_auth_form();
}

require_once dirname( __FILE__ ) . '/lib/InitServer.php';

$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// display an authorization form
if (empty($_POST)) {
    $template = new ModuleTemplate( $tlabelReq, 'authorization_form.tpl.php' );
    $template->assign('clientName', 'TestClient');

    ClaroBody::getInstance()->appendContent($template->render());

    echo Claroline::getDisplay()->render();
} else {
    // print the authorization code if the user has authorized your client
    $is_authorized = isset($_POST['authorized']);
    $server->handleAuthorizeRequest($request, $response, $is_authorized, claro_get_current_user_id());
    $response->send();
}

?>