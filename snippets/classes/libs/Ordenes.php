<?php

/**
 * entidad de venta
 * @package classes
 * @subpackage libs
 * @category Entities
 */

class Ordenes extends Model{
    
    protected $_PDOConn = null;
    
    public function __construct( $conn )
    {
        $this->_tableName = 'REGISTROS_VENTAS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
        $this->_PDOConn = $conn;
    }
    
    public function obtenerOrdenes ( $data )
    {
        $table = '';
        $filter = ' 1';
        $filter .= ( isset ( $data['id_empresa'] ) && $data['id_empresa'] > 0 )? " AND a.empresa_id={$data['id_empresa']}" : '';
        $filter .= ( isset ( $data['id_empleado']) && $data['id_empleado'] > 0 )? " AND a.vendedor_id={$data['id_empleado']}" : '';
        $filter .= ( isset ( $data['incluye_officeOEM'] ) && $data['incluye_officeOEM'] > 0 )? " AND a.tiene_productos_microsoft = 1": '';
        $filter .= ( isset ( $data['incluye_officeOEM'] ) && $data['incluye_officeOEM'] > 0 )? " AND a.tiene_productos_microsoft = 1": '';
                
        $sql = "SELECT a.id, a.orden, a.fecha_fact, CONCAT_WS(' ', a.nombre, a.apellido_paterno, a.ap_materno) AS full_name, 
            a.empresa, a.ISR, a.cliente, a.Puntos
            FROM V_REGISTRO_VENTAS AS a WHERE {$filter}
        ";
        
        $this->setSqlQuery( $sql )->execQuery();
        $resultSet = $this->getResultSet();
        $_SESSION['user']['ordenes'] = $resultSet;
        $table = $this->_generarRepote($resultSet);
        return $table;
    }
    
    private function _generarRepote ( $data )
    {
        $head = '<table>
            <thead>
                <tr>
                    <th class="column_name">N&ordm; de &Oacute;rden</th>
                    <th class="column_date">Fecha de facturaci&oacute;n</th>
                    <th class="column_agent">Agente</th>
                    <th class="column_business">Empresa</th>
                    <th class="column_isr">I.S.R.</th>
                    <th class="column_client">Cliente</th>
                    <th class="column_points">N&ordm; de puntos que gener&oacute; registro</th>
                </tr>
            </thead>
            <tbody>
        ';
        $body = '';
        
        foreach ( $data as $ordenes ) {
            $body .= "<tr>
                    <td class=\"column_name\">{$ordenes['orden']}</td>
                    <td class=\"column_date\">{$ordenes['fecha_fact']}</td>
                    <td class=\"column_agent\">{$ordenes['full_name']}</td>
                    <td class=\"column_business\">{$ordenes['empresa']}</td>
                    <td class=\"column_isr\">{$ordenes['ISR']}</td>                    
                    <td class=\"column_client\">{$ordenes['cliente']}</td>
                    <td class=\"column_points\">{$ordenes['Puntos']}</td>
                </tr>
            ";
        }
        
        $footer = '</tbody></table>';
        $table = $head . $body . $footer;
        $_SESSION['user']['excel'] = $table;
        return $table;
    }   
    
}