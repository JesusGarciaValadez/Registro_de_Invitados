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
    protected $_privateInfo;

    public function __construct ( $conn ) {
        $this->_tableName = 'persona p';
        $this->_primaryKey = 'id';
        $this->setMysqlConn( $conn );
    }
    
    public function getTopTen ( ) {
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

            $html .= "<tr><td>{$empleado[ 'First_Name' ]}</td><td>{$empleado[ 'Last_Name' ]}</td><td>{$empleado[ 'Name' ]}</td><th><a href='edit.php?id={$empleado[ 'ID' ]}' title='Editar'>Editar</a></th></tr>";
            $indice++;
        }
        
        return (string) $html;
    }

    public function getInfoUser ( $id_user ) {
        $indice = 1;
        $obtain = (integer) $id_user;

        $fields = array (
            'p.`id` AS ID_User ' 
            ,'p.`firstname` AS User_Name '
            ,'p.`lastname` AS First_Name '
            ,'p.`lastname2` AS Last_Name '
            ,'p.`email` AS Email '
            ,'( SELECT cat.`title` FROM catalogo AS cat WHERE cat.`id`= p.`gender_id` ) AS Genero '
            ,'p.`age` AS Edad '
            ,'( SELECT cat.`title`  
 FROM catalogo AS cat, link_persona_generacion AS g WHERE cat.`id`= g.`distrito_id` AND cat.`id` = ( SELECT g.`distrito_id` FROM link_persona_generacion AS g, persona AS p WHERE g.`persona_id` = ID_User GROUP BY g.`persona_id` ORDER BY g.`persona_id` LIMIT 1 ) GROUP BY cat.`title` ORDER BY cat.`title` LIMIT 1  ) AS Distrito '
            ,'e.`name` AS Estado '
            ,'p.`semblance` AS Ocupacion '
            ,'( SELECT h.`description` AS Escol FROM historial_academico AS h, link_persona_generacion AS g WHERE h.`id`= ID_User  GROUP BY Escol ORDER BY Escol LIMIT 1 ) AS Escolaridad '
            ,'p.`suplente` AS Suplente '
            ,'( SELECT cat.`title` AS Area_id FROM catalogo AS cat, link_persona_generacion AS g WHERE cat.`id`= ( SELECT g.`area_id` FROM link_persona_generacion AS g WHERE g.`persona_id` = ID_User ) GROUP BY Area_id ORDER BY Area_id LIMIT 1 ) AS Area '
            ,'p.`pic` AS Foto '
        );
        $conditions = array(
            'where' => "p.`id` = {$obtain} AND e.`id` = p.`estado_id` GROUP BY p.`id`"
            ,'order' => ' User_Name'
            ,'orderSense' => 'ASC'
            ,'limit' => '1'
        );
        $this->setTableName( 'persona p, estado e, historial_academico h' );
        
        return $resultSet = $this->select($fields, $conditions);
    }

    public function getUserComissions ( $id_user ) {
        $indice = 1;
        $obtain = (integer) $id_user;
        $html   = '';

        $fields = array (
            "p.`id` AS ID_User "
            ,"link.`id` AS ID_Link "
    		,"link.`participacion_id` AS ID_Participacion "
            ,"link.`comision_id` AS ID_Comision "
            ,"link.`cargo_id` AS ID_Cargo "
    		,"cat.`title` AS Comision "
    		,"( SELECT cat.`title` FROM catalogo AS cat WHERE p.`id` = {$obtain} AND   link.`participacion_id` = p.`id` AND   cat.`id` = link.`cargo_id` GROUP BY cat.`id` ORDER BY cat.`id` ASC ) AS Cargo "
        );
        $conditions = array(
            "where" => "p.`id` = {$obtain} AND   link.`participacion_id` = p.`id` AND   cat.`id` = link.`comision_id` GROUP BY cat.`id`"
            ,"order" => "cat.`id`"
            ,"orderSense" => "ASC"
            ,"limit" => "0, 99"
        );
        $this->setTableName( "catalogo AS cat, persona AS p, link_participacion_comision AS link" );
                
        $resultSet = $this->select($fields, $conditions);
        
        if ( $this->getNumRows( ) ) {
            
            foreach ( $resultSet as $comisionCargo ) {
    
                $html .= "<tr><td>{$comisionCargo[ 'Comision' ]}</td><td>{$comisionCargo[ 'Cargo' ]}</td><td><a href='snippets/control.php?action=deleteComision&id={$comisionCargo[ 'ID_Link' ]}' class='del_button' title='Eliminar'>Eliminar</a></td></tr>";
                $indice++;
            }
        } else {
            
            $html .= "<tr><td>No hay comisiones ni cargos disponibles para &eacute;ste usuario</td></tr>";
        }
                
        return (string) $html;
    }
    
    public function getDistrito ( ) {
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'cat.`id` AS ID_Distrito '
            ,'cat.`title` AS Distrito '
        );
        $conditions = array(
            'where' => "cat.`tipo_id` = 10 GROUP BY cat.`title`"
            ,'order' => ' cat.`id`'
            ,'orderSense' => 'ASC'
            ,'limit' => '0,99'
        );
        $this->setTableName( 'catalogo AS cat' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $distritos ) {
            $html  .= "<option value='{$distritos['ID_Distrito']}'>{$distritos['Distrito']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }

    public function getEstados ( ) {
        
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'e.`id` AS ID_Estado '
            ,'e.`name` AS Estado '
        );
        $conditions = array(
            'order' => ' e.`id`'
            ,'orderSense' => 'ASC'
            ,'limit' => '0,99'
        );
        $this->setTableName( 'estado AS e' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $estado ) {
            
            $html  .= "<option value='{$estado['ID_Estado']}'>{$estado['Estado']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }

    public function getEleccion ( ) {
        $indice = 1;
        $html = '';
        
        $fields = array (
            'cat.`id` AS ID_Eleccion '
            ,'cat.`title` AS Eleccion '
        );
        $conditions = array(
            'where' => "cat.`id` = 136 
					OR cat.`id` = 137 
					OR cat.`id` = 138 
					OR cat.`id` = 139 
					GROUP BY Eleccion"
            ,'order' => ' Eleccion'
            ,'orderSense' => 'ASC'
            ,'limit' => '0,99'
        );
        $this->setTableName( 'catalogo AS cat' );
        
        $resultSet = $this->select($fields, $conditions);
        
        foreach ( $resultSet as $eleccion ) {
            
            $html  .= "<option value='{$eleccion['ID_Eleccion']}'>{$eleccion['Eleccion']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }
    
    public function getGenero ( ) {
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'cat.`id` AS ID_Genero '
            ,'cat.`title` AS Genero '
        );
        $conditions = array(
            'where' => "cat.`tipo_id` = 1 GROUP BY cat.`title`"
            ,'order' => ' cat.`id`'
            ,'orderSense' => 'ASC'
            ,'limit' => '0,99'
        );
        $this->setTableName( 'catalogo AS cat' );
        
        $resultSet = $this->select($fields, $conditions);

        foreach ( $resultSet as $genero ) {

            $html  .= "<option value='{$genero['ID_Genero']}'>{$genero['Genero']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }

    public function getComisiones ( ) {
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'com.`comision_id` AS ID_Comision '
            ,'cat.`title` AS Comision '
        );
        $conditions = array(
            'where' => "cat.`id` = com.`comision_id` GROUP BY ID_Comision"
            ,'order' => ' ID_Comision'
            ,'orderSense' => 'ASC'
        );
        $this->setTableName( 'link_participacion_comision AS com, catalogo AS cat' );
        
        $resultSet = $this->select($fields, $conditions);

        foreach ( $resultSet as $comision ) {

            $html  .= "<option value='{$comision['ID_Comision']}'>{$comision['Comision']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }

    public function getCargos ( ) {
        $indice = 0;
        $html   = '';
        
        $fields = array (
            'com.`cargo_id` AS ID_Cargo '
            ,'cat.`title` AS Cargo '
        );
        $conditions = array(
            'where' => "cat.`id` = com.`cargo_id` GROUP BY ID_Cargo"
            ,'order' => ' ID_Cargo'
            ,'orderSense' => 'ASC'
        );
        $this->setTableName( 'link_participacion_comision AS com, catalogo AS cat' );
        
        $resultSet = $this->select($fields, $conditions);

        foreach ( $resultSet as $cargo ) {

            $html  .= "<option value='{$cargo['ID_Cargo']}'>{$cargo['Cargo']}</option>";
            $indice++;
        }
        
        return (string) $html;
    }

/*SELECT p.`id` AS ID_User, 
        p.`firstname` AS User_Name, 
        p.`lastname` AS First_Name, 
        p.`lastname2` AS Last_Name, 
        p.`email` AS Email, 
        ( SELECT cat.`title` 
          FROM catalogo AS cat 
          WHERE cat.`id`= p.`gender_id` ) AS Gender, 
        p.`age` AS Age, 
        ( SELECT cat.`title`  
          FROM catalogo AS cat, 
                link_persona_generacion AS g
          WHERE cat.`id`= g.`distrito_id` 
          AND cat.`id` = ( SELECT g.`distrito_id`  
                 FROM link_persona_generacion AS g, 
                       persona AS p 
                 WHERE g.`persona_id` = ID_User 
                 GROUP BY g.`persona_id` 
                 ORDER BY g.`persona_id` 
                 LIMIT 1 ) 
           GROUP BY cat.`title` 
           ORDER BY cat.`title` 
           LIMIT 1  ) AS Distrito, 
        e.`name` AS Estado, 
        p.`semblance` AS Ocupacion, 
        ( SELECT h.`description` AS Escol
          FROM historial_academico AS h, 
                link_persona_generacion AS g
          WHERE h.`id`= ID_User 
          GROUP BY Escol 
         ORDER BY Escol 
         LIMIT 1 ) AS Escolaridad, 
        p.`suplente` AS Suplente, 
        ( SELECT cat.`title` AS Area_id
          FROM catalogo AS cat, 
                link_persona_generacion AS g
          WHERE cat.`id`= ( SELECT g.`area_id` 
          FROM link_persona_generacion AS g 
          WHERE g.`persona_id` = ID_User ) 
          GROUP BY Area_id 
         ORDER BY Area_id 
         LIMIT 1 ) AS Area, 
        p.`pic` AS Foto 
FROM persona p, 
      estado e, 
      historial_academico h 
WHERE p.`id` = 597 
AND    e.`id` = p.`estado_id`
GROUP BY p.`id` 
ORDER BY User_Name ASC
LIMIT 1*/

/*SELECT cat.`title` AS Distrito
FROM catalogo AS cat 
WHERE cat.`tipo_id` = 10 
GROUP BY cat.`title` 
ORDER BY cat.`id` ASC 
LIMIT 0, 99*/

/*SELECT p.`id` AS ID_User, 
        p.`firstname` AS User_Name, 
        p.`lastname` AS First_Name, 
        p.`lastname2` AS Last_Name, 
        p.`email` AS Email, 
        ( SELECT cat.`title` 
          FROM catalogo AS cat 
          WHERE cat.`id`= p.`gender_id` ) AS Gender, 
        p.`age` AS Age, 
        ( SELECT cat.`title`  
          FROM catalogo AS cat, 
                link_persona_generacion AS g
          WHERE cat.`id`= g.`distrito_id` 
          AND cat.`id` = ( SELECT g.`distrito_id`  
                 FROM link_persona_generacion AS g, 
                       persona AS p 
                 WHERE g.`persona_id` = ID_User 
                 GROUP BY g.`persona_id` 
                 ORDER BY g.`persona_id` 
                 LIMIT 1 ) 
           GROUP BY cat.`title` 
           ORDER BY cat.`title` 
           LIMIT 1  ) AS Distrito, 
        e.`name` AS Estado, 
        ( SELECT cat.`id` AS ID_Eleccion, 
          			cat.`title` AS Eleccion 
          FROM catalogo AS cat, 
    			  persona AS per, 
    			  link_persona_generacion AS link 
          WHERE link.`persona_id` = ID_User 
          AND cat.`id` = link.`eleccion_id` 
          GROUP BY cat.`id` 
          ORDER BY cat.`id` ) AS Eleccion, 
        p.`semblance` AS Ocupacion, 
        ( SELECT h.`description` AS Escol
          FROM historial_academico AS h, 
                link_persona_generacion AS g
          WHERE h.`id`= ID_User 
          GROUP BY Escol 
         ORDER BY Escol 
         LIMIT 1 ) AS Escolaridad, 
        p.`suplente` AS Suplente, 
        ( SELECT cat.`title` AS Area_id
          FROM catalogo AS cat, 
                link_persona_generacion AS g
          WHERE cat.`id`= ( SELECT g.`area_id` 
          FROM link_persona_generacion AS g 
          WHERE g.`persona_id` = ID_User ) 
          GROUP BY Area_id 
         ORDER BY Area_id 
         LIMIT 1 ) AS Area, 
        p.`pic` AS Foto 
FROM persona p, 
      estado e, 
      historial_academico h 
WHERE p.`id` = 51 
AND    e.`id` = p.`estado_id`
GROUP BY p.`id` 
ORDER BY User_Name ASC
LIMIT 1*/

/*SELECT cat.`title` AS Distrito
FROM catalogo AS cat 
WHERE cat.`tipo_id` = 10 
GROUP BY cat.`title` 
ORDER BY cat.`id` ASC 
LIMIT 0, 99*/

}