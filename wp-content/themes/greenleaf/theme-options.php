<?php

add_action( 'admin_init', 'greenleaf_theme_options_init' );
add_action( 'admin_menu', 'greenleaf_theme_options_add_page' );

/**
 * Init plugin options to white list our options
*/
function greenleaf_theme_options_init(){
	register_setting( 'greenleaf_options', 'greenleaf_theme_options', 'greenleaf_theme_options_validate' );
}

/**
 * Load up the menu page
*/
function greenleaf_theme_options_add_page() {
	add_theme_page( 'GreenLeaf Theme Options', 'GreenLeaf Theme Options', 'edit_theme_options', 'greenleaf_theme_options', 'greenleaf_theme_options_do_page' );
}

/**
 * Create the options page
*/
function greenleaf_theme_options_do_page() {
	global $greenleaf_radio_options;
	$theme_data = get_theme_data(TEMPLATEPATH . '/style.css');

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . " Theme Options</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong>Options saved.</strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php" style="float: left; width: 70%;">
			<?php settings_fields( 'greenleaf_options' ); ?>
			<?php $options = get_option( 'greenleaf_theme_options' ); ?>

			<table class="form-table">
				<tr valign="top">
					<td colspan="2">
						<h2>Logo Upload</h2>
						<p>Upload logo image or paste logo URL.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Logo:</th>
					<td>
			
						<?php
							$logo = get_option('greenleaf_theme_options_logo');
							if (!empty($logo)) {
						?>
								<script type="text/javascript">
									jQuery(document).ready(function() {
											jQuery('.logoupload').hide();
									});
								</script>
						<?php
							}
						?>		
						
						<input type="text" value="<?php echo $logo;?>" name="greenleaf_theme_options_logo" style="width: 100px; height: 30px;" class="postbox small" />
		
						<span id="<?php echo 'greenleaf_theme_options_logo'?>" class="button upload gd_upload logoupload show">Upload Image</span>
						<span class="button gd_remove" id="remove_greenleaf_theme_options_logo">Remove Image</span>
						<div class="gd_image_preview">
							<img src="<?php echo $logo; ?>" />
						</div>

					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr valign="top">
					<td colspan="2">
						<h2>Home Page Banner</h2>
						<p>Home Page Banner will be hidden if Main Punchline and Headline fields left empty.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Main Punchline:</th>
					<td>
						<input id="greenleaf_theme_options[greenleaf_main_punchline]" class="regular-text" type="text" name="greenleaf_theme_options[greenleaf_main_punchline]" value="<?php esc_attr_e(stripslashes($options['greenleaf_main_punchline'])); ?>" />
					</td>
				</tr>
				<tr valign="top"><th scope="row">Headline:</th>
					<td>
						<textarea id="greenleaf_theme_options[greenleaf_headline]" class="large-text" cols="50" rows="3" name="greenleaf_theme_options[greenleaf_headline]"><?php echo esc_attr(stripslashes($options['greenleaf_headline'])); ?></textarea>
					</td>
				</tr>
			</table>
			
			<table class="form-table">			
				<tr valign="top">
					<td colspan="2">
						<h2>Google Analytics</h2>
						<p>Google Analytics code will NOT be included if Google Analytics ID field left empty.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Google Analytics ID:</th>
					<td>
						<input id="greenleaf_theme_options[ga_code]" class="regular-text" type="text" name="greenleaf_theme_options[ga_code]" value="<?php esc_attr_e(stripslashes($options['ga_code'])); ?>" /><br />
						<label class="description" for="greenleaf_theme_options[ga_code]">Copy and paste your Google Analytics account ID (UA-XXXXXXXX-X) here.</label>
					</td>
				</tr>		
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
		</form>

<style>
.postbox .hndle {
    cursor: auto;
    font-size: 13px;
    margin: 0;
    padding: 6px 10px 7px;
}
.panel-wrap.inside {
    padding: 0 10px 10px;
}
.panel-wrap.inside ul {
    padding-top: 12px;
}
.panel-wrap.inside ul li {
	font-size: 11px;
    padding-bottom: 6px;
}
</style>
		
		<div class="side-wrap" style="float: right; width: 26%; padding-right: 25px;">
		
			<div class="postbox">
				<div><h3 class="hndle"><?php echo get_current_theme(); ?> Theme v<?php echo $theme_data['Version']; ?></h3></div>
				<div class="panel-wrap inside">
					<p>GreenLeaf Theme <a href="http://www.freethemeforwp.com/themes/greenleaf-wordpress-theme/" target="_blank">Home Page</a></p>
					<p>Developed by <a href="http://www.freethemeforwp.com/" target="_blank">Free Theme For WP</a></p>
					<p>Follow us on <a href="http://twitter.com/FreeThemeForWP" target="_blank">Twitter</a> or subscribe to our <a href="http://www.freethemeforwp.com/feed/" target="_blank">RSS</a> feed</p>
				</div>
			</div>
			
			<div class="postbox">
				<div><h3 class="hndle">Support <?php echo get_current_theme(); ?> Theme</h3></div>
				<div class="panel-wrap inside">
					<p>Developing this awesome theme took a lot of time and effort, and months of work. If you like this theme, or if you are using it for commercial website, please consider a donation to help support future updates and development of the <?php echo get_current_theme(); ?> Theme.</p>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align: center;" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
						<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
			
			<div class="postbox">
				<div><h3 class="hndle">Latest News and Updates</h3></div>
				<div class="panel-wrap inside">
					<ul id="twitter_update_list"><li></li></ul>
					<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
					<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/FreeThemeForWP.json?callback=twitterCallback2&amp;count=5"></script>				
				</div>
			</div>
			
		</div>
		
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
*/
function greenleaf_theme_options_validate( $input ) {
	// Say our textarea option must be safe text with the allowed tags for posts
	$input['greenleaf_main_punchline'] = wp_filter_post_kses( $input['greenleaf_main_punchline'] );
	$input['greenleaf_headline'] = wp_filter_post_kses( $input['greenleaf_headline'] );
	$input['ga_code'] = wp_filter_post_kses( $input['ga_code'] );

	return $input;
}


//Save image via AJAX
add_action('wp_ajax_gd_ajax_upload', 'gd_ajax_image_upload'); //Add support for AJAX save

function gd_ajax_image_upload(){
	global $wpdb; //Now WP database can be accessed
	
	
	$image_id=$_POST['data'];
	$image_filename=$_FILES[$image_id];	
	$override['test_form']=false; //see http://wordpress.org/support/topic/269518?replies=6
	$override['action']='wp_handle_upload';    
	
	$uploaded_image = wp_handle_upload($image_filename,$override);
	
	if(!empty($uploaded_image['error'])){
		echo 'Error: ' . $uploaded_image['error'];
	}	
	else{ 
		update_option($image_id, $uploaded_image['url']);		 
		echo $uploaded_image['url'];
	}
			
	die();

}

//Remove image via AJAX
add_action('wp_ajax_gd_ajax_remove', 'gd_ajax_image_remove'); //Add support for AJAX save

function gd_ajax_image_remove(){
	global $wpdb; //Now WP database can be accessed
	
	
	$image_id=$_POST['data'];
	
	$query = "DELETE FROM $wpdb->options WHERE option_name LIKE '$image_id'";
    $wpdb->query($query);
			
	die();

}

?>