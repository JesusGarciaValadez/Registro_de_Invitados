<?php
/**
 * Entidad Productos
 * @author augusto@ingeniagroup.com.mx
 * @package classes
 * @subpackage libs
 * @category entities 
 */

class PuntosProductos extends Model{
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'PUNTOS_PRODUCTOS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
    }
    
    public function searchByIdProducto ( $idProducto ,$microsoft = 0 )
    {
        $fields = array('puntos', 'lineamiento');
        $conditions = array(
            'where' => "id_producto={$idProducto} AND is_productos_microsoft={$microsoft}"
        );
       
        return $this->select($fields, $conditions);
    }
    
}