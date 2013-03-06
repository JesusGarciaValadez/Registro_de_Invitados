<?php

class Vendedores extends Model{
    
    private $_PDOConn = null;

    public function __construct($conn) 
    {
        $this->_tableName = 'VENDEDORES';
        $this->_primaryKey = 'id';
        $this->setMysqlConn($conn);
        $this->_PDOConn = $conn;
    }
    
    /**
     * Registrar al usuario de rol Vendedor.
     * @author Augusto Silva <augusto@ingeniagroup.com.mx>
     * @method registrarVendedor()
     * @var mixed $data
     */
    
    public function registrarVendedor( $data )
    {
        
        $response = array();
        $fields = array('id');
        $conditions = array();
        
        $session_code = ( isset ($_SESSION['security_code']) && strlen($_SESSION['security_code']) > 0 )? $_SESSION['security_code'] : '';
        
        $parameters = array(
            'nombre' => array(
                'requerido' => 1, 'validador' => 'esAlfa', 'mensaje' => utf8_encode('El nombre es obligatorio.'))
            ,
            'ap_paterno' => array(
                'requerido' => 1, 'validador' => 'esAlfa', 'mensaje' => utf8_encode('El apellido paterno es obligatorio.'))
            ,
            'ap_materno' => array(
                'requerido' => 0, 'validador' => 'esAlfa', 'mensaje' => utf8_encode('El apellido materno tiene caracteres invalidos.'))
            ,
            'rfc' => array(
                'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('El RFC es obligatorio.'))
            ,
            'telefono' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El teléfono es obligatorio.'))
            ,
            'email' => array(
                'requerido' => 1, 'validador' => 'esEmail', 'mensaje' => utf8_encode('El correo electrónico es obligatorio.'))
            ,
            'password' => array(
                'requerido' => 1, 'validador' => 'password', 'mensaje' => utf8_encode('La contraseña es obligatoria.'))
            ,
            're_password' => array(
                'requerido' => 1, 'validador' => 'password', 'mensaje' => utf8_encode('La contraseña es obligatoria.'))
            ,
            'empresa' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('Selecciona la empresa.'))
            ,            
            'estado' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('Selecciona el estado.'))
            ,
            'ciudad' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('Selecciona la ciudad.'))
            ,
            'terminos_condiciones' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('Leé y acepta los terminos y condiciones.'))
            ,
            'captcha' => array(
                'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('El captcha es obligatorio.'))            
        );
        
        $form = new Validator($data, $parameters);
        
        if ( !$form->validate() ){
            $response = array('success' => 'false','message'=> $form->getMessage());
        }
        elseif ( $session_code == '' || ( $session_code != $data['captcha'] ) ){
            $response = array('success' => 'false','message'=> utf8_encode('El codigo de seguridad (CAPTCHA) no es correcto.'));
        }elseif( $data['password'] != $data['re_password'] ){
            $response = array ('success'=>'false','msg'=>'las contraseñas no son iguales, por favor verifica.');
        }
        elseif ( (int) $this->getNumRows( $this->setSqlQuery("SELECT id FROM USUARIOS WHERE email='{$data['email']}'")->execQuery() ) > 0 ){
            $response = array('success' => 'false','message'=> utf8_encode('El correo proporcionado ya se encuentra registrado.'));
        }else{
            if ( !$this->_isvalidEmail($data['email']) ){
                $response = array('success' => 'false','message'=> utf8_encode('El correo proporcionado no debe ser de alguna de estas cuentas (gmail,hotmail,outlook,yahoo)'));
            }else{
                            
                // localizar si el rfc ya esta registrado
                
                try {
                    
                    $this->_PDOConn->beginTransaction();
                    
                    if ( !$this->_searchRFC($data['rfc']) ){
                    $activo = 1;
                    $tipo = 'registroCorrecto';
                    }else{
                        $activo = 0;
                        $tipo = 'registroDuplicado';
                    }
                    
                    // Crear Usuario

                    $this->setTableName('USUARIOS');

                    $dataUser = array(
                        'rol_id' => 2
                        ,'email' => $data['email']
                        ,'password' => md5($data['password'])
                        ,'activo' => $activo
                    );

                    $idUsuario = $this->insert($dataUser);

                    // Crear al vendedor

                    $this->setTableName('VENDEDORES');

                    $dataSeller = array(
                        'id_usuario' => $idUsuario
                        ,'nombre' => $data['nombre']
                        ,'ap_paterno' => $data['ap_paterno']
                        ,'ap_materno' => $data['ap_materno']
                        ,'RFC' => $data['rfc']
                        ,'telefono' => $data['telefono']
                    );

                    $idVendedor = $this->insert($dataSeller);

                    // Crear las relaciones

                    $this->setTableName('VENDEDORES_EMPRESA_REL');

                    $dataRel = array(
                        'id_vendedor' => $idVendedor
                        ,'id_empresa' => $data['empresa']
                        ,'id_estados_ciudades_rel' => $data['estado']
                        ,'activo' => $activo
                    );

                    $idRel = $this->insert($dataRel);
                    $date = date('Y-m-d H:i:s');
                    $description = "El usuario {$idUsuario} se ha inscrito exitosamente.";
                    
                    if ( (int) $idUsuario > 0 && (int) $idVendedor > 0 && (int) $idRel > 0 ){
                        $dataLog = array(
                            'id_evento' => 1
                            ,'id_usuario' => $idUsuario
                            ,'fecha' => $date
                            ,'descripcion' => $description
                        );

                        $this->setTableName('LOG_EVENTOS');

                        $this->insert($dataLog);
                    
                        if ($this->_sendEmail($data,$tipo)){
                            $response = array('success' => 'true','message'=> utf8_encode('El registro esta completo, revisa la cuenta de correo proporcionado.'));
                        }else{
                            $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                        }                    
                    }else{
                        $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                    }
                    $this->_PDOConn->commit();
                }
                catch( PDOException $e ){
                    $this->_PDOConn->rollBack();
                    $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                } catch (phpmailerException $e) {
                    $this->_PDOConn->rollBack();  
                    $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                }                
            }
        }
        
        return json_encode($response);
        
    }
    
    /**
     * Obtener los registros con el RFC 
     * @method _searchRFC
     * @param string $rfc   
     * @return boolean 
     */
    
    private function _searchRFC( $rfc )
    {
        $boolean = false;
        
        $rfc = strtoupper($rfc);
        $fields = array('id');
        $conditions['where'] = " UPPER(RFC='{$rfc}')";
        
        $resulSet = $this->select($fields, $conditions);
        
        if ( count($resulSet) > 0 )
            $boolean = true;
        
        return (boolean) $boolean;
    }
    
    /**
     * Validar si el correo electronico ingresado no es algun dominio
     * como gmail, hotmail, outlook, yahoo
     * @method _validateEmail
     * @access private
     * @param string $email
     * @return boolean 
     */
    
    private function _isvalidEmail($email)
    {
        $boolean = false;
        $patterns = '/(gmail|hotmail|outlook|yahoo)/i';
        
        if (!preg_match($patterns, $email))
            $boolean = true; 
        
        return (boolean) $boolean;
    }
    
    /**
     * Envio de correo electronico, notificar al usuario y al administrador.
     * @method _sendEmail()
     * @access private
     * @param Array $data
     * @param String $tipo
     * @return Boolean 
     */
    
    private function _sendEmail ($data,$tipo)
    {        
        
        
        $mail = new PHPMailer();
        
        $name = $data['nombre'].' '.$data['ap_paterno'].''.$data['ap_materno'];
        $email = $data['email'];
        $filename = CHUNKS_PATH .'email' . DIRECTORY_SEPARATOR;
                
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->CharSet 	  = 'utf-8';
        $mail->Host       = 'smtp.emailsrvr.com';
        $mail->Username   = 'notificaciones@incentivosdell.com.mx';
        $mail->Password   = 'ingeniahosting';
	$mail->Port       = 587; 
        $mail->SetFrom('notificaciones@incentivosdell.com.mx','Contacto');
        $mail->Subject = 'Incentivos Dell.::Contacto';
        
        switch ($tipo) {
            case 'registroCorrecto':
                $filename .= 'registro_exitoso.html';
                $html = file_get_contents($filename);
                $html = sprintf($html, $name);
                $mail->AddCC('PartnerDirect_Mexico@Dell.com', 'Karewytt Gonzalez');
                break;
            case 'registroDuplicado':
                $filename .= 'registro_duplicado.html';
                $email = 'PartnerDirect_Mexico@Dell.com';
                $name = 'Karewytt Gonzalez';
                $html = file_get_contents($filename);
                $html = sprintf($html, $data['rfc']);
                break;
            default:
                break;
        }        
        
        $mail->Body = $html;
        $mail->IsHTML(true);
        $mail->AddAddress($email,$name);
        $mail->AddBCC('augusto@ingeniagroup.com.mx', 'Augusto Silva');
        
        if($mail->Send()){
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * actualizar la empresa del vendedor
     * @method actualizarVendedorEmpresaRel
     * @access public
     * @param int $idVendedor identificador del usuario con rol del vendedor.
     * @param int $idEmpresa identificador de la empresa.
     * @return boolean 
     */
    public function actualizarVendedorEmpresaRel ( $idVendedor, $idEmpresa )
    {
        $success = false;
        
        if ( (int) $idVendedor > 0 && (int) $idEmpresa ){
            $this->setSqlQuery("UPDATE VENDEDORES_EMPRESA_REL SET id_empresa={$idEmpresa} WHERE id_vendedor={$idVendedor} LIMIT 1")->execQuery();
            $success = ( $this->getResultSet() )? true : false;
        }
        
        return (boolean) $success;
    }
    
}