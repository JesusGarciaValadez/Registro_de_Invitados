<?php
session_cache_limiter( 'none' ); //Initialize session
session_start( );

header( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past

if ( file_exists( 'snippets/config/config.php' ) ) {
    define( 'CURRENT_PATH',dirname ( __FILE__ ) );
    require_once 'snippets/config/config.php';
    require_once LIBS_PATH . '/class.phpmailer.php';
    require_once SNIPPETS_PATH . '/db/connection.php';
}
else {
    exit( 'no fue posible localizar el archivo de configuración.' );
}

function __autoload( $className ) { 
    require_once LIBS_PATH . "/{$className}.php";
}

$usuario = new Usuarios( $dbh );
$vendedores = new VistaVendedores( $dbh );

$site_url = SITE_URL . 'login.html?msg=la_sesi&oacute;n_no_es_v&aacute;lida';

if ( $usuario->isValidSession( ) ) {
    
    if ( isset( $_SESSION[ 'user' ][ 'id' ] ) ) {
    
        View::setViewFilesRepository( CHUNKS_PATH );

        $vista = new View( 'search.html' );

        $topVendedores = $vendedores->getTopTen( );

        $vista->setVars( array(
            'full_name' => $_SESSION[ 'user' ][ 'nombre' ]
            ,'top_vendedores' => $topVendedores
        ));
    
        $page = $vista->render( );
        
        echo $page;

    } else {
        header( "location:{$site_url}" );
    }
    
} else {
    header( "location:{$site_url}" );
}