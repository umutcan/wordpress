<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */

get_header(); ?>

	<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">	
		<?php $options = voyage_get_options(); 
		if (  $options['showauthor'] == '1' && have_posts() ) : 
			the_post(); ?>

			<header class="page-header">
				<h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'voyage' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
			</header>

			<?php rewind_posts(); ?>

			<?php voyage_content_nav( 'nav-above' ); ?>

			<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<div id="author-info">
					<div id="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'voyage_author_bio_avatar_size', 60 ) ); ?>
					</div><!-- #author-avatar -->
					<div id="author-description">
						<h2><?php printf( __( 'About %s', 'voyage' ), get_the_author() ); ?></h2>
						<?php the_author_meta( 'description' ); ?>
					</div><!-- #author-description	-->
				</div><!-- #author-info -->
			<?php endif; ?>

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
