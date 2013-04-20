<?php
/**
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.2.2
 */
?>
<?php
	if ( is_active_sidebar( 'nav-widget-area' )  ) { ?>
	  <div id="nav-widget" class="widget-area">
		<ul class="xoxo">
			<?php dynamic_sidebar( 'nav-widget-area' ); ?>
		</ul>
	  </div>
<?php
	}
?>

