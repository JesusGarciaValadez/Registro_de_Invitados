<?php
/**
 * Entidad ISR
 * @author Augusto Silva <augusto@ingeniagroup.com.mx
 * @package classes
 * @subpackage libs
 * @category entities
 */
class ISR extends Model{
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'ISR';
        $this->_primaryKey = 'id';
        $this->setMysqlConn($conn);
    }
    
    /**
     * metodo para obtener todos los registros de la entidad ISR
     * @method fetchAll
     * @access public
     * @return string retorna la coleccion en formato JSON.
     */
    
    public function fetchAll() {
        return json_encode( parent::fetchAll() );
    }
    
}