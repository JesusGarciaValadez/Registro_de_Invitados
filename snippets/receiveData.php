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
	foreach($_POST as $index => $value){
	   
		switch($index){
		
			case 'nombre':
			     if( strlen ( trim ( $value ) ) == 0 ) {
			         die('Debe escribir su nombre.');
			     }
			     $query  = "WHERE p.`nombre` LIKE '%{$value}%' 
                                OR p.`apellido_pat` LIKE '%{$value}%' 
                                OR p.`apellido_mat` LIKE '%{$value}%' 
                                OR CONCAT(p.`nombre`, ' ', p.`apellido_pat`, ' ', p.`apellido_mat`) LIKE '%{$value}%' 
                            ORDER BY p.`id_persona` ";
			     break;
			     
			case 'comision':
			     if( strlen ( trim ( $value ) ) == 0 ) {
			         die('Debe escribir una opción válido.');
			     }
			     if ( $value == "mesa_directiva" ) {
    			     $value  = ucwords( str_replace( '_', ' ', $value ) );
			         $query  = "WHERE car.`cargo` LIKE '%{$value}%' 
			                   ORDER BY p.`id_persona` ";
                 } else if ( $value == "integrante" ) {
                     $value  = ucwords( str_replace( '_', ' ', $value ) );
			         $query  = "WHERE car.`cargo` LIKE '%{$value}%'  
                               ORDER BY p.`id_persona` ";
                 } else {
                     die('Debe elegir una opción válida.');
                 }
			     break;
			     
			case 'estado':
			     if( strlen ( trim ( $value ) ) == 0 ) {
			         die('Debe elegir un estado.');
			     }
			     if ( $value == "escoge_una_opcion" ) {
                     die('Debe elegir una opción válida.');
			     } else {
			         $value  = ucwords( str_replace( '_', ' ', $value ) );
    			     $query  = "WHERE d.`estado` LIKE '%{$value}%' 
                               ORDER BY d.`id_estado` ";
			     }
			     break;
			     
			case 'area':
			     if( strlen ( trim ( $value ) ) == 0 ) {
			         die('Debe escribir su área válida.');
			     }
			     $query  = "WHERE a.`area` LIKE '%{$value}%' 
                           ORDER BY a.`id_area` ";
			     break;
		}

		$$index	= strip_tags ( stripslashes ( htmlentities( ( string ) $value, ENT_COMPAT, 'utf-8' ) ) );
	}

	// Body Query
	$principalQuery    = 'SELECT p.`id_persona` , 
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
                            ON p.`id_area`		= a.`id_area` ';
    $completeQuery      = $principalQuery . $query;

    //  Do the query and obtain the results put them into a table
	$dbConnector	= new DBQueries ( );
	$dbConnector->executeQuery( $completeQuery );

	if ( $dbConnector->getRow( $completeQuery ) ) {
	    $container = $dbConnector->getRows( $completeQuery );

	    $template  = '<table>
                        <thead>
                            <tr>
                                <td>Nombre</td>
                                <td>Apellido Paterno</td>
                                <td>Apellido Materno</td>
                                <td>Edad</td>
                                <td>Escolaridad</td>
                                <td>Ocupación</td>
                                <td>Correo</td>
                                <td>Distrito</td>
                                <td>Semblanza</td>
                                <td>Foto</td>
                                <td>Género</td>
                                <td>Tipo de vendedor</td>
                                <td>Cargo</td>
                                <td>Elección</td>
                                <td>Circunscripción</td>
                                <td>Estado</td>
                                <td>Comisión</td>
                                <td>Área</td>
                            </tr>
                        </thead>
                        <tbody>';

	    foreach ( $container as $index => $array ) {
	    
    	    $template  .= '                            <tr>';
	    
    	    foreach ( $array as $key => $value ) {
    	       if ( $key == 'id_persona' ) { 
    	           $id = $value;
    	           continue;
    	       }
    	       if ( $key == 'nombre' || $key == 'apellido_pat' || $key == 'apellido_mat' ) { 
        	       $template  .= '                                <td><a href="persona.phtml?id='.$id.'" target="_blank" title="'.$value.'">'.$value.'</a></td>';
    	       } else {
        	       $template  .= "                                <td>{$value}</td>";
    	       }

    	    }
    	    
    	    $template .= '                            </tr>';
	    }
	    
	    $template  .= '</tbody>
                    </table>';
        
        echo $template;
	} else {
    	die( 'No hay resultados disponibles.' );
	}
?>