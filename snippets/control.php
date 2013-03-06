<?php

session_cache_limiter('none'); //Initialize session
session_start();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

error_reporting ( E_ALL | E_STRICT );
ini_set ( "display_errors", 1 );
date_default_timezone_set( 'America/Mexico_City' );

if (file_exists('config/config.php') ){
    define('CURRENT_PATH',dirname(__FILE__));
    require_once 'config/config.php';
    require_once LIBS_PATH . '/class.phpmailer.php';
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
    case 'ingresar':
        header('Content-Type: application/json');
        $usuario = new Usuarios($dbh);
        $success = $usuario->login($_POST);
        break;  
    case 'obtenerEmpresa':
        header('Content-Type: application/json');
        $empresas = new Empresas($dbh);
        $success = $empresas->findEmpresas($_REQUEST['name_startsWith']);
        break;
    case 'obtenerEstados':
        header('Content-Type: application/json');
        $estados = new Estados($dbh);
        $success = $estados->findEstados($_REQUEST['empresa']);
        break;
    case 'obtenerCiudad':
        header('Content-Type: application/json');
        $ciudades = new Ciudades($dbh);
        $success = $ciudades->findCiudades($_REQUEST['estado']);
        break;
    case 'validarSesion':
        header('Content-Type: application/json');
        $usuario = new Usuarios($dbh);
        $success = $usuario->isValidSession();
        $success = json_encode(array( 'success' => "'{$success}'", 'message' => '' ));
        break;
    case 'actualizarPassword':
        header('Content-Type: application/json');
        $usuario = new Usuarios($dbh);
        $success = $usuario->updatePassword($_POST);
        break;
    case 'obtenerISR':
        header('Content-Type: application/json');
        $isr = new ISR( $dbh );
        $success = $isr->fetchAll();
        break;
    case 'obtenerEmpresas':
        header('Content-Type: application/json');
        $empresas = new Empresas ( $dbh );
        $success = $empresas->fetchAll();
        break;
    case 'obtenerProductos':
        header('Content-Type: application/json');
        $productos = new Productos( $dbh );
        $success = $productos->getProductos();
        break;
    case 'registrarOrdenVenta':
        header('Content-Type: application/json');
        $ventas = new RegistrarVenta ( $dbh );
        $success = $ventas->registrarVenta( $_POST );
        $success = json_encode( $success );
        break;
    case 'obtenerVendedorPorEmpresa' :
        header('Content-Type: application/json');
        $vendedores = new VendedoresEmpresasRel ( $dbh );
        $success = $vendedores->obtenerVendedores( $_POST );
        $success = json_encode( $success );
        break;
    case 'obtenerReporte':
        $ordenes = new Ordenes ( $dbh );
        $success = $ordenes->obtenerOrdenes( $_POST );
        break;
    default:
        break;
    
}

echo $success;
