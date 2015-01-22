Colorcase
=========

A plugin to add custom color schemes to your WordPress theme.


###Example theme support declaration

```php
$color_locations = array(

	'sections' => array(

		'Sidebar' => array(
			'Background Color' => array(
				'selector' => 'body:before',
				'attribute' => 'background-color',
				'default' => '#FFFFFF',
			),
			'Text Color' => array(
				'selector' => '#sidebar, #sidebar .widget-title',
				'attribute' => 'color',
				'default' => '#333333',
			),
			'Link Color' => array(
				'selector' => '#sidebar a',
				'attribute' => 'color',
				'default' => '#333333',
			),
			'Link Hover Color' => array(
				'selector' => '#sidebar a:hover, #sidebar a:focus',
				'attribute' => 'color',
				'default' => '#707070',
			),
		),

		'Content' => array(
			'Background Color' => array(
				'selector' => '.hentry',
				'attribute' => 'background-color',
				'default' => '#FFFFFF',
			),
			'Text Color' => array(
				'selector' => '#content',
				'attribute' => 'color',
				'default' => '#333333',
			),
			'Link Color' => array(
				'selector' => '#content a',
				'attribute' => 'color',
				'default' => '#333333',
			),
			'Link Hover Color' => array(
				'selector' => '#content a:hover, #content a:focus',
				'attribute' => 'color',
				'default' => '#707070',
			),
		),

	),

	'palettes' => array(

		'Default' => array(

			'Sidebar' => array(
				'Background Color' => '#FFFFFF',
				'Text Color' => '#333333',
				'Link Color' => '#333333',
				'Link Hover Color' => '#707070',
			),

			'Content' => array(
				'Background Color' => '#FFFFFF',
				'Text Color' => '#333333',
				'Link Color' => '#333333',
				'Link Hover Color' => '#707070',
			),

		),

		'Winter Sky' => array(

			'Sidebar' => array(
				'Background Color' => '#FFF6C6',
				'Text Color' => '#543900',
				'Link Color' => '#543900',
				'Link Hover Color' => '#333333',
			),

			'Content' => array(
				'Background Color' => '#543900',
				'Text Color' => '#EBF5FF',
				'Link Color' => '#EBF5FF',
				'Link Hover Color' => '#FFF6C6',
			),

		),

		'Mirkwood' => array(

			'Sidebar' => array(
				'Background Color' => '#C7C095',
				'Text Color' => '#302A20',
				'Link Color' => '#382F22',
				'Link Hover Color' => '#D8F536',
			),

			'Content' => array(
				'Background Color' => '#302A20',
				'Text Color' => '#C7C095',
				'Link Color' => '#D8F536',
				'Link Hover Color' => '#615F3C',
			),

		),

		'Crooked Crown' => array(

			'Sidebar' => array(
				'Background Color' => '#332C0B',
				'Text Color' => '#F0A50E',
				'Link Color' => '#A34212',
				'Link Hover Color' => '#6B091C',
			),

			'Content' => array(
				'Background Color' => '#9E970B',
				'Text Color' => '#332C0B',
				'Link Color' => '#A34212',
				'Link Hover Color' => '#6B091C',
			),

		),

	),



);

add_theme_support( 'colorcase', $color_locations );
```