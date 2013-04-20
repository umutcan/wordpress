<?php
/**
 * Template Name: Featured Home Page
 * Description: A fullwidth Page Template that displays Sticky Posts and Home Widgets
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
	get_header(); 
	global $voyage_options;

	$featured_args = array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'posts_per_page' => $voyage_options['fp_postnum'],
						'ignore_sticky_posts' => 1,
						'no_found_rows' => 1
						);
	if ($voyage_options['fp_sticky'] == 1) {
		$sticky = get_option( 'sticky_posts' );	
		if (!empty($sticky))
			$featured_args['post__in'] = $sticky;
	}
	if ($voyage_options['fp_category'] > 0)
		$featured_args['category__in'] = $voyage_options['fp_category'];

	$featured = new WP_Query( $featured_args );	
	if ( $featured->have_posts() ) {  //Got Sticky Posts ?>
	  <div id="featured-content" class="featured carousel <?php echo voyage_carousel_class(); ?>" role="main">
		<div class="carousel-inner">
<?php	  $first = "active ";
		  $count = 0;
		  while ( $featured->have_posts() ) : 	  
			$featured->the_post(); ?>
 			<div class="<?php echo $first; $first = ''; ?> item post-<?php the_ID(); ?>">
			  <?php get_template_part( 'content', 'featured' ); ?>
			</div>
<?php		$count++;
		  endwhile;	?>
		</div>
<?php	if ($count > 1)
			voyage_carousel_controls($count);
?>
	  </div>
<?php 
	}
	wp_reset_postdata();
?>
</div><!-- #container -->
</div><!-- featured wrapper -->
<?php
	if ($voyage_options['fp_headline'] == 1) {
?>
	  <div class="headline-wrapper clearfix">
		<div class="<?php echo voyage_container_class(); ?>">
		  <div class="<?php echo voyage_grid_full(); ?> headline">
<?php		if (!empty($voyage_options['headline']))
				printf('<h1>%s</h1>', $voyage_options['headline']);
			if (!empty($voyage_options['tagline']))
				printf('<p>%s</p>', $voyage_options['tagline'] );
?>
		  </div>
		</div>
	  </div>
<?php
	}
?>
<div id="content-wrapper" class="<?php echo voyage_container_class(); ?> clearfix">
<?php
	get_sidebar('home');
	get_footer();
?>
