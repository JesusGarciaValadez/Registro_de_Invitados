(function ( $ ) {
    var queryTestIntranet   = {
        //  Contructor inicialice tabs
        QueryTestIntranet:  function () {
            $("ul.tabs").tabs("div.panes > div");
        }, 
        //  Process the information user inputs with a custom message and sended
        //  via ajax. Retrieve a response from server.
        processForm:    function ( inputSelector, message ) {
            $( '#result' ).empty();
            var myInputs    = inputSelector.serializeArray();
            var inputName   = myInputs[0].name;
            
            jQuery("#result").fadeTo(500,1).empty();
            
            jQuery('.error').remove();
    
    		var myValue;
    		var referenceIndex;

    		//    Check if input is not empty and validate
    		jQuery.each(myInputs, function(index, value){
    			referenceIndex = index;
    
    			myValue = value.value;
    			myValue = myValue.trim();

    			var noValid = inputSelector.eq(referenceIndex);			
    			 if(!noValid.hasClass('validate')){
    				if (!myValue || myValue == '' || myValue == null || myValue == 'escoge_una_opcion' ) { 
    					noValid.parent(this).append(jQuery('<label class="error">'+message+'</label>'));
    					resultadoValidate = false;
    
    				}else{
    					resultadoValidate = true;
    				}
    
    			 }
    
    		});
    		
    		jQuery('.error').eq(0).prev().focus();
    		
    		if(!resultadoValidate){
    			jQuery("#result").fadeOut(500);
    		} else {
    		      //  Send information and retrieve server response
    			jQuery.ajax( 'snippets/receiveData.php', { 
    				async:			true, 
    				beforeSend: 	function ( XMLHttpRequest ) {
    				  this;
    		        }, 
    				cache:			false, 
    				complete:		function ( XMLHttpRequest, textStatus ) { 
    				  this; 
    		        }, 
    				contentType:	'application/x-www-form-urlencoded', 
    				data:			myInputs, 
    				dataType:		'html', 
    				error:			function ( XMLHttpRequest, textStatus, errorThrow ) { 
    				    $( '#result' ).append( '<p>Hubo un error. Intenta nuevamente.</p>' );
    				}, 
    				success:		function ( data, textStatus ) { 
    				    $( '#result' ).append( data );
    				}, 
    				type:			'POST'
    			} );
    		}

    		//    When user blur some input controler check if is valid and remove the error tag
        	inputSelector.blur(function(e){
        		if(jQuery(e.currentTarget).val() != ''){
        			jQuery(e.currentTarget).next('.error').remove();
        		}
        		return false;
        	});
        }
    }
    
    //  Executes when page loads completely.
    $("Document").ready( function ( e ) {
    
        //  Inicialice the tabs behavior
        queryTestIntranet.QueryTestIntranet();
        
        //  Event handlers for each tab criteria
        $("#persona_submit").on( "click", function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            queryTestIntranet.processForm( $('#persona_form input[type="text"]'), 'Por favor, escribe un nombre válido.' );
        } );
        $("#comision").on( "change", function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            queryTestIntranet.processForm( $('#comision_form select'), 'Por favor, escoje una opción.' ); 
        } );
        $("#estado").on("change", function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            queryTestIntranet.processForm( $('#estado_form select'), 'Por favor, escribe un área válida.' ); 
        } );
        $("#area_submit").on("click", function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            queryTestIntranet.processForm( $('#area_form input[type="text"]'), 'Por favor, escribe un estado válido.' );
        } );
    });
})( jQuery, null );