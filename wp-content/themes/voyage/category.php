<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */

get_header(); ?>

	<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">	
				<?php $category_description = category_description();							$pg_title_class ="";
				if ( ! empty( $category_description ) ) {
						$pg_title_class = " hide";
				} ?>
				<h1 class="page-title <?php echo $pg_title_class ?>"><?php
					printf( __( 'Category Archives: %s', 'voyage' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>
				<?php
					if ( ! empty( $category_description ) )
						echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
				?>
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
