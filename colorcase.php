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
	private function colorcase_theme_support(){
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

		$wp_customize->add_section(
			'theme_colors',
			array(
				'title' => 'Theme Colors',
				'description' => 'Your theme supports Colorcase for custom colors.',
				'priority' => 35,
			)
		);

		// remove title color customization
		$wp_customize->remove_control( 'header_textcolor' );

		foreach( $theme_color_locations as $color_location ){

			$slug = sanitize_title( $color_location['label'] );

			if( isset( $color_location['description'] ) ){
				$color_location['label'] .= '<p class="description"><small>' . $color_location['description'] . '</small></p>';
			}

			$wp_customize->add_setting(
				$slug,
				array(
					'default' => $color_location['default'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$slug,
					array(
						'label' => $color_location['label'],
						'section' => 'theme_colors',
						'settings' => $slug,
					)
				)
			);

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

		// loop through each theme color location
		foreach( $theme_color_locations as $theme_color_location ){

			// create a unique slug
			$slug = sanitize_title( $theme_color_location['label'] );

			// stash customizer color
			$customizer_color = get_theme_mod( $slug, $theme_color_location['default'] );

			// skip color if empty (none selected)
			if( empty( $customizer_color ) ){
				continue;
			}

			// if customizer color isn't te default add it to styles
			if( $customizer_color != $theme_color_location['default'] ){

				if( !isset( $theme_color_location['is_text'] ) ){
					$theme_color_location['is_text'] = false;
				}

				$css_declaration = ( $theme_color_location['is_text'] ) ? 'color' : 'background-color';

				// add the styles
				$styles[] = $theme_color_location['selector'] . "{ $css_declaration: $customizer_color; }";

			}

		}

		// if we have some styles other than defaults
		if( !empty( $styles ) ){

			// print them out
			echo '<style type="text/css">' . "\n\t" . implode( "\n\t", $styles ) . "\n" . '</style>' . "\n" . '<!--==-- End Theme Color Declarations --==-->' . "\n";

		}
	}

}

$Colorcase = Colorcase::init();