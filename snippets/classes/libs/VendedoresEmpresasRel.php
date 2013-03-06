<?php
/**
 * Proporcionar metodos de acceso y control sobre 
 * la entidad VENDEDORES_EMPRESA_REL
 * @package classes
 * @subpackage libs
 * @category entities
 */

class VendedoresEmpresasRel extends Model{
    
    public function __construct( $conn ) 
    {
        $this->_tableName = 'VENDEDORES_EMPRESA_REL';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
    }
    
    /**
     * Obtener la coleccion de datos (Vendedores) que cumplan con 
     * el criterio establecido ($idEmpresa)
     * @method obtenerVendedores
     * @access public
     * @param int $idEmpresa identificador de la empresa.
     * @return array
     */
    
    public function obtenerVendedores ( $data )
    {        
        $idEmpresa = ( isset ( $data['id_empresa'] ) && (int) $data['id_empresa'] > 0 )? $data['id_empresa'] : 0 ;
        
        $sql = "SELECT a.id AS id_vendedor, CONCAT_WS(' ', a.nombre, a.ap_paterno, a.ap_materno ) AS full_name
        FROM VENDEDORES AS a INNER JOIN {$this->_tableName} AS b 
        ON ( a.id = b.id_vendedor ) 
        WHERE b.id_empresa={$idEmpresa} AND b.activo = 1
        ";
        
        $this->setSqlQuery($sql)->execQuery();
        
        return (array) $this->getResultSet();
    }
    
}
