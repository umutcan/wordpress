<?php
/**
 * Template Name: Blog (Full Posts)
 *
 * @package Voyage
 * @subpackage Voyage
 * @since 1.1
 */
	get_header();
    
	if ( get_query_var('paged') )
	    $paged = get_query_var('paged');
	elseif ( get_query_var('page') ) 
	    $paged = get_query_var('page');
	else 
		$paged = 1;
	if ( have_posts() && is_page()) {
		the_post();
		$pt_category = get_post_meta($post->ID, '_voyage_category', true);
		$postperpage = get_post_meta($post->ID, '_voyage_postperpage', true);
		$sidebar = get_post_meta($post->ID, '_voyage_sidebar', true);
		
		if ($paged == 1) {
			$content = get_the_content();
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			if (!empty($content)) {
				echo '<div id="blog-intro" class="' . voyage_grid_full() .'">';
				echo '<div class="entry-content clearfix">';
				echo $content;
				echo '</div></div>';			
			}
		}
	}
	else {
		$pt_category = '';
		$sidebar = 1;
		$postperpage = '';
	}
	$blog_args = array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'paged'	=> $paged,				
						);
	if ($postperpage)
		$blog_args['posts_per_page'] = $postperpage;
	if ($pt_category)
		$blog_args['category__in'] = $pt_category;
	$blog = new WP_Query( $blog_args );	
?>
<div id="content" class="<?php echo $sidebar ? voyage_grid_class() : voyage_grid_full(); ?>" role="main">
<?php 
	global $more;

 	if ( $blog->have_posts() ) :
		voyage_content_nav_link( $blog->max_num_pages, 'nav-above' );
		while ( $blog->have_posts() ) :
			$blog->the_post();
			$more = 0;
			get_template_part( 'content', get_post_format() );
		endwhile;				
		voyage_content_nav_link( $blog->max_num_pages, 'nav-below' );
	elseif ( current_user_can( 'edit_posts' ) ) :
		get_template_part( 'content-none', 'index' );
	endif;
	wp_reset_postdata();
?>
</div>
<?php if ($sidebar) get_sidebar(); ?>
<?php get_footer(); ?>
