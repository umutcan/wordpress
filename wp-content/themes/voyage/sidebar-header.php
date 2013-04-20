<?php
/**
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.1.7
 */
?>
<?php
	if ( is_active_sidebar( 'header-widget-area' )  ) { ?>
	  <div id="header-widget" class="pull-right widget-area">
		<ul class="xoxo">
			<?php dynamic_sidebar( 'header-widget-area' ); ?>
		</ul>
	  </div>
<?php
	}
?>

