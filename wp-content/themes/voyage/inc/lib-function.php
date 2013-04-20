<?php 
if ( !defined('ABSPATH')) exit;
/**
 * Voyage Theme functions: Functions that extends WordPress Functions
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.1
 */

function voyage_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	$title .= get_bloginfo( 'name' );
	
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'voyage' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'voyage_wp_title', 10, 2 );

/* Content Filter: Remove image from post*/
function remove_images( $content ) {
   $postOutput = preg_replace('/<img[^>]+./','', $content);
   return $postOutput;
}

/* The following function extends wp_nav_menu class for the dropdown menu */
function voyage_hasSub($menu_item_id, &$items) {
	foreach ($items as $item) {
		if ($item->menu_item_parent && $item->menu_item_parent==$menu_item_id) {
			return true;
		}
    }
	return false;
};

function voyage_parant_menu_class($items) {
    foreach ($items as $item) {
        if (voyage_hasSub($item->ID, $items)) {
            $item->classes = array_merge( array('parent','dropdown'), $item->classes); 
        }
    }
    return $items;    
}
add_filter('wp_nav_menu_objects', 'voyage_parant_menu_class');

class voyage_walker_nav_menu extends Walker_Nav_Menu {
 
// add classes to ul sub-menus
function start_lvl( &$output, $depth ) {
    // depth dependent classes
    $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
    $display_depth = ( $depth + 1); // because it counts the first submenu as 0
    $classes = array(
        'dropdown-menu',
        'menu-depth-' . $display_depth
        );
    $class_names = implode( ' ', $classes );
  
    // build html
    $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
}
  
// add main/sub classes to li's and links
 function start_el( &$output, $item, $depth, $args ) {
    global $wp_query;
    $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
  
    // depth dependent classes
    $depth_classes = array(
        ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
        ( ($depth >=1 && $item->classes[0] == "parent") ? 'dropdown-submenu' : '' ),
        'menu-item-depth-' . $depth
    );
    $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
  
    // passed classes
    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
  
    // build html
	if ($item->title == "-" ) {
		$attributes = '';
		if ($depth == 0)
		    $output .= $indent . '<li class="divider-vertical">';
		else
		    $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' divider">';
		$item_output = "";
	}
	else {
    $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
  
    // link attributes
    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
	$link_after =  $args->link_after;
	if ( $depth ==0 && $item->classes[0] == "parent") {
		$link_after = '</a><a class="dropdown-toggle" data-toggle="dropdown" href="#"><b class="caret"></b>';
	}
		  
    $attributes .= ' class="menu-link"';
    $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
        $args->before,
        $attributes,
        $args->link_before,
        apply_filters( 'the_title', $item->title, $item->ID ),
        $link_after ,
        $args->after
    );		
	}  
    // build html
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
}

} // Class voyage_walker_nav_menu

/* Category Array */
function voyage_categories() {
	$category = get_categories();
	return apply_filters( 'voyage_categories', $category );
}	

function voyage_thumbnail_size($option, $x = 96, $y = 96) {
	switch ($option) {
		case 2:
			return 'medium';
		case 3:
			return 'large';
		case 4:
			return 'full';
		case 5:
			if (($x > 0) && ($y > 0) ) {
				return array( $x, $y);
			}
			else
				return 'thumbnail';
		case 6:
			return 'none';
		default:
			return 'thumbnail';
	}	
}

/**
 * Returns the theme options array.
 */
function voyage_get_options() {
	return wp_parse_args( get_option( 'voyage_theme_options' ), voyage_default_theme_options());
}

/**
 * Returns the default theme options.
 * @since Voyage
 */
function voyage_default_theme_options() {
	$default_theme_options = array(
		'grid_pixel' => 960,
		'grid_column' => 12,
		'blog_layout' => 1, //Right Sidebar
		'grid_style' => 1, //Fluid
		'mobile_style' => 1, //Responsive
		'column_content'  => 8,
		'column_sidebar1' => 4,
		'column_sidebar2' => 4,
		'column_home1' => 4,
		'column_home2' => 4,
		'column_home3' => 4,
		'column_home4' => 0,
		'column_footer1' => 3,
		'column_footer2' => 3,
		'column_footer3' => 3,
		'column_footer4' => 3,
		'showauthor' => 1, //Show author
		'showdate' => 1, //Show author
		'voyage_inline_css' => '',
		'sharesocial' => 0,	
		'share_sum_top' => 0,
		'share_sum_bottom' => 0,
		'share_top' => 0,
		'share_bottom' => 1,
		'sociallink_top' => 1,
		'sociallink_middle' => 0,
		'sociallink_bottom' => 0,
		'sociallink_text' => __('Follow Us','voyage'),		
		'url_vimeo' => '',
		'url_youtube' => '',
		'url_facebook' => '',
		'url_linkedin' => '',
		'url_twitter' => '',
		'url_gplus' => '',
		'url_flickr' => '',
		'url_instagram' => '',
		'url_rss' => get_bloginfo('rss2_url'),	
		'currenttab' => 0,
		'homepage' => 2, //Landing Page
		'fp_image' => 1, //Normal		
		'headline' => __( 'Hello, World!', 'voyage' ),		
		'tagline' => __( 'This is your landing page. The headline, tagline and media content can be changed in theme options.', 'voyage' ),		
		'mediacontent' => '<img src="' . get_template_directory_uri() . '/images/theme-landing.png" alt="" />',			
		'actionlabel' => __( 'Learn More', 'voyage' ),		
		'actionurl' => '',
		'fp_interval' => 8, //seconds
		'fp_effect' => 2, //Slide
		'fp_height' => 300, //pixels
		'fp_postnum' => 10,	//posts
		'fp_category' => 0,	//All Categories		
		'fp_sticky' => 1, //Sticky post only
		'fp_headline' => 0, //No headlines
		'fp_action' => __( 'Learn More', 'voyage' ),
		'fb_height' => 300, //pixels
		'fixed_menu' => 0,
		'logopos' => 1, //Inside Navbar
		'searchform' => 1, //Insude Navbar
		'navbarcolor' => 0,
		'nonavbar' => 0,
		'ao_pprint' => 1,
		'ao_colorbox' => 1,
		'pp_commoff' => 0,
		'colorscheme' => 0,
		'schemecss' => '',
		'sl_topicon' => 1, //16x16
		'sl_middleicon' => 2, //24x24
		'sl_bottomicon' => 2, //24x24
		'bodyfont' => 0,
		'sitetitlefont' => 900,
		'sitedescfont' => 103,
		'entrytitlefont' => 0,
		'headingfont' => 0,
		'widgettitlefont' => 0,
		'sidebarfont' => 0,
		'footerfont' => 0,
		'mainmenufont' => 0,
		'otherfont1' => '',
		'otherfont2' => '',
		'otherfont3' => '',
		'otherfont4' => '',		
	);
/*	$theme_options = voyage_theme_options_array();
	foreach ($theme_options as $theme_option) {
		$default_theme_options[$theme_option['name']] = $theme_option['default'];
	}*/
	return apply_filters( 'voyage_default_theme_options', $default_theme_options );
}
/**
 * Grid CSS and Custom CSS
 */
function voyage_custom_css() {
	global $voyage_options, $voyage_fonts;

//	$voyage_options = voyage_get_options();
	echo '<!-- Voyage CSS Style (Theme Options) -->' . "\n";
    echo '<style type="text/css" media="screen">' . "\n";
	
    if ($voyage_options['grid_pixel'] != 960 ) {
        echo '.container_12, .container_16 {' . "\n";
		echo ' width: ' . $voyage_options['grid_pixel'] . "px; }\n";
		
		global $content_width;
		$content_width = $voyage_options['grid_pixel'];
	}
	
	// Fluid
    if ($voyage_options['grid_style'] == "1" ) {
        echo '@media screen and (max-width: ' . $voyage_options['grid_pixel'] . "px ){";
		echo '.container_12, .container_16 { width: auto } }' . "\n";
	}
    else {
		if ($voyage_options['mobile_style'] == "1" ) {
			echo "@media screen and (max-width: 767px) {" . "\n";
			echo '.container_12, .container_16 { width: auto } }' . "\n";
		}
		else {
	        echo '#wrapper {' . "\n";
			echo ' min-width: ' . $voyage_options['grid_pixel'] . "px; }\n";
		}
	} 
	
	if ($voyage_options['fp_height'] > 0 ) {
    	echo '.featured .carousel-inner .small-thumbnail,' . "\n";	
    	echo '.featured .carousel-inner .no-thumbnail,' . "\n";	
		echo '.featured .carousel-inner .item {height:' . "\n";	
		echo $voyage_options['fp_height'] . "px;}\n";
	}
	if ($voyage_options['fb_height'] > 0 ) {
		echo '.featured-blog .carousel-inner .item { height: ';	
		echo $voyage_options['fb_height'] . "px; }\n";
	}
	
	if ( $voyage_options['bodyfont'] > 0 )
		echo 'body {font-family:' . $voyage_fonts[$voyage_options['bodyfont']]['family'] . ';}' . "\n";
	if ( $voyage_options['headingfont'] > 0 )
		echo 'h1, h2, h3, h4, h5, h6 {font-family:' . $voyage_fonts[$voyage_options['headingfont']]['family'] . ';}' . "\n";
	if ( $voyage_options['entrytitlefont'] > 0 )
		echo '.entry-title {font-family:' . $voyage_fonts[$voyage_options['entrytitlefont']]['family'] . ';}' . "\n";
	if ( $voyage_options['sitetitlefont'] > 0 )
		echo '#site-title {font-family:' . $voyage_fonts[$voyage_options['sitetitlefont']]['family'] . ';}' . "\n";
	if ( $voyage_options['sitedescfont'] > 0 )
		echo '#site-description {font-family:' . $voyage_fonts[$voyage_options['sitedescfont']]['family'] . ';}' . "\n";
	if ( $voyage_options['widgettitlefont'] > 0 )
		echo '.widget-title {font-family:' . $voyage_fonts[$voyage_options['widgettitlefont']]['family'] . ';}' . "\n";
	if ( $voyage_options['sidebarfont'] > 0 )
		echo '#sidebar_one,#sidebar_two {font-family:' . $voyage_fonts[$voyage_options['sidebarfont']]['family'] . ';}' . "\n";
	if ( $voyage_options['footerfont'] > 0 )
		echo '#footer {font-family:' . $voyage_fonts[$voyage_options['footerfont']]['family'] . ';}' . "\n";
	if ( $voyage_options['mainmenufont'] > 0 )
		echo '#mainmenu {font-family:' . $voyage_fonts[$voyage_options['mainmenufont']]['family'] . ';}' . "\n";

	echo '</style>' . "\n";	
	// Slider JQuery Script
	if ($voyage_options['fp_interval'] > 0)
		$slider_interval = $voyage_options['fp_interval'] * 1000;
	else
		$slider_interval = 5000;
	echo '<script>jQuery(document).ready(function($){';
	echo '$(".carousel").carousel({interval:' . $slider_interval . '})';
	echo "});</script>\n";
	
	// Inline CSS
    if (!empty($voyage_options['voyage_inline_css'])) {
		echo '<!-- Custom CSS Styles -->' . "\n";
        echo '<style type="text/css" media="screen">' . "\n";
		echo $voyage_options['voyage_inline_css'] . "\n";
		echo '</style>' . "\n";
	}
}
add_action('wp_head', 'voyage_custom_css');
/** 
* @param undefined $links
*/
function voyage_category_count_span($links) {
  $links = str_replace('</a> (', '</a> <span>(', $links);
  $links = str_replace(')', ')</span>', $links);
  return $links;
}
add_filter('wp_list_categories', 'voyage_category_count_span');

function voyage_archive_count_span($links) {
  $links = str_replace('</a>&nbsp;(', '</a> <span>(', $links);
  $links = str_replace(')', ')</span>', $links);
  return $links;
}
add_filter('get_archives_link', 'voyage_archive_count_span');

function voyage_fonts_array() {
	global $voyage_options;
	$fonts = array(
	'0' => array( 'key' => '0',
				'label' => 'Default',
				'url'  => '',
				'family' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
				'type' => 'Sans',
		),
//Sans
	'100' => array(	'key' => '100',
				'label' => 'Arial',
				'url'  => '',
				'family' => "Arial, Helvetica, sans-serif",
				'type' => 'Sans',
		),
	'101' => array(	'key' => '101',
				'label' => 'Arial Black',
				'url'  => '',
				'family' => "Arial Black, Gadget, sans-serif",
				'type' => 'Sans',
		),
	'102' => array(	'key' => '102',
				'label' => 'Impact',
				'url'  => '',
				'family' => "Impact, Charcoal, sans-serif",
				'type' => 'Sans',
		),		
	'103' => array(	'key' => '103',
				'label' => 'Lucida Sans',
				'url'  => '',
				'family' => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
				'type' => 'Sans',
		),		
	'104' => array(	'key' => '104',
				'label' => 'Tahoma',
				'url'  => '',
				'family' => "Tahoma, Geneva, sans-serif",
				'type' => 'Sans',
		),
	'105' => array(	'key' => '105',
				'label' => 'Trebuchet MS',
				'url'  => '',
				'family' => "'Trebuchet MS', sans-serif",
				'type' => 'Sans',
		),
	'106' => array(	'key' => '106',
				'label' => 'Verdana',
				'url'  => '',
				'family' => "Verdana, Geneva, sans-serif",
				'type' => 'Sans',
		),
	'107' => array(	'key' => '107',
				'label' => 'MS Sans Serif',
				'url'  => '',
				'family' => "'MS Sans Serif', Geneva, sans-serif",
				'type' => 'Sans',
		),		
		
//Sans Webs
	'200' => array(	'key' => '200',
				'label' => 'Open Sans',
				'url'  => '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic',
				'family' => "'Open Sans', sans-serif",
				'type' => 'Sans',
		),
	'201' => array(	'key' => '201',
				'label' => 'Ubuntu',
				'url'  => '//fonts.googleapis.com/css?family=Ubuntu:400,400italic,700italic,700',
				'family' => "'Ubuntu', sans-serif;",
				'type' => 'Sans',
		),	
/*
    Myriad Pro
    League Gothic
    Cabin
    Corbel
    Museo Slab
    Bebas Neue
    Lobster
    Franchise
    PT Serif
*/			
		
//Serif
	'400' => array(	'key' => '400',
				'label' => 'Georgia',
				'url'  => '',
				'family' => "Georgia, serif",
				'type' => 'Serif',
		),
	'401' => array(	'key' => '401',
				'label' => 'Palatino',
				'url'  => '',
				'family' => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
				'type' => 'Serif',
		),
	'402' => array(	'key' => '402',
				'label' => 'Times New Roman',
				'url'  => '',
				'family' => "'Times New Roman', Times, serif",
				'type' => 'Serif',
		),	
	'403' => array(	'key' => '403',
				'label' => 'MS Serif',
				'url'  => '',
				'family' => "'MS Serif', 'New York', serif",
				'type' => 'Serif',
		),		
			
//Serif Webfonts

//Monospae
	'600' => array(	'key' => '600',
				'label' => 'Courier New',
				'url'  => '',
				'family' => "'Courier New', monospace",
				'type' => 'Monospace',
		),
	'601' => array(	'key' => '601',
				'label' => 'Lucida Console',
				'url'  => '',
				'family' => "'Lucida Console', Monaco, monospace",
				'type' => 'Monospace',
		),

//Monospae Webfonts

//Cursive
	'800' => array(	'key' => '800',
				'label' => 'Comic Sans MS',
				'url'  => '',
				'family' => "'Comic Sans MS', cursive",
				'type' => 'Cursive',
		),
//Cursive Webfonts
	'900' => array(	'key' => '900',
				'label' => 'Berkshire Swash',
				'url'  => '//fonts.googleapis.com/css?family=Berkshire+Swash',
				'family' => "'Berkshire Swash', cursive",
				'type' => 'Cursive',
		),
	);
//User defined google fonts
	if (!empty($voyage_options['otherfont1'])) {
		$fonts['1001'] = 	array(	'key' => '1001',
				'label' => $voyage_options['otherfont1'],
				'url'  => voyage_google_font_url($voyage_options['otherfont1']),
				'family' => "'" . $voyage_options['otherfont1'] ."', Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);
	}
	else {
		$fonts['1001'] = 	array(	'key' => '1001',
				'label' => 'Other Font 1',
				'url'  => '',
				'family' => "Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);		
	}	
	if (!empty($voyage_options['otherfont2'])) {
		$fonts['1002'] = 	array(	'key' => '1002',
				'label' => $voyage_options['otherfont2'],
				'url'  => voyage_google_font_url($voyage_options['otherfont2']),
				'family' => "'" . $voyage_options['otherfont2'] ."', Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);
	}
	else {
		$fonts['1002'] = 	array(	'key' => '1002',
				'label' => 'Other Font 2',
				'url'  => '',
				'family' => "Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);		
	}	
	if (!empty($voyage_options['otherfont3'])) {
		$fonts['1003'] = 	array(	'key' => '1003',
				'label' => $voyage_options['otherfont3'],
				'url'  => voyage_google_font_url($voyage_options['otherfont3']),
				'family' => "'" . $voyage_options['otherfont3'] ."', Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);
	}
	else {
		$fonts['1003'] = 	array(	'key' => '1003',
				'label' => 'Other Font 3',
				'url'  => '',
				'family' => "Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);		
	}	
	if (!empty($voyage_options['otherfont4'])) {
		$fonts['1004'] = 	array(	'key' => '1004',
				'label' => $voyage_options['otherfont4'],
				'url'  => voyage_google_font_url($voyage_options['otherfont4']),
				'family' => "'" . $voyage_options['otherfont4'] ."', Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);
	}	
	else {
		$fonts['1004'] = 	array(	'key' => '1004',
				'label' => 'Other Font 4',
				'url'  => '',
				'family' => "Helvetica, Arial, sans-serif",
				'type' => 'Others',
							);		
	}	
	return apply_filters( 'voyage_fonts_array', $fonts);	
}

if ( ! function_exists( 'voyage_google_font_url' ) ) :
function voyage_google_font_url($name) {
	return '//fonts.googleapis.com/css?family=' . str_replace(' ', '+', $name) . ':400,400italic,700italic,700';
}
endif;
?>
