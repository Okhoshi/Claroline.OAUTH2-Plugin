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

require_once __DIR__ . '/../../claroline/inc/claro_init_global.inc.php';
require_once __DIR__ . '/lib/InitServer.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
// Transform the Authorization Token in an Access Token valid for 3600 sec by default
// and a Refresh Token to use to get new Access Tokens when the previous are expired.
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
