<?php
/**
 * Voyage Theme Widgets
 *
 * Learn more: http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
class Voyage_Recent_Post extends WP_Widget {

	// Constructor
	function Voyage_Recent_Post() {
		$widget_ops = array( 'classname' => 'voyage_recent_post', 'description' => __( 'Use this widget to list your recent post summary', 'voyage' ) );
		$this->WP_Widget( 'widget_voyage_recent_post', __( '(Voyage) Recent Posts', 'voyage' ), $widget_ops );
		$this->alt_option_name = 'widget_voyage_recent_post';
		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	// Widget outputs
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_voyage_recent_post', 'widget' );
		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '10';
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;
			
		if ( ! isset( $instance['column'] ) )
			$instance['column'] = '1';
		if ( ! $column = absint( $instance['column'] ) )
 			$column = 1;

		if ( ! isset( $instance['category'] ) )
			$category = 0;
		else 
			$category = absint($instance['category']);

		if ( ! isset( $instance['sticky_post'] ) )
			$sticky = get_option( 'sticky_posts' );
		elseif ( $instance['sticky_post'] == '1' )
			$sticky = array();
		else
			$sticky = get_option( 'sticky_posts' );

		if ( ! isset( $instance['random_post'] ) )
			$sortby = '';
		elseif ( $instance['random_post'] == '1' )
			$sortby = 'rand';
		else
			$sortby = '';

		global $voyage_display_excerpt;
		if ( ! isset( $instance['display_excerpt'] ) )
			$voyage_display_excerpt = 1;
		else
			$voyage_display_excerpt = (int)$instance['display_excerpt'];

		global $voyage_entry_meta;		
		if ( ! isset( $instance['entry_meta'] ) )
			$voyage_entry_meta = 0;
		else
			$voyage_entry_meta = (int)$instance['entry_meta'];	

		if ( ! isset( $instance['category_link'] ) )
			$category_link = 0;
		else
			$category_link = (int)$instance['category_link'];	
				
		if ( ! isset( $instance['thumbnail'] ) )
			$thumbnail = 1;
		else 
			$thumbnail = absint($instance['thumbnail']);
		if ( ! isset( $instance['thumbnail_x'] ) )
			$instance['thumbnail_x'] = '64';
		if ( ! $thumbnail_x = absint( $instance['thumbnail_x'] ) )
 			$thumbnail_x = 64;
		if ( ! isset( $instance['thumbnail_y'] ) )
			$instance['thumbnail_y'] = '64';
		if ( ! $thumbnail_y = absint( $instance['thumbnail_y'] ) )
 			$thumbnail_y = 64;

		$query_str = array(
			'order' => 'DESC',
			'orderby' => $sortby,
			'posts_per_page' => $number,
			'post_status' => 'publish',
			'post_type' => 'post',
			'category__in'   => $category,
			'post__not_in' => $sticky,
			'ignore_sticky_posts' => 1,
			'no_found_rows' => 1,
		);

		$recent_posts = new WP_Query( $query_str );

		if ( $recent_posts->have_posts() ) :
			echo $before_widget; 
			echo '<div class="clear"></div>';
			if (!empty($title) ) {
				echo $before_title;
				echo $title; // Can set this with a widget option, or omit altogether
				echo $after_title;			
				if ($category_link == 1 && $category > 0) {
			
					printf('<a href="%1$s" title="%2$s" class="voyage_recent_post_link btn btn-small btn-transparent">%3$s</a>',
						get_category_link( $category ) ,
						get_the_category_by_ID( $category ),
						__('See All','voyage') );					
				}	
			}

			global $voyage_thumbnail;
			
			$voyage_thumbnail = voyage_thumbnail_size($thumbnail, $thumbnail_x, $thumbnail_y);
			$col = 0;
			while ( $recent_posts->have_posts() ) : 
				$recent_posts->the_post();
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
					echo '<div class="' . $div_class .'">';
				get_template_part( 'content', 'summary' );
				
				if  ($column > 1) {
					echo '</div>';				
					if ($col == 0)
						echo '<div class="clear"></div>';
				}
			endwhile;
			
			if ($col > 0)
				echo '<div class="clear"></div>';
			echo $after_widget;
			// Reset the post globals as this query will have stomped on it
			wp_reset_postdata();
		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_voyage_recent_post', $cache, 'widget' );
	}

	// Update options
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$col = (int) $new_instance['column'];
		if ($col > 4)
			$col = 4;
		if ($col <1 )
			$col = 1;
		$instance['column'] = $col;
		$instance['category'] =  (int) $new_instance['category'];
		$instance['sticky_post'] =  (int) $new_instance['sticky_post'];
		$instance['random_post'] =  (int) $new_instance['random_post'];
		$instance['entry_meta'] =  (int) $new_instance['entry_meta'];
		$instance['category_link'] =  (int) $new_instance['category_link'];
		$instance['display_excerpt'] =  $new_instance['display_excerpt'];
		$instance['thumbnail'] = $new_instance['thumbnail'];
		$size = (int) $new_instance['thumbnail_x'];
		if ($size < 1)
			$size = 64;
		$instance['thumbnail_x'] = $size;
		$size = (int) $new_instance['thumbnail_y'];
		if ($size < 1)
			$size = 64;
		$instance['thumbnail_y'] = $size;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_voyage_recent_post'] ) )
			delete_option( 'widget_voyage_recent_post' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_voyage_recent_post', 'widget' );
	}

	// Display options
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
		$category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : 0;
		$sticky_post = isset( $instance['sticky_post'] ) ? esc_attr( $instance['sticky_post'] ) : 0;
		$random_post = isset( $instance['random_post'] ) ? esc_attr( $instance['random_post'] ) : 0;		
		$column = isset( $instance['column'] ) ? absint( $instance['column'] ) : 1;
		$thumbnail = isset( $instance['thumbnail'] ) ? absint( $instance['thumbnail'] ) : 1;
		$thumbnail_x = isset( $instance['thumbnail_x'] ) ? absint( $instance['thumbnail_x'] ) : 64;
		$thumbnail_y = isset( $instance['thumbnail_y'] ) ? absint( $instance['thumbnail_y'] ) : 64;
		$display_excerpt = isset( $instance['display_excerpt'] ) ? esc_attr( $instance['display_excerpt'] ) : 1;
		$entry_meta = isset( $instance['entry_meta'] ) ? esc_attr( $instance['entry_meta'] ) : 0;	
		$category_link = isset( $instance['category_link'] ) ? esc_attr( $instance['category_link'] ) : 0;	

?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'voyage' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'voyage' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
		
		<p><input id="<?php echo esc_attr( $this->get_field_name( 'random_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'random_post' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $random_post ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'random_post' ) ); ?>"><?php _e( 'Random Posts', 'voyage' ); ?></label></p>
		
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'column' ) ); ?>"><?php _e( 'No of Columns (1-4):', 'voyage' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'column' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'column' ) ); ?>" type="text" value="<?php echo esc_attr( $column ); ?>" size="3" /></p>
		
		<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Category:', 'voyage' ); ?></label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
		
		<?php if ( $category == '0' )
				$selected = 'selected="selected"';
			  else 
			  	$selected = ''; ?>
				
			<option value="<?php echo "0"; ?>" <?php echo $selected; ?>><?php _e('All Categories','voyage'); ?></option>
		<?php
			$selected = '';
			foreach ( voyage_categories() as $option ) {
				if ( $category == $option->term_id ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				} ?>
				<option value="<?php echo $option->term_id; ?>" <?php echo $selected; ?>><?php echo $option->name; ?></option>
		<?php } ?>
		</select>
		
		<p><input id="<?php echo esc_attr( $this->get_field_name( 'sticky_post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sticky_post' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $sticky_post ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'sticky_post' ) ); ?>"><?php _e( 'include sticky posts in the category', 'voyage' ); ?></label></p>

		<p><label><b><?php _e('Thumbnail: ','voyage'); ?></b></label>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="1" <?php checked( '1', $thumbnail ); ?> /><?php _e( 'Thumbnail', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="2" <?php checked( '2', $thumbnail ); ?> /><?php _e( 'Medium<br>', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="3" <?php checked( '3', $thumbnail ); ?> /><?php _e( 'Large', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="4" <?php checked( '4', $thumbnail ); ?> /><?php _e( 'Full', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="5" <?php checked( '5', $thumbnail ); ?> /><?php _e( 'Custom', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" type="radio" value="6" <?php checked( '6', $thumbnail ); ?> /><?php _e( 'None', 'voyage' ); ?></p>

		<p><label><?php _e( 'Custom size: ', 'voyage' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_x' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_x' ) ); ?>" type="text" value="<?php echo esc_attr( $thumbnail_x ); ?>" size="3" /><label><?php _e( ' x ', 'voyage' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'thumbnail_y' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail_y' ) ); ?>" type="text" value="<?php echo esc_attr( $thumbnail_y ); ?>" size="3" /></p>

		<p><label><b><?php _e('Intro Text: ','voyage'); ?></b></label>
		<input id="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" type="radio" value="1" <?php checked( '1', $display_excerpt ); ?> /><?php _e( 'Excerpt', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" type="radio" value="2" <?php checked( '2', $display_excerpt ); ?> /><?php _e( 'Content', 'voyage' ); ?>
		<input id="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" type="radio" value="3" <?php checked( '3', $display_excerpt ); ?> /><?php _e( 'None', 'voyage' ); ?></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'entry_meta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'entry_meta' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $entry_meta ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'entry_meta' ) ); ?>"><?php _e( 'Display post meta', 'voyage' ); ?></label></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'category_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category_link' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $category_link ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'category_link' ) ); ?>"><?php _e( 'Check to show link to category archive', 'voyage' ); ?></label></p>
		<?php
	}
}

class Voyage_Navigation extends WP_Widget {

	// Constructor
	function Voyage_Navigation() {
		$widget_ops = array( 'classname' => 'voyage_navigation', 'description' => __( 'Navigation Tabs (Voyage)', 'voyage' ) );
		$this->WP_Widget( 'widget_voyage_navigation', __( '(Voyage) Navigation Tabs', 'voyage' ), $widget_ops );
		$this->alt_option_name = 'widget_voyage_navigation';
		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	// Widget outputs
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_voyage_navigation', 'widget' );
		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		if ( ! isset( $instance['category'] ) )
			$category = 1;
		else 
			$category = absint($instance['category']);
		if ( ! isset( $instance['archive'] ) )
			$archive = 1;
		else 
			$archive = absint($instance['archive']);
		if ( ! isset( $instance['recent'] ) )
			$recent = 1;
		else 
			$recent = absint($instance['recent']);
		if ( ! isset( $instance['tag'] ) )
			$tag = 1;
		else 
			$tag = absint($instance['tag']);

		echo $before_widget; 

		if (!empty($title) ) {
			echo $before_title;
			echo $title; // Can set this with a widget option, or omit altogether
			echo $after_title;
		} ?>
			

        <ul id="vntTab" class="nav nav-tabs">
			<?php $active = ' class="active"'; ?>
			<?php if ($category == 1) : ?>
        		<li<?php echo $active; $active = ''; ?>><a href="#category_<?php echo $args['widget_id'] ?>" data-toggle="tab"><?php _e('Categories','voyage'); ?></a></li>
			<?php endif; ?>
 			<?php if ($archive == 1) : ?>
            <li<?php echo $active; $active = ''; ?>><a href="#archive_<?php echo $args['widget_id'] ?>" data-toggle="tab"><?php _e('Archives','voyage'); ?></a></li>
			<?php endif; ?>
 			<?php if ($recent == 1) : ?>
            <li<?php echo $active; $active = ''; ?>><a href="#recent_<?php echo $args['widget_id'] ?>" data-toggle="tab"><?php _e('Recent','voyage'); ?></a></li>
			<?php endif; ?>
 			<?php if ($tag == 1) : ?>
            <li<?php echo $active; $active = ''; ?>><a href="#tag_<?php echo $args['widget_id'] ?>" data-toggle="tab"><?php _e('Tags','voyage'); ?></a></li>
			<?php endif; ?>
        </ul>
        <div id="vntTabContent" class="tab-content">
			<?php $active = " in active"; ?>
			<?php if ($category == 1) : ?>
        	<div class="widget_categories tab-pane fade <?php echo $active; $active = ''; ?>" id="category_<?php echo $args['widget_id'] ?>">
				<ul>
				<?php
					$cat_args = array();
					$cat_args['show_count'] = 1;
					$cat_args['title_li'] = '';
					$cat_args['exclude'] = 1;
					wp_list_categories( $cat_args ); ?>
				</ul>
            </div>
			<?php endif; ?>
 			<?php if ($archive == 1) : ?>
            <div class="widget_archive tab-pane fade <?php echo $active; $active = ''; ?>" id="archive_<?php echo $args['widget_id'] ?>">
				<ul>
				<?php
					$arc_args = array();
					$arc_args['type'] = 'monthly';
					$arc_args['show_post_count'] = true;					
					$arc_args['limit'] = 10;
					wp_get_archives( $arc_args ); ?>
				</ul>
            </div>
			<?php endif; ?>
 			<?php if ($recent == 1) : ?>
            <div class="widget_recent_entries tab-pane fade <?php echo $active; $active = ''; ?>" id="recent_<?php echo $args['widget_id'] ?>">
				<ul>
				<?php
					$rec_args = array();
					$rec_args['numberposts'] = 10;
					$rec_args['post_status'] = 'publish';
					$recent_posts = wp_get_recent_posts( $rec_args ); 
					foreach( $recent_posts as $recent ){
						echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="Look '.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
					}
				?>
				</ul>
            </div>
			<?php endif; ?>
 			<?php if ($tag == 1) : ?>
            <div class="widget_tag_cloud tab-pane fade <?php echo $active; $active = ''; ?>" id="tag_<?php echo $args['widget_id'] ?>">
				<ul>
				<?php
					$tag_args = array();
					wp_tag_cloud( $tag_args ); 
				?>
				</ul>
            </div>
			<?php endif; ?>
        </div>

		<?php echo $after_widget;
		// Reset the post globals as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_voyage_navigation', $cache, 'widget' );
	}

	// Update options
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] =  (int) $new_instance['category'];
		$instance['archive'] =  (int) $new_instance['archive'];
		$instance['recent'] =  (int) $new_instance['recent'];
		$instance['tag'] =  (int) $new_instance['tag'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_voyage_navigation'] ) )
			delete_option( 'widget_voyage_navigation' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_voyage_navigation', 'widget' );
	}

	// Display options
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : 1;
		$archive = isset( $instance['archive'] ) ? esc_attr( $instance['archive'] ) : 1;
		$recent = isset( $instance['recent'] ) ? esc_attr( $instance['recent'] ) : 0;
		$tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : 1;
		
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'voyage' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $category ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Categories Tab', 'voyage' ); ?></label></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'archive' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'archive' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $archive ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'archive' ) ); ?>"><?php _e( 'Archives Tab', 'voyage' ); ?></label></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'recent' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'recent' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $recent ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'recent' ) ); ?>"><?php _e( 'Recent Posts Tab', 'voyage' ); ?></label></p>

		<p><input id="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $tag ); ?> />
		<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"><?php _e( 'Tags Tab', 'voyage' ); ?></label></p>
		
		<?php
	}
}

?>
