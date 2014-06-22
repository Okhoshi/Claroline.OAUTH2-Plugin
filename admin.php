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

$pageTitle = array('mainTitle' => 'OAuth 2.0 Administration', 'subTitle' => 'OAuth Clients');

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if(!claro_is_platform_admin()){
    claro_die('Not Allowed');
}

$tableName = get_module_main_tbl( array('oauth_clients') );
$tableName = $tableName['oauth_clients'];

$allowedCommandList = array(
    'Delete',
    'Create'
);

$cmd = isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $allowedCommandList)
    ? $_REQUEST['cmd']
    : '';

$clientId = isset($_REQUEST['clientid'])
    ? $_REQUEST['clientid']
    : '';

$clientName = isset($_REQUEST['client_name'])
    ? $_REQUEST['client_name']
    : '';

$redirectUri = isset($_REQUEST['redirect_uri'])
    ? $_REQUEST['redirect_uri']
    : '';

$dialogBox = new DialogBox();

if(!empty($cmd)){
    switch( $cmd ){
        case 'Delete':
        {
            if ( $clientId !== '' )
            {
                $successMsg = get_lang( 'Client deleted' );
                $sql = "DELETE FROM `" . $tableName . "` WHERE `client_id` = '" . Claroline::getDatabase()->escape($clientId) . "';";
                Claroline::getDatabase()->exec($sql);
            }
        } break;
        case 'Create':
        {
            if ( $clientName === '' || $redirectUri === '' )
            {
                $errorMsg = get_lang( 'Missing required field(s)' );
                break;
            }

            function random($car) {
                $string = "";
                $chaine = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                srand((double)microtime()*1000000);
                for($i=0; $i<$car; $i++) {
                    $string .= $chaine[rand()%strlen($chaine)];
                }
                return $string;
            }

            $sql = 'SELECT `client_id` FROM `' . $tableName . '`;';
            $ids = Claroline::getDatabase()->query($sql);
            $ids = $ids->fetch();

            do
            {
                $clientId = random(16);
            } while ( $ids && in_array( $clientId, $ids ) );

            $clientSecret = random(64);

            $sql = sprintf('INSERT INTO `%s` (`client_id`, `client_name` ,`client_secret`, `redirect_uri`) VALUES (\'%s\', \'%s\', \'%s\', \'%s\')',
                $tableName,
                $clientId,
                Claroline::getDatabase()->escape($clientName),
                $clientSecret,
                Claroline::getDatabase()->escape($redirectUri)
            );
            Claroline::getDatabase()->exec($sql);
            $successMsg = get_lang( 'Client created' );
        } break;
    }
}

$sql = "SELECT `client_name`, `client_id`, `client_secret`, `redirect_uri` FROM `" . $tableName . "`;";
$clients = Claroline::getDatabase()->query($sql);

$template = new ModuleTemplate( $tlabelReq , 'admin.tpl.php' );
$template->assign('clients', $clients);

if( isset( $successMsg ) )
{
    $dialogBox->success( $successMsg );
}

if( isset( $errorMsg ) )
{
    $dialogBox->error( $errorMsg );
}

ClaroBreadCrumbs::getInstance()->append( get_lang('Administration'), get_path('rootAdminWeb') );
ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'mainTitle' ] , $_SERVER[ 'PHP_SELF' ] );
ClaroBreadCrumbs::getInstance()->append( $pageTitle[ 'subTitle' ] );
ClaroBody::getInstance()->appendContent( claro_html_tool_title( $pageTitle )
                                       . $dialogBox->render()
                                       . $template->render()
);

echo Claroline::getInstance()->display->render();

?>