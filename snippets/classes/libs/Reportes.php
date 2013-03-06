<?php

class Reportes {
    
    protected $_PDOConn = null;
    
    public function __construct( $conn ) 
    {
        $this->_PDOConn = $conn;
    }
    
    public function obtenerReporteOrdenes ( array $data = array() )
    {
        $ordenes = new Ordenes ( $this->_PDOConn );
        $reporte = $ordenes->obtenerOrdenes( $data );
    }
    
    
}