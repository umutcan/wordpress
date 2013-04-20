<?php
/**
 * Template Name: Fullwidth Page
 * Description: A full width Page Template
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0.1
 */
get_header(); ?>
	<div id="content" class="<?php echo voyage_grid_full(); ?> fullwidth " role="main">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>

			<?php comments_template( '', true ); ?>

		<?php endwhile; // end of the loop. ?>
	</div><!-- #content -->

<?php get_footer(); ?>
