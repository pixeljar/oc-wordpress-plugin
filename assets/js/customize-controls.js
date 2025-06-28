jQuery( document ).ready( function() {

	/* === Checkbox Multiple Control === */

	jQuery( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on(
		'change',
		function() {

			checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
				function() {
					return this.value;
				}
			).get().join( ',' );

			jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
		}
	);

} ); // jQuery( document ).ready


// Disable Custom Theme for non-paid.
// wp.customize.bind( 'ready', function() { // Ready?

// 	var customize = this; // Customize object alias.
// 	customize( 'ocwp_color_theme', function( value ) {

// 		var fontFamily = customize.control( 'ocwp_font_family' ).container.find( 'select' );
// 		var fontSize = customize.control( 'ocwp_font_size' ).container.find( 'select' );
// 		var fontColor = customize.control( 'ocwp_font_color' ).container.find( 'button' );
// 		var buttonTextColor = customize.control( 'ocwp_button_text_color' ).container.find( 'button' );
// 		var buttonBGColor = customize.control( 'ocwp_button_bg_color' ).container.find( 'button' );
// 		var bannerBGColor = customize.control( 'ocwp_banner_bg_color' ).container.find( 'button' );

// 		// 1. On loading.
// 		fontFamily.prop( 'disabled', 'disabled' );
// 		fontSize.prop( 'disabled', 'disabled' );
// 		fontColor.prop( 'disabled', 'disabled' );
// 		buttonTextColor.prop( 'disabled', 'disabled' );
// 		buttonBGColor.prop( 'disabled', 'disabled' );
// 		bannerBGColor.prop( 'disabled', 'disabled' );

// 		// 2. Binding to value change.
// 		value.bind( function( to ) {
// 			fontFamily.prop( 'disabled', 'disabled' );
// 			fontSize.prop( 'disabled', 'disabled' );
// 			fontColor.prop( 'disabled', 'disabled' );
// 			buttonTextColor.prop( 'disabled', 'disabled' );
// 			buttonBGColor.prop( 'disabled', 'disabled' );
// 			bannerBGColor.prop( 'disabled', 'disabled' );
// 		} );

// 	} );

// } );
