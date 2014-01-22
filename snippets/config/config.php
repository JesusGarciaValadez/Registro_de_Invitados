<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors','On');
date_default_timezone_set('America/Mexico_City');
define('BASE_PATH',dirname(dirname((dirname(__FILE__)))) . DIRECTORY_SEPARATOR);    ///Applications/XAMPP/xamppfiles/htdocs/lukajobs/registro_de_invitados/
define('SITE_URL', 'http://localhost/lukajobs/registro_de_invitados/');
define('CODE_PATH', BASE_PATH );
define('SNIPPETS_PATH', CODE_PATH . 'snippets' . DIRECTORY_SEPARATOR);
define('CLASSES_PATH', SNIPPETS_PATH . 'classes'. DIRECTORY_SEPARATOR);
define('LIBS_PATH', CLASSES_PATH . 'libs' . DIRECTORY_SEPARATOR);
define('CHUNKS_PATH', CODE_PATH .'chunks' . DIRECTORY_SEPARATOR );
define('VIEWS_PATH', CHUNKS_PATH . 'email' . DIRECTORY_SEPARATOR );

function _convertUTF8 ( &$item , $keys ){
    $item = utf8_encode($item);
}
?>
