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
        validateEdit:    function ( ) {
            var formActive = $( '#edit_user_form' ).validate( { 
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
                    error.prependTo( element.parent() );
                },
                //debug: true, 
                rules: { 
                    user_edit_name: {
                        minlength: 3
                    }, 
                    user_edit_first_name: {
                        minlength: 3
                    }, 
                    user_edit_last_name: {
                        minlength: 3
                    }, 
                    user_edit_email: {
                        minlength: 3, 
                        email: true 
                    }, 
                    user_edit_gender: {
                        number: true, 
                        digits: true 
                    }, 
                    user_edit_age: {
                        number: true, 
                        digits: true, 
                        maxlength: 2
                    }, 
                    user_edit_district: {
                        number: true, 
                        digits: true 
                    }, 
                    user_edit_estate: {
                        number: true, 
                        digits: true 
                    }, 
                    user_edit_election: {
                        number: true, 
                        digits: true 
                    }, 
                    user_edit_ocupation: {
                    }, 
                    user_edit_schooling: {

                    }, 
                    user_edit_substitute: {

                    }, 
                    edit_area_input: {
                        accept: "image/*", 
                        extension: "png|jpe?g|gif"
                    }, 
                    edit_photo_file: {
                        accept: "image/*", 
                        extension: "png|jpe?g|gif"
                    }, 
                    comission: {
                        number: true, 
                        digits: true 
                    }, 
                    job: {
                        number: true, 
                        digits: true 
                    }
                },
                messages: {
                    user_edit_name: "Éste campo es obligatorio.", 
                    user_edit_first_name: "Éste campo es obligatorio.", 
                    user_edit_last_name: "Éste campo es obligatorio.", 
                    user_edit_email: "Éste campo es obligatorio.", 
                    user_edit_gender: "Éste campo es obligatorio.", 
                    user_edit_age: "Falta poner la edad o está escribiendo más de 2 dígitos.", 
                    user_edit_district: "Éste campo es obligatorio.", 
                    user_edit_estate: "Éste campo es obligatorio.", 
                    user_edit_election: "Éste campo es obligatorio.", 
                    user_edit_ocupation: "Éste campo es obligatorio.", 
                    user_edit_schooling: "Éste campo es obligatorio.", 
                    user_edit_substitute: "Éste campo es obligatorio.", 
                    edit_area_input: "Éste campo es obligatorio.", 
                    edit_photo_file: "Éste campo es obligatorio.", 
                    comission: "Éste campo es obligatorio.", 
                    job: "Éste campo es obligatorio.", 
                    required: "Campo obligatorio", 
                    minlength: "Por favor, haga su respuesta más amplia.", 
                    maxlength: "Por favor, acorte su respuesta", 
                    email: "Escriba un email válido",
                    number: "Escriba solo números", 
                    digits: "Escriba solo números", 
                    accept: "Introduzca Solamente Imágenes", 
                    extension: "Éste formato de archivo no es válido"
                }, 
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).val( '' );
                    $( element ).addClass( 'error_edit' );
                    $( element ).parent().prepend( '<div class="error_indicator"></div>' );
                },
                unhighlight: function ( element, errorClass ) { 
                    $( element ).siblings( errorClass ).remove();
                    $( element ).removeClass( 'error_edit' );
                    $( element ).parent().children( '.error_indicator' ).remove( ); 
                }, 
                submitHandler: function ( form ) {
                    
                    // Form submit
                    $( form ).ajaxSubmit( {
                        //    Before submitting the form
                        beforeSubmit: function ( arr, form, options ) {
                            $( 'label' ).removeClass( 'error' );
                            $( '.error_indicator' ).remove( );
                        },
                        //processData: true, 
                        dataType: "json", 
                        type: "POST", 
                        //  !Function for handle data from server
                        success: function ( responseText, statusText, xhr, form ) {

                            //console.log(responseText.success);
                            $( '#message' ).empty( ).addClass( 'thank_you_message' );
                            
                            if( responseText.success == 'true' || responseText.success == true ) {
                                   
                                $( '#message' ).html( '<h4>Gracias</h4><p>Los datos han sido modificados exitosamente.</p>' );
                                $( '#message' ).fadeIn( 10000, function ( ) {
                                    
                                    window.location.reload();
                                } );
                            } else {
                                $( '#message' ).html( '<h4>Error</h4<p>Verifique que sus datos están correctos o que no falte ningún dato.</p><p>Por favor, vuelvalo a intentar.</p>' );
                                $( '#message' ).fadeIn( 10000 );
                            }
                        }, 
                        resetForm: false, 
                        clearForm: false, 
                        //   If something is wrong
                        error: function ( jqXHR, textStatus, errorThrown ) { 
                            //console.log(textStatus);
                            //console.log(errorThrown);
                            $( '#message' ).empty( );
                            $( '#message' ).html( '<p>Error al ingresar la información.</p><p>Verifique que sus datos están correctos o que no falte ningún dato.</p><p>Por favor, vuelvalo a intentar.</p>' );
                            $( '#message' ).addClass( 'wrong' ).hide( 'fast', function( ) {
                                $( '#message' ).show( 'fast' );
                            });
                        }, 
                        cache: false
                    });
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
    //  !Login Page
    if ( $( '#login' ).exists( ) ) {

        $( '#login .container' ).centerHeight();
        dire.validateLogin();
    }
    //  !Results Page
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
    //  !Edit Page
    if ( $( '#edit_user' ).exists( ) ) {
        
        $( '.delete_comission_row .del_button' ).on( 'click', function ( e ) {
            
            e.preventDefault();
            e.stopPropagation();
            
            var _url    = $( e.currentTarget ).attr( 'href' );
            
            $.ajax( _url, {
                beforeSend: function ( jqXHR, settings ) {

                }, 
                cache: false, 
                complete: function ( jqXHR, textStatus ) {

                }, 
                success: function ( data, textStatus, jqXHR ) {
                    
                    data    = $.parseJSON( data );

                    if ( data.success == true || data.success == "true" ) {
                        
                        var _tr = $( e.currentTarget ).parent(  ).parent(  );
                        _tr.empty().html( '<td>La comision se ha eliminado exitosamente.</td>' );
                    } else {
                        
                        $( '#message' ).text( data.message );
                    }
                }, 
                type:   'GET', 
                
            } );
        } );
        
        var indexTR   = 1;
        
        $( '.add' ).on( 'click', function ( e ) {

            e.preventDefault();
            e.stopPropagation();

            var body    =  $( '.edit_comission_row tbody' );
            
            if ( indexTR == 1 ) {
                
                body.children( 'tr' ).last().clone( true ).appendTo( body );
                
                //  !Adding Erase Button
                $( '.edit_comission_row tbody' ).children( 'tr' ).last( ).children( 'td.erase' ).append( '<a href="#" title="Eliminar" class="delete">Eliminar</a>' );

                indexTR++;
            } else if ( indexTR > 1 && indexTR < 6 ) {
                
                body.children( 'tr' ).last().clone( true ).appendTo( body );
                
                if( indexTR == 5 ) {
                    
                    //  !Adding Erase Button
                    $( '.edit_comission_row tbody' ).children( 'tr' ).last( ).children( 'td.add_one' ).children( 'a.add' ).remove();
                }
                indexTR++;
            }
        } );
        
        $( '.erase' ).on( 'click', function ( e ) {

            e.preventDefault();
            e.stopPropagation();

            var body    =  $( '.edit_comission_row tbody' );
            
            if ( indexTR > 1 ) {
                
                body.children( 'tr' ).last().remove();
                
                indexTR--;
            }
        } );
        
        dire.validateEdit();
        
        $( '#edit_user_form' ).on( 'submit', function ( e ) {
            
            //e.stopPropagation();
            e.preventDefault();
        } );
        
    }
});