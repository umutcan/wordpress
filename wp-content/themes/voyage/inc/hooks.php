<?php
if ( !defined('ABSPATH')) exit;
/**
 * @package Voyage
 * @subpackage voyage
 * @since Voyage 1.1.7
*/

function voyage_header_branding() {
	do_action('voyage_header_branding');
}

function voyage_header_before_navbar() {
	do_action('voyage_header_before_navbar');
}

function voyage_header_before_main() {
	do_action('voyage_header_before_main');
}

function voyage_header_after_main() {
	do_action('voyage_header_after_main');
}
/**
 * WooCommerce Support
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'voyage_woocommerce_content_wrapper', 10);
add_action('woocommerce_after_main_content', 'voyage_woocommerce_content_wrapper_end', 10);

function voyage_woocommerce_content_wrapper() {
  echo '<div id="content" class="' . voyage_grid_class() .'">';
}
 
function voyage_woocommerce_content_wrapper_end() {
  echo '</div><!-- end of #content -->';
}

/**
 * Jigoshop Support
 */
remove_action( 'jigoshop_before_main_content', 'jigoshop_output_content_wrapper', 10 );
remove_action( 'jigoshop_after_main_content', 'jigoshop_output_content_wrapper_end', 10);

add_action( 'jigoshop_before_main_content', 'voyage_jigoshop_content_wrapper', 10 );
add_action( 'jigoshop_after_main_content', 'voyage_jigo_content_wrapper_end', 10 );

function voyage_jigoshop_content_wrapper() {
  echo '<div id="content" class="' . voyage_grid_class() .'">';
}
 
function voyage_jigo_content_wrapper_end() {
  echo '</div><!-- end of #content -->';
}
?>