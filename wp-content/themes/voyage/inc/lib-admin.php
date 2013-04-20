<?php 
if ( !defined('ABSPATH')) exit;
/**
 * Voyage Theme functions: Admin related functions
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.2.7
 */
//Add meta boxes to page/post
function voyage_meta_box() {
	global $voyage_meta_box;
	
	$voyage_meta_box['page'] = array( 
		'id' => 'voyage-page-meta',
		'title' => __('Template Options (Voyage)', 'voyage'),  
		'context' => 'side',  //normal, advaned, side  
		'priority' => 'low', //high, core, default, low
		'fields' => array(
        	array(
            	'name' => __('Post Category :','voyage'),
            	'desc' => '',
            	'id' => '_voyage_category',
            	'type' => 'category',
            	'default' => ''
        	),
        	array(
            	'name' => __('Posts per page :', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_postperpage',
            	'type' => 'number',
            	'default' => '',
        	),
        	array(
            	'name' => __('Sidebar :', 'voyage'),
            	'desc' => __('check to show sidebar<hr>Note: Some options may not be relevant for this page template.','voyage'),
            	'id' => '_voyage_sidebar',
            	'type' => 'checkbox',
            	'default' => '',
        	),
        	array(
            	'name' => __('Layout :', 'voyage'),
            	'desc' => __('Columns','voyage'),
            	'id' => '_voyage_column',
            	'type' => 'select',
            	'default' => '',
				'options' => array( 
								array( 'value' => '1',
									   'name' => '1' ),
								array( 'value' => '2', 
									   'name' => '2' ),
								array( 'value' => '', //Dedault
									   'name' => '3' ),
								array( 'value' => '4', 
									   'name' => '4' ),
							 ),
        	),
        	array(
            	'name' => __('Display Thumbnail : <br />', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_thumbnail',
            	'type' => 'radio',
            	'default' => '',
				'options' => array( 
								array( 'value' => '',
									   'name' => __('Thumbnail<br />','voyage') ),
								array( 'value' => '2', 
									   'name' => __('Mediumn<br />','voyage') ),
								array( 'value' => '3', 
									   'name' => __('Large<br />','voyage') ),
								array( 'value' => '5', //4 is Full
									   'name' => __('Custom<br />','voyage') ),
								array( 'value' => '6', 
									   'name' => __('None','voyage') ),
							 ),
        	),
        	array(
            	'name' => __('Custom Size (Width) :', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_size_x',
            	'type' => 'number',
            	'default' => '',
        	),
        	array(
            	'name' => __('Custom Size (Height) :', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_size_y',
            	'type' => 'number',
            	'default' => '',
        	),
        	array(
            	'name' => __('Intro Text : <br />', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_intro',
            	'type' => 'radio',
            	'default' => '',
				'options' => array( 
								array( 'value' => '',
									   'name' => __('Excerpt<br />','voyage') ),
								array( 'value' => '2', 
									   'name' => __('Content<br />','voyage') ),
								array( 'value' => '3', 
									   'name' => __('None<br />','voyage') ),
							 ),
        	),
        	array(
            	'name' => __('Post Meta :', 'voyage'),
            	'desc' => __('check to display post meta','voyage'),
            	'id' => '_voyage_disp_meta',
            	'type' => 'checkbox',
            	'default' => '',
        	),
    	)
	);
	$voyage_meta_box['post'] = array( 
		'id' => 'voyage-post-meta',
		'title' => __('Voyage Post Options', 'voyage'),  
		'context' => 'side',  //normal, advaned, side  
		'priority' => 'high', //high, core, default, low
		'fields' => array(
        	array(
            	'name' => __('Layout :', 'voyage'),
            	'desc' => '',
            	'id' => '_voyage_layout',
            	'type' => 'select',
            	'default' => '',
				'options' => array( 
								array( 'value' => '', //Dedault
									   'name' => 'Default' ),
								array( 'value' => '1', 
									   'name' => 'Fullwidth' ),
							 ),
        	),
    	)
	);

    foreach($voyage_meta_box as $post_type => $value) {
        add_meta_box($value['id'], $value['title'], 'voyage_meta_display', $post_type, $value['context'], $value['priority']);
    }
}
add_action('admin_menu', 'voyage_meta_box');

//Display Meta Box
function voyage_meta_display() {
  global $voyage_meta_box, $post;
 
  // Use nonce for verification
  echo '<input type="hidden" name="voyage_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
  foreach ($voyage_meta_box[$post->post_type]['fields'] as $field) {
      // get current post meta data
      $meta = get_post_meta($post->ID, $field['id'], true);
 
      echo '<p><strong>' . $field['name'] . ' </strong>';
      switch ($field['type']) {
          case 'text':
              echo '<input type="text" name="'. $field['id']. '" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['default']) . '" size="30" />';
              break;
          case 'textarea':
              echo '<textarea name="'. $field['id']. '" id="'. $field['id']. '" cols="60" rows="4" >'. ($meta ? $meta : $field['default']) . '</textarea>'. '<br />'. $field['desc'];
              break;
          case 'number':
              echo '<input type="text" name="'. $field['id']. '" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['default']) . '" size="4" />';
              break;
          case 'select':
              echo '<select name="'. $field['id'] . '" id="'. $field['id'] . '">';
              foreach ($field['options'] as $option) {
                  echo '<option value="' . $option['value']. '" ' . ( $meta == $option['value'] ? ' selected="selected"' : '' ) . '>'. $option['name'] . '</option>';
              }
              echo '</select> ' . $field['desc'];
              break;
          case 'category':
              echo '<select name="'. $field['id'] . '" id="'. $field['id'] . '">';		  
              echo '<option value="" ' . ( $meta ? '' : 'selected="selected"' ) . '>'
					. __('All Categories','voyage') . '</option>';
						  
              foreach (voyage_categories()  as $category) {
                  echo '<option value="' . $category->term_id . '" '. ( $meta == $category->term_id ? ' selected="selected"' : '' ) . '>'. $category->name . '</option>';
              }
              echo '</select>';
              break;
          case 'radio':
              foreach ($field['options'] as $option) {
                  echo '<input type="radio" name="' . $field['id'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . ' /> ' . $option['name'] . ' ';
              }
              break;
          case 'checkbox':
              echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" value="1"' . ( $meta ? ' checked="checked"' : '' ) . ' /> ' . $field['desc'];;
              break;
      }
	  echo '</p>';
  }
 
}
// Save data from meta box
function voyage_meta_save($post_id) {
    global $voyage_meta_box,  $post;
    
    //Verify nonce
    if ( !isset($_POST['voyage_meta_box_nonce']) )
		return $post_id;
	
	if (!wp_verify_nonce($_POST['voyage_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
 
    //Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
    //Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    foreach ($voyage_meta_box[$post->post_type]['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
		if (isset($_POST[$field['id']]) ) {
			$new = $_POST[$field['id']];
			if ($field['type'] == 'number') {
				$new = (int)$new;
			}			
		}
		else {
        	$new = '';			
		}

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}
add_action('save_post', 'voyage_meta_save');

if ( ! function_exists( 'voyage_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @since Voyage 1.0
 */
function voyage_admin_header_style() {
?>
<style type="text/css">

.appearance_page_custom-header #headimg {
	background-repeat:no-repeat;
	border: none;
}
#headimg h1,
#desc {
	font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
}
#headimg h1 {
	margin: 0;
}
#headimg h1 a {
	font-size: 32px;
	line-height: 36px;
	text-decoration: none;
}
#desc {
	font-size: 14px;
	line-height: 23px;
	padding: 0 0 3em;
}
<?php
		// If the user has set a custom color for the text use that
	if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
?>
	#site-title a,
	#site-description {
		color: #<?php echo get_header_textcolor(); ?>;
	}
<?php endif; ?>
</style>
<?php
}
endif;

if ( ! function_exists( 'voyage_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in voyage_setup().
 *
 * @since Voyage 1.0
 */
function voyage_admin_header_image() { ?>
<div id="headimg">
<?php
	$color = get_header_textcolor();
	$image = get_header_image();
	if ( $color && $color != 'blank' )
		$style = ' style="color:#' . $color . '"';
	else
		$style = ' style="display:none"';
?>
	<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="" />
	<?php endif; ?>
	</div>
<?php }
endif; // voyage_admin_header_image
?>
