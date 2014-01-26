<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=db', 'username', 'password');
    $dbh->exec("SET CHARACTER SET utf8");    
}
catch ( PDOException $e ){
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}