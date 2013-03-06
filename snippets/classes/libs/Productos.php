<?php
/**
 * Entidad Productos
 * @author augusto@ingeniagroup.com.mx
 * @package classes
 * @subpackage libs
 * @category entities 
 */
class Productos extends Model{
    
    protected $_logs = null;
    protected $_conn = null;


    public function __construct( $conn ) 
    {
        $this->_tableName = 'PRODUCTOS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos( $conn );
        $this->_conn = $conn;
    }
        
    /**
     * Obtener los productos agrupados por categoria
     * @method getProductos
     * @access public 
     * @return String 
     */
    
    public function getProductos() {
        $resultSet = array();
        
        try{
            $sql = 'SELECT id, nombre FROM CATEGORIAS_PRODUCTOS WHERE activo = 1 ORDER BY nombre ASC';
            $this->setSqlQuery($sql)->execQuery();
            $categorias = $this->getResultSet();
            
            if ( count( $categorias ) > 0 ){
                
                foreach ($categorias as $categoria) {
                    $pos = 0;
                    $idCategoria   = $categoria['id'];
                    $descCategoria = str_replace( ' ', '_', $categoria['nombre'] );
                    $sql = "SELECT id, nombre FROM PRODUCTOS WHERE id_categoria_producto={$idCategoria} AND activo = 1 ORDER BY nombre ASC";
                    
                    $this->setSqlQuery($sql)->execQuery();
                    $productos = $this->getResultSet();
                    
                    if ( count($productos ) > 0 ){
                        foreach ( $productos as $producto ) {                            
                            $resultSet[$descCategoria][$pos]['id'] = $producto['id']; 
                            $resultSet[$descCategoria][$pos]['nombre'] = str_replace( ' ', '_',$producto['nombre'] );
                            $pos++;
                        }
                    }                    
                }                
            }
            
        }catch ( PDOException $e ){
            $resultSet = array ( 'success' => 'false', 'message' => utf8_encode('No es posible obtener los productos') );
        }
                
        return (string) json_encode($resultSet) ;
    }

    
}