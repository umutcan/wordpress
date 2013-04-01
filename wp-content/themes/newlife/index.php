<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no front-page.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @subpackage New life
 * @since New life
 */
?>
<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="page_child">
	<?php global $wp_query;
	while ( have_posts() ) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( is_single() ) : // Only display excerpts for single. ?>				
				<div class="date_back">
					<div class="date_post">
						<p class='month'><?php the_date('M');?></p>
						<p class='day'><?php the_time('j');?></p>
			
					</div>		
				</div>
			<?php endif; ?>
				<div  id="widjet_title" ><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
			<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
						<div class="read_more"><a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>"><?php _e( 'read more', 'test' );?></a></div>
				</div><!-- .entry-summary -->
			<?php else : ?>
				<div class="entry-content">
					<?php the_content( __( 'Continue reading', 'newlife' ).' <span class="meta-nav">&rarr;</span>' ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'newlife' ), 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->
			<?php endif; ?>
			<div class="entry-utility">
				<?php if ( count( get_the_category() ) ) : ?>
					<span class="cat-links">
							<?php printf( '<span class="%1$s">'.__( 'Posted in', 'newlife' ).'</span> %2$s', 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
						</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<?php
				$tags_list = get_the_tag_list( '', ', ' );
				if ( $tags_list ):?>
					<span class="tag-links">
						<?php printf( '<span class="%1$s">'.__( 'Tagged', 'newlife' ).'</span> %2$s', 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<?php if (! is_single() ) : // Only display excerpts for single. ?>	
						<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'newlife' ), __( '1 Comment', 'newlife' ), '% '.__( 'Comments', 'newlife' ), __( 'Comments Off', 'newlife' ) ); ?></span>
					<?php edit_post_link( __( 'Edit', 'newlife' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
				<?php endif; ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->
		<div class='clear'></div>	
		<?php comments_template( '', true ); ?>
	<?php endwhile;?>
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<?php previous_posts_link( '<span class="read_more">'.__( 'Newer', 'newlife' ).' &rarr;</span>' ); ?>
			<?php next_posts_link( '<span class="read_more">&larr; '.__( 'Older', 'newlife' ).'</span>' ); ?>
	<?php endif; ?>
</div><!-- #page_child -->
<?php get_footer(); ?>