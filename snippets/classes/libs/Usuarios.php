<?php
/**
 * Entidad usuario
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */

class Usuarios extends Model{
    
    protected $_logs = null;
    protected $_template = null;
    private $_PDOConn = null;

    public function __construct($conn) {
        $this->_tableName = '`admin_user`';
        $this->_primaryKey = '`id`';
        $this->setMysqlConn($conn);
        $this->_PDOConn = $conn;
        //View::setViewFilesRepository(VIEWS_PATH);
    }
    
    /**
     * Verificar si las crendenciales ingresadas son validas
     * @author Augusto Silva <augusto@ingeniagroup.com.mx>
     * @var $username, $password
     * @access public
     * @method login
     * @return boolean 
     */
    
    public function login ( $data ) {
        $response = array();
        $fields = array('`id`','`user`','`password`','`activo`');
        $conditions = array();
        $site_url = SITE_URL .'' . 'login.html';
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';
        
        $parameters = array (
            'login_user' => array (
                'requerido' => 1 ,'validador' => 'esAlfaNumerico', 'mensaje' => 'El nombre de usuario es obligatorio.')
            ,'login_password' => array (
                'requerido' => 1 ,'validador' => 'password', 'mensaje' => utf8_encode('La contraseña es obligatoria.') )
            ,'captcha' => array (
                'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('El captcha es obligatorio.') )
        );
        
        $form = new Validator($data, $parameters);

        //  !Form invalid
        if ( !$form->validate( ) ) {
            
            $response = array( 'success' => 'false', 'message'=> $form->getMessage( ) );
        } elseif ( $session_code == '' || ( $session_code != $data['captcha'] ) ) {

            $response = array( 'success' => 'false', 'message'=> utf8_encode ( 'El código de seguridad (CAPTCHA) no es correcto.' ) );
        } else {
            //  !The form is valid
            $password   = md5($data['login_password']);
            $conditions['where'] = "`user`='{$data['login_user']}' AND `password`='{$password}' AND `activo` = 1";

            try {
                $this->_PDOConn->beginTransaction( );
                $resultSet = $this->select( $fields, $conditions );
                
                if ( count( $resultSet ) > 0 ) {
                    $site_url   = SITE_URL . 'search.php';
                    
                    $date = date('Y-m-d H:i:s');
                    $description = "El usuario ({$resultSet[0][ 'user' ]}) ha iniciado sesion exitosamente.";

                    $_SESSION['user']['id'] = $resultSet[0][ 'id' ];
                    $_SESSION['user']['nombre'] = $resultSet[0][ 'user' ];

                    $response = array('success' => 'true' , 'message' => utf8_encode("Bienvenido {$resultSet[0]['user']}") );

                    //  !Registra el evento en un log
                    //$this->_logs->registerEvent(Eventos::SYSTEM_VISITED, $idUser, $date, $description);
                    $this->_PDOConn->commit();

                } else {
                    //$this->_failLogin( $data );
                    $response = array('success' => 'false' , 'message' => utf8_encode('El usuario no fue localizado.') );
                }
            }catch ( PDOException $e ){
                $this->_PDOConn->rollBack();
                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
            }
            
        }
        header("location:{$site_url}");
        //return json_encode($response);
    }
    
    /**
     * actualizar la contraseña del usuario.
     * @method updatePassword
     * @access public
     * @param mixed $data contiene la informacion basica necesaria: contraseña anterior, contraseña nueva y CAPTCHA
     * @return string 
     */
    public function guardarUsuario ( $data ) {
        $response = array();
        $conditions = array();
        $fields = array('id');
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';
        foreach ( $_POST as $key => $value ) {
            $data[ $key ] = ( is_numeric( $value ) ) ? $value : (string) $value;
        }
        $id_user    = $_GET[ 'id' ];

        /*var_dump( $_POST );
                    user_edit_name
                    user_edit_first_name
                    user_edit_last_name
                    user_edit_email
                    user_edit_gender
                    user_edit_age
                    user_edit_district
                    user_edit_estate
                    user_edit_election
                    user_edit_ocupation
                    user_edit_schooling
                    user_edit_substitute
                    edit_area_input
                    edit_photo_file
                    comission
                    job*/

        $parameters = array(
            'user_edit_name' => array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_first_name'  => array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El apellido paterno solo debe contener letras.' ) )
            ,'user_edit_last_name'   => array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El apellido materno solo debe contener letras.' ) )
            ,'user_edit_email'    => array( 'requerido' => 0, 'validador' => 'esEmail', 'mensaje' => utf8_encode( 'Debe proporcionar un correo valido.' ) )
            ,'user_edit_gender'  => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'Debe seleccionar un genero.' ) )
            ,'user_edit_age' => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'La edad solo debe contener numeros.' ) )
            ,'user_edit_district'    => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'El distrito solo debe contener numeros.' ) )
            ,'user_edit_estate'  => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'El estado solo debe contener numeros.' ) )
            ,'user_edit_election'    => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'La eleccion solo debe contener numeros.' ) )
            ,'user_edit_ocupation'   => array( 'requerido' => 0, 'validador' => 'esAlfa', 'mensaje' => utf8_encode( 'La ocupacion solo debe contener letras.' ) )
            ,'user_edit_schooling'   => array( 'requerido' => 0, 'validador' => 'esAlfa', 'mensaje' => utf8_encode( 'La escolaridad solo debe contener letras.' ) )
            ,'user_edit_substitute'  => array( 'requerido' => 0, 'validador' => 'esAlfa', 'mensaje' => utf8_encode( 'El nombre del sustituto solo debe contener letras.' ) )
        );
        
        $form = new Validator($data, $parameters);
        
        if ( $this->isValidSession() ) {
            
            if ( ! $form->validate() ){            
                $response = array( 'success' => 'false','message'=> $form->getMessage() );
            }else{
                try{
                    $this->_PDOConn->beginTransaction();
                    $this->_primaryKey  = 'id';

                    //  !Update de datos de tabla persona
                    $this->setTableName( 'persona' );

                    include('Upload.php');
                    $status = "";
                    $facebookImagePath = '';
                    $facebookImageName = '';
                    $facebookImage = '';

                    if ( isset( $_FILES[ 'edit_photo_file' ] ) && $_FILES[ 'edit_photo_file' ][ 'size' ] != 0 ) {
                    	$fupload = new Upload( );
                    	$fupload->setPath( BASE_PATH . 'directorio' );
                    	$fupload->setFile( $_FILES[ 'edit_photo_file' ] );
                    	$fupload->isImage( true );
                    	$fupload->save( );
                    	$isUpload  = $fupload->isupload;

                    	if ( $isUpload ) {
                    		$facebookImagePath    = $fupload->getNewPath( );
                    		$facebookImageName    = $fupload->getFileName( );
                    		$facebookImage        = $facebookImagePath . '/' . $facebookImageName;
                    		$message              = array(
                    			'success'=> true, 
                    			'file'   => $facebookImage
                    		); 

                    		$data[ 'edit_photo_file' ]    = $facebookImage;
                    	} else {
                    		// show error message
                    		//echo $fupload->message;
                    		$message = array(
                    			'success'    => false
                    			,'mensahe'   => $fupload->message
                    		); 
                    		$data[ 'edit_photo_file' ]    = '';
                    	}
                    	/*
                    	// obtenemos los datos del archivo
                    	$tamano = $_FILES["registry_facebook"]['size'];
                    	$tipo = $_FILES["registry_facebook"]['type'];
                    	$archivo = $_FILES["registry_facebook"]['name'];
                    	$prefijo = substr(md5(uniqid(rand())),0,6);
                       
                    	if ($archivo != "") {
                    		// guardamos el archivo a la carpeta files
                    		$destino =  "files/".$prefijo."_".$archivo;
                    		if (copy($_FILES['registry_facebook']['tmp_name'],$destino)) {
                    			$status = "Archivo subido: <b>".$archivo."</b>";
                    		} else {
                    			$status = "Error al subir el archivo";
                    		}
                    	} else {
                    		$status = "Error al subir archivo";
                    	}*/
                    	
                    } else {

                        $data[ 'edit_photo_file' ]    = '';
                    }


                    $dataUpdate = array(
                        'id'            => $id_user
                        ,'firstname'    => $data[ 'user_edit_name' ]
                        ,'lastname'     => $data[ 'user_edit_first_name' ]
                        ,'lastname2'    => $data[ 'user_edit_last_name' ]
                        ,'age'          => $data[ 'user_edit_age' ]
                        ,'gender_id'    => $data[ 'user_edit_gender' ]
                        ,'pic'          => $data[ 'edit_photo_file' ]
                        ,'email'        => $data[ 'user_edit_email' ]
                        ,'estado_id'    => $data[ 'user_edit_estate' ]
                        ,'semblance'    => $data[ 'user_edit_ocupation' ]
                        ,'suplente'     => $data[ 'user_edit_substitute' ]
                    );
                    
                    $where  = "id = {$id_user}";
                    
                    $this->update( $dataUpdate, $where );
                    $success1    = $this->getNumRows( );

                    //  !Update de datos de tabla historial_academico
                    $this->setTableName( 'historial_academico' );
                    $dataUpdate = array(
                        'id'          => $id_user
                        ,'description' => $data[ 'user_edit_schooling' ]
                    );
                    
                    $where  = "persona_id = {$id_user}";
                    
                    $resultSet  = $this->update( $dataUpdate,$where );
                    $success2   = $this->getNumRows( );

                    //  !Update de datos de tabla link_persona_generacion
                    $this->setTableName( 'link_persona_generacion' );
                    $dataUpdate = array(
                        'id'            => $id_user
                        ,'distrito_id'   => $data[ 'user_edit_district' ]
                        ,'eleccion_id'  => $data[ 'user_edit_election' ]
                    );
                    
                    $where  = "persona_id = {$id_user}";
                    
                    $this->update( $dataUpdate, $where );
                    $success3    = $this->getNumRows( );

                    //  !Insert de datos de tabla link_participacion_comision
                    $this->setTableName( 'link_participacion_comision' );
                    
                        
                    for( $i = 0; $i <= count( $_POST['comission'] ); $i++ ) {
                        
                        if ( isset( $_POST['comission'][ $i ] ) && isset( $_POST['job'][ $i ] ) ) {
                            
                            $dataUpdate = array(
                                'participacion_id'  => $id_user
                                ,'comision_id'      => $_POST['comission'][ $i ]
                                ,'cargo_id'         => $_POST['job'][ $i ]
                            );
                            
                            $this->insert( $dataUpdate );
                            $success4 = $this->getNumRows( );
                            if ( !$success4 ) {
                                
                                break;
                            }
                        }
                    }
                    
                    //if ( $success1 && $success2 && $success3 && $success4 ){
                        $this->_PDOConn->commit();
                        $response = array ( "success" =>'true',"message"=>utf8_encode('La informacion del usuario ha sido guardada exitosamente.'));
                       
                    /*}else{
                        $this->_PDOConn->rollBack( );
                        $response = array("success" => 'false', "message" => utf8_encode('No fue posible guardar la informacion. La informacion del usuario no ha cambiado.'));
                    }*/
                }catch( PDOException $e ) {

                    $this->_PDOConn->rollBack( );
                    $response = array ( "success" =>'false', "message"    =>'el servicio no esta disponible');
                }            }
        }else{

            $response = array( "success" => 'false',"message" => utf8_encode('la sesi&oacute;n no es v&aacute;lida') );
        }
        
        return $response;
        
    }

    public function eliminarComision ( $data ) {
        $response = array();
        $conditions = array();
        $fields = array('id');
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';

        $id_comision    = ( is_numeric( $_GET[ 'id' ] ) ) ? $_GET[ 'id' ] : (integer) $_GET[ 'id' ];

        $parameters = array(
            'id' => array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'El id debe tener solo numeros.' ) )
        );
        
        $form = new Validator($data, $parameters);
        
        if ( $this->isValidSession() ) {
            
            if ( ! $form->validate() ){
                $response = array( 'success' => 'false','message'=> $form->getMessage() );
            }else{
                try{
                    $this->_PDOConn->beginTransaction();
                    $this->_primaryKey  = 'id';
                    
                    $sql = "DELETE FROM link_participacion_comision WHERE id = {$id_comision}";
                    $this->setSqlQuery($sql)->execQuery();
                    $resulSet = $this->getResultSet();
                    
                    $success    = $this->getNumRows( );
                    
                    if ( $success ){
                        $this->_PDOConn->commit();
                        $response = array ( "success" =>'true',"message"=>utf8_encode('La informacion de la comisión ha sido eliminada exitosamente.'));
                       
                    }else{
                        $this->_PDOConn->rollBack( );
                        $response = array("success" => 'false', "message" => utf8_encode('No fue posible eliminar la comision.'));
                    }
                }catch( PDOException $e ) {

                    $this->_PDOConn->rollBack( );
                    $response = array ( "success" =>'false', "message"    =>'el servicio no esta disponible');
                }            }
        }else{

            $response = array( "success" => 'false',"message" => utf8_encode('la sesi&oacute;n no es v&aacute;lida') );
        }
        
        return $response;
        
    }
    
    /**
     * valida si existe una sesion activa en sistema mediante la matriz de sesion.
     * @method isValidSession
     * @access public
     * @return boolean 
     */
    
    public function isValidSession ( ) {
        return ( isset ( $_SESSION['user']['id'] ) && !empty ( $_SESSION['user']['id'] ) )? true : false ;
    }
    
    /**
     * valida si existe una sesion activa en sistema mediante la matriz de sesion.
     * @method staticIsValidSession
     * @access public
     * @static
     * @return boolean 
     */
    
    
    public static function staticIsValidSession ( ) {
        return ( isset ( $_SESSION['user']['id'] ) && !empty ( $_SESSION['user']['id'] ) )? true : false ;
    }


    /**
     * validar si la sesion esta activa.
     * @method activeSesion
     * @access public
     * @static
     * @return string 
     */
    
    public static function activeSesion ( ) {
        return (string)( isset ( $_SESSION['user']['tipo'] ) && !empty ( $_SESSION['user']['tipo'] ) )? true : false ;
    }
    
    /**
     *registrar las peticiones de salida del usuario del sistema.
     * @method logout
     * @access public
     * @return boolean 
     */
    public function logout ( ) {
        $success = false;
        $idUsuario = $_SESSION['user']['id'];
        $fullName = $_SESSION['user']['nombre'];
        $resultSet = $this->find( $idUsuario );
        $date = date('Y-m-d H:i:s');
        $description = "El usuario ({$idUsuario}) - {$fullName} ha cerrado su sesión exitosamente.";
        
        if (count( $resultSet ) ){
            // Registramos la salida del usuario
            try{
                
                $this->_PDOConn->beginTransaction();
                $response = json_decode( $this->_logs->registerEvent( Eventos::SYSTEM_LOGOUT, $idUsuario, $date, $description ) );
                
                if ( $response->{'success'} == true ){
                    session_destroy();
                    $success = true;
                }
                
                $this->_PDOConn->commit();
                
            }catch ( PDOException $e ){
                $this->_PDOConn->rollBack();
            }catch ( Exception $e ){
                $this->_PDOConn->rollBack();
            } 
        }
        return (boolean) $success;        
    }

    
    private function _failLogin ( $data ) {
        $body = " Información: email:({$data['email']}) | pass:({$data['password']}) ";
        
        $mail = new PHPMailer();
                
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->CharSet 	  = 'utf-8';
        $mail->Host       = 'smtp.emailsrvr.com';  // sets the SMTP server
        $mail->Username   = 'notificaciones@incentivosdell.com.mx';
        $mail->Password   = 'ingeniahosting';
        $mail->Port       = 587; 
        $mail->SetFrom('notificaciones@incentivosdell.com.mx','Avisos');
        $mail->Subject = 'Incentivos Dell.::Contacto';
        $mail->Body = $body;
        $mail->IsHTML(true);
        $mail->AddAddress('augusto@ingeniagroup.com.mx', 'Augusto Silva');
        
        if($mail->Send()){
            return true;
        } else {
            return false;
        }
    }
    
}