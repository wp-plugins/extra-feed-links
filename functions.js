jQuery( function($) {
	// Shift select
	var lastClicked = false;
	$( 'tbody :checkbox' ).click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			var checks = $( lastClicked ).parents( 'form:first' ).find( ':checkbox' );
			var first = checks.index( lastClicked );
			var last = checks.index( this );
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
			}
		}
		lastClicked = this;
		return true;
	} );

	// Check all
	$( 'thead :checkbox' ).click( function() {
		checkAll( $(this).parents('form:first'), $(this) );
	} );
} );

function checkAll(context, controller) {
	jQuery(context).find( 'tbody :checkbox' ).attr( 'checked', function() {
		return controller.attr( 'checked' );
	} );
}
