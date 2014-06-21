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
  'Delete'
);

$cmd = isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $allowedCommandList)
    ? $_REQUEST['cmd']
    : '';

$clientId = isset($_REQUEST['clientid'])
    ? $_REQUEST['clientid']
    : '';

$dialogBox = new DialogBox();

if(!empty($cmd)){
    switch( $cmd ){
        case 'Delete':
        {
            $successMsg = get_lang('Client deleted');
            $sql = "DELETE FROM `" . $tableName . "` WHERE `client_id` = '" . $clientId . "';";
            Claroline::getDatabase()->exec($sql);
        } break;
    }
}

$sql = "SELECT `client_name`, `client_id`, `client_secret`, `redirect_uri` FROM `$tableName`;";
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