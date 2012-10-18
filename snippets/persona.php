<?php
	/**
	 *	@author:	Jesús Antonio García Valadez
	 *	@descr:		File to process the information from the form
	 *	@require:	lib/DBQueries.php
	 *
	 */
	//error_reporting ( E_ALL | E_STRICT );
	ini_set ( "display_errors", 1 );
	date_default_timezone_set( 'America/Mexico_City' );

	//	Include the DB Library
	require_once ( 'lib/DBQueries.php' );

	// Obtain the parameter from the page, make variable with they and discriminate them to make the query

	foreach($_GET as $index => $value){
		switch($index){
		
			case 'id':
			     if( strlen ( trim ( $value ) ) == 0 ) {
			         die('Debe escribir su nombre.');
			     }
			     break;
			 default:
			     die('No se encuentra el perfil solicitado.');
			     break;
		}

		$$index	= strip_tags ( stripslashes ( htmlentities( ( string ) $value, ENT_COMPAT, 'utf-8' ) ) );
	}

	// Body Query
	$principalQuery    = "SELECT p.`id_persona` , 
	                    p.`nombre` , 
                        p.`apellido_pat` , 
                        p.`apellido_mat` , 
                        p.`edad` , 
                        p.`escolaridad` , 
                        p.`ocupacion` , 
                        p.`correo` , 
                        p.`distrito` , 
                        p.`semblanza` , 
                        p.`foto` , 
                        m.`genero` , 
                        m.`tipo_vendedor` , 
                        car.`cargo` , 
                        e.`eleccion` , 
                        u.`circu` , 
                        d.`estado` ,
                        o.`comision` ,  
                        a.`area` 
                        FROM `persona` p LEFT OUTER JOIN `metadata` m 
                            ON p.`id_metadata`  = m.`id_metadata` 
                        LEFT OUTER JOIN `cargo` car 
                            ON p.`id_cargo`		= car.`id_cargo` 
                        LEFT OUTER JOIN `eleccion` e 
                            ON p.`id_eleccion`	= e.`id_eleccion` 
                        LEFT OUTER JOIN `circu` u 
                            ON p.`id_circu`		= u.`id_circu` 
                        LEFT OUTER JOIN `estado` d 
                            ON p.`id_estado`	= d.`id_estado` 
                        LEFT OUTER JOIN `comision` o 
                            ON p.`id_comision`	= o.`id_comision` 
                        LEFT OUTER JOIN `area` a 
                            ON p.`id_area`		= a.`id_area` 
                        WHERE p.`id_persona` = '{$id}' 
                        ORDER BY p.`id_persona` ";

    //  Do the query and obtain the results put them into a table
	$dbConnector	= new DBQueries ( );
	$dbConnector->executeQuery( $principalQuery );

	if ( $dbConnector->getRow( $principalQuery ) ) {
	    $container = $dbConnector->getRows( $principalQuery );

	    foreach ( $container as $index => $array ) {
	    
    	    foreach ( $array as $key => $value ) {
    	       switch ( $key ) {
    	           case 'id_persona':      $id_persona     = $value;
    	               break;
    	           case 'nombre':          $nombre         = $value;
    	               break;
    	           case 'apellido_pat':    $apellido_pat   = $value;
    	               break;
    	           case 'apellido_mat':    $apellido_mat   = $value;
    	               break;
    	           case 'edad':            $edad           = $value;
    	               break;
    	           case 'escolaridad':     $escolaridad    = $value;
    	               break;
    	           case 'ocupacion':       $ocupacion      = $value;
    	               break;
    	           case 'correo':          $correo         = $value;
    	               break;
    	           case 'distrito':        $distrito       = $value;
    	               break;
    	           case 'semblanza':       $semblanza      = $value;
    	               break;
    	           case 'foto':            $foto           = $value;
    	               break;
    	           case 'genero':          $genero         = $value;
    	               break;
    	           case 'tipo_vendedor':   $tipo_vendedor  = $value;
    	               break;
    	           case 'cargo':           $cargo          = $value;
    	               break;
    	           case 'eleccion':        $eleccion       = $value;
    	               break;
    	           case 'circu':           $circu          = $value;
    	               break;
    	           case 'estado':          $estado         = $value;
    	               break;
    	           case 'comision':        $comision       = $value;
    	               break;
    	           case 'area':            $area           = $value;
    	               break;
    	           default:
    	               break;
    	       }
    	   }

    	   $id_persona     = html_entity_decode( $id_persona, ENT_COMPAT, 'UTF-8' );
    	   $nombre         = ucwords( html_entity_decode( $nombre, ENT_COMPAT, 'UTF-8' ) );
    	   $apellido_pat   = ucwords( html_entity_decode( $apellido_pat, ENT_COMPAT, 'UTF-8' ) );
    	   $apellido_mat   = ucwords( html_entity_decode( $apellido_mat, ENT_COMPAT, 'UTF-8' ) );
    	   $edad           = html_entity_decode( $edad, ENT_COMPAT, 'UTF-8' );
    	   $escolaridad    = ucwords( html_entity_decode( $escolaridad, ENT_COMPAT, 'UTF-8' ) );
    	   $ocupacion      = ucwords( html_entity_decode( $ocupacion, ENT_COMPAT, 'UTF-8' ) );
    	   $correo         = html_entity_decode( $correo, ENT_COMPAT, 'UTF-8' );
    	   $distrito       = ucwords( html_entity_decode( $distrito, ENT_COMPAT, 'UTF-8' ) );
    	   $semblanza      = ucwords( html_entity_decode( $semblanza, ENT_COMPAT, 'UTF-8' ) );
    	   $foto           = html_entity_decode( $foto, ENT_COMPAT, 'UTF-8' );
    	   $genero         = ucwords( html_entity_decode( $genero, ENT_COMPAT, 'UTF-8' ) );
    	   $tipo_vendedor  = ucwords( html_entity_decode( $tipo_vendedor, ENT_COMPAT, 'UTF-8' ) );
    	   $cargo          = ucwords( html_entity_decode( $cargo, ENT_COMPAT, 'UTF-8' ) );
    	   $eleccion       = ucwords( html_entity_decode( $eleccion, ENT_COMPAT, 'UTF-8' ) );
    	   $circu          = ucwords( html_entity_decode( $circu, ENT_COMPAT, 'UTF-8' ) );
    	   $estado         = ucwords( html_entity_decode( $estado, ENT_COMPAT, 'UTF-8' ) );
    	   $comision       = ucwords( html_entity_decode( $comision, ENT_COMPAT, 'UTF-8' ) );
    	   $area           = ucwords( html_entity_decode( $area, ENT_COMPAT, 'UTF-8' ) );
            
    	   //	Obtain the phtml file for form the email body
    	   $file	= '../persona.phtml';
            
    	   if ( $file && !empty ( $file ) && is_string ( $file ) ) {
    	       if ( file_exists ( $file ) ) {
            		$handle	= fopen ( $file, 'r' );
            		$body	= fread ( $handle, filesize ( $file )  );
            		fclose ( $handle );
               }
 
               $body	= str_replace( 'nombreUser', $nombre, $body );
               $body	= str_replace( 'apellidoPatUser', $apellido_pat, $body );
               $body	= str_replace( 'apellidoMatUser', $apellido_mat, $body );
               $body	= str_replace( 'edadUser', $edad, $body );
               $body	= str_replace( 'escolaridadUser', $escolaridad, $body );
               $body	= str_replace( 'ocupacionUser', $ocupacion, $body );
               $body	= str_replace( 'correoUser', $correo, $body );
               $body	= str_replace( 'distritoUser', $distrito, $body );
               $body	= str_replace( 'semblanzaUser', $semblanza, $body );
               $body	= str_replace( 'fotoUser', $foto, $body );
               $body	= str_replace( 'generoUser', $genero, $body );
               $body	= str_replace( 'tipoVendedorUser', $tipo_vendedor, $body );
               $body	= str_replace( 'cargoUser', $cargo, $body );
               $body	= str_replace( 'eleccionUser', $eleccion, $body );
               $body	= str_replace( 'circuUser', $circu, $body );
               $body	= str_replace( 'estadoUser', $estado, $body );
               $body	= str_replace( 'comisionUser', $comision, $body );
               $body	= str_replace( 'areaUser', $area, $body );
           }
    	    
	    }
        
        echo $body;
	} else {
    	die( 'No hay resultados disponibles.' );
	}
?>