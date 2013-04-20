<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>
<?php global $voyage_options; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	if ( get_the_title() != "" ) { ?>
		<header class="entry-header clearfix">
			<h1 class="entry-title"><?php the_title(); ?></h1>
<?php		voyage_posted_on(); ?>			
		</header><!-- .entry-header -->
<?php
	} ?> 
	<div class="entry-content clearfix">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'voyage' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<footer class="entry-meta clearfix">
<?php
		edit_post_link( __( '[Edit]', 'voyage' ), '<span class="edit-link">', '</span>' );
		if ( $voyage_options['sharesocial'] == 1
				&& $voyage_options['share_bottom'] == 1
				&&  function_exists( 'sharing_display' ) )
			echo sharing_display();
?>				
	</footer>
</article>
