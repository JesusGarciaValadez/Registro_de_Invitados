<?php
/**
 * Entidad Vista Vendedores 
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */
class VistaVendedores extends Model{
    
    protected $_logs = null;


    public function __construct( $conn ) 
    {
        $this->_tableName = 'persona p';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos($conn);
    }
    
    public function getTopTen ()
    {
        $indice = 1;
        $html = '';
        $fields = array (
            'p.`id` as ID'
            , 'p.`firstname` as Name '
            , 'p.`lastname` as First_Name '
            , 'p.`lastname2` as Last_Name'
        );
        $conditions = array(
            'order' => ' Name'
            ,'orderSense' => 'DESC'
        );
        
        $resultSet = $this->select($fields, $conditions);

        foreach ( $resultSet as $empleado ) {

            $html .= "<tr><td>{$empleado[ 'First_Name' ]}</td><td>{$empleado[ 'Last_Name' ]}</td><td>{$empleado[ 'Name' ]}</td><th><a href='snippets/control.php?action=edit&id={$empleado[ 'ID' ]}' title='Editar'>Editar</a></th></tr>";
            $indice++;
        }
        
        return (string) $html;
    }
    
}