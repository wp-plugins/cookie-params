var data = { action: 'debug' }; 
jQuery.post( debug.ajaxurl, data, function(response) {
	console.log( 'Got this from the server: ' + response );
});
