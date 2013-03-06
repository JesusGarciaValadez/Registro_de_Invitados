<?php
/**
 * Entidad Ciudad
 * @author augusto@ingeniagroup.com.mx
 * @package classes
 * @subpackage libs
 * @category entities 
 */

class Ciudades extends Model{
    
    /**
     * Entidad Ciudaad.
     * @method __construct 
     * @access public
     * @param PDO $conn Objeto tipo PDO
     * @return void
     */
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'CIUDADES';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
    }
    
    /**
     * Obtner las ciudades del estado.
     * @method findCiudades()
     * @access public
     * @param int $idEstado identficador unico del estado.
     * @return string 
     */
    
    public function findCiudades ( $idEstado )
    {
        
        $sql = "SELECT a.id , a.nombre
            FROM CIUDADES AS a INNER JOIN ESTADOS_CIUDADES_REL AS b
            ON ( a.id = b.id_ciudad )
            WHERE b.id_estado = {$idEstado}
        ";
            
        $this->setSqlQuery($sql)->execQuery();
        
        if ( count($this->getResultSet()) > 0 )
            $resultSet = json_encode($this->getResultSet());
        else
            $resultSet = json_encode ( array('id' => '0' , 'nombre' => "No hay registros de Estados para la Empresa seleccionado" ) );
                
        return (String) $resultSet;
    }
}