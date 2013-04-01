<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
	*
 * @subpackage New life
 * @since New life
 */
?>

<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'newlife' ); ?></p>
	</div><!-- #comments -->
	<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
	?>
	<?php // You can start editing here -- including this comment! ?>
	<?php if ( have_comments() ) : ?>
		<h3 id="comments-title"><?php printf( _n(__( 'One Response to', 'newlife' ).' %2$s', '%1$s '.__( 'Responses to', 'newlife' ).' %2$s',get_comments_number(),'newlife'), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' ); ?></h3>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div class="navigation">
			<div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> '.__( 'Older Comments', 'newlife' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'newlife' ).' <span class="meta-nav">&rarr;</span>' ); ?></div>
		</div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'newlife_comment' ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div class="navigation">
			<div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> '.__( 'Older Comments', 'newlife' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'newlife' ).' <span class="meta-nav">&rarr;</span>' ); ?></div>
		</div><!-- .navigation -->
		<?php endif; // check for comment navigation ?>

	<?php else : // or, if we don't have comments:

		/* If there are no comments and comments are closed,
		 * let's leave a little note, shall we?
		 * But only on posts! We don't really need the note on pages.
		 */
		if ( ! comments_open() ) :
		?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'newlife' ); ?></p>
		<?php endif; // end ! comments_open() ?>
	<?php endif; // end have_comments() ?>
	<?php comment_form(); ?>
</div><!-- #comments -->
