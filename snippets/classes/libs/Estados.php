<?php
/**
 * Entidad Estados.
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package class
 * @subpackage libs
 * @category entities
 */

class Estados extends Model{
    
    /**
     * Entidad Estado, obtener los estados de la empresa
     * @method __construct()
     * @access pubic
     * @param PDO $conn objeto de conexion tipo PDO
     * @return void 
     */
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'ESTADOS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn($conn);
    }
    
    /**
     * Obtener el conjunto de datos (estados) de la empresa.
     * @method findEstados()
     * @access public
     * @param Integer $idEmpresa Identificador de la empresa
     * @return String 
     */

    public function findEstados ( $idEmpresa = 0 )
    {
        $resultSet = array();
        $sql = "SELECT a.id AS id, a.nombre AS nombre FROM ESTADOS AS a INNER JOIN EMPRESAS_ESTADOS_REL AS b 
            ON (a.id = b.id_estado)
            WHERE b.id_empresa = {$idEmpresa}
        ";
            
        $this->setSqlQuery($sql)->execQuery();
        
        if ( count($this->getResultSet()) > 0 )
            $resultSet = json_encode($this->getResultSet());
        else
            $resultSet = json_encode ( array('id' => '0' , 'nombre' => "No hay registros de Estados para la Empresa seleccionado" ) );
                
        return (String) $resultSet;
    }
    
}