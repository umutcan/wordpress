<?php get_header(); ?>
<?php get_sidebar(); ?>
<div class="content">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<div class="title_content"><?php the_title(); ?></div>
		<?php the_content(); ?>
	<?php endwhile; ?>
	<div class='clear'></div>	
	<?php comments_template( '', true ); ?>
</div><!-- #content -->
<?php get_footer(); ?>
