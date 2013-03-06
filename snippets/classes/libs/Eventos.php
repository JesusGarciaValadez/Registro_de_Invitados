<?php
/**
 * Entidad Eventos
 */

class Eventos extends Model{
    
    const SYSTEM_JOIN = 1;
    const SYSTEM_VISITED = 2;
    const SYSTEM_FORGOTTEN_PASSWORD = 3;
    const SYSTEM_NEW_PASSWORD_REQUEST = 4;
    const NEW_ORDER = 5;
    const ORDER_RECONCILING = 6;
    const ORDER_BALANCE = 7;
    const ORDER_PROCESSED = 8;
    const ORDER_DOWNLOAD = 9;
    const USER_CHANGE_COMPANY =10;
    const SALES_UPDATE_SCORE = 11;
    const COMPANY_UPDATE_SCORE = 12;
    const SYSTEM_LOGOUT = 13;

        public function __construct( $conn ) 
    {
        $this->_tableName = 'LOG_EVENTOS';
        $this->_primaryKey = 'id';
        $this->setMysqlConn($conn);
    }
    
    /**
     * Registro en el log los eventos.
     * @method registerEvent()
     * @access public
     * @param int $event identificador de evento
     * @param int $user identificador deusuario
     * @param int $date fecha de registro
     * @param string $description descripcion del evento
     * @return string 
     */
    
    public  function registerEvent( $event, $user, $date ,$description )
    {
        $id = 0;
        $parameters = array(
            'id_evento' => array(
                'requerido' => 1 ,'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador del evento no es valido')
            )
            ,'id_usuario' => array(
                'requerido' => 1 ,'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador del usuario no es valido.')
            )
            ,'fecha' => array(
                'requerido' => 1 ,'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('La fecha no es valida.')
            )
            ,'descripcion' => array(
                'requerido' => 1, 'validador' => 'serialize', 'mensaje' => utf8_encode('La descripción del evento no es valida')
            )
        );
        
        $data = array (
            'id_evento' => $event
            ,'id_usuario' => $user
            ,'fecha' => $date
            ,'descripcion' => $description
        );
        
        $form = new Validator($data, $parameters);
        
        if ( ! $form->validate() ){
            echo  $form->getMessage();
            $response = array('success' => 'false','message'=> $form->getMessage());
        }else{
            $id = $this->insert($data);
            if ( (int) $id > 0 )
                $response = array('success' => 'true','message'=> utf8_encode('El evento se ha registrado exitosamente.') );
            else
                $response = array('success' => 'false','message'=> utf8_encode('No es posible registrar el evento.'));            
        }        
        
        return json_encode($response);
    }
    
    /**
     *obtener el historico de movimientos en las ordenes de venta.
     * @method filterLog
     * @access public
     * @param int $user identificador del usuario con rol de vendedor.
     * @param int $page pagina actual de consulta.
     * @return string 
     */
    
    public function filterLog ( $user = 0, $page = 0 )
    {
        $fields = array('id_evento AS evento','fecha','descripcion');
        $conditions = array(
            'where' => 'id_evento IN(5,6,7,10,11)'
            ,'order' => 'id'
            ,'orderSense' => 'DESC'
        );
                        
        $parameters = array(
            'id_usuario' => array(
                'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El identificador del usuario no es valido.')
            )            
        );
        
        $data = array(
            'id_usuario' => $user
        );
        
        $form = new Validator( $data, $parameters );
        
        if ( ! $form->validate() ){
            $response = array('success' => 'false','message'=> $form->getMessage());
        }else{
        
            $conditions['where'].= " AND id_usuario = {$user}";            
            
           if ( $page > 1 ){
               $start = $page * 15 ;
               $conditions['limit'] = "{$start} ,15";
           }else{
               $conditions['limit'] = ' 0 , 15';
           }
                
                
            $resultSet = $this->select($fields, $conditions);
            
            if ( count($resultSet) > 0 ) {
                $html = $this->_formatoReporte( $resultSet );
            } else {
                $html = '<tr class="column_message">
                            <td></td>
                            <td>No hay movimientos a mostrar</td>
                            <td></td>
                        </tr>';
            }
            
        }
        
        return (string) "<tbody>{$html}</tbody>";
    }
    
    /**
     *dar formato a los datos.
     * @method _formatoReporte
     * @access private
     * @param array $data coleccion de datos para mostrar el historico en los
     * moventos de los puntos del usuario.
     * @return string 
     */
    
    private function _formatoReporte ( $data )
    {
        $records = '';
        
        if (is_array( $data) ){
            foreach ( $data as $evento ) {
                $idEvento = $evento['evento'];
                $fechaEvento = date_create($evento['fecha']);
                $fecha = date_format( $fechaEvento, 'd/m/y' );
                $descripcionEvento = $evento['descripcion'];
                switch ( $idEvento ) {
                    case '5':
                        $cssClass = 'row_change';
                        $descripcion = $descripcionEvento;
                        $puntaje = '&nbsp;';
                        break;
                    case '6':
                        $cssClass = 'row_register';
                        $unserialize = unserialize( $descripcionEvento );
                        $descripcion = $unserialize['descripcion'];
                        $puntaje = $unserialize['puntaje'];
                        break;
                    case '7':
                        $cssClass = 'row_change';
                        $unserialize = unserialize( $descripcionEvento );
                        $descripcion = $unserialize['descripcion'];
                        $puntaje = $unserialize['puntaje'];
                        break;
                    case '10':
                        $cssClass = 'row_points';
                        $unserialize = unserialize( $descripcionEvento );
                        $descripcion = $unserialize['descripcion'];
                        $puntaje = $unserialize['puntaje'];
                        break;
                    case '11':
                        $cssClass = 'row_change';
                        $unserialize = unserialize( $descripcionEvento );
                        $descripcion = $unserialize['descripcion'];
                        $puntaje = $unserialize['puntaje'];
                        break;
                    default:
                        $cssClass = 'row_register';
                        break;
                }
                
                $records .= "
                    <tr class=\"{$cssClass}\">
                        <td class=\"column_date\">{$fecha}</td>
                        <td class=\"column_description\">{$descripcion}</td>
                        <td class=\"column_points\">{$puntaje}</td>
                    </tr>
                ";
            }
        }        
        return $records;
    }
    
}
