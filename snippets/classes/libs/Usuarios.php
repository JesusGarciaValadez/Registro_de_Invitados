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
        
        $this->_tableName = '`persona`';
        $this->_primaryKey = '`id`';
        $this->setMysqlConn($conn);
        $this->_PDOConn = $conn;
    }
    
    /**
     * Verificar si el registro ingresado existe
     * @author Jesús Antonio García Valadez
     * @var $data
     * @access public
     * @method login
     * @return string 
     */
    public function getExists ( $data ) {
        $response = array();
        $fields = array( '`mail`' );
        $conditions = array();
        $site_url = SITE_URL . 'search.html';
        
        $parameters = array (
            'mail' => array ( 'requerido' => 1 ,'validador' => 'esEmail', 'mensaje' => 'El correo es obligatorio.')
        );
        
        $form = new Validator( $data, $parameters );
        
        //  !Form invalid
        if ( !$form->validate( ) ) {
            
            $response = array( 'success' => 'false', 'message'=> $form->getMessage( ) );
        } else {
            //  !The form is valid
            array_push( $fields, '`is_completed`' );
            $conditions['where']    = "`mail`='{$data['mail']}'";
            
            try {
                $this->_PDOConn->beginTransaction( );
                $resultSet = $this->select( $fields, $conditions );
                
                $encode = sha1( $data[ 'mail' ] );
                
                if ( count( $resultSet ) > 0 ) {
                    
                    if ( $resultSet[0]['is_completed'] == '0' ) {
                        
                        $_SESSION[ 'is_completed' ]   = false;
                        if ( $encode == $_SESSION[ 'mail' ] ) {
                            
                            $site_url = SITE_URL . "edit.php?m={$encode}";
                        }
                    } else {
                        
                        $site_url = SITE_URL . "search.html?response=no-editable";
                    }
                    
                    $this->_PDOConn->commit();
                } else {
                    
                    $_SESSION[ 'is_completed' ]   = false;
                    if ( $encode == $_SESSION[ 'mail' ] ) {
                        
                        $site_url = SITE_URL . "create.php?m={$encode}";
                    }
                }
                
                return $site_url;
            }catch ( PDOException $e ){
                $this->_PDOConn->rollBack();
                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
            }
        }
    }
    
    /**
     * actualizar los datos del usuario.
     * @method saveUser
     * @access public
     * @param mixed $data contiene la informacion basica necesaria
     * @return string 
     */
    public function saveUser ( $data, $action ) {
        $response = array();
        $conditions = array();
        $fields = array('id');
        
        foreach ( $_POST as $key => $value ) {
            
            $data[ $key ] = ( is_numeric( $value ) ) ? $value : (string) $value;
        }
        
        $parameters = array(
            'uset_edit_mail'            =>  array( 'requerido' => 0, 'validador' => 'esEmail', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_first_name'     =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_last_name'      =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_name'           =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_job'            =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_where'          =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_lada'           =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_phone'          =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_ext'            =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_dependency'     =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_title'          =>  array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_state'          =>  array( 'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
            ,'user_edit_city'           =>  array( 'requerido' => 0, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode( 'El nombre solo debe contener letras.' ) )
        );
        
        $form = new Validator( $data, $parameters );
        
        if ( $this->isValidSession() ) {
            
            if ( ! $form->validate() ){
                $response = array( 'success' => 'false','message'=> $form->getMessage() );
            }else{
                try{
                    $this->_PDOConn->beginTransaction();
                    $this->_primaryKey  = 'a.`id_persona`';
                    
                    //  !Update de datos de tabla persona
                    $this->setTableName( '`persona` as A' );
                    
                    $dataUpdate = array(
                        'mail'          =>  $data[ 'uset_edit_mail' ]
                        ,'first_name'   =>  $data[ 'user_edit_first_name' ]
                        ,'last_name'    =>  $data[ 'user_edit_last_name' ]
                        ,'user_name'    =>  $data[ 'user_edit_name' ]
                        ,'job'          =>  $data[ 'user_edit_job' ]
                        ,'where_from'   =>  $data[ 'user_edit_where' ]
                        ,'lada'         =>  $data[ 'user_edit_lada' ]
                        ,'phone'        =>  $data[ 'user_edit_phone' ]
                        ,'ext'          =>  $data[ 'user_edit_ext' ]
                        ,'dependency'   =>  $data[ 'user_edit_dependency' ]
                        ,'id_title'     =>  $data[ 'user_edit_title' ]
                        ,'id_state'     =>  $data[ 'user_edit_state' ]
                        ,'city'         =>  $data[ 'user_edit_city' ]
                        ,'is_completed' =>  1
                        ,'date_registry'=>  date( 'd m Y H:i:s' )
                    );
                    
                    if ( $action == 'edit' ) {
                        
                        $where      = "a.`mail` = '{$data[ 'uset_edit_mail' ]}'";
                        
                        $this->update( $dataUpdate, $where );
                    } else if( $action == 'create' ) {
                        
                        $this->setTableName( '`persona`' );
                        
                        $this->insert( $dataUpdate );
                    }
                    $success    = $this->getNumRows( );
                    
                    $this->_PDOConn->commit();
                    $response = array ( "success" =>'true',"message"=>utf8_encode('La informacion del usuario ha sido guardada exitosamente.¿Quieres volver a la pagina de busqueda?'));
                }catch( PDOException $e ) {
                    
                    $this->_PDOConn->rollBack( );
                    $response = array ( "success" =>'false', "message"    =>'el servicio no esta disponible');
                }
            }
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
        
        $mail   = ( isset( $_GET[ 'm' ] ) && !empty( 'm' ) ) ? $_GET[ 'm' ] : ( isset( $_SESSION[ 'mail' ] ) && !empty( $_SESSION[ 'mail' ]) ) ? $_SESSION[ 'mail' ] : false;
        
        $mail   = ( isset( $_SESSION[ 'is_completed' ] ) && $_SESSION[ 'is_completed' ] == false && $mail === true ) ? true : $mail;
        
        return ( $mail ) ? true : false;
    }
}
