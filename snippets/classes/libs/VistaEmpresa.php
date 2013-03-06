<?php
/**
 * Entidad Vista Vendedores 
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */
class VistaEmpresa extends Model{
    
    protected $_logs = null;

    public function __construct( $conn ) 
    {
        $this->_tableName = 'V_PUNTOS_EMPRESAS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos($conn);
    }
    
    public function getTopTen ()
    {
        $indice = 1;
        $html = '';
        $fields = array (
            'empresa', 'final as puntaje'
        );
        $conditions = array(
            'where' => '1'
            ,'order' => ' final'
            ,'limit' => '10'
            ,'orderSense' => 'DESC'
        );
        
        $resultSet = $this->select($fields, $conditions);
        foreach ( $resultSet as $empresa ) {
            $html .= "<li><span>{$indice}</span><p>{$empresa['empresa']} Puntos {$empresa['puntaje']}</p></li>";
            $indice++;
        }
        
        return (string) '<ul>'.$html.'</ul>';
    }
    
}