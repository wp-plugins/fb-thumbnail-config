<?php
/**
 * Plugin Name: FB Thumbnail Config
 * Plugin URI: http://www.silversquareinc.com/blog/facebook-thumbnail-config-wordpress-plugin/
 * Description: FB Thumbnail Config lets you to control exactly how your posts/pages appear on Facebook. Customize thumbnails and descriptions generated on Facebook when someone links to a post/page on your Wordpress site. 
 * Author: Dean Verleger
 * Version: 1.0
 * Stable Tag: 1.0
 * Author URI: http://www.silversquareinc.com
 *
 * @license GNU General Public License
 */

/**
 * Include Rilwsi's Meta Boxes
 */
// get path to plugin
include( 'meta-box.php' );

/**
 * Set up 100 by 100 thumbnail size for fb
 */
if ( function_exists( 'add_theme_support' ) ) 
	add_theme_support( 'post-thumbnails' );
if ( function_exists( 'add_image_size' ) )
	add_image_size( 'fb-thumb', 100, 100 );

/** 
 * BEGIN Meta Boxes
 * 
 * Uses Rilwis's Meta Boxes 
 * (http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html) 
 */
$meta_boxes = array();
$prefix = 'fbtc_';
$meta_boxes[] = array(
	'id' => 'fb_thumbmail_config_details',
	'title' => 'Fb Thumbnail Configuration',
	'pages' => array('post','page'),
	'context' => 'normal', // normal, advanced, side (optional)
	'priority' => 'low',
	'fields' => array(
		array(
			'name' => 'Add Image:',
			'desc' => 'Copy and paste the url to the image you want to use here. For best results use a 100 by 100 pixel image.',
			'id' => $prefix . 'the_thumb',
			'type' => 'text'
		),
		array(
			'name' => 'Use Thumbnail:',
			'desc' => 'Check to use the thubmnail you entered above as the Facebook thumbnail (leave unchecked to let Facebook auto-generate).',
			'id' => $prefix . 'use_thumb',
			'type' => 'checkbox'
		),
		array(
			'name' => 'Description:',
			'desc' => 'Enter the description shown on Facebook. Keep your description under 43 words - anything over will be ignored.',
			'id' => $prefix . 'the_desc',
			'type' => 'textarea'
		),
		array(
			'name' => 'Use Description:',
			'desc' => 'Check to use the description you entered above as the Facebook description (leave unchecked to let Facebook auto-generate).',
			'id' => $prefix . 'use_desc',
			'type' => 'checkbox'
		)
	)
);

// create the meta boxes 
foreach ($meta_boxes as $meta_box)
	$my_box = new RW_Meta_Box($meta_box);
/** 
 * END Meta Boxes
 */


/**
 *  Add meta data Facebook is looking for to head of post/page
 */
function fb_thumbnail_config_header_action()
{
	global $post;
	$prefix = 'fbtc_';
	$use_thumb = get_post_meta($post->ID, $prefix.'use_thumb', true);
	$the_thumb = get_post_meta($post->ID, $prefix.'the_thumb',true );
	$use_desc = get_post_meta($post->ID, $prefix.'use_desc', true);
	$the_desc = get_post_meta($post->ID, $prefix.'the_desc',true );
	
	// add meta for type, url, and title
	if($use_thumb || $use_desc):
	?><meta property="og:type" content="article" />
<meta property="og:url" content="http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:title" content="<?php wp_title('|', 1, 'right'); bloginfo('name'); ?>"/>

	<?php endif;

	// add meta for description
	if($use_desc && $the_desc)
		echo "<meta property=\"og:description\" content=\"$the_desc\"/>\n";

	// add meta for image
	if($use_thumb) {
		echo "<meta property=\"og:image\" content=\"$the_thumb\"/>\n";
	}
}
add_action('wp_head', 'fb_thumbnail_config_header_action');

/**
 *  Style Meta Box in Admin menu
 */
function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/fb-thumbnail-config' . '/fb-thumbnail-config.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');