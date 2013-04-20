<?php
/**
 * The Sidebar containing the First and Second widget areas.
 *
 * @package Voyage
 * @subpackage voyage1
 * @since Voyage 1.0
 */  ?>
<?php
	global $voyage_options;

	if ( $voyage_options['column_sidebar1'] > 0 ) {
		$sidebar_class = "grid_" . $voyage_options['column_sidebar1'] . ' ';
		if ( $voyage_options['blog_layout'] != "1"  ) {
			$sidebar_class = $sidebar_class . "pull_" . ($voyage_options['column_content'] ) . ' ';
		}
?>	

		<div id="sidebar_one" class="<?php echo $sidebar_class ?> widget-area" role="complementary">
		<ul class="xoxo">		
<?php		if ( is_active_sidebar( 'first-widget-area' ) ) {
				dynamic_sidebar( 'first-widget-area' );	
			}
			elseif (!is_active_sidebar( 'second-widget-area' ) || $voyage_options['column_sidebar2'] == 0 ) { //If no sidebar used at all, show some default widgets
				voyage_default_widgets();				
			}
?>
		</ul>
		</div>
<?php
	}
	// Second Sidebar
	if ( is_active_sidebar( 'second-widget-area' ) && ($voyage_options['column_sidebar2'] > 0) ) {
		$sidebar_class = "grid_" . $voyage_options['column_sidebar2'] . ' ';
		if ( $voyage_options['blog_layout'] == "2"  ) {
			$sidebar_class = $sidebar_class . "pull_" . ($voyage_options['column_content'] ) . ' ';
		}
?>
		<div id="sidebar_two" class="<?php echo $sidebar_class ?> widget-area pull-right" role="complementary">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'second-widget-area' ); ?>
			</ul>
		</div>
<?php
	}
?>	

