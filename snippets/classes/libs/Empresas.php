<?php
/**
 * Entidad Empresa.
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */
class Empresas extends Model{
    
    /**
     * Entidad Empresa. Filtrar empresas por nombre.
     * Actualizar estado de un conjunto de datos
     * @method __construct()
     * @param PDO $conn
     * @access public
     * @return void
     */
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'EMPRESAS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn($conn);
    }
    
    /**
     * Obtener un conjunto de datos en base al filtro establecido.
     * @method findEmpresas()
     * @access public
     * @param String $filter
     * @return String 
     */
    
    public function findEmpresas ( $filter ){
        
        $resultSet = array();
        
        if ( strlen($filter) >= 3 ){
            $fields = array('id AS adminName1','nombre AS countryName');
            $conditions['where'] = " id != 314 AND  UPPER(nombre) LIKE '%{$filter}%'";
            $resultSet = $this->select($fields, $conditions);
            if ( count($resultSet) > 0){
                $result = array( 'total' => count($resultSet), 'geonames' => $resultSet );
                $resultSet = json_encode($result);
            }
            else{
                $resultSet = json_encode ( array('adminName1' => '0', 'countryName' => "No hay empresas con el criterio: {$filter}") );
            }
        }
        else{
             $resultSet = json_encode ( array('adminName1' => '0', 'countryName' => "No hay empresas con el criterio: {$filter}") );
        }
        //return str_replace(array('"adminName1"','"countryName"'), array('adminName1','countryName'), $resultSet);
        return $resultSet;
    }
    
    /**
     * metodo para obtener todos los registros de la entidad
     * @method fetchAll
     * @access public
     * @return string retorna la coleccion en formato JSON.
     */
    
    public function fetchAll() 
    {
        $resultSet = array();
        $this->setSqlQuery("SELECT * FROM EMPRESAS WHERE id != 314;")->execQuery();
        $resultSet = $this->getResultSet();
        return json_encode( $resultSet );
    }
    
}