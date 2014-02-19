<?php

class Connection{
    
    private $_instance = null;
    private $_dbHost = '';
    private $_dbUser = '';
    private $_dbPassword = '';
    private $_dbName = '';
    
    protected $_sqlQuery = '';
    protected $_numRows = 0;
    protected $_resource = null;
    
    public function __construct( $host = 'localhost' , $user = 'alfabrad', $password = '_Asukal01_', $name = 'registry_guest' ) {
        $this->_dbHost = $host; 
        $this->_dbUser = $user;
        $this->_dbPassword = $password;
        $this->_dbName = $name;
        $this->_connect();
    }
    
    private function _connect(){
        $dbh = new PDO('mysql:host=localhost;dbname=registry_guest', 'user', 'password');
        $dbh->exec("SET CHARACTER SET utf8");
    }
    
    public static function getInstance(){
        
        if( empty ($this->_instance) ){
            $this->_instance = new self();
        }
        return self::$_instance;
    }
    
    public function setSqlQuery ()
    {
        if(!empty ($sql) && is_string($sql)){
            $this->_sqlQuery = $sql;
        }else{
            exit('$sql no es una consulta valida.');
        }
        return $this;
    }
    
    public function execQuery()
    {
        
    }
    
    private function _parseResults()
    {
        $this->_resultSet = array();
        $statementWords = explode(' ', $this->_sqlQuery);
        
        if ( preg_match( '/SELECT/', strtoupper( $statementWords[0] ) ) ){
            
            $statement = $this->_PDOmySQLConn->prepare($this->_sqlQuery);
            $statement->execute();
            $this->_numRows = $statement->rowCount();
            
            if ( (int) $this->_numRows > 0 ){
                while ( $row = $this->_resource->fetch(PDO::FETCH_ASSOC)){
                    array_push($this->_resultSet,$row);
                }
            }
            
        }else{
            $statement = $this->_PDOmySQLConn->prepare($this->_sqlQuery);
            $statement->execute();
            $this->_numRows = $statement->rowCount();
        }        
    }
    
    public function fetchAll ()
    {
        $this->setSqlQuery("SELECT * FROM {$this->_tableName};")->execQuery();
    }
    
    
}