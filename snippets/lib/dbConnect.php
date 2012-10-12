
<?php

class dbConnect {
    private $_host;
    private $_user;
    private $_password;
    private $_dbname;
    private $_link;

    public function __construct( 
		$host		= 'localhost', 
		$user		= 'alfabrad',
		$password	= '_Asukal01_',
		$dbname		= 'queryTestIntranet'
    ) {
          $this->_host      = $host;
          $this->_user      = $user;
          $this->_password  = $password;
          $this->_dbname    = $dbname;
    }

    public function connect(){
         $this->_link = mysql_connect($this->_host, $this->_user, $this->_password) ;
         if(!$this->_link )
             throw new Exception(mysql_error());
         else
             mysql_select_db($this->_dbname,$this->_link);
         
    }

    public function close(){
        if (mysql_close($this->_link)){
            return true;
        }else{
            return false;
        }
    }

    public function executeQuery($query){
        $result = mysql_query($query);
        if(!$result) {
             $mysqlError = mysql_errno($this->_link)." : " . mysql_error($this->_link) ;
             throw new Exception( "Surgió un error al tratar de realizar la consulta '{$query}'.\n {$mysqlError}\n" );
        } else {
            if ( is_resource( $result ) ) {

		$arrayResultSet = array();
		
		if( mysql_num_rows( $result ) > 0 ) {

			while ( $row = mysql_fetch_assoc( $result ) ) {

                              while( list ($key,$value) = each($row) ){
                                  $data [utf8_encode($key)] = utf8_encode($value);
                              }
                              
		              array_push( $arrayResultSet, $data );
		        }

		}

                return (array) $arrayResultSet;

            } else {                
            	return (bool) $result;    
            }
        }
    }

    public function lastInsertedId(){
        return mysql_insert_id($this->_link);
    }
}
?>
