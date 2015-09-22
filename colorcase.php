<?php
/*
Plugin Name: Colorcase
Plugin URI: http://upthemes.com
Description: A plugin that makes it dead simple to add custom color schemes to your website.
Version: 0.1
Author: UpThemes
Author URI: http://upthemes.com
License: GPL2
*/

// don't call the file directly
if ( !defined( 'ABSPATH' ) ){
	return;
}

$colorcase_file = __FILE__;

if ( isset( $plugin ) ) {
	$colorcase_file = $plugin;
}
else if ( isset( $mu_plugin ) ) {
	$colorcase_file = $mu_plugin;
}
else if ( isset( $network_plugin ) ) {
	$colorcase_file = $network_plugin;
}

define( 'COLORCASE_FILE', $colorcase_file );

define( 'COLORCASE_PATH', WP_PLUGIN_DIR.'/'.basename( dirname( $colorcase_file ) ) );

/**
 * Colorcase class
 *
 * @class Colorcase	The class that holds the entire Colorcase plugin
 */
class Colorcase {

	/**
	 *
	 * Defines variables
	 *
	 * $url and $dir are filtered so the plugin can optionally be moved into a theme
	 *
	 * Adds setup to after_setup_theme action
	 *
	 * Initializes the Colorcase() class
	 *
	 * Checks for an existing Colorcase() instance
	 * and if it doesn't find one, creates it.
	 *
	 */
	public static function &init() {

		static $instance = false;

		if ( !$instance ) {

			$instance = new Colorcase();

		}

		return $instance;
	}

	/**
	 * Adds actions and hooks
	 *
	 * Runs on after_theme_setup hook
	 *
	 */
	public function __construct(){

		add_action( 'customize_register', array( &$this, 'theme_color_customizer' ), 99 );

		add_action( 'wp_head',array( &$this, 'add_selectors' ) );

		add_action( 'customize_preview_init', array( &$this, 'customizer_live_preview' ) );

	}

	/**
	 * PHP 5.3 and lower compatibility
	 *
	 * @uses Colorcase::__construct()
	 *
	 */
	public function Colorcase(){
		$this->__construct();
	}



	/**
	 * Check for theme support
	 *
	 * @uses require_if_theme_supports
	 *
	 */
	private static function colorcase_theme_support(){
		// does the theme support colorcase?
		$theme_support = get_theme_support( 'colorcase' );

		// bail if no theme support for colorcase
		if( $theme_support === false ){

			return false;

		}

		// bail if no theme support doesn't return an array or it's empty
		if( !is_array( $theme_support ) || empty( $theme_support ) ){

			return false;

		}

		// bail if no arguments passed
		if( !isset( $theme_support[0] ) || !is_array( $theme_support[0] ) || empty( $theme_support[0] ) ){

			return false;

		}

		// otherwise define $theme_color_locations
		return $theme_support[0];
	}

	/**
	 * Loads customizer options if theme support for colorcase exists
	 *
	 * @uses require_if_theme_supports
	 *
	 */
	public function theme_color_customizer( $wp_customize ){

		$theme_color_locations = $this->colorcase_theme_support();

		// bail if no theme support
		if( $theme_color_locations == false ){
			return;
		}

		// get custom inputs
		include_once('customizer-inputs.php');

		// remove title color customization
		$wp_customize->remove_control( 'header_textcolor' );

		// add theme colors panel
		$wp_customize->add_panel( 'theme_colors', array(
			'priority' => 35,
			'capability' => 'edit_theme_options',
			'theme_supports' => 'colorcase',
			'title' => 'Theme Colors',
			'description' => 'Pick a color palette, or choose custom colors.',
		) );

		// bail if not areas to customize
		if( !isset( $theme_color_locations['sections'] ) || empty( $theme_color_locations['sections'] ) ){
			return;
		}

		if( isset( $theme_color_locations['palettes'] ) && !empty( $theme_color_locations['palettes'] ) ){

			$section_label = 'Color Palettes';
			$section_slug = sanitize_title( $section_label );

			// add theme color palettes section to customizer
			$wp_customize->add_section(
				'theme_colors_' . $section_slug,
				array(
					'title' => $section_label,
					'description' => '',
					'priority' => 10,
					'panel' => 'theme_colors',
				)
			);

			$wp_customize->add_setting(
				$section_slug . '_Picker',
				array(
					'default' => 'default',
				)
			);

			$wp_customize->add_control(
				new Color_Palette_Picker_Customize_Control(
					$wp_customize,
					$section_slug . '_Picker',
					array(
						'label' => 'Color Palette',
						'section' => 'theme_colors_' . $section_slug,
						// 'settings' => ''
					)
				)
			);
		}

		foreach( $theme_color_locations['sections'] as $section_label => $theme_color_section_locations ){

			$section_slug = sanitize_title( $section_label );

			// add theme colors section to customizer
			$wp_customize->add_section(
				'theme_colors_' . $section_slug,
				array(
					'title' => $section_label,
					'description' => '',
					'priority' => 10,
					'panel' => 'theme_colors',
				)
			);

			foreach( $theme_color_section_locations as $color_location_label => $color_location ){

				$slug = sanitize_title( $section_label . '_' . $color_location_label );

				$wp_customize->add_setting(
					$slug,
					array(
						'default' => $color_location['default'],
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);

				if( isset( $color_location['description'] ) ){
					$color_location_label .= '<p class="description"><small>' . $color_location['description'] . '</small></p>';
				}

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$slug,
						array(
							'label' => $color_location_label,
							'section' => 'theme_colors_' . $section_slug,
							'settings' => $slug,
						)
					)
				);

			}

		}

	}

	/**
	* Adds customizer theme color CSS selectors to head
	*
	* @uses sanitize_title
	* @uses get_theme_mod
	*
	*/
	public function add_selectors(){

		$theme_color_locations = $this->colorcase_theme_support();

		// bail if no theme support
		if( $theme_color_locations == false ){
			return;
		}

		// placeholder array for CSS styles
		$styles = array();

		foreach( $theme_color_locations['sections'] as $section_label => $theme_color_section_locations ){

			$section_slug = sanitize_title( $section_label );

			foreach( $theme_color_section_locations as $color_location_label => $theme_color_location ){

				// create unique slug
				$slug = sanitize_title( $section_label . '_' . $color_location_label );

				// stash customizer color
				$customizer_color = get_theme_mod( $slug, $theme_color_location['default'] );

				// skip color if empty (none selected)
				if( empty( $customizer_color ) ){
					continue;
				}

				$selector = $theme_color_location['selector'];
				$attribute = $theme_color_location['attribute'];

				// if customizer color isn't the default add it to styles
			if( $customizer_color != $theme_color_location['default'] ){

				// add the styles
				$styles[] = $selector . "{ $attribute: $customizer_color; }";

			}

			}

		}

		// if we have some styles other than defaults
		if( !empty( $styles ) ){

			// print them out
			echo '<style type="text/css">' . "\n\t" . implode( "\n\t", $styles ) . "\n" . '</style>' . "\n" . '<!--==-- End Theme Color Declarations --==-->' . "\n";

		}
	}

	/**
	 * Returns color palettes
	 *
	 */
	public static function colorcase_get_palettes(){
		$theme_color_locations = Colorcase::colorcase_theme_support();

		// bail if no theme support
		if( $theme_color_locations == false ){
			return false;
		}

		// bail if no color palettes
		if( !isset( $theme_color_locations['palettes'] ) || empty( $theme_color_locations['palettes'] ) ){
			return false;
		}

		return $theme_color_locations['palettes'];
	}

	public function customizer_live_preview(){

		$color_palette_sections = self::colorcase_get_palettes();

		// bail if no color palettes
		if( $color_palette_sections == false ){
			return;
		}

		$color_palettes = array();

		foreach( $color_palette_sections as $color_palette_label => $color_palette_sections ){

			$color_palette_slug = sanitize_title( $color_palette_label );

			foreach( $color_palette_sections as $section_label => $sections ){

				$color_palette_section_slug = sanitize_title( $section_label );

				foreach( $sections as $section => $palette_color ){

					$color_palette_input_slug = $color_palette_section_slug . '_' . sanitize_title( $section );

					$color_palettes[$color_palette_slug][$color_palette_input_slug] = $palette_color;

				}

			}

		}

		// bail if no color palettes
		if( empty( $color_palettes ) ){
			return;
		}

		// enqueue CSS file to customizer
		wp_enqueue_style( 'colorcase-customizer',  plugins_url( 'css/colorcase.css', __FILE__ ) );

		// enqueue JS file to customizer
		wp_enqueue_script( 'colorcase-customizer',  plugins_url( 'js/colorcase.js', __FILE__ ), array( 'jquery','customize-preview', 'wp-color-picker' ), false, true );

		// set some variables
		$colorcaseWordPressVars = array(
			'colorPalettes' => $color_palettes,
		);

		// and localize them for the JS
		wp_localize_script( 'colorcase-customizer',  'colorcaseWordPressVars', $colorcaseWordPressVars );

	}

}

$Colorcase = Colorcase::init();