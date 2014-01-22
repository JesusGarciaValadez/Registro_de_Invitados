<?php

session_cache_limiter('none'); //Initialize session
session_start();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/*error_reporting ( E_ALL | E_STRICT );
ini_set ( "display_errors", 1 );
date_default_timezone_set( 'America/Mexico_City' );*/

if (file_exists('config/config.php') ){
    define('CURRENT_PATH',dirname(__FILE__));
    require_once 'config/config.php';
}
else {
    exit('no fue posible localizar el archivo de configuración.');
}

function __autoload($className) {
    require_once LIBS_PATH . "/{$className}.php";
}

require_once SNIPPETS_PATH . '/db/connection.php';

$action = $_GET['action'];
switch ($action) {
    case 'save':
        $edit    = new Usuarios( $dbh );
        $success = $edit->guardarUsuario( $_POST );
        $success = json_encode( $success );
        break;
    case 'edit': 
        $delete    = new Usuarios( $dbh );
        $success = $delete->eliminarComision( $_POST );
        $success = json_encode( $success );
        break;
    default:
        
        break;
}

echo $success;
