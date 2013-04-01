jQuery( function( $ ) {
	// Show/hide the multiselect containers when user clicks on "limit by" widget options
	$( '.calp-limit-by-cat, .calp-limit-by-tag, .calp-limit-by-event' ).live( 'click', function() {
		$( this ).parent().next( '.calp-limit-by-options-container' ).toggle();
	} );
} );