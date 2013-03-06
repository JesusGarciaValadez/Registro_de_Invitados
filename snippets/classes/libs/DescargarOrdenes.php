<?php

class DescargarOrdenes {
    
    protected $_PDOConn = null;
    protected $_logs = null;

    public function __construct( $conn ) 
    {
        $this->_PDOConn = $conn;
        $this->_logs = new Eventos ( $conn );
    }
    
    public function descargarRepote ( $data )
    {
        $excel = $_SESSION['user']['excel'];
        $this->_actualizarEstado( $data );
        return $excel;
    }
    
    private function _actualizarEstado ( $data )
    {
        $orden = new Ordenes ( $this->_PDOConn );
        $user = $_SESSION['user']['id'];
        $date = date('Y-m-d H:i:s');
        
        if (is_array( $data ) ){
            foreach ( $data as $ordenes ) {
                
                $success = false;
                $idOrden = (int) $ordenes['id'];
                $folio = $ordenes['orden'];
                
                $fields = ' descargado=1';
                $where = " id={$idOrden}";
                               
                try {
                    $this->_PDOConn->beginTransaction();
                    $orden->setSqlQuery( "UPDATE REGISTROS_VENTAS SET {$fields} WHERE {$where};" )->execQuery();
                    $success = $orden->getNumRows();
                    if ( $success ){
                        $description = "Se ha descargado la orden {$folio}";
                        $this->_logs->registerEvent(Eventos::ORDER_PROCESSED, $user, $date, $description);
                    }
                    $this->_PDOConn->commit();
                }catch ( PDOException $e ){
                    $this->_PDOConn->rollBack();
                }catch ( Exception $e ){
                    $this->_PDOConn_PDOConn->rollBack();
                }
            }
        }
    }
    
}
