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

require_once dirname(__FILE__) . '/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

?>