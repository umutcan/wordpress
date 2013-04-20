<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/**
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0.4
 */
?>
<?php 	
// return Social Links array
function voyage_social_links() {
	$social_links = array(
		'facebook' => array(
			'name'  => 'url_facebook',
			'label' => __( 'Facebook', 'voyage' ),
		),
		'linkedin' => array(
			'name'  => 'url_linkedin',
			'label' => __( 'Linkedin', 'voyage' ),
		),		
		'twitter' => array(
			'name'  => 'url_twitter',
			'label' => __( 'Twitter', 'voyage' ),
		),
		'gplus' => array(
			'name'  => 'url_gplus',
			'label' => __( 'Google+', 'voyage' ),
		),
		'youtube' => array(
			'name'  => 'url_youtube',
			'label' => __( 'YouTube', 'voyage' ),
		),
		'vimeo' => array(
			'name'  => 'url_vimeo',
			'label' => __( 'Vimeo', 'voyage' ),
		),
		'flickr' => array(
			'name'  => 'url_flickr',
			'label' => __( 'Flickr', 'voyage' ),
		),
		'instagram' => array(
			'name'  => 'url_instagram',
			'label' => __( 'Instagram', 'voyage' ),
		),
		'rss' => array(
			'name'  => 'url_rss',
			'label' => __( 'RSS Feed', 'voyage' ),
		),
	);
	return apply_filters( 'voyage_social_links', $social_links );
}

if ( ! function_exists( 'voyage_social_connection' ) ) :
function voyage_social_connection($pos = 'top') { 
	global $voyage_options;
	$social_links = voyage_social_links();
	$flag = 0;
	foreach ($social_links as $link ) {
		if (!empty( $voyage_options[$link['name']]) ) {
			$flag = 1;
		}
	}
	if ($flag) {
		$icon_option = 'sl_' . $pos . 'icon';
		if ($voyage_options[$icon_option] == 2)
			$icon_class = "medium-icon";
		elseif ($voyage_options[$icon_option] == 3)
			$icon_class = "large-icon";
		else
			$icon_class = "small-icon";
		echo '<div class="social-links '. $pos . ' ' . $icon_class .'"><ul>';
		if (!empty($voyage_options['sociallink_text'])) {
			printf ('<li><span>%s</span></li>', $voyage_options['sociallink_text']);			
		}
		foreach ($social_links as $link ) {
			if (!empty( $voyage_options[$link['name']]) ) {
				echo '<li><a class="' . $link['name'];
				echo '" href="' . esc_url( $voyage_options[$link['name']] );
				echo '" title="' . esc_attr($link['label']);
				echo '" target="_blank">' . esc_attr($link['label']) . '</a></li>';
			}			
		}
		echo '</ul></div>';
	}
}
endif;

if ( ! function_exists( 'voyage_social_post_top' ) ) :
function voyage_social_post_top() {
	global $voyage_options;

	if ( $voyage_options['sharesocial'] == 1 && ! has_post_format('aside')
				&&  function_exists( 'sharing_display' ) ) {
		if (is_single() && $voyage_options['share_top'] == 1)
			echo sharing_display();			
		elseif (!is_single() && $voyage_options['share_sum_top'] == 1)
			echo sharing_display();		
	}	
}
endif;

if ( ! function_exists( 'voyage_social_post_bottom' ) ) :
function voyage_social_post_bottom() {
	global $voyage_options;
	if ( $voyage_options['sharesocial'] == 1 && ! has_post_format('aside')
				&&  function_exists( 'sharing_display' ) ) {
		if (is_single()  && $voyage_options['share_bottom'] == 1) 
			echo sharing_display();	
		elseif (!is_single() && $voyage_options['share_sum_bottom'] == 1)
			echo sharing_display();		
	}	
}
endif;
// Check for Jetpack Sharing
function voyage_remove_sharing_filters() {
	global $voyage_options;

	if ( $voyage_options['sharesocial'] == 1 && function_exists( 'sharing_display' ) ) {
			remove_filter( 'the_content', 'sharing_display', 19 );
			remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}
}
add_action('voyage_header_before_main','voyage_remove_sharing_filters');
?>