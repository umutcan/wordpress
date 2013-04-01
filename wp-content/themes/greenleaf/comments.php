<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */
?>

			<div id="comments">
<?php if ( post_password_required() ) : ?>
				<p class="nopassword">This post is password protected. Enter the password to view any comments.</p>
			</div><!-- #comments -->
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php
			printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number() ),
			number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
			?></h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link('<span class="meta-nav">&larr;</span> Older Comments'); ?></div>
				<div class="nav-next"><?php next_comments_link('Newer Comments <span class="meta-nav">&rarr;</span>'); ?></div>
			</div> <!-- .navigation -->
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php
					/* Loop through and list the comments. Tell wp_list_comments()
					 * to use coaster_comment() to format the comments.
					 * If you want to overload this in a child theme then you can
					 * define coaster_comment() and that will be used instead.
					 * See coaster_comment() in coaster/functions.php for more.
					 */
					wp_list_comments();
				?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link('<span class="meta-nav">&larr;</span> Older Comments'); ?></div>
				<div class="nav-next"><?php next_comments_link('Newer Comments <span class="meta-nav">&rarr;</span>'); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>

<?php else : // or, if we don't have comments:

	/* If there are no comments and comments are closed,
	 * let's leave a little note, shall we?
	 */
	if ( ! comments_open() ) :
?>
	<p class="nocomments"></p>
<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>

<?php comment_form(); ?>

</div><!-- #comments -->
