<?php
/**
 * Template Name: Landing Page
 * Description: A Page Template that display headlines home home widgets
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.1
 */
	get_header();

	global $voyage_options;
	if (!empty($voyage_options['headline'])
			|| !empty($voyage_options['tagline'])
			|| !empty($voyage_options['mediacontent']) ) {			
?>
<div id="content" class="<?php echo voyage_grid_full(); ?>" role="main">	
	<article id="post-0" class="post landing">
<?php
	$flag = 0;
	if ( !empty($voyage_options['headline'])
			|| !empty($voyage_options['tagline']) ) {
		if (!empty($voyage_options['mediacontent']) )			
			echo '<div id="landing-text" class="one_half alpha">';
		if (!empty($voyage_options['headline']))
			printf('<h1>%s</h1>',  esc_attr($voyage_options['headline']));
		if (!empty($voyage_options['tagline']))
			printf('<p>%s</p>', do_shortcode($voyage_options['tagline']) );
		if (!empty($voyage_options['actionlabel']))
			printf('<p class="action-full"><a class="btn btn-primary btn-large" href="%s">%s</a></p>',
				esc_attr($voyage_options['actionurl']),
				esc_attr($voyage_options['actionlabel']) );
		if (!empty($voyage_options['mediacontent']) )					
			echo '</div>';
		$flag = 1;
	}
	
	if ($flag == 1)
		echo '<div id="landing-media" class="one_half omega">';			
	if (!empty($voyage_options['mediacontent'])) { 
		printf('<p>%s</p>',  do_shortcode($voyage_options['mediacontent']) );
	}		
	if ($flag == 1)
		echo '</div>';
?>
		</article>
</div>
<?php				
	}
?>
</div><!-- #container -->
</div><!-- featured wrapper -->
<div class="<?php echo voyage_container_class(); ?> clearfix">
<?php
	get_sidebar('home');
	get_footer();
?>
