<?php

/**
 * Interface general para los objetos de tipo Model para el acceso a las 
 * entidades de la Base de Datos.
 * @author      Julio Mora<julio@ingeniagroup.com>
 * @package classes
 * @subpackage libs
 * @category Data Base Access
 * 
 */

abstract class Model{
    
    /**
     * Recurso de conexion PDO MySQL
     * @var resource $_PDOmySQLConn
     * @access private
     */
    
    private $_PDOmySQLConn = null;
    
    /**
     * Nombre de la entidad (tabla o vista)
     * @var String $_tableName
     * @access protected
     */
    
    protected $_tableName = '';
    
    /**
     * Campo(s) que sirven como llave primaria.
     * @var Mixed $_primaryKey
     * @access protected
     */
    
    protected $_primaryKey = '';
    
    /**
     * Consulta a ejecutar.
     * @var String $_sqlQuery
     * @access protected
     */
    
    protected $_sqlQuery = '';
    
    /**
     * Número de registros encontrados en una consulta SQL de tipo SELECT.
     * @var     int $_numRows
     * @access  protected
     */
    
    protected $_numRows = 0 ;
    
    /**
     * Recurso de tipo mysql utilizado en la ejecución de consultas.
     * @var     resource $_resource
     * @access  protected
     */
    
    protected $_resource = null;
    
    /**
     * @var     array $_resultSet
     * @access  protected
     * @desc    Conjunto de Resultados obtenidos en la ejecución de una consulta SQL de tipo SELECT
     */
    
    protected $_resultSet = array();
    
    /**
     * Establece el recurso de Conexión a MySQL a utilizar.
     * @method  setMySQLConn()
     * @static
     * @access  public
     * @param   resource $conn
     * @throws  Exception
     * @see     self::_throwModelException()
     */

    public function setMysqlConn( $conn = NULL ) {
       if ( !self::isValidResource( $conn ) ) {
           exit( '$conn no es objeto PDO valido.' );
       }
       $this->_PDOmySQLConn = $conn;
    }
    
    /**
     * Establece el nombre de la tabla o vista a utilizar.
     * @method  setTableName()
     * @access  public
     * @param   String $tableName
     * @throws  Exception
     * @see     self::_throwModelException()
     */
    
    public function setTableName ( $tableName ) {
        if ( !empty ( $tableName ) && is_string( $tableName ) ) {
            $this->_tableName = $tableName;
        } else {
            exit( '$tableName no es un nombre de tabla o vista valido.' );
        }
    }
    
    /**
     * Obtiene el nombre de la tabla o vista que se está utilizando actualmente.
     * @method  getTableName()
     * @access  public
     * @return  String    
     */
    
    public function getTableName( ) {
        return (string) $this->_tableName;
    }
    
    /**
     * @method  setPrimaryKey()
     * @access  public
     * @param   String|array $primaryKey
     * @throws  Exception
     * @desc    Establece el o los campos que se utilizarán como lleve primaria.
     * @see     self::_throwModelException()
     */
    
    public function setPrimaryKey ( $primaryKey = '' ) {
        if ( !empty ( $primaryKey ) && ( is_string( $primaryKey ) || is_array( $primaryKey ) ) ) {
            $this->_primaryKey = $primaryKey;
        } else {
            exit( '$primaryKey no es un nombre de llave valido.' );
        }
    }
    
    /**
     * @method  getPrimaryKey()
     * @access  public
     * @return  String|array
     * @desc    Obtiene el nombre de los campos que se utilizan como llave primaria.
     */
    
    public function getPrimaryKey( ) {
        return $this->_primaryKey;
    }
    
    /**
     * Establece la consulta SQL que se ejecutará.
     * @method  setSqlQuery()
     * @access  public
     * @param   String $sql
     * @return  Base
     * @throws  Exception    
     * @see     self::_throwModelException()
     */
    
    public function setSqlQuery ( $sql  ='' ) {
        if ( !empty ( $sql ) && is_string( $sql ) ) {
            $this->_sqlQuery    = $sql;
        } else {
            exit( '$sql no es una consulta valida.' );
        }
        return $this;            
    }

    /**
     * Obtiene la consulta SQL que se ejecutará o que ya fue previamente ejecutada.
     * @method  getSqlQuery()
     * @access  public
     * @return  String
     */
    
    public function getSqlQuery( ) {
        return ( string ) $this->_sqlQuery;
    }
    
    /**
     * Obtiene el conjunto de resultados de las consultas SQL de tipo SELECT.
     * @method  getResultSet()
     * @access  public
     * @return  array
     */
    
    public function getResultSet( ) {
        return ( array ) $this->_resultSet;
    }
    
    /**
     * Obtiene el número de registros encontrados en las consultas SQL de tipo SQL.
     * @method  getNumRows()
     * @access  public
     * @return  int
     */
    
    public function getNumRows( ) {
        return ( int ) $this->_numRows;
    }

    /**
     * Ejecuta la consulta SQL expresada en la variable _sqlQuery.
     * @method  execQuery()
     * @access  public
     * @return  resorce|boolean|null
     * @throws  Exception    
     * @see     self::_throwModelException(), self::isValidConnResource(), self::_parseResults()
     */
    
    public function execQuery( ) {
        if ( !empty ( $this->_sqlQuery ) && !is_string( $this->_sqlQuery ) ) {
            exit( '$sql no es una consulta valida.' );
        }
        
        $this->_resource = $this->_PDOmySQLConn->query( $this->_sqlQuery );
        
        $this->_parseResults( );
    }
    
    /**
     * @method  _parseResults()
     * @access  private
     * @throws  Exception
     * @desc    Parsea los resultados obtenidos en cualquier ejecución de consultas SQL.
     * @see     self::_throwModelException()
     */
    
    private function _parseResults( ) {
        $this->_resultSet = array( );
        $statementWords = explode( ' ', $this->_sqlQuery );
        
        if ( preg_match( '/SELECT/', strtoupper( $statementWords[ 0 ] ) ) ) {
            
            $statement = $this->_PDOmySQLConn->prepare( $this->_sqlQuery );
            $statement->execute();
            $this->_numRows = $statement->rowCount( );
            
            if ( ( int ) $this->_numRows > 0 ) {
                while ( $row = $this->_resource->fetch( PDO::FETCH_ASSOC ) ) {
                    array_push( $this->_resultSet, $row );
                }
            }
            
        }else{
            $this->_numRows = $this->_resource->rowCount( );            
        }        
    }
    
    /**
     * Obtiene todos los registros de la Tabla o Vista que se está utilizando.
     * @method fetchAll()
     * @access public
     * @return array
     */   
    
    public function fetchAll( ) {
        $this->setSqlQuery( "SELECT * FROM {$this->_tableName};" )->execQuery( );
        return $this->_resultSet;
    }
    
    /**
     * Solicitar un conjunto de datos que cumplan con los filtros.
     * @method select()
     * @var mixed $fields , mixed $conditions
     * @access public
     * @return mixed $resultSet
     */
    
    public function select ( array $fields = array( ), array $conditions = array( ) ) {
        $fields = ( !empty( $fields ) ) ? implode( ', ', $fields ) : '*';
        $where = ( isset( $conditions['where'] ) ) ? " WHERE {$conditions['where']}" : '';
        $order = ( isset( $conditions['order'] ) ) ? " ORDER BY {$conditions['order']} {$conditions['orderSense']}" : '';
        $limit = ( isset( $conditions['limit'] ) ) ? " LIMIT {$conditions['limit']}" : '';
        
        $this->setSqlQuery( "SELECT {$fields} FROM {$this->_tableName}{$where}{$order}{$limit};" )->execQuery();
        
        return $this->getResultSet();
    }
    
    
    public function insert ( array $data ) {
        self::prepareDataToSave( $data );
        
        $lastInsertId = 0;
        
        $fields = implode( ', ', array_keys($data) );
        $values = implode( ', ', $data );
        
        $this->setSqlQuery("INSERT INTO {$this->_tableName} ({$fields}) VALUES ( $values );")->execQuery();
        $lastInsertId = ( $this->getNumRows() )? $this->_PDOmySQLConn->lastInsertId() : 0;
           
        return $lastInsertId;
    }

        /**
     * Edita los datos del registro que cumpla las condiciones establecidas.
     * @method update
     * @access public
     * @param array $data
     * @param type $where
     * @return boolean
     */
    
    public function update ( array $data , $where   = '' ) {
        self::prepareDataToSave( $data );
        
        if ( isset( $this->_primarykey ) && empty( $where ) ) {
            
            $id     = ( int ) $data[ $this->_primaryKey ];
        }
        $where  = ( !empty ( $where ) ) ? ( string ) $where : "{$this->_primaryKey}={$id}";
        $fields = array( );
        
        foreach ( $data as $field => $value ) {
            if( $field != $this->_primaryKey ) {
                array_push( $fields, "`{$field}` = {$value}" );
            }
        }
        
        $fields = implode( ', ', $fields );
        
        return ( $this->setSqlQuery( "UPDATE {$this->_tableName} SET {$fields} WHERE {$where};" )->execQuery( ) ) ? true : false;
    }
    
    /**
     * Buscar un registro apartir de si identificador
     * @method find
     * @access public
     * @param int $id
     * @return array
     */
    
    public function find ( $id  = 0 ) {
        $id = (int) $id;
        
        $this->setSqlQuery( "SELECT * FROM {$this->_tableName} WHERE {$this->_primaryKey} = {$id};" )->execQuery();
        
        return $this->getResultSet();
    }


    /**
     * @method  isValidResource()
     * @static
     * @access  public
     * @param   mixed $resource
     * @return  boolean
     * @desc    Verifica que el parámetro de entrada representa un recurso.
     */
    
    public static function isValidResource( $resource    = NULL ) {
        return ( !empty ($resource) && is_object($resource));
    }
    
    /**
     * Prepara por referencia la información a ser salvada de
     * acuerdo al tipo de dato almacenado en cada elemento del
     * arreglo recibido.
     * @method prepareDataToSave
     * @access public
     * @static
     * @param array $data 
     */
    
    public static function prepareDataToSave ( array &$data ) {
        foreach ( $data as $key => $value ) {
            $data[ $key ] = ( is_numeric( $value ) ) ? $value : "'{$value}'";
        }
    }
    
    
}