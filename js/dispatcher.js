(function( $, window, undefined ) {

   var _dire    = window.dire, 


	// Use the correct document accordingly with window argument (sandbox)
	document = window.document,
	location = window.location,
	navigator = window.navigator,

    // Map over incDell in case of overwrite
    _dire    = window.dire;

	// Define a local copy of dire
    dire = function() {
        if ( !( this instanceof dire ) ) {

            // The dire object is actually just the init constructor 'enhanced'
            return new dire.fn.init();
        }
        return dire.fn.init();
    };

    //  Object prototyping
    dire.fn = dire.prototype = {
        //  Constructor method
        constructor:    dire, 
        //  Inicializer method 
        init:           function () {
            console.log('Hi');
        }, 
        /**
         *
         *  @function:  !validateLogin
         *  @description:   Makes the validation of the forgot password form
         *  @see:   http://bassistance.de/jquery-plugins/jquery-plugin-validation/ || 
         *          http://docs.jquery.com/Plugins/Validation
         *  @author: @_Chucho_
         *
         */
        //  Validación del formulario de contacto.
        validateLogin:    function ( ) {
            var formActive = $( '#login_form' ).validate( { 
                onfocusout: true,
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
                errorPlacement: function ( error, element ) {
                    error.appendTo( element.parent() );
                },
                //debug: true, 
            	rules: { 
                    login_user: {
            			required: true,
            			minlength: 3
                    },
                    login_password: {
                        required: true, 
            			minlength: 3
                    }, 
                    captcha: {
                        required: true
                    }
            	},
            	messages: {
			    	login_user: "Escriba su nombre de usuario.", 
			    	login_password: "Escriba su contraseña.", 
                    captcha: "Escriba los caracteres de la imagen.", 
            		required: "Campo obligatorio.", 
            		minlength: "Por favor, haga su respuesta más amplia.", 
            		maxlength: "Por favor, acorte su respuesta.", 
            		email: "Escriba un email válido.",
            		number: "Escriba solo números.", 
            		digits: "Escriba solo números."
            	}, 
            	highlight: function ( element, errorClass, validClass ) {
                	$( element ).val( '' );
                	$( element ).addClass( 'error_login' );
            	},
            	unhighlight: function ( element, errorClass ) { 
                	$( element ).siblings( errorClass ).remove();
                	$( element ).removeClass( 'error_login' );
            	}, 
            	submitHandler: function ( form ) {
                	form.submit();
            	}, 
                /*invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                        $("div#message").html(message);
                        $("div#message").show();
                    } else {
                        $("div#message").hide();
                    }
                }*/
            } );
        }, 
        makeDatatable: function ( selector, options ) {

            selector.dataTable( options );
        }
    };
    
    // Give the init function the dire prototype for later instantiation
    dire.fn.init.prototype = dire.fn;
    
    dire = dire.fn;

    // Expose incDell to the global object
    window.dire  = dire;

} )( jQuery, window );

$( document ).ready( function ( e ) {
    
    if ( $( '#login' ).exists( ) ) {

        $( '#login .container' ).centerHeight();
        dire.validateLogin();
    }
    
    if ( $( '#results' ).exists( ) ) {
        
        dire.makeDatatable( $( 'table' ), {
            aoColumnDefs: [ { 
                bSearchable: true, 
                aTargets: [ "_all" ]
            } ], 
            acColumns: [ 
                null, 
                null, 
                null, 
                { sType: "date" }, 
                { sType: "date" }, 
                null, 
                null, 
                null, 
                null
            ], 
            bAutoWidth: false, 
            bDestroy: true, 
            bInfo: false,
            bFilter: true, 
            bLengthChange: false, 
            bPaginate: true, 
            bSort: true, 
            iDisplayLenght: 7, 
            sDom: '<"top"rf>t<"bottom"p>', 
            sPaginationType: "full_numbers", 
            oLanguage: {
                sLengthMenu: "Mostrar _MENU_ registro(s)",
                sZeroRecords: "Lo sentimos, no hay resultados",
                sInfo: "Mostrando: _START_ de _END_ of _TOTAL_ registro(s)",
                sInfoEmpty: "Mostrando 0 a 0 de 0 registro(s)",
                sInfoFiltered: "(Filtrado por _MAX_ registros totales)", 
                sEmptyTable: "No hay datos disponibles en la página", 
                sInfo: "_START_ de _END_", 
                sInfoEmpty: "Sin registros que mostrar",
                sInfoFiltered: " - Filtrando de _MAX_ registros", 
                sLoadingRecords: "Por favor espere - leyendo...", 
                sProcessing: "La tabla se esta procesando", 
                sSearch: "Buscar: _INPUT_", 
                oPaginate: {
                    sFirst:     "Primera Página", 
                    sLast:      "Última Página", 
                    sNext:      "Siguiente", 
                    sPrevious:  "Anterior"
                }
            }
        } );
    }
    
});