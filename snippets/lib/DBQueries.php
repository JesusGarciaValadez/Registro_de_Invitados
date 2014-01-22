<?php

require_once 'dbConnect.php';

class DBQueries {
    private $_connection ;
    
    public function  __construct() {
       $this->_connection = new dbConnect();
       $this->_connection->connect();
    }

    public function  __destruct() {
        $this->_connection->close();
    }

    public function executeQuery( $query ){
        return $this->_connection->executeQuery( $query );
    }

    public function insertRow( $query ){
         return $this->_connection->executeQuery( $query );
    }

    protected function updateRow( $query ){
         return $this->executeQuery( $query );
    }

    public function getRows( $query ){
        return $this->_connection->executeQuery( $query );
    }

    public function getRow( $query ){
        $result = $this->getRows( $query );
        return !empty( $result ) ? $result[0] : $result ;
    }

    protected function lastInsertedId(){
        return $this->_connection->lastInsertedId();
    }

}
?>
