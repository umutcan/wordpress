<?php
/**
 * Add scheme related options
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.2.6
 */
if ( !defined('ABSPATH')) exit;

function voyage_scheme_options($scheme) {
	$theme_uri = get_template_directory_uri();
	
	$scheme[] = array(	'key' => 'lightblue',
				   		'label' => __('Light Blue','voyage'),
						'css' => $theme_uri . '/scheme/lightblue.css',
						'demoimg' => '',
				   		'options' => array(
							array( 'name'  => 'navbarcolor','value' => 0),
						),
				);
	$scheme[] =	array(	'key' => 'dark',
				   		'label' => __('Dark','voyage'),
						'css' => $theme_uri . '/scheme/dark.css',
						'demoimg' => '',
				   		'options' => array(
							array( 'name'  => 'navbarcolor','value' => 1),
						),
				);
	$scheme[] =	array(	'key' => 'sandy',
				   		'label' => __('Sandy','voyage'),
						'css' => $theme_uri . '/scheme/sandy.css',
						'demoimg' => '',
				   		'options' => array(
							array( 'name'  => 'navbarcolor','value' => 0),
						),
				);

	return $scheme;			
}
add_filter('voyage_colorscheme_array','voyage_scheme_options');
?>