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

// error reporting (this is a demo, after all!)
ini_set('display_errors',1);error_reporting(E_ALL);

From::module($tlabelReq)->uses('OAuth2/Autoloader');
OAuth2\Autoloader::register();

From::module($tlabelReq)->uses('ClarolineStorage');
$storage = new OAuth2\Storage\ClarolineStorage();

$server = new OAuth2\Server($storage);
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));

?>