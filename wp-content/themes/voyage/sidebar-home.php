<?php
/** The home widget area is used for featured page templaye
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>

<?php
	global $voyage_options;	 
	if (  $voyage_options['column_home1'] == 0 && $voyage_options['column_home2'] == 0 &&
		$voyage_options['column_home3'] == 0 && $voyage_options['column_home4'] == 0 )
		return;
	$flag = 1;
	// If we get this far, we have widgets. Let do this.
?>
<div id="home-widget-area" class="clearfix">
	<?php if ( is_active_sidebar( 'first-home-widget-area' ) && $voyage_options['column_home1'] > 0 ) : $flag = 0; ?>
		<div id="first-home" class="<?php echo voyage_grid_columns($voyage_options['column_home1']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'first-home-widget-area' ); ?>
			</ul>
		</div><!-- #first .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'second-home-widget-area' ) && $voyage_options['column_home2'] > 0) : $flag = 0; ?>
		<div id="second-home" class="<?php echo voyage_grid_columns($voyage_options['column_home2']); ?> widget-area">	
			<ul class="xoxo">
				<?php dynamic_sidebar( 'second-home-widget-area' ); ?>
			</ul>
		</div><!-- #second .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'third-home-widget-area' ) && $voyage_options['column_home3'] ) : $flag = 0; ?>
		<div id="third-home" class="<?php echo voyage_grid_columns($voyage_options['column_home3']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'third-home-widget-area' ); ?>
			</ul>
		</div><!-- #third .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'fourth-home-widget-area' ) && $voyage_options['column_home4'] ) : $flag = 0; ?>
		<div id="fourth-home" class="<?php echo voyage_grid_columns($voyage_options['column_home4']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'fourth-home-widget-area' ); ?>
			</ul>
		</div><!-- #fourth .widget-area -->
	<?php endif; ?>
</div>
<?php 
	if ($flag) { //No widget in home page, display post summary in column.
 		get_template_part( 'page-templates/blog'  ); 	
	}
?>
