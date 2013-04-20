<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */

get_header(); ?>

	<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title">
						<?php if ( is_day() ) : ?>
							<?php printf( __( 'Daily Archives: %s', 'voyage' ), '<span>' . get_the_date() . '</span>' ); ?>
						<?php elseif ( is_month() ) : ?>
							<?php printf( __( 'Monthly Archives: %s', 'voyage' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'voyage' ) ) . '</span>' ); ?>
						<?php elseif ( is_year() ) : ?>
							<?php printf( __( 'Yearly Archives: %s', 'voyage' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'voyage' ) ) . '</span>' ); ?>
						<?php else : ?>
							<?php _e( 'Blog Archives', 'voyage' ); ?>
						<?php endif; ?>
					</h1>
				</header>

				<?php voyage_content_nav( 'nav-above' ); ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

				<?php voyage_content_nav( 'nav-below' ); ?>

			<?php else : ?>
				<?php get_template_part( 'content-none' ); ?>
			<?php endif; ?>

		</div><!-- #content -->
		<?php get_sidebar(); ?>

<?php get_footer(); ?>
