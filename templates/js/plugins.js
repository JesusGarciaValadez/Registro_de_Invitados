// Avoid `console` errors in browsers that lack a console.
if (!(window.console && console.log)) {
    (function() {
        var noop = function() {};
        var methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'markTimeline', 'table', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn'];
        var length = methods.length;
        var console = window.console = {};
        while (length--) {
            console[methods[length]] = noop;
        }
    }());
}

// Place any jQuery/helper plugins in here.

jQuery.fn.exists = function(){return this.length>0;}

jQuery.fn.centerHeight = function(){
    var winWidth = $( window ).height() / 2;
    var elemWidth = this.width() / 2;
    var elemLeft = winWidth - elemWidth;

    this.css( 'top', elemLeft );
}

//  @codekit-append "jquery.form.js", "jquery.validate.js", "additional-methods.js", , "jquery.tools.custom.min.js", "jquery.dataTables.min.js";
//
