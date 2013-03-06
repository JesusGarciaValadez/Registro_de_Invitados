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

    public function __construct($conn) 
    {
        $this->_tableName = '`admin_user`';
        $this->_primaryKey = '`id`';
        $this->setMysqlConn($conn);
        $this->_logs = new Eventos($conn);
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
     * Actualizar la contraseña del usuario.
     * @method resetPassword
     * @access public
     * @param mixed $data información necesaria : $data['username'] como correo electronico.
     * @return String cadana en formato JSON 
     */
    
    public function resetPassword ( $data ) {
        $response = array();
        $conditions = array();
        $fields = array('id');
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';
        
        $parameters = array(
            'email' => array(
                'requerido' => 1 ,'validador' => 'esEmail', 'mensaje' => 'El correo electronico es obligatorio.')
            ,
            'captcha' => array(
                'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('El captcha es obligatorio.'))
        );
        
        $form = new Validator($data, $parameters);
        
        if ( !$form->validate()){
            $response = array('success' => 'false','message'=> $form->getMessage());
        }elseif ( $session_code == '' || ( $session_code != $data['captcha'] ) ){
            $response = array('success' => 'false','message'=> utf8_encode('El codigo de seguridad (CAPTCHA) no es correcto.'));
        }else{
            
            try {
                
                $this->_PDOConn->beginTransaction();
            
                $conditions['where'] = "email='{$data['email']}'";
                $resulSet = $this->select($fields, $conditions);

                if (count($resulSet) > 0 ){

                    $newPassword = $this->_randomPassword(9);                

                    $pars = array( 
                        'password' => md5($newPassword)
                        ,'id' => $resulSet[0]['id']
                    );

                    $this->update( $pars, "id={$resulSet[0]['id']}" );
                    $success = $this->getNumRows();

                    if ( $success ){
                        $id_user = (int) $resulSet[0]['id'];
                        $sql = "SELECT id, CONCAT_WS(' ',nombre,ap_paterno,ap_materno) AS full_name FROM VENDEDORES WHERE id_usuario={$id_user}";
                        $this->setSqlQuery($sql)->execQuery();
                        $resulSet = $this->getResultSet();

                        $pars = array('password' => $newPassword);
                        
                        if ($this->_sendEmail($resulSet[0]['full_name'],$data['email'],$newPassword)){
                            
                            $date = date('Y-m-d H:i:s');
                            $description = "El usuario {$id_user} se ha restablecido su contraseña exitosamente.";
                            
                            $this->_logs->registerEvent(Eventos::SYSTEM_FORGOTTEN_PASSWORD, $id_user, $date, $description);
                            
                            $response = array ('success'=>'true','message'=>utf8_encode('Tu contraseña se ha actualizado y se ha enviado por correo electrónico.'));
                        }else{
                            $response = array ('success'=>'false','message'=>'el servicio no esta disponible');
                        }

                    }else{
                        $response = array('success' => 'false', 'message' => utf8_encode('No fue posible reestablecer la contraseña.'));
                    }
                    
                }else{
                    $response = array('success' => 'false', 'message' => utf8_encode('La contraseña se ha enviado por correo electronico.'));
                }
                
                $this->_PDOConn->commit();
                
            }catch ( PDOException $e ){
                $this->_PDOConn->rollBack();
                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
            }catch ( phpmailerException $e ){
                $this->_PDOConn->rollBack();
                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
            }
        }        
        return json_encode($response);        
    }
    
    /**
     * actualizar la contraseña del usuario.
     * @method updatePassword
     * @access public
     * @param mixed $data contiene la informacion basica necesaria: contraseña anterior, contraseña nueva y CAPTCHA
     * @return string 
     */
    public function updatePassword ( $data ) {
        $response = array();
        $conditions = array();
        $fields = array('id');
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';
        
        $parameters = array(
            'old_password' => array(
                'requerido' => 1 ,'validador' => 'password', 'mensaje' => utf8_encode('La contraseña anterior es obligatoria.'))
            ,
            'new_password' => array(
                'requerido' => 1 ,'validador' => 'password', 'mensaje' => utf8_encode('La contraseña anterior es obligatoria.'))
            ,
            're_password' => array(
                'requerido' => 1 ,'validador' => 'password', 'mensaje' => utf8_encode('La contraseña anterior es obligatoria.'))
        );
        
        $form = new Validator($data, $parameters);
        
        if ( $this->isValidSession() ) {
            
            if ( ! $form->validate() ){            
                $response = array( 'success' => 'false','message'=> $form->getMessage() );          
            }elseif( $data['new_password'] != $data['re_password'] ){
                $response = array( 'success' => 'false','message' => utf8_encode('las contraseñas no coinciden.') );
            }elseif( $_SESSION['user']['password'] != md5($data['old_password']) ){
                $response = array( 'success' => 'false','message' => utf8_encode('la contraseña actual no es correcta.') );
            }else{
                try{
                    $this->_PDOConn->beginTransaction();
                    
                    $dataUpdate = array(
                        'id' => $_SESSION['user']['id']
                        ,'password' => md5($data['new_password'])
                    );
                    
                    $this->update($dataUpdate);
                    $success = $this->getNumRows();
                    
                    if ( $success ){
                        
                        $_SESSION['user']['password'] = md5($data['new_password']);
                                                                                    
                        $date = date('Y-m-d H:i:s');
                        $description = "El usuario {$_SESSION['user']['id']} se ha actualizado su contraseña exitosamente.";

                        $this->_logs->registerEvent(Eventos::SYSTEM_NEW_PASSWORD_REQUEST, $_SESSION['user']['id'], $date, $description);

                        $response = array ('success'=>'true','message'=>utf8_encode('Tu contraseña se ha actualizado y se ha enviado por correo electrónico.'));
                       
                    }else{
                        $response = array('success' => 'false', 'message' => utf8_encode('No fue posible cambiar la contraseña.'));
                    }
                    $this->_PDOConn->commit();
                }catch(PDOException $e){
                    $this->_PDOConn->rollBack();
                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                }catch( phpmailerException $e){
                    $this->_PDOConn->rollBack();
                    $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                }
            }
            
        }else{
            $response = array( 'success' => 'false','message' => utf8_encode('la session no es valida') );
        }
        
        return json_encode($response);
        
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
     * Obtener los accesos a las secciones privadas en funcion del tipo de usuario.
     * @method getMenu
     * @access public
     * @return string 
     */
    
    public function getMenu ( ) {
        $html = '';
        
        if ( $this->isValidSession() ){
            
            if ( $_SESSION['user']['tipo'] == 'V' ){
                $view = new View( 'menu_vendedor.html' );
                $html = $view->render( false );
            }elseif( $_SESSION['user']['tipo'] == 'A' ){
                $view = new View( 'menu_administrador.html' );
                $html = $view->render( false );
            }
            
        }        
        return $html;        
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

        /**
     * Algoritmo para generar una nueva contraseña.
     * @method _randomPassword
     * @access private
     * @param int $characters Indica el numero de
     * @return String  
     */
    
    private function _randomPassword ($characters = 9) {
        $possible = '123456789abcdfghjkmnpqrstvwxyzABCDFGHJKMNPQRSTVWXYZ!#¡.-_*+';
        $code = '';
        $i = 0;
        while ($i < $characters) { 
            $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            $i++;
        }
        return $code;
    }
    
    /**
     * Enviar correo de notificacion de operacion.
     * @method _sendEmail
     * @access private
     * @param string $name Nombre del usuario.
     * @param string $email Cuenta de correo electrónico.
     * @return boolean 
     */
   
    private function _sendEmail ( $name, $email, $password ) {
        $view = new View( 'actualizar_password.html' );
        $mail = new PHPMailer();
        
        $view->setVars( array(
            'full_name' => $name
            ,'new_password' => $password
        ));
        
        $html = $view->render( false );
                
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->CharSet 	  = 'utf-8';
        $mail->Host       = 'smtp.emailsrvr.com';  // sets the SMTP server
        $mail->Username   = 'notificaciones@incentivosdell.com.mx';
        $mail->Password   = 'ingeniahosting';
        $mail->Port       = 587; 
        $mail->SetFrom('notificaciones@incentivosdell.com.mx','Avisos');
        $mail->Subject = 'Incentivos Dell.::Contacto';
        $mail->Body = $html;
        $mail->IsHTML(true);
        $mail->AddAddress($email,$name);
        
        if($mail->Send()){
            return true;
        } else {
            return false;
        }
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