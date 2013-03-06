<?php

/**
 * Proporciona los metodos para persistir una orden de venta.
 * @author Augusto Silva <augusto@ingeniagroup.com.mx>
 * @package classes
 * @subpackage libs
 * @category Core
 */

class RegistrarVenta extends Model{
    
    private $_puntosOrden = 0;
    private $_puntosVendedor = 0;
    private $_puntosEmpresa = 0;
    private $_idUsuario = 0;
    
    protected $_productos = null;
    protected $_logs = null;
    protected $_conn = null;
    protected $_lineamientos = null;
    protected $_detalle = null;
    protected $_puntaje = null;
    protected $_empresa = null;


    public function __construct( $conn )
    {
        $this->_tableName = 'REGISTROS_VENTAS';
        $this->_primaryKey = 'id';
        $this->_conn = $conn;
        $this->setMysqlConn( $conn );
        $this->_logs = new Eventos ( $conn );
        $this->_lineamientos = new PuntosProductos( $conn );
        $this->_detalle = new DetalleRegistrosVentas( $conn );
        $this->_puntaje = new PuntajeVendedores( $conn );
        $this->_empresa = new PuntajeEmpresas ( $conn );
        View::setViewFilesRepository(VIEWS_PATH);
    }
    
    /**
     * metodo para registrar una orden de venta
     * @method registrarVenta
     * @access public
     * @param array $data coleccion de datos para registrar la orden de venta
     * @return string  
     */
    
    public function registrarVenta ( array $data = array() )
    {
        $idOrden = 0;
        $response = $dataEmail = array();
                
        if ( Usuarios::activeSesion() == 'V' ){
            
            $idVendedor = $_SESSION['user']['vendedor'];
            $this->_idUsuario = $_SESSION['user']['id'];
            
            $parameters = array(
                'no_orden' => array(
                    'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El número de orden es obligatorio.')
                )
                ,'fecha_fact' => array(
                    'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('La fecha de facturación es obligaoria.')
                )
                ,'id_empresa' => array(
                    'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('La empresa es obligatoria.')
                )
                ,'id_estado' => array(
                    'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El estado es obligatorio.')
                )
                ,'id_ciudad' => array(
                    'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('La ciudad es obligatoria.')
                )
                ,'id_isr' => array(
                    'requerido' => 1, 'validador' => 'esNumerico', 'mensaje' => utf8_encode('El reprentantes (Inside Sales Representative) es obligatorio.')
                )
                ,'cliente' => array(
                    'requerido' => 1, 'validador' => 'esAlfaNumerico', 'mensaje' => utf8_encode('El nombre del cliente es requerido.')
                )
            );

            $forma = new Validator($data, $parameters);
            
            if ( ! $forma->validate() ){
                $response = array( 'success' => 'false', 'message' => utf8_encode( $forma->getMessage() ) );
            }else{
                
                $vendedores = new Vendedores( $this->_conn );
                $totalProducto = count ( $data['id_productos'] );
                
                // Obener identificador de la relacion entre estado y ciudad
                
                $sql = "SELECT id FROM ESTADOS_CIUDADES_REL WHERE id_estado={$data['id_estado']} AND id_ciudad={$data['id_ciudad']}";
                $this->setSqlQuery($sql)->execQuery();
                $resultSet = $this->getResultSet();
                $idRelEstados = $resultSet[0]['id'];
                
                // Obtener la empresa actual del Vendedor
                
                if ( $vendedores->find($idVendedor) ){
                    
                    $this->setSqlQuery("SELECT id_empresa FROM VENDEDORES_EMPRESA_REL WHERE id_vendedor={$idVendedor} ")->execQuery();
                    $resultSet = $this->getResultSet();
                    $empresaActual = $resultSet[0]['id_empresa'];
                                        
                    if ( $empresaActual != $data['id_empresa'] ){
                        // obtener los nombres de las empresas y actualizar el puntaje del vendedor a 0                        
                        $empresaAnterior = $this->_obtenerNombreEmpresa( $idEmpresa );
                        $empresaNueva = $this->_obtenerNombreEmpresa( $data['id_empresa'] );
                        $this->_puntaje->resetScore( $idVendedor, $empresaAnterior, $empresaNueva );
                        $vendedores->actualizarVendedorEmpresaRel( $idVendedor, $data['id_empresa'] );
                    }
                                        
                    if ( $totalProducto > 0  ){
                        
                        $this->setSqlQuery("SELECT id FROM REGISTROS_VENTAS WHERE no_orden={$data['no_orden']}")->execQuery();
                        $resultSet = $this->getResultSet();
                        
                        if (count( $resultSet ) > 0 ){
                            $response = array( 'success' => 'false', 'message' => utf8_encode( 'La orden ya fue ingresada anteriormente.' ) );
                        }else{
                        
                            try {
                                $this->_conn->beginTransaction();

                                $venta = array(
                                    'id_vendedor' => $idVendedor
                                    ,'id_empresa' => $data['id_empresa']
                                    ,'id_estados_ciudades_rel' => $idRelEstados
                                    ,'id_isr' => $data['id_isr']
                                    ,'no_orden' => $data['no_orden']
                                    ,'fecha_fact' => $data['fecha_fact']
                                    ,'cliente' => $data['cliente']
                                    ,'puntos_generados' =>0
                                    ,'descargado' => 0
                                );

                                $idOrden = $this->insert( $venta );

                                if ( (int) $idOrden > 0 ){

                                    for ( $indice = 0; $indice < $totalProducto; $indice++ ){

                                        $cantidadConMicrosoft = 0;
                                        $cantidadSinMicrosoft = 0;
                                        $puntosSCM = 0;
                                        $puntosCCM = 0;

                                        $productoPartida   = (int) $data['id_productos'][$indice];
                                        $cantidadTotalPartida = (int) $data['cantidad_prod'][$indice];
                                        $incluyeProductos = ( isset( $data['incluye'][$indice] ) ) ? (int) $data['incluye'][$indice] : 0 ;
                                        $cantidadConMicrosoft = (int) $data['cantidad_micro'][$indice];
                                        $cantidadSinMicrosoft = (int) ( $cantidadTotalPartida - $cantidadConMicrosoft );

                                        // insertar el detalle de la orden (productos sin complementos)

                                        if ( $cantidadTotalPartida > 0 ){
                                            $producto = array(
                                                'id_reporte_ventas' => $idOrden
                                                ,'id_producto' => $productoPartida
                                                ,'cantidad' => $cantidadSinMicrosoft
                                                ,'tiene_productos_microsoft' => 0
                                                ,'procesada' => 0
                                                ,'puntos_generados' => 0
                                            );                                        
                                            $puntosSCM = $this->_insertarProductos( $producto,$data['id_empresa'], $idVendedor );
                                        }

                                        // insertar el detalle de la orden (productos con complementos)

                                        if ( $cantidadConMicrosoft > 0 ){                                      
                                            $producto = array(
                                                'id_reporte_ventas' => $idOrden
                                                ,'id_producto' => $productoPartida
                                                ,'cantidad' => $cantidadConMicrosoft
                                                ,'tiene_productos_microsoft' => 1
                                                ,'procesada' => 0
                                                ,'puntos_generados' => 0
                                            );
                                            $puntosCCM = $this->_insertarProductos( $producto,$data['id_empresa'], $idVendedor );
                                        }

                                        $totalPuntos = ( $puntosSCM + $puntosCCM );

                                        $partidas[$indice] = array(
                                            'clave_producto' => $productoPartida
                                            ,'cantidad' => $cantidadTotalPartida
                                            ,'incluye' => $incluyeProductos
                                            ,'cantidad_micro' => $cantidadConMicrosoft
                                            ,'puntos' => $totalPuntos                                        
                                        );

                                    }

                                    $date = date('Y-m-d H:i:s');
                                    $description = "Has ingresado la Orden de Venta {$data['no_orden']}.";
                                    $this->_logs->registerEvent(Eventos::NEW_ORDER, $this->_idUsuario, $date, $description);

                                    // actualizar puntaje de la orden

                                    if ( $this->_puntosOrden > 0 ){
                                        $this->_establecerPuntaje( $idOrden, $data['no_orden'], $this->_puntosOrden );
                                    }

                                    // actualizar puntaje vendedor

                                    if ( $this->_puntosVendedor > 0 ){
                                        $this->_puntaje->updateScore( $idVendedor, $this->_puntosVendedor, $data['no_orden'] );
                                    }

                                    // actualizar puntaje empresa

                                    $this->_empresa->updateScore( $data['id_empresa'], $this->_puntosEmpresa, $idOrden, $data['no_orden']);

                                    // envio de correo electronico

                                    $dataEmail = array(
                                        'orden_no' => $data['no_orden']
                                        ,'fecha_fact' => $data['fecha_fact']
                                        ,'empresa' => $data['id_empresa']
                                        ,'estado' => $data['id_estado']
                                        ,'ciudad' => $data['id_ciudad']
                                        ,'isr' => $data['id_isr']
                                        ,'cliente' => $data['cliente']
                                        ,'puntaje_orden' => $this->_puntosOrden
                                        ,'puntaje_actual' => $this->_puntosVendedor
                                        ,'partidas' => $partidas
                                    );

                                    $this->_sendEmail( $dataEmail );

                                    $response = array( 'success' => 'true', 'message' => utf8_encode( 'La orden se ha ingresado correctamente.' ) );

                                }
                                $this->_conn->commit();
                            }catch ( PDOException $e ){
                                $this->_conn->rollBack();
                                echo " Error: {$e->getMessage()}";
                                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                            }catch ( Exception $e ){
                                $this->_conn->rollBack();
                                $response = array ('success'=>'false','msg'=>'el servicio no esta disponible');
                            }
                        }
                        
                    }else{
                        $response = array( 'success' => 'false', 'message' => utf8_encode( 'no hay producto(s) en la orden.' ) );
                    }
                    
                    
                }else{
                    $response = array ( 'success' => 'false', 'message' => utf8_encode('El vendedor no fue localizado.') );
                }
            }            
        }
        else{
            $response = array ( 'success' => 'false', 'message' => utf8_encode('La sesion no es valida') );
        }
        
        return $response;
    }
    
    /**
     * persistir en base de datos el detalle de la orden de venta.
     * @method _insertarProductos
     * @access private
     * @param array $data coleccion de datos
     * @param int $idEmpresa identificador de la empresa.
     * @param int $idVendedor identifica al usuario con rol de vendedor
     * @return void
     */
    private function _insertarProductos ( $data, $idEmpresa, $idVendedor )
    {        
        $puntosAcumulados = 0;
        $acomulados = array();
        
        // obtener lineamientos del producto.
        
        $lineamientos = $this->_obtenerLineamiento( $data['id_producto'] );
        $lineamiento = $lineamientos[0]['lineamiento'];
        $puntosUnidad = $lineamientos[0]['puntos'];
        
        // obtener total de producto acumulado.
        
        $acomulados = $this->_obtenerAcumulado( $data['id_producto'], $data['tiene_productos_microsoft'] , $idEmpresa, $lineamiento , $idVendedor );
        $cantidadAcumulada = $acomulados['totalAcumulado'];
        
        if ( $cantidadAcumulada >= $lineamiento ){
             // actualizar puntos actuales
            $data['puntos_generados'] = ( $data['cantidad'] * $puntosUnidad );
        }
        else{
            if ( ( $cantidadAcumulada + $data['cantidad'] ) >= $lineamiento ){
                // actualizar puntos acumulados y actuales
                if ( isset ( $acomulados['ordenes'] ) &&  count ( $acomulados['ordenes'] ) > 0 ){
                    $puntosAcumulados = $this->_actualizarPuntosOrdenes( $acomulados['ordenes'], $puntosUnidad );
                }
                $data['puntos_generados'] = ( $data['cantidad'] * $puntosUnidad );
            }
        }
        
        // calcular total de puntos de la orden
        if ( $data['puntos_generados'] > 0 )
            $this->_puntosOrden = $this->_puntosOrden + $data['puntos_generados'];
        // calcular total de puntos por partida para la empresa        
        $this->_puntosEmpresa = ( $this->_puntosEmpresa + ( $data['cantidad'] * $puntosUnidad ) );
        // calcular total de puntos generados para el vendedor
        if ( $data['puntos_generados'] > 0 )
            $this->_puntosVendedor = ( $this->_puntosVendedor + ( $this->_puntosOrden + $puntosAcumulados ) );
        // almacenar el detalle de la venta
        $this->_detalle->insert( $data );
        
        return $data['puntos_generados'];
    }
    
    /**
     * actualizar los puntos de la orden
     * @method _establecerPuntaje
     * @access private
     * @param int $idOrden identificador de la orden de venta.
     * @param int $noOrden numero de orden de venta.
     * @param int $score cantidad de puntos a actualizar
     * @return void
     */
    
    private function _establecerPuntaje ( $idOrden, $noOrden, $score )
    {        
        $fields = "puntos_generados = ( puntos_generados + {$score} ),descargado=0" ;        
        $where = "id={$idOrden}";
        $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery();
        $success = $this->getNumRows();
        
        if ( $success ){
            
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d H:i:s');
            $unserialize = array( 
                'descripcion' => 'Tus puntaje se ha incrementado en'
                ,'puntaje' => $score
                ,'orden_id' => $idOrden
                ,'orden_no'  => $noOrden 
            );
            
            $serialize = serialize( $unserialize );
            $this->_logs->registerEvent( Eventos::ORDER_BALANCE, $this->_idUsuario, $date, $serialize);
        }
    }
    
    /**
     * Obtener las unidades minimas necesarias para obtener el puntaje por unidad
     * @method _obtenerLineamiento
     * @access private
     * @param int $idProducto identificador del producto.
     * @param int $microsoft identifica si el producto tiene componenetes "microsoft".
     * @return array 
     */
    
    private function _obtenerLineamiento ( $idProducto = 0 , $microsoft = 0 )
    {
        return $this->_lineamientos->searchByIdProducto( $idProducto, $microsoft );
    }
    
    /**
     * obtener el conjunto de datos que aun no se han actualizado.
     * @method _obtenerAcumulado
     * @access private
     * @param int $idProducto identificador del producto.
     * @param int $microsoft identifica si el producto tiene componenetes "microsoft".
     * @param int $idEmpresa identificador de la empresa.
     * @param int $lineamiento cantidad minima 
     * @param int $idVendedor identifica al usuario con rol de vendedor
     * @return array 
     */
    
    private function _obtenerAcumulado ( $idProducto = 0, $microsoft = 0 , $idEmpresa = 0, $lineamiento = 0, $idVendedor = 0 )
    {
        $acumulado = $ordenes = array();
        $indice = 0;
        
        $sql = "SELECT SUM(cantidad) AS acumulado 
            FROM DETALLE_REGISTROS_VENTAS AS a INNER JOIN REGISTROS_VENTAS AS b
            ON ( a.id_reporte_ventas = b.id )
            WHERE a.id_producto={$idProducto} AND a.tiene_productos_microsoft={$microsoft}
            AND b.id_empresa={$idEmpresa} AND b.id_vendedor={$idVendedor}
        ";
        
        $this->setSqlQuery($sql)->execQuery();
        $resultSet = $this->getResultSet();
        $totalAcumulado = $resultSet[0]['acumulado'];
        
        $acumulado = array( 'totalAcumulado' => $totalAcumulado );
        
        if ( $totalAcumulado > 0 && ( $totalAcumulado < $lineamiento ) ){
            // Obtener las partidas para ser actualizadas
            $sql = "SELECT b.id AS orden_id, a.id AS detalle, a.cantidad AS cantidad, b.no_orden AS orden_no
                FROM DETALLE_REGISTROS_VENTAS AS a INNER JOIN REGISTROS_VENTAS AS b
                ON ( a.id_reporte_ventas = b.id )
                WHERE a.id_producto={$idProducto} AND a.tiene_productos_microsoft={$microsoft}
                AND b.id_empresa={$idEmpresa} AND b.id_vendedor={$idVendedor}
            ";
                
            $this->setSqlQuery($sql)->execQuery();
            $resultSet = $this->getResultSet();
            
            if ( count( $resultSet ) > 0 ){
                
                foreach ($resultSet as $keys) {
                    $ordenes[$indice]['orden_id'] = $keys['orden_id'];
                    $ordenes[$indice]['orden_no'] = $keys['orden_no'];
                    $ordenes[$indice]['detalle']  = $keys['detalle'];
                    $ordenes[$indice]['cantidad'] = $keys['cantidad'];
                    $indice++;
                }
                
                $acumulado = array( 
                    'totalAcumulado' => $totalAcumulado
                    ,'ordenes' => $ordenes
                );
                
            }
                
        }        
        return (array) $acumulado;
    }
    
    /**
     * actualizar los puntos generados en las ordenes de venta.
     * @method _actualizarPuntosOrdenes
     * @access private
     * @param array $data conjunto de datos para actualizar el puntaje
     * @param int $puntoPorUnidad puntos por unidad
     * @return int 
     */
    
    private function _actualizarPuntosOrdenes ( array $data = array() , $puntoPorUnidad = 0 )
    {
        $puntosGenerados = 0;
        
        foreach ( $data as $orden ) {            
            $puntos = ( $orden['cantidad'] * $puntoPorUnidad );
            
            // actulizar puntos en el detalle de la venta.
            
            $this->_detalle->update( array( 'id' => $orden['detalle'], 'puntos_generados' => $puntos ) );
            
            // actualizar puntos la orden
                        
            $this->_establecerPuntaje( $orden['orden_id'], $orden['orden_no'], $puntos );
            
            $puntosGenerados = ( $puntosGenerados + $puntos );
        }
        return $puntosGenerados;
    }
    
    /**
     * obtener el nombre de la empresa.
     * @method _obtenerNombreEmpresa
     * @access private
     * @param int $idEmpresa identificador de la empresa.
     * @return string
     */
    
    private function _obtenerNombreEmpresa ( $idEmpresa = 0 )
    {
        $empresa = '';
        
        if ( (int) $idEmpresa > 0 ){
            $this->setSqlQuery("SELECT nombre FROM EMPRESAS WHERE id={$idEmpresa}")->execQuery();
            $resultSet = $this->getResultSet();
            $empresa = $resultSet[0]['nombre'];
        }
        return (string) $empresa;
    }
    
    private function _obtenerNombreEstado ( $idEstado = 0 )
    {
        $estado = '';
        
        if ( (int) $idEstado > 0 ){
            $this->setSqlQuery("SELECT nombre FROM ESTADOS WHERE id={$idEstado}")->execQuery();
            $resultSet = $this->getResultSet();
            $estado = $resultSet[0]['nombre'];
        }
        return (string) $estado;
    }
    
    private function _obtenerNombreCiudad ( $idCiudad = 0 )
    {
        $ciudad = '';
        
        if ( (int) $idCiudad > 0 ){
            $this->setSqlQuery("SELECT nombre FROM CIUDADES WHERE id={$idCiudad}")->execQuery();
            $resultSet = $this->getResultSet();
            $ciudad = $resultSet[0]['nombre'];
        }
        return (string) $ciudad;
    }
    
    private function _obtenerNombreISR ( $idIsr = 0 )
    {
        $isr = '';
        
        if ( (int) $idIsr > 0 ){
            $this->setSqlQuery("SELECT nombre FROM ISR WHERE id={$idIsr}")->execQuery();
            $resultSet = $this->getResultSet();
            $isr = $resultSet[0]['nombre'];
        }
        return (string) $isr;
    }
    
    private function _obtenerNombreProducto ( $idProducto )
    {
        $producto = '';
        
        if ( (int) $idProducto > 0 ){
            $this->setSqlQuery("SELECT nombre FROM PRODUCTOS WHERE id={$idProducto}")->execQuery();
            $resultSet = $this->getResultSet();
            $producto = $resultSet[0]['nombre'];
        }
        return (string) $producto;
    }
    
    private function _obtenerPuntaje ( $idVendedor )
    {
        $puntaje = '';
        
        if ( (int) $idVendedor > 0 ){
            $this->setSqlQuery("SELECT puntaje FROM PUNTAJE_VENDEDORES WHERE id_vendedor={$idVendedor}")->execQuery();
            $resultSet = $this->getResultSet();
            $puntaje = $resultSet[0]['puntaje'];
        }
        return (string) $puntaje;
    }


    private function _sendEmail( $dataEmail )
    {
        $view = new View( 'partidas_venta.html' );
        $partidas = '';
        
        foreach ( $dataEmail['partidas'] as $partida ) {
            $incluye = ($partida['incluye'] == 0 )? 'NO' : 'SI';
            $producto = $this->_obtenerNombreProducto( $partida['clave_producto'] );
            $data = array (
                'clave_producto' => $producto
                ,'cantidad' => $partida['cantidad']
                ,'incluye' => $incluye
                ,'cantidad_micro' => $partida['cantidad_micro']
                ,'puntos' => $partida['puntos']
            );
            
            $view->setVars( $data );
            
            $partidas .= $view->render( false );
        }
        
        $view->setTemplate( 'registro_venta.html' );
        
        $empresa = $this->_obtenerNombreEmpresa( $dataEmail['empresa'] );
        $estado = $this->_obtenerNombreEstado( $dataEmail['estado'] );
        $ciudad = $this->_obtenerNombreCiudad( $dataEmail['ciudad'] );
        $isr = $this->_obtenerNombreISR( $dataEmail['isr'] );
        $puntaje = $this->_obtenerPuntaje( $_SESSION['user']['vendedor'] );
        
        $data = array(            
            'orden_no' => $dataEmail['orden_no']
            ,'fecha_fact' => $dataEmail['fecha_fact']
            ,'empresa' => $empresa
            ,'estado' => $estado
            ,'ciudad' => $ciudad
            ,'isr' => $isr
            ,'vendedor' => $_SESSION['user']['nombre']
            ,'cliente' => $dataEmail['cliente']
            ,'puntaje_orden' => $dataEmail['puntaje_orden']
            ,'puntaje_actual' => $puntaje
            ,'partidas' => $partidas
        );
        
        
        $view->setVars( $data );
                
        $html = $view->render( false );
        
        $mail = new PHPMailer();
        
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->CharSet 	  = 'utf-8';
        $mail->Host       = 'smtp.emailsrvr.com';  // sets the SMTP server
        $mail->Username   = 'notificaciones@incentivosdell.com.mx';
        $mail->Password   = 'ingeniahosting';
        $mail->Port       = 587; 
        $mail->SetFrom('notificaciones@incentivosdell.com.mx','Notificaciones Incentivos Dell');
        $mail->Subject = "Nuevo Registro de Venta [{$dataEmail['orden_no']}]::Incentivos Dell";
        $mail->Body = $html;
        $mail->IsHTML(true);
        $mail->AddAddress('PartnerDirect_Mexico@Dell.com', 'Karewytt Gonzalez');
        $mail->AddBCC('augusto@ingeniagroup.com.mx', 'Augusto Silva');
        
        if($mail->Send()){
            return true;
        } else {
            return false;
        }
        
    }
    
    
}