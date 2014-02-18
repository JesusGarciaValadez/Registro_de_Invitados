/**
 *
 *  @function
 *  @description:   Anonimous function autoexecutable
 *  @params jQuery $.- An jQuery object instance
 *  @params window window.- A Window object Instance
 *  @author: @_Chucho_
 *
 */
(function ( $, window, document, undefined ) {
    
    var _Registry   = window.Registry,
   
    // Use the correct document accordingly with window argument (sandbox)
    document        = window.document,
    location        = window.location,
    navigator       = window.navigator,
    
    // Map over Registry in case of overwrite
    _Registry       = window.Registry;
    
    // Define a local copy of Registry
    Registry    = function() {
        
        if ( !( this instanceof Registry ) ) {
            
            // The Registry object is actually just the init constructor 'enhanced'
            return new Registry.fn.init();
        }
        return Registry.fn.init();
    };
    
    //  Object prototyping
    Registry.fn = Registry.prototype = {
        //  Constructor method
        constructor:    Registry,
        //  Inicializer method 
        init:           function () {
            console.log('Hi');
        },
        //  Validación del formulario de contacto.
        /**
         *
         *  @function:  !validateForm
         *  @description:   Makes the validation of the contact form
         *  @see:   http://bassistance.de/jquery-plugins/jquery-plugin-validation/ || 
         *          http://docs.jquery.com/Plugins/Validation
                    http://jqueryvalidation.org/documentation/
         *  @author: @_Chucho_
         *
         */
        //  !Validación del formulario de contacto.
        validateFormWOAjax:    function ( rule, messages ) {
            
            var _rule               = ( typeof( rule ) == 'object' ) ? rule : {};
            var _message            = ( typeof( messages ) == 'object' ) ? messages : {};
            var _beforeSubmitFunc   = ( typeof( beforeSubmitFunc ) == undefined && typeof( beforeSubmitFunc ) == 'undefined' && typeof( beforeSubmitFunc ) == 'null' ) ?  $.noop : ( ( typeof( beforeSubmitFunc ) == 'function' ) ? beforeSubmitFunc : $.noop );
            
            var formActive = $( 'form' ).validate( { 
                onfocusout: false,
                onclick: true, 
                onkeyup: false,
                onsubmit: true, 
                focusCleanup: true, 
                focusInvalid: false, 
                errorClass: "error", 
                validClass: "valid", 
                errorElement: "label", 
                ignore: "", 
                /*showErrors: function( errorMap, errorList ) {
                    $('#message').empty().removeClass();
                    $("#message").html('<p>Error al ingresar la información.</p><p>Verifique que sus datos están correctos o que no falte ningún dato.</p><p>Por favor, vuelvalo a intentar.</p>');
                    $('#message').addClass('wrong').show('fast', function(){
                        $('#message').show('fast');
                    });
                    this.defaultShowErrors();
                },*/
                errorPlacement: function(error, element) {
                    error.appendTo( element.parent() );
                },
                //debug:true, 
                rules: _rule,
                messages: _message, 
                ignore: 'textarea', 
                highlight: function( element, errorClass, validClass ) {
                    $( element ).parent().addClass( 'error_wrapper' );
                },
                unhighlight: function( element, errorClass ) {
                    $( element ).parent().removeClass( 'error_wrapper' );
                }, 
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                        $("div#summary").html(message);
                        $("div#summary").show();
                    } else {
                        $("div#summary").hide();
                    }
                }
            } ); 
        }, 
        //  Validación del formulario de contacto.
        /**
         *
         *  @function:  !validateForm
         *  @description:   Makes the validation of the contact form
         *  @see:   http://bassistance.de/jquery-plugins/jquery-plugin-validation/ || 
         *          http://docs.jquery.com/Plugins/Validation
                    http://jqueryvalidation.org/documentation/
         *  @author: @_Chucho_
         *
         */
        //  !Validación del formulario de contacto.
        validateFormAjax:    function ( rule, messages ) {
            
            var _rule               = ( typeof( rule ) == 'object' ) ? rule : {};
            var _message            = ( typeof( messages ) == 'object' ) ? messages : {};
            var _beforeSubmitFunc   = ( typeof( beforeSubmitFunc ) == undefined && typeof( beforeSubmitFunc ) == 'undefined' && typeof( beforeSubmitFunc ) == 'null' ) ?  $.noop : ( ( typeof( beforeSubmitFunc ) == 'function' ) ? beforeSubmitFunc : $.noop );
            
            var formActive = $( 'form' ).validate( { 
                onfocusout: false,
                onclick: true, 
                onkeyup: false,
                onsubmit: true, 
                focusCleanup: true, 
                focusInvalid: false, 
                errorClass: "error", 
                validClass: "valid", 
                errorElement: "label", 
                ignore: "", 
                /*showErrors: function( errorMap, errorList ) {
                    $('#message').empty().removeClass();
                    $("#message").html('<p>Error al ingresar la información.</p><p>Verifique que sus datos están correctos o que no falte ningún dato.</p><p>Por favor, vuelvalo a intentar.</p>');
                    $('#message').addClass('wrong').show('fast', function(){
                        $('#message').show('fast');
                    });
                    this.defaultShowErrors();
                },*/
                errorPlacement: function(error, element) {
                    error.appendTo( element.parent() );
                },
                //debug:true, 
                rules: _rule,
                messages: _message, 
                ignore: 'textarea', 
                highlight: function( element, errorClass, validClass ) {
                    $( element ).parent().addClass( 'error_wrapper' );
                },
                unhighlight: function( element, errorClass ) {
                    $( element ).parent().removeClass( 'error_wrapper' );
                },
                submitHandler: function( form ) {
                    // Form submit
                    $( form ).ajaxSubmit( {
                        //    Before submitting the form
                        beforeSubmit: function showRequestLogin( arr, form, options ) {
                            
                            _beforeSubmitFunc();
                        },
                        //  !Function for handle data from server
                        success: function showResponseLogin( responseText, statusText, xhr, form ) {
                            
                            //console.log(responseText.success);
                            responseText    = $.parseJSON( responseText );
                            
                            if( responseText && ( responseText.success == 'true' || responseText.success == true ) ) {
                                
                                var regularExp      = new RegExp( '(Â|Ê|Î|Ô|Û)|(â|ê|î|ô|û)', 'gim' );
                                var response        = String( responseText.message ).replace( regularExp, ' ' );
                                var confirmation    = confirm( response );
                                
                                $( '#user_edit_submit' ).attr( 'disabled', 'disable' );
                                
                                if ( confirmation ) {
                                    
                                    window.location = "search.html";
                                }
                            } else {
                                
                                alert( responseText.message );
                            }
                        },
                        resetForm: false,
                        clearForm: false,
                        //   If something is wrong
                        error: function( jqXHR, textStatus, errorThrown ) {
                            //console.log(textStatus);
                            //console.log(errorThrown);
                        },
                        cache: false
                    } );
                },
                invalidHandler: function( form, validator ) {
                    
                    var errors  = validator.numberOfInvalids();
                    if ( errors ) {
                        var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                        $( "div#summary" ).html( message );
                        $( "div#summary" ).show();
                    } else {
                        $( "div#summary" ).hide();
                    }
                }
            } );
        },
        /**
         *
         *  @function:  !toggleValue
         *  @description:   Does toggle if the input have a value or if doesn't
         *  @params jQuery selector.- A jQuery Selector 
         *  @params String valueChange.- A String with the value to change or preserve
         *  @author: @_Chucho_
         *
         */
        //  !Revisa si el valor de un input es el original o no y lo preserva o 
        //  respeta el nuevo valor.
        toggleValue:    function ( selector, valueChange ) {
            
            _selector       = ( typeof( selector ) == "undefined" ) ? "*" : selector;
            _selector       = ( typeof( _selector ) == "object" ) ? _selector : $( _selector );
            
            _valueChange  = ( valueChange == "" ) ? 400 : valueChange;
            _valueChange  = ( typeof( _valueChange ) == "string" ) ? parseInt( _valueChange ) : _valueChange;
            _valueChange  = ( typeof( _valueChange ) != "number" ) ? parseInt( _valueChange ) : _valueChange;
            
            var _placeholder;
            
            if ( _selector.attr( 'placeholder' ) != undefined ) {
                
                _placeholder = String ( _selector.attr( 'placeholder' ) ).toLowerCase();
            } else {
                
                _placeholder = String ( _selector.val( ) ).toLowerCase();
            }
            
            if ( ( _placeholder == "" ) || ( _placeholder == _valueChange ) ) {
                
                _selector.css( { 
                    color: '#aaa'
                } );
            }
            
            _selector.on( {
                blur: function ( e ) {
                    
                    _comment = String( $( e.currentTarget ).val() ).toLowerCase();
                    if ( ( _comment == _placeholder ) || ( _comment == "" ) ) {
                        
                        $( e.currentTarget ).val( valueChange ).css( {
                            color: '#aaa'
                        } );
                        return false;
                    }
                },
                focus: function ( e ) {
                    
                    _comment = String( $( e.currentTarget ).val() ).toLowerCase();
                    if ( _comment == _placeholder ) {
                        
                        $( e.currentTarget ).val( '' ).css( { color: '#666' } );
                        return false;
                    }
                }
            } );
        }, 
        /**
         *
         *  @function:  !toggleClass
         *  @description:   Toggle an HTML class
         *  @params jQuery selector.- A jQuery Selector 
         *  @params String className.- A class to toggle to the target
         *  @author: @_Chucho_
         *
         */
        //  !Hace toggle de una clase a un selector específico
        toggleClass: function ( selector, className ) {
            
            _selector       = ( typeof( selector )  == "undefined" ) ? "*" : selector;
            _selector       = ( typeof( _selector ) == "object" )    ? _selector : $( _selector );
            _class          = ( typeof( className ) == "undefined" ) ? ".active" : className;
            _class          = ( typeof( _class )    == "string" )    ? _class : String( _class );
            
            if ( selector.exists() ) {
                
                _selector.toggleClass( _class );
            }
        }, 
    };
    
    // Give the init function the Registry prototype for later instantiation
    Registry.fn.init.prototype = Registry.fn;
    
    Registry = Registry.fn;
    
    // Expose Registry to the global object
    window.Registry  = Registry;
    
    $( function () {
        
        if ( $( '#search' ).exists() ) {
            
            Registry.toggleValue( $( '#mail' ), "hola@gmail.com" );
            
            var rules       = { 
                    mail :   {
                        required    :   true,
                        email       :   true
                    }
                };
            var messages    = {
                    mail        :   "Por favor, introduce una dirección de correo", 
                    required    :   "Por favor, selecciona una opción", 
                    minlength   :   "Por favor, haga su respuesta más amplia.", 
                    maxlength   :   "Por favor, acorte su respuesta", 
                    email       :   "Escriba un email válido",
                    number      :   "Escriba solo números", 
                    digits      :   "Escriba solo números"
                };
            
            Registry.validateFormWOAjax( rules, messages );
            
            /**
             *  Function to detect the id of document by param and set current element on menu
             *
             */ 
            var Url = String( location.href );
            if ( Url.search( /search.html/i ) ) {
                
                Url         = Url.replace( /.*\?(.*?)/, "$1" );
                Variables   = Url.split ("&");
                for ( i = 0; i < Variables.length; i++ ) {
                    
                    Separ   = Variables[ i ].split( "=" );
                        
                    if ( Separ[ 1 ] != undefined && Separ[ 1 ] != null ) {
                        
                        eval ( 'var ' + Separ[ 0 ] + '="' + Separ[ 1 ] + '"' );
                    }
                }
            }
            
            if ( response ) {
                
                switch( String( response ) ) {
                    
                    case 'no-editable': alert( 'El usuario no puede ser editado' );
                        break:
                    default:
                        break;
                }
            }
        }
        
        if ( $( '#create' ).exists() || $( '#edit' ).exists() ) {
            
            if ( $( '#user_edit_mail' ).exists( ) )
                Registry.toggleValue( $( '#user_edit_mail' ), "Correo" );
            
            Registry.toggleValue( $( '#user_edit_first_name' ), "Apellido Materno" );
            Registry.toggleValue( $( '#user_edit_last_name' ), "Apellido Paterno" );
            Registry.toggleValue( $( '#user_edit_name' ), "Nombre" );
            Registry.toggleValue( $( '#user_edit_job' ), "Cargo" );
            Registry.toggleValue( $( '#user_edit_where' ), "Institución y/o Empresa" );
            Registry.toggleValue( $( '#user_edit_lada' ), "Lada" );
            Registry.toggleValue( $( '#user_edit_phone' ), "Teléfono" );
            Registry.toggleValue( $( '#user_edit_ext' ), "Extensión" );
            Registry.toggleValue( $( '#user_edit_dependency' ), "Dependencia" );
            Registry.toggleValue( $( '#user_edit_city ' ), "Ciudad" );
            
            var rules       = { 
                    user_edit_first_name    :   { required  : true },
                    user_edit_last_name     :   { required  : true },
                    user_edit_name          :   { required  : true },
                    user_edit_job           :   { required  : true },
                    user_edit_where         :   { required  : true },
                    user_edit_lada          :   { 
                        required    : true, 
                        number      : true, 
                        digits      : true, 
                        minlength   : 2, 
                        maxlength   : 3
                    },
                    user_edit_phone         :   { 
                        required  : true, 
                        number      : true, 
                        digits      : true, 
                        minlength   : 5, 
                        maxlength   : 8
                    },
                    user_edit_ext           :   { 
                        required  : false, 
                        number      : true, 
                        digits      : true, 
                        minlength   : 2, 
                        maxlength   : 5
                    },
                    user_edit_dependency    :   { required  : true },
                    user_edit_title         :   { required  : true },
                    user_edit_state         :   { required  : true },
                    user_edit_city          :   { required  : true },
                };
                
            if ( $( '#user_edit_mail' ).exists( ) ) {
                
                rules.user_edit_mail        = {
                        required    :   true,
                        email       :   true
                    };
            }
            
            var messages    = {
                    user_edit_first_name    :   "Por favor, escribe tu apellido paterno",
                    user_edit_last_name     :   "Por favor, escribe tu apellido materno",
                    user_edit_name          :   "Por favor, escribe tu nombre",
                    user_edit_job           :   "Por favor, escribe tu cargo",
                    user_edit_where         :   "Por favor, escribe tu que empresa vienes",
                    user_edit_lada          :   "Por favor, escribe tu lada",
                    user_edit_phone         :   "Por favor, escribe tu teléfono",
                    user_edit_ext           :   "Por favor, escribe tu extensión",
                    user_edit_dependency    :   "Por favor, escribe la dependencia de la que vienes",
                    user_edit_title         :   "Por favor, escribe un título", 
                    user_edit_state         :   "Por favor, selecciona un estado",
                    user_edit_city          :   "Por favor, escribe tu ciudad",
                    required                :   "Por favor, selecciona una opción", 
                    minlength               :   "Por favor, haga su respuesta más amplia.", 
                    maxlength               :   "Por favor, acorte su respuesta", 
                    email                   :   "Escriba un email válido",
                    number                  :   "Escriba solo números", 
                    digits                  :   "Escriba solo números"
                };
            
            if ( $( '#user_edit_mail' ).exists( ) )
                messages.user_edit_mail     =   "Por favor, escribe una dirección de correo";
            
            Registry.validateFormAjax( rules, messages );
        }
    } );
    
    $( document ).ready( function ( e ) {
        
        if ( $( '#search' ).exists() ) {
            
            if ( $( 'form' ).attr( 'novalidate' ) == 'novalidate' ) {
                
                $( 'form' ).removeAttr( 'novalidate' );
            }
        }
    });

} )( jQuery, window, document );
