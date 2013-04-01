<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */
/*
Template Name: Full Width
*/

get_header(); ?>

	<div id="col-wide">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

				<div id="morepage-list"><?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?></div>
				
				<div class="cboth"></div>
			</div>
		</div>
		
		<?php comments_template(); ?>
		
		<?php endwhile; endif; ?>
		
		<?php edit_post_link('Edit this entry.', '<p class="cboth"><br /><br />', '</p>'); ?>
		
	</div>

<?php get_footer(); ?>
