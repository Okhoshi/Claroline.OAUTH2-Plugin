<?php
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for OAUTH config file
 *
 * @version 0.1
 *
 * @copyright 2014 Quentin Devos
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Quentin Devos <q.devos@student.uclouvain.be>
 *
 * @package OAUTH
 */

$conf_def['config_file']='OAUTH.conf.php';
$conf_def['config_code']='OAUTH';
$conf_def['config_name']='OAuth Servers Suite';
$conf_def['config_class']='applet';


?>