<?php

/**
 * proporcionar lo medio necesarios para la actualzacion de puntaje.
 * @package classes
 * @subpackage libs
 * @category Core
 */

class ActualizarPuntajes {
    
    protected $_PDOConn = null;


    public function __construct( $conn ) 
    {
        $this->_PDOConn = $conn;
    }
    
    /**
     * obtener las coleccion de datos (Vendedores) que cumplan
     * con el criterio (id_empresa)
     * @param array $data coleccion de datos enviados via POST o GET
     * @return type 
     */
    
    public function obtenerEmpleadosPorEmpresa ( array $data = array() )
    {
        $parameters = array (
            'id_empresa' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador la de empresa es obligatoria.')
            )
        );
        
        if ( self::isValidData( $data, $parameters ) ){
            // obtener los vendedores de la empresa
            
            $relVendedores = new VendedoresEmpresasRel( $this->_PDOConn );
            $vendedores = $relVendedores->obtenerVendedores( $data['id_empresa'] );
            
        }else{
            // los datos no son validos
        }
        
        return (array) $vendedores;
        
    }
    
    public function actualizarPuntajeVendedores ( $data )
    {
        $response =  array();
        $parameters = array(
            'id_empresa' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador de la Empresa no es valido.')
            ),
            'id_vendedor' => array(
                'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador del Vendedor no es valido.')
            )
            ,'score' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El puntaje ingresado no es valido')
            )
        );
        
        if ( self::isValidData($data, $parameters) ){
            
            if ( (int) $data['id_empresa'] > 0 && (int) $data['id_vendedor'] == 0 ){
                $puntaje = new PuntajeEmpresas ( $this->_PDOConn );
                $success = $puntaje->updateScoreReconciling( $data['id_empresa'], $data['score'] );
            }elseif ( (int) $data['id_empresa'] > 0 && (int) $data['id_vendedor'] > 0 ){
                $idUsuario = $this->_obtenerIdUsuario( $data['id_vendedor'] );                
                $puntaje = new PuntajeVendedores ( $this->_PDOConn );
                $success = $puntaje->updateScoreReconciling( $idUsuario, $data['id_vendedor'], $data['score'] );
            }else{
                // este escenario no es valido
            }
            
        }else{
            // los datos no son validos
            $success = array ( 'success' => 'false', 'message' => utf8_encode('error, no fue posible actualizar el puntaje') );
        }
        return $success;    
    }


    /**
     * validar los datos enviados por post
     * @method isValidData
     * @access public
     * @static
     * @param array $data coleccion de datos POST
     * @param array $parameters coleccion de criterios para evaluar la matriz POST
     * @return boolean 
     */
    
    public static function isValidData ( array &$data , array &$parameters )
    {
        $forma = new Validator($data, $parameters);
        if ( !$forma->validate() ){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     *obtener el identificador del usuario.
     * @method _obtenerIdUsuario
     * @access private
     * @param int $idVendedor identificador del usuario con rol de vendedor.
     * @return int 
     */
    
    private function _obtenerIdUsuario ( $idVendedor = 0 )
    {
        $idUsuario = 0;
        $vendedor = new Vendedores ( $this->_PDOConn );
        $resultSet = $vendedor->find( $idVendedor );
        $idUsuario = $resultSet[0]['id_usuario'];
        return $idUsuario;
    }
    
}
