<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */

get_header(); 

global $voyage_thumbnail, $voyage_display_excerpt, $voyage_entry_meta;	
$voyage_display_excerpt = 1;
$voyage_thumbnail = 'thumbnail';
$voyage_entry_meta = 1;
?>

	<div id="content" class="<?php echo voyage_grid_class(); ?> voyage_recent_post" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'voyage' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header>

			<?php voyage_content_nav( 'nav-above' ); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'summary' ); ?>

			<?php endwhile; ?>

			<?php voyage_content_nav( 'nav-below' ); ?>

		<?php else : ?>
			<?php get_template_part( 'content-none' ); ?>
		<?php endif; ?>
	</div><!-- #content -->
	<?php get_sidebar(); ?>
<?php get_footer(); ?>
