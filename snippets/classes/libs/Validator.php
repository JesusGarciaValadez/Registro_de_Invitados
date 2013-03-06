<?php
class Validator {
    
    private $_data;
    private $_parameters;
    private $_messageError;
    private $_pattern = array(
        'esNumerico' =>'/^[0-9\.,]*$/',
        'esAlfa' => '/^[a-zA-Z áéíóúñÑÁÉÍÓÚ,\.¿¡!\?]*$/',
        'esAlfaNumerico' => '/^[a-zA-Z0-9 áéíóúñÑÁÉÍÓÚ,\.¿¡!\?:{}()-=]*$/',
        'esEmail' => '/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/',
        'esURL' => '/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/',
        'esFecha' => '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/',
        'password' => '/^[a-zA-Z0-9 ,\.¿¡!\?:-_*+ ,-]*$/',
        'serialize' => '/^([a-zA-Z0-9 áéíóúñÑÁÉÍÓÚ,\.¿¡!\?:{}()-=]|^a:\d+:{.*?}$)*$/'
    );
        
    public function __construct( $data , $parameters )
    {
        $this->_parameters = $parameters;
        foreach ($data as $key => $value) {
            if ( !is_array($value) )
                $this->_data[$key] = strip_tags(trim($value));
        }
        
    }
    
    private function _patterns( $data , $pattern)
    {        
        return preg_match($this->_pattern[$pattern], $data);
    }
    
    private function _isRequire( $toEval )
    {
        if ( !$toEval || trim($toEval) == '' ){
            return false;
        }else {
            return true;
        }
    }
    
    private function _setMessage($message)
    {
        $this->_messageError = $message;
    }


    public function validate()
    {
        foreach ($this->_parameters as $key => $data) {
            
            if ( $data['requerido'] == 1 ){                
                $isRequire = $this->_isRequire($this->_data[$key]);
            }else{
                $isRequire = true;
            }
            
            $isPattern = $this->_patterns($this->_data[$key], $data['validador']);
            
            if ( $isRequire && $isPattern ){
                $isValid = true;
            }else{
                $this->_setMessage($data['mensaje']);
                $isValid = false;
                break;
            }            
        }
        return $isValid;
    }
    
    public function getMessage()
    {
        return $this->_messageError;
    }
    
}
