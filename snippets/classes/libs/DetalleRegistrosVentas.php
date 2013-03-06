<?php

/**
 * Entidad detalle_registros_venta
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */

class DetalleRegistrosVentas extends Model {
    
    protected $_logs = null;
    protected $_PDOConn = null;

    public function __construct( $conn ) 
    {
        $this->_tableName = 'DETALLE_REGISTROS_VENTAS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_PDOConn = $conn;
        $this->_logs = new Eventos( $conn );
    }
    
    /**
     * registrar detalle de orden de venta.
     * @method registrarDetalle
     * @access public
     * @param array $data conjunto de datos que componen la entidad detalle_registros_venta
     * @return int 
     */
    
    public function registrarDetalle ( $data )
    {
        $idDetalle = 0;
        
        $parameters = array(
            'id_reporte_ventas' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador de Venta es invalido.')
            )
            ,'id_producto' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador del Producto es invalido.')
            )
            ,'cantidad' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('La cantidad de Producto es invalida.')
            )
            ,'tiene_productos_microsoft' => array(
                'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El componente Microsoft es invalido.')
            )
            ,'procesada' => array(
                'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El valor de partida procesada es invalida.')
            )
            ,'puntos_generados' => array(
                'requerido' => 0, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('La cantidad de puntos del Producto es invalida.')
            )
        );
        
        $forma = new Validator($data, $parameters);
        
        if ( !$forma->validate() ){
            $response = array( 'success' => 'false', 'message' => utf8_encode( $forma->getMessage() ) );
        }else{            
            try{
                $this->_conn->beginTransaction();                
                $idDetalle = $this->insert($data);                
                $this->_conn->commit();
            }catch ( PDOException $e ){
                $this->_PDOConn->rollBack();
            }catch ( Exception $e ){
                
            }            
        }
        return $idDetalle;
    }    
    
}