<?php
session_cache_limiter( 'none' ); //Initialize session
session_start( );

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

error_reporting ( E_ALL | E_STRICT );
ini_set ( "display_errors", 1 );
date_default_timezone_set( 'America/Mexico_City' );

if (file_exists('config/config.php') ){
    define('CURRENT_PATH',dirname(__FILE__));
    require_once 'config/config.php';
} else {
    exit('no fue posible localizar el archivo de configuración.');
}

function __autoload($className) {
    require_once LIBS_PATH . "/{$className}.php";
}

require_once SNIPPETS_PATH . '/db/connection.php';

$site_url = SITE_URL . 'search.html';

$action                         = ( isset( $_GET['action'] ) ) ? $_GET['action']: "";
if ( isset( $_POST[ 'mail' ] ) ) 
    $_SESSION[ 'mailComparer' ]     = $_POST[ 'mail' ];

switch ($action) {
    case 'create':
        $edit    = new Usuarios( $dbh );
        $success = $edit->saveUser( $_POST, 'create' );
        $success = json_encode( $success );
        break;
    case 'edit': 
        $delete    = new Usuarios( $dbh );
        $success = $delete->saveUser( $_POST, 'edit' );
        $success = json_encode( $success );
        break;
    default:
        if ( !isset( $_POST ) ) {
            header( "location:{$site_url}" );
        } else {
            
            $_SESSION[ 'mail' ] = sha1( $_SESSION[ 'mailComparer' ] );
            $check  = new Usuarios( $dbh );
            $success = $check->getExists( $_POST );
            
            header( "location:{$success}" );
        }
        break;
}

echo $success;
