<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=registry_guest', 'user', 'password');
    $dbh->exec("SET CHARACTER SET utf8");    
}
catch ( PDOException $e ){
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}