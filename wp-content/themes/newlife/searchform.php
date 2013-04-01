<?php
/**
 * The template for displaying search forms in NewLife
 *
 * @subpackage New life
 * @since New life
 */
?>

<div id="search_block_form">
	<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<p><input type="text" name="s" id="s" value="<?php _e('Search ...','newlife');?>"/></p>
	</form>
</div>
