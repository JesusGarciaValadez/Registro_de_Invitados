<?php
	/**
	 *	@author:	Jesús Antonio García Valadez
	 *	@descr:		File to process the information from the form
	 *	@require:	lib/DBQueries.php
	 *
	 */
	error_reporting ( E_ALL | E_STRICT );
	ini_set ( "display_errors", 1 );
	date_default_timezone_set( 'America/Mexico_City' );

	//	Include the DB Library
	require_once ( 'lib/DBQueries.php' );

	// Body Query
	$principalQuery    = 'SELECT a.`id_area` , 
                        a.`area` 
                        FROM `area` a 
                        ORDER BY a.`id_area` ';
    $completeQuery      = $principalQuery;

    //  Do the query and obtain the results putting into a JSON
	$dbConnector	= new DBQueries ( );
	$dbConnector->executeQuery( $completeQuery );

	if ( $dbConnector->getRow( $completeQuery ) ) {
    	$container = $dbConnector->getRows( $completeQuery );
    	
    	$template  = array();
    	foreach ( $container as $index => $array ) { 

    	   foreach ( $array as $key => $value ) {
    	       if ( $key != "id_area" ) {
    	           $template[$key][$index + 1] = $value;
    	       }
    	       $template['registros']  = $index + 1;

           } 
        }
        
        echo json_encode( $template );
	} else {

    	die( 'No hay resultados disponibles.' );
	}
?>