<?php
/**
 * Entidad Puntaje Vendedores
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */

class PuntajeVendedores extends Model{
    
    protected $_logs = null;

    public function __construct( $conn ) 
    {
        $this->_tableName = 'PUNTAJE_VENDEDORES';
        $this->_primaryKey ='id';
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos( $conn );
    }
    
    /**
     * actualizar el puntaje general de vendedor.
     * @method updateScore
     * @access public
     * @param int $idVendedor
     * @param int $score
     * @param int $noOrder 
     * @return void
     */
    public function updateScore ( $idVendedor, $score, $noOrder )
    {
        if ( $this->_exists($idVendedor) ){            
            $fields = "puntaje = ( puntaje + {$score} )" ;
            $where = "id_vendedor={$idVendedor}";
            
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d H:i:s');            
            $unserialize = array(
                'descripcion' => 'Tu puntaje total al momento es'
                ,'puntaje' => $score
                ,'orden_id' => 0
                ,'orden_no' => 0
            );
            
            $serialize = serialize( $unserialize );
            $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
            $success = $this->getNumRows();
            
            if ( $success ){
                $this->_logs->registerEvent( Eventos::SALES_UPDATE_SCORE , $user, $date, $serialize);
            }
        }
    }
    
    /**
     * metodo para actualizar el puntaje de la empresa
     * @method updateScoreReconciling
     * @access public
     * @param int $idUsuario identificador del usuario.
     * @param int $idVendedor identificador  del vendedor.
     * @param int $score cantidad de puntos a actualizar.
     * @return array
     */
    
    public function updateScoreReconciling ( $idUsuario, $idVendedor, $score )
    {   
        $response = array( 'success' => 'false', 'message' => utf8_encode('El servicio no esta disponible.') );
        
        if ( $this->_exists($idVendedor) ){
            
            $fields = "puntaje={$score}, final={$score}";
            $where = "id_vendedor={$idVendedor}";
            
            $user = $idUsuario;
            $date = date('Y-m-d H:i:s');
            $unserialize = array (
                'descripcion' => 'El administrador ha confirmado tu puntaje en:'
                ,'puntaje' => $score
                ,'orden_id' => 0
                ,'orden_no' => 0
            );
            
            $serialize = serialize( $unserialize );
            $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
            $success = $this->getNumRows();
            
            if ( $success ){
                $response = array( 'success' => 'true', 'message' => utf8_encode('El puntaje se ha actualizado correctamente') );
                $this->_logs->registerEvent( Eventos::ORDER_RECONCILING , $user, $date, $serialize);
            }
        }
        return $response;
    }
    
    /**
     * actualizar los punto del vendedor a 0 por cambio de empresa.
     * @method resetScore
     * @access public
     * @param int $idVendedor identificador del usuario con rol del vendodor.
     * @param string $empresaAnterior nombre de la empresa anterior.
     * @param string $empresaNueva nombre de la empresa empresa.
     * @return void
     */
    
    public function resetScore ( $idVendedor, $empresaAnterior, $empresaNueva )
    {
        if ( $this->_exists($idVendedor) ){
            $fields = 'puntaje = 0';
            $where = "id_vendedor={$idVendedor}";
            
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d H:i:s');
            $unserialize = array (
                 'descripcion' => "Has cambiado de empresa de {$empresaAnterior} a {$empresaNueva}, tus puntos se han restablecido"
                ,'puntaje' => 0
                ,'orden_id' => 0
                ,'orden_no' => 0
            ); 
            
            $serialize = serialize( $unserialize );
            $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
            $success = $this->getNumRows();
            
            if ( $success ){
                $this->_logs->registerEvent( Eventos::USER_CHANGE_COMPANY, $user, $date, $serialize);
            }
        }
    }
    
    /**
     * validar si el puntaje del vendedor existe o no.
     * @method _exists
     * @access private
     * @param int $idVendedor identifica al usuario con rol de vendedor.
     * @return boolean 
     */
    private function _exists ( $idVendedor )
    {
        $exists = false;
        $fields = array('id');
        $conditions = array(
            'where' => "id_vendedor={$idVendedor}"
        );
            
        $resultSet = $this->select($fields, $conditions);
        
        if ( count($resultSet) > 0 ){
            $exists = true;
        }else{
            $data = array(
                'id_vendedor' => $idVendedor
                ,'puntaje' => 0
                ,'final' => 0
                ,'active' => 1
            );
            $exists = ( $this->insert($data) > 0 )? true: false;
        }            
        
        return (boolean) $exists;
    }
    
}