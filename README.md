Colorcase
=========

A plugin to add custom color schemes to your WordPress theme.


###Example theme support declaration

```php
$color_locations = array(
	// each array is an option
	// these options are displayed in
	// the main theme fonts panel section
	array(
		// the label displayed in customizer
		'label' => 'Header',
		// the CSS selector to apply the color to
		'selector' => '.site-header',
		// the default color for the selector
		'default' => '#000000',
	),
	array(
		'label' => 'Sidebar',
		'selector' => '#secondary',
		'default' => 'transparent',
	),
	array(
		'label' => 'Text Color',
		'selector' => 'body',
		'default' => '"#FFFFFF',
		// set is_text to true to target "color"
		// instead of the default "background-color"
		'is_text' => true,
	),
	array(
		'label' => 'Link Color',
		// an optional description
		'description' => 'May not apply to all links, such as navigation and widgets',
		'selector' => 'body a',
		'default' => '"#FFFFFF',
		'is_text' => true,
	),
);

add_theme_support( 'colorcase', $color_locations );
```
