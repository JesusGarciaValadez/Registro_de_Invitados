<?php

session_cache_limiter('none'); //Initialize session
session_start();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: text/html; charset=utf-8');


if (file_exists('config/config.php') ){
    define('CURRENT_PATH',dirname(__FILE__));    
    require_once 'config/config.php';
    require_once SNIPPETS_PATH . '/db/connection.php';
    require_once LIBS_PATH . '/class.phpmailer.php';
}
else {
    exit('no fue posible localizar el archivo de configuración.');
}

function __autoload($className)
{    
    require_once LIBS_PATH . "{$className}.php";    
}

echo '/************** Registrar Venta ********************** / <br />';

$vendedores = new VendedoresEmpresasRel ( $dbh );
        $data = array( 'id_empresa' => 7 );
        $response = $vendedores->obtenerVendedores( $data );


/*
$usuario = new Usuarios( $dbh );

$data = array (
    'email' => 'augusto@ingeniagroup.com.mx'
    ,'password' => '123456789'
);

$response = $usuario->login($data);
*/
var_dump($response);