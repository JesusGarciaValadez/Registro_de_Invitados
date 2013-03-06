<?php

class PuntajeEmpresas extends Model{
    
    protected $_logs = null;
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'PUNTAJE_EMPRESAS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos( $conn );        
    }
    
    /**
     * actualizar el punteje de la empresa por orden.
     * @method updateScore
     * @access public
     * @param int $idEmpresa identificador  de la empresa.
     * @param int $score cantidad de puntos a actualizar.
     * @param int identificador de la orden de venta.
     * @param int $noOrder numero de orden de venta.
     */
    
    
    public function updateScore ( $idEmpresa, $score, $idOrder, $noOrder )
    {
        if ( $this->_exists($idEmpresa) ){            
            $fields = "puntaje = ( puntaje + {$score} )";
            $where = "id_empresa={$idEmpresa}";
            
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d H:i:s');
            $unserialize = array(
                'descripcion' => 'Se ha actualizado el puntaje de la empresa'
                ,'puntaje' => $score
                ,'orden_id' => $idOrder
                ,'orden_no' => $noOrder
            );
            
            $serialize = serialize( $unserialize );
            
            $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
            $success = $this->getNumRows();
            
            if ( $success ){
                $this->_logs->registerEvent( Eventos::COMPANY_UPDATE_SCORE , $user, $date, $serialize);
            }
        }
    }
    
    /**
     * metodo para actualizar el puntaje de la empresa
     * @method updateScoreReconciling
     * @access public
     * @param int $idEmpresa identificador  de la empresa.
     * @param int $score cantidad de puntos a actualizar.
     * @return array
     */
    
    public function updateScoreReconciling ( $idEmpresa, $score )
    {   
        $response = array( 'success' => 'false', 'message' => utf8_encode('El servicio no esta disponible.') );
        
        if ( $this->_exists($idEmpresa) ){
            $fields = "puntaje={$score}, final={$score}";
            $where = "id_empresa={$idEmpresa}";
            
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d H:i:s');
            $unserialize = array(
                'descripcion' => 'El administrador ha conciliado al presente.'
                ,'puntaje' => $score
                ,'orden_id' => 0
                ,'orden_no' => 0
            );
            
            $serialize = serialize( $unserialize );
            $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
            if ( $this->getNumRows() ){
                $response = array( 'success' => 'true', 'message' => utf8_encode('El puntaje se ha actualizado correctamente') );
                $this->_logs->registerEvent( Eventos::ORDER_RECONCILING , $user, $date, $serialize);
            }
        }
        return $response;
    }
    
    /**
     * validar si el registro de puntaje de la empresa existe. si no es asi
     * entonces crea el registro con los valores por default
     * @method _exists
     * @access private
     * @param int $idVendedor identifica al usuario con rol de vendedor. 
     * @return boolean 
     */
    
    private function _exists ( $idEmpresa )
    {
        $exists = false;
        $fields = array('id');
        $conditions = array(
            'where' => "id_empresa={$idEmpresa}"
        );
            
        $resultSet = $this->select($fields, $conditions);
        
        if ( count($resultSet) > 0 ){
            $exists = true;
        }else{
            $data = array(
                'id_empresa' => $idEmpresa
                ,'puntaje' => 0
                ,'final' => 0
                ,'active' => 1
            );
            $exists = ( $this->insert($data) > 0 )? true: false;
        }            
        
        return (boolean) $exists;
    }
    
}