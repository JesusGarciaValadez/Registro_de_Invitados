<?php

/**
 * Class        View
 * @author      Julio Mora<julio@ingeniagroup.com>
 * @version     2.3
 * @category    View
 * @desc        Object representing the V from MVC pattern.
 */    
    class View
    {//------------------------------------------------------------------>> Class View
        
        /**
         * @var     String $_viewFilesRepository
         * @static
         * @access  private
         * @desc    Default Directory where the template file are located.
         */
        private static $_viewFilesRepository = '';
        
        /**
         * @var     String $_viewType
         * @access  private 
         * @desc    View Type (file extension).
         */
        private $_viewType = '';
        
        /**
         * @var     String $_viewFilePath
         * @access  private
         * @desc    Path where the file to use as templete is located. 
         */
        private $_viewFilePath = '';
        
        /**
         * @var     array $_vars
         * @access  private 
         * @desc    Assosiative Array (pair->value) containing the value of the variables
         *          to be remplaced on the templete.
         */ 
        private $_vars = array();
        
        /**
         * @var     String $_template
         * @access  private
         * @desc    Original Templete String.
         */
        private $_template = '';
        
        /**
         * @var     String $_view
         * @access  private 
         * @desc    Resulting string after the variable sustitution on $_template propertie.
         */ 
        private $_view = '';
        
        /**
         * @method	__construct()
         * @access	public
         * @param   String $filePath -> Path where the file to use as templete is located.
         * @desc	Object's Constructor.
         *          Tries to set the file to be used as template if the param $filePath is 
         *          not empty.
         * @see     self::setTemplate()
         */
        public function __construct( $filePath = '' ) 
        {//---------------------------------------->> __construct()
            
            if( !empty( $filePath ) ) {//-------------------->> if not empty param
                $this->setTemplate( $filePath );
            }//-------------------->> End if not empty param
            
        }//---------------------------------------->> End __construct()
        
        /**
         * @method  setViewFilesRepository()
         * @static
         * @access  public
         * @param   String $dirPath
         * @throws  Exception
         * @desc    Sets a directory path as the template files repository.
         */
        public static function setViewFilesRepository( $dirPath = '' )
        {//---------------------------------------->> setViewFilesRepository()
            
            if( !is_dir( $dirPath ) ) {//-------------------->> if param is not a dir
                self::_throwViewException( "Directory {$dirPath} not found" );
            }//-------------------->> End if param is not a dir
            
            self::$_viewFilesRepository = $dirPath;
            
        }//---------------------------------------->> End setViewFilesRepository()
        
        /**
         * @method  setTemplate()
         * @access  public
         * @param   String $filePath -> Path where the file to use as templete is located.
         * @return  View
         * @throws  Exception
         * @desc    Method to ensure that the file expressed in $filePath exist and set
         *          the object's properties to be used in template's variable sustitution.
         * @see     self::getFileContent(), self::_throwViewException()
         */
        public function setTemplate( $filePath = '' )
        {//---------------------------------------->> setTemplate()
            
            if( !empty( $filePath ) && is_string( $filePath ) ) {//-------------------->> if not empty param
                
                $filePath = self::$_viewFilesRepository . $filePath;
                
                if( file_exists( $filePath ) ) {//-------------------->> if file exist
                    
                    $this->_viewFilePath = $filePath;
                    $this->_viewType = strtolower( substr( strrchr( $this->_viewFilePath, "." ), 1 ) );
                    $this->_template = preg_replace( '#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", 
                                                     str_replace ( "'", "\'", self::getFileContent( $this->_viewFilePath ) ) );
                    
                } else {//-------------------->> else if file doesn't exist
                    self::_throwViewException( "The file {$filePath} doesn't exist" );
                }//-------------------->> End if file exist
                
            } else {//-------------------->> else if empty param
                self::_throwViewException( 'Please set the File Path to use as a non empty String' );
            }//-------------------->> End if not empty param
            
            return $this;
            
        }//---------------------------------------->> End setTemplate()
        
        /**
         * @method	setVars()
         * @access  public
         * @param   array $vars -> Assosiative Array (pair->value) containing the value of the variables to be replaced on template's variables.
         * @return	View
         * @desc    Set the value of the variables to be remplaced on template's variables.
         * @see		self::_throwViewException()
         */
        public function setVars( array $vars )
        {//---------------------------------------->> setVars()
            
            if( empty( $vars ) ) {//-------------------->> if empty param
                self::_throwViewException( 'Set the variables as a non empty assosiative array' );
            }//-------------------->> End if empty param
            
            $this->_vars = ( empty( $this->_vars ) ) ? $vars : array_merge( (array) $this->_vars, $vars );
            
            return $this;
            
        }//---------------------------------------->> End setVars()
        
        
        /**
         * @method	buildView()
         * @access  public
         * @return	View
         * @desc    Make the var sustitution according to the _vars property content 
         *          on _template property to get the new _view value.
         * @see     self::_throwViewException()
         */
        public function buildView()
        {//---------------------------------------->> buildView()
            
            if( empty( $this->_template ) ) {//-------------------->> if empty _template
                self::_throwViewException( 'Please set the path from a non empty file before to render the resulting View' );
            }//-------------------->> End if empty _template
            
            $this->_view = $this->_template;
            
            reset( $this->_vars );
            foreach( (array) $this->_vars as $key => $val ) {//-------------------->> foreach _vars
                $$key = $val;
            }//-------------------->> End foreach _vars
            
            eval( "\$this->_view = '$this->_view';" );
            
            reset( $this->_vars );
            foreach( (array) $this->_vars as $key => $val ) {//-------------------->> foreach _vars
                unset( $$key );
            }//-------------------->> End foreach _vars

            $this->_view = str_replace( "\'", "'", $this->_view );
            
            return $this;
            
        }//---------------------------------------->> End buildView()
        
        /**
         * @method	getView()
         * @access  public
         * @param	boolean $render -> Send view to out buffer?
         * @desc    Return the _view property value.
         *          If $render is a logic true, the value will be sent to the out
         *          buffer.
         * @return	String
         * @see		self::compressHTML()
         */
        public function getView( $render = false ) 
        {//---------------------------------------->> getView()
            
            if( $this->_viewType === 'html' ) {//-------------------->> if is html
                //$this->_view = self::compressHTML( $this->_view );
            }//-------------------->> End if is html
            
            if ( (bool) $render === true ) {//-------------------->> if render
                echo $this->_view;
            }//-------------------->> End if render
            
            return $this->_view;
            
        }//---------------------------------------->> End getView()
        
        /**
         * @method  render()
         * @access  public
         * @param   boolean $display -> Flag to define if the resulting _view
         *          will be sent to out buffer.
         * @return  String
         * @desc    Make the vars sustitution on template's format and get the 
         *          resulting string.
         * @see     self::buildView(), self::getView()
         */
        public function render( $display = false )
        {//---------------------------------------->> render()
            return $this->buildView()->getView( $display );
        }//---------------------------------------->> End render()
        
        /**
         * @method	 compressHTML()
         * @static
         * @access  public
         * @param   String $buffer -> (X)HTML string to be compressed.
         * @return  String
         * @desc    Removes comentaries, tabs and return chars on (X)HTML strings.
         */
        public static function compressHTML( $buffer = '' )
        {//---------------------------------------->> compressHTML()
            
            $buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
            $buffer = preg_replace( '(<!-- (.*)-->)', '', $buffer );
            $buffer = str_replace( array( "\r\n", "\r", "\n", "\t", '    ', '     ', '      '), '', $buffer );
            
            return $buffer;
            
        }//---------------------------------------->> End compressHTML()
        
        /**
         * @method	getFileContent()
         * @static
         * @access  public
         * @param   $filePath -> Path where to file to search is located.
         * @return	String
         * @desc    Obtains the file content as a string.
         * @see		self::_throwViewException()
         */
        public static function getFileContent( $filePath = '' ) 
        {//---------------------------------------->> getFileContent()
        	
        	$fileContent = '';
        	
            if( empty( $filePath ) && is_string( $filePath ) ) {//-------------------->> if empty param
                self::_throwViewException( 'Set the path from the file to search as a non empty string' );
            }//-------------------->> End if empty param
            
            if( file_exists( $filePath ) ) {//-------------------->> if file exist
                $fileContent = file_get_contents( $filePath, true );
            } else {//-------------------->> else if file dosen't exist
                self::_throwViewException( "The file {$filePath} was not found" );
            }//-------------------->> End if file exist
        	
            return $fileContent;
            
        }//---------------------------------------->> End getFileContent()
        
        /**
         * @method	_throwViewException()
         * @static
         * @access  private
         * @param	String $message
         * @throws	Exception
         * @desc    Throws an Exception describing a View process error.
         */ 
        private static function _throwViewException( $message = '' ) 
        {//---------------------------------------->> _throwViewException()
            throw new Exception( "View Exception: {$message}" );
        }//---------------------------------------->> End _throwViewException()
        
        
    }//------------------------------------------------------------------>> End Class View