<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */

get_header(); ?>
	<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
			<?php $tag_description = tag_description();
			$pg_title_class ="";
			if ( ! empty( $tag_description ) ) {
				$pg_title_class = " hide";
			} ?>
			<h1 class="page-title <?php echo $pg_title_class ?>"><?php
				printf( __( 'Tag Archives: %s', 'voyage' ), '<span>' . single_tag_title( '', false ) . '</span>' );
			?></h1>

			<?php if ( ! empty( $tag_description ) )
				echo apply_filters( 'tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>' );
			?>
			</header>

			<?php voyage_content_nav( 'nav-above' ); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>

			<?php endwhile; ?>
			<?php voyage_content_nav( 'nav-below' ); ?>

		<?php else : ?>
				<?php get_template_part( 'content-none' ); ?>
		<?php endif; ?>
	</div><!-- #content -->
	<?php get_sidebar(); ?>
<?php get_footer(); ?>
