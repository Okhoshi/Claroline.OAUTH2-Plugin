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

$conf_def['config_file']  = 'auth.oauth.conf.php';
$conf_def['config_code']  = 'OAUTH';
$conf_def['config_name']  = 'OAuth Servers Suite';
$conf_def['config_class'] = 'admin';

$conf_def['section']['OAuth']['label']       = 'OAuth 2.0 Resources';
$conf_def['section']['OAuth']['description'] = '';
$conf_def['section']['OAuth']['properties']  = array(
    'OAuthEnabled'
);

$conf_def_property_list[ 'OAuthEnabled' ] =
    array ( 'label'       => 'Autorise les connexions via OAuth',
            'description' => '',
            'default'     => TRUE,
            'type'        => 'boolean',
            'display'     => TRUE,
            'readonly'    => FALSE,
            'acceptedValue' => array('TRUE' => 'Oui', 'FALSE' => 'Non')
);

?>