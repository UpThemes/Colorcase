/* globals _wpCustomizeHeader, _wpCustomizeBackground, _wpMediaViewsL10n */

// wait for window load - no iframe ready event (yet)
jQuery(window).load(function () {

	var $ = jQuery;

	// stash localized WordPress variables
	var WPvars = colorcaseWordPressVars;

	//	stash the wp.customize object
    var api = wp.customize;

	$('#accordion-panel-theme_colors .customize-control-color').each(function(){

		var currentInput = $('input[type="text"]', this);

		currentInput.attr( 'data-colorcase-slug', $(this).attr('id').replace( 'customize-control-', '' ) );

	});

	var colorChangeInProgress = false;

	$.each( WPvars.colorPalettes, function( paletteSlug, colorInputs ){
		$('#accordion-panel-theme_colors .customize-palette-control-option[data-value="' + paletteSlug + '"]').click( function(e){

			if ( colorChangeInProgress ==false ) {
				e.preventDefault();
				return;
			}

			colorChangeInProgress = true;

			$.each( colorInputs, function( colorInputSlug, newColor ){

				$('#accordion-panel-theme_colors .customize-control-color input[data-colorcase-slug="' + colorInputSlug + '"]')
					.iris( 'color', newColor );

			});

			var colorChangeInProgress = false;

		});
	});

	// $('#accordion-panel-theme_colors');

	/*
	* Function to reset advanced font options
	*/
	/*
	function resetAdvancedFonts( showAdvanced ){

		// if show advanced options is false
		if ( !showAdvanced ) {

			jQuery('select', advancedInputs).each(function () {

				// get the unique setting slug
				var slug = jQuery(this).attr('data-customize-setting-link');

				// reset the value in customizer
				api.instance(slug).set('');

			});

		}

		// refresh the preview
		api.previewer.refresh();

	}

	// get the show advanced options value
	var showAdvanced = api.value('show_advanced_fonts')();

	// when show advanced fonts option is changed
    api('show_advanced_fonts', function (callback) {

		// bind a callback function to the value
        callback.bind(function ( showAdvanced ) {

			// show/hide advanced fonts based on preference
			toggleAdvancedFonts( showAdvanced );

			// reset advanced fonts
			resetAdvancedFonts( showAdvanced );

        });

    });

    */

	jQuery('a.customize-controls-close').click(function(e){
		window.location.href = jQuery(this).attr('href');
	});

});