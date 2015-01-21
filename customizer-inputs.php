<?php
class Color_Palette_Picker_Customize_Control extends WP_Customize_Control{

    public function render_content(){
		// get color palettes
		$color_palettes = (array) Colorcase::colorcase_get_palettes();

		// bail if no color palettes
		if( $color_palettes == false || empty( $color_palettes ) ){
			return;
		}
		?>
		<ul>
			<?php
			foreach( $color_palettes as $color_palette_label => $color_palette_sections ){
				// create unique slug
				$color_palette_slug = sanitize_title( $color_palette_label );

				// initialize colors array
				$palette_colors = array();

				echo '<li><label class="customize-palette-control-option" data-value="' . $color_palette_slug . '">';

				echo '<input type="radio" name="' . $this->id . '" id="' . $this->id . '" value="' . $color_palette_slug . '"  class="customize-palette-control" />';

				echo '<span class="customize-palette-control-label">' . $color_palette_label . '</span>';

				echo '<div class="palette-blocks">';

					foreach( $color_palette_sections as $section_label => $section_areas ){

						foreach( $section_areas as $section_area => $palette_color ){

							// if we haven't displayed this color yet
							if( !in_array( $palette_color, $palette_colors ) ){
								// output the color span
								echo '<div class="palette-color-block" style="background: ' . $palette_color . '; "></div>';

								// add it to the palette colors array
								$palette_colors[] = $palette_color;
							}

						}

					}

				echo '</div>';

				}

				echo '</label></li>';
				?>
		</ul>
        <?php
    }
}