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

// We load the Autoloader for all the classes of the OAuth library. We can't use the loader included into Claroline,
// because there are some dependencies inside the library and so, the loading order is important.
From::module('OAUTH')->uses('OAuth2/Autoloader');
OAuth2\Autoloader::register();

// For the same reason, we load the Claroline-specific storage after the Autoloader. This way, all the dependencies are
// resolved in the correct order.
From::module('OAUTH')->uses('ClarolineStorage');
$storage = new OAuth2\Storage\ClarolineStorage();

// If we need to customize the configuration of the server, we can just do it here.
$config = array();

// We can now create the OAuth server with the Claroline-specific Storage class.
$server = new OAuth2\Server($storage, $config);

// Finally, we define the type of Grant that are allowed by the server.
// AuthorizationCode is the base of OAuth 2.0 (three-pass access)
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
// RefreshToken allows the regeneration of the Access Tokens without re-asking permission from the user.
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));

?>