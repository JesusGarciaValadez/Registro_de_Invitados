<?php
/**
 * Entidad Vista Vendedores 
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category entities
 */
class VistaUsuarios extends Model{
    
    protected $_logs = null;
    protected $_privateInfo;
    protected $_title;
    protected $_state;

    public function __construct ( $conn ) {
        $this->_tableName = '`persona` as a';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
    }
    
    public function getInfoUser ( $mail_user ) {
        $obtain = $mail_user;
        
        $fields = array (
            'a.`id_persona` AS ID ' 
            ,'a.`mail` AS Mail ' 
            ,'a.`first_name` AS First_Name '
            ,'a.`last_name` AS Last_Name '
            ,'a.`user_name` AS User_Name'
            ,'a.`job` AS Job'
            ,'a.`where_from` AS WhereFrom'
            ,'a.`lada` AS Lada'
            ,'a.`phone` AS Phone'
            ,'a.`ext` AS Ext'
            ,'a.`dependency` AS Dependency'
            ,'( SELECT c.title FROM `title` AS c WHERE c.`id_title` = a.`id_title` ) AS Title'
            ,'( SELECT b.state FROM `state` AS b WHERE b.`id_state` = a.`id_state` ) AS State'
            ,'a.`city` AS City'
            ,'a.`is_completed` AS Completed'
        );
        $conditions = array(
            'where' => "a.`mail` = '{$obtain}' GROUP BY ID"
            ,'order' => ' User_Name'
            ,'orderSense' => 'ASC'
            ,'limit' => '1'
        );
        
        $this->setTableName( '`persona` as a, `state` AS b, `title` AS c' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $cargo ) {
            
            if ( $cargo['Title'] ) {
                
                $this->_title = $cargo[ 'Title' ];
            }
            if ( $cargo['State'] ) {
                
                $this->_state = $cargo[ 'State' ];
            }
        }
        
        return $resultSet;
    }
    
    public function getTitle ( ) {
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'c.`id_title` AS id_Title '
            ,'c.`title` AS Title '
        );
        $conditions = array(
            'order' => ' c.`id_Title`'
            ,'orderSense' => 'ASC'
        );
        $this->setTableName( '`title` AS c' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $cargo ) {
            
            if ( $cargo[ 'Title' ] == $this->_title ) {
                
                $html  .= "<option value='{$cargo['id_Title']}' selected=\"selected\">{$cargo['Title']}</option>";
            } else {
                
                $html  .= "<option value='{$cargo['id_Title']}'>{$cargo['Title']}</option>";
            }
            $indice++;
        }
        
        return (string) $html;
    }
    
    public function getStates ( ) {
        
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'b.`id_state` AS id_State '
            ,'b.`state` AS State '
        );
        $conditions = array(
            'order' => ' b.`id_State`'
            ,'orderSense' => 'ASC'
            ,'limit' => '0,99'
        );
        $this->setTableName( '`state` AS b' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $estado ) {
            
            if ( $estado[ 'State' ] == $this->_state ) {
                
                $html  .= "<option value='{$estado['id_State']}' selected=\"selected\">{$estado['State']}</option>";
            } else {
                
                $html  .= "<option value='{$estado['id_State']}'>{$estado['State']}</option>";
            }
            
            $indice++;
        }
        
        return (string) $html;
    }
}