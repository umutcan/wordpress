<?php
if ( !defined('ABSPATH')) exit;
/**
 * Voyage Theme functions: Layout
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.1
 */
if ( ! function_exists( 'voyage_grid_class' ) ) :
/** return grid class */
function voyage_grid_class() {
    global $voyage_options;
	$class = "grid_" . $voyage_options['column_content'] . ' ' ;
	if ( $voyage_options['blog_layout'] == "2" && ( $voyage_options['column_sidebar1'] > 0 || $voyage_options['column_sidebar2'] >0  )) {
		if (($voyage_options['column_content'] + $voyage_options['column_sidebar1'] + $voyage_options['column_sidebar2']) > $voyage_options['grid_column'] ) {
			if ($voyage_options['column_sidebar1'] > $voyage_options['column_sidebar2'])
				$push_col = $voyage_options['column_sidebar1']; 
			else
				$push_col = $voyage_options['column_sidebar2'];
		}
		else {
			$push_col = $voyage_options['column_sidebar1'] + $voyage_options['column_sidebar2']; 			
		}
		$class = $class . "push_" . $push_col . ' ';
	}
	elseif ( $voyage_options['blog_layout'] == "3" && $voyage_options['column_sidebar1'] > 0 ) {
		$push_col = $voyage_options['column_sidebar1']; 
		$class = $class . "push_" . $push_col . ' ';		
	}
	return $class;
}
endif;

if ( ! function_exists( 'voyage_container_class' ) ) :
/** return container class */
function voyage_container_class() {
    global $voyage_options;
	
	return "container_" . $voyage_options['grid_column'];
}
endif;

if ( ! function_exists( 'voyage_grid_full' ) ) :
/** return grid_16 or grid_12 */
function voyage_grid_full() {
    global $voyage_options;
	
	return "grid_" . $voyage_options['grid_column'];
}
endif;

if ( ! function_exists( 'voyage_grid_half' ) ) :
/** return grid_8 or grid_6 */
function voyage_grid_half() {
    global $voyage_options;
	
	$col = $voyage_options['grid_column'] / 2;
	return "grid_" . $col;
}
endif;

if ( ! function_exists( 'voyage_grid_quarter' ) ) :
/** return grid_4 or grid_3 */
function voyage_grid_quarter() {
    global $voyage_options;
	
	$col = $voyage_options['grid_column'] / 4;
	return "grid_" . $col;
}
endif;

if ( ! function_exists( 'voyage_grid_columns' ) ) :
function voyage_grid_columns($col) {
	return 'grid_' . $col;
}
endif; 

if ( ! function_exists( 'voyage_carousel_class' ) ) :	
/************************************************
Return Carousel effect class
************************************************/ 
function voyage_carousel_class() {
	global $voyage_options;
	
	$class = 'slide';
	if ($voyage_options['fp_effect'] == 1)
		$class = '';
	elseif ($voyage_options['fp_effect'] == 3 
				&& $voyage_options['fp_image'] != 3)
		$class = 'slide fading';

	return $class;
}
endif;

?>
