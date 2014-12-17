jQuery( document ).ready(function() {
	var data = { action: 'cookie_logic' }; 
	jQuery.post( check.ajaxurl, data, function(response) {
		console.log( 'Got this from the server: ' + response );
	});
});
