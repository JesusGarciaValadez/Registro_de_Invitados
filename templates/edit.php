<?php
session_cache_limiter( 'none' ); //Initialize session
session_start( );

header( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past

if ( file_exists( 'snippets/config/config.php' ) ) {
    define( 'CURRENT_PATH',dirname ( __FILE__ ) );
    require_once 'snippets/config/config.php';
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
    
        $indice = 1;
        $distritosHTML  = '';
    
        View::setViewFilesRepository( CHUNKS_PATH );

        $vista = new View( 'edit_user.html' );

        $infoUsers = $vendedores->getInfoUser( $_GET['id'] );

        foreach ( $infoUsers as $key => $value ) {
            foreach ( $value as $empleado => $valor ) {

                $$empleado  = $valor;
                $indice++;
            }
        }
        
        $distritos          = $vendedores->getDistrito( );
        $generos            = $vendedores->getGenero( );
        $estados            = $vendedores->getEstados( );
        $eleccion           = $vendedores->getEleccion( );
        $comisiones         = $vendedores->getComisiones( );
        $cargos             = $vendedores->getCargos( );
        $comisionesCargos   = $vendedores->getUserComissions( $_GET['id'] );
        
        $vista->setVars( array(
            'full_name' => $_SESSION[ 'user' ][ 'nombre' ]
            ,'id' => $ID_User
            ,'nombre' => $User_Name
            ,'apellido_paterno' => $First_Name
            ,'apellido_materno' => $Last_Name
            ,'email' => $Email
            ,'genero' => $generos
            ,'edad' => $Edad
            ,'distritos' => $distritos
            ,'estado' => $estados
            ,'eleccion' => $eleccion
            ,'ocupacion' => $Ocupacion
            ,'escolaridad' => $Escolaridad
            ,'suplente' => $Suplente
            ,'area' => $Area
            ,'foto' => $Foto
            ,'comisiones' => $comisiones
            ,'cargos' => $cargos
            ,'comisionesCargos' => $comisionesCargos
        ));
    
        $page = $vista->render( );
        
        echo $page;

    } else {
        header( "location:{$site_url}" );
    }
    
} else {
    header( "location:{$site_url}" );
}