<?php
/**
 * Template Name: Blog Summary/Portfolio
 *
 * @package Voyage
 * @subpackage Voyage
 * @since 1.1.9
 */
	get_header();

    if ( get_query_var('paged') )
	    $paged = get_query_var('paged');
	elseif ( get_query_var('page') ) 
	    $paged = get_query_var('page');
	else 
		$paged = 1;
	
	global $voyage_thumbnail, $voyage_display_excerpt, $voyage_entry_meta;	
	
	if ( have_posts() && is_page()) {
		the_post();
		$pt_category = get_post_meta($post->ID, '_voyage_category', true);

		$column = get_post_meta($post->ID, '_voyage_column', true);
		if ('' == $column)
			$column = 3;

		$postperpage = get_post_meta($post->ID, '_voyage_postperpage', true);
		if ($postperpage < $column)
			$postperpage = 12;

		$pt_thumbnail = get_post_meta($post->ID, '_voyage_thumbnail', true);
		$pt_size_x = get_post_meta($post->ID, '_voyage_size_x', true);
		$pt_size_y = get_post_meta($post->ID, '_voyage_size_y', true);
			
		$voyage_thumbnail = voyage_thumbnail_size($pt_thumbnail, $pt_size_x, $pt_size_y);

		$voyage_display_excerpt = get_post_meta($post->ID, '_voyage_intro', true);
		if ('' == $voyage_display_excerpt)
			$voyage_display_excerpt = 1;

		$voyage_entry_meta = get_post_meta($post->ID, '_voyage_disp_meta', true);
		$sidebar = get_post_meta($post->ID, '_voyage_sidebar', true);
		
		$content = get_the_content();
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		if (!empty($content)) {
			echo '<div id="portfolio-intro" class="' . voyage_grid_full() .'">';
			echo '<div class="entry-content clearfix">';
			echo $content;
			echo '</div></div>';			
		}
	}
	else {
		$pt_category = '';
		$voyage_display_excerpt = 1;
		$column = 1;
		$postperpage = 0;
		$voyage_thumbnail = 'thumbnail';
		$voyage_entry_meta = 1;
		$sidebar = 1;
	}

?>  
<div id="content" class="<?php echo $sidebar ? voyage_grid_class() : voyage_grid_full(); ?> voyage_recent_post" role="main">
<?php 
	$blog_args = array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'paged'	=> $paged,
						'posts_per_page' => $postperpage,
						'ignore_sticky_posts' => 1,
						);
	if ($pt_category) {
		$blog_args['category__in'] = $pt_category;
	}
	$blog = new WP_Query( $blog_args );	
	if ( $blog->have_posts() ) :
		voyage_content_nav_link( $blog->max_num_pages, 'nav-above' );
		$col = 0;
		while ( $blog->have_posts() ) : $blog->the_post();
			$div_class = '';
			if ($column == 2) {
				$div_class = "one_half ";
				if ($col == 0)
					$div_class .= "alpha";
				else
					$div_class .= "omega";
				$col = $col + 1;
				if ($col == 2)
					$col = 0;
			}
			elseif ($column == 3) {
				$div_class = "one_third ";
				if ($col == 0)
					$div_class .= "alpha";
				elseif ($col == 2)
					$div_class .= "omega";
				$col = $col + 1;
				if ($col == 3)
					$col = 0;
			}
			elseif ($column == 4) {
				$div_class = "one_quarter ";
				if ($col == 0)
					$div_class .= "alpha";
				elseif ($col == 3)
					$div_class .= "omega";
				$col = $col + 1;
				if ($col == 4)
					$col = 0;
			}

			if  ($column > 1)
				echo '<div class="' . $div_class .' post-grid">';
			get_template_part( 'content', 'summary' );
				
			if  ($column > 1) {
				echo '</div>';				
				if ($col == 0)
					echo '<div class="clear"></div>';
			}
		endwhile;				
		echo '<div class="clear"></div>';					
		voyage_content_nav_link( $blog->max_num_pages, 'nav-below' );
	endif;	
	wp_reset_postdata();
?>						
</div>
<?php if ($sidebar) get_sidebar(); ?>
<?php get_footer(); ?>
