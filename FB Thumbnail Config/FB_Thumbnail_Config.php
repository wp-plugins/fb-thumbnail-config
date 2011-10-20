<?php
/**
 * Plugin Name: FB Thumbnail Config
 * Plugin URI: http://www.silversquareinc.com/blog/facebook-thumbnail-config-wordpress-plugin/
 * Description: FB Thumbnail Config lets you to control exactly how your posts/pages appear on Facebook. Customize thumbnails and descriptions generated on Facebook when someone links to a post/page on your Wordpress site. 
 * Author: Dean Verleger
 * Version: 1.0
 * Author URI: http://www.silversquareinc.com
 *
 * @license GNU General Public License
 */

/**
 * Include Rilwsi's Meta Boxes
 */
// get path to plugin
//$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$x = '';
include( $x.'meta-box.php' );


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
			'name' => 'Add Image',
			'desc' => 'Upload or select ONE image and use "Insert into Post" to select - Works best with one image - use no more than 3.',
			'id' => $prefix . 'the_thumb',
			'type' => 'image'
		),
		array(
			'name' => 'Use Thumbnail',
			'desc' => 'Check to use the thubmnail you entered above as the Facebook thumbnail (leave unchecked to let Facebook auto-generate).',
			'id' => $prefix . 'use_thumb',
			'type' => 'checkbox'
		),
		array(
			'name' => 'Enter Description',
			'desc' => 'Enter the description shown on Facebook. Keep your description under 43 words - anything over will be ignored.',
			'id' => $prefix . 'the_desc',
			'type' => 'textarea'
		),
		array(
			'name' => 'Use Description',
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
	// add meta for type, url, and title
	if($use_thumb || $use_desc) {
?><meta property="og:type" content="article" />
<meta property="og:url" content="http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:title" content="<?php wp_title('|', 1, 'right'); bloginfo('name'); ?>"/><?php
	}

	global $post;
	$prefix = 'fbtc_';
	$use_thumb = get_post_meta($post->ID, $prefix.'use_thumb', true);
	$use_desc = get_post_meta($post->ID, $prefix.'use_desc', true);
	$the_desc = get_post_meta($post->ID, $prefix.'the_desc',true );

	// add meta for description
	if($use_desc && $the_desc)
		echo "<meta property=\"og:description\" content=\"$the_desc\"/>\n";

	// add meta for image
	if($use_thumb) {
		$attachs = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'attachment',
			'post_parent' => $post->ID,
			'post_mime_type' => 'image',
			'output' => ARRAY_A
		));

		if (!empty($attachs)) {
			foreach ($attachs as $att) {
				$src = wp_get_attachment_image_src($att->ID, array(100,100));
				$src = $src[0];
				
				// add meta
				echo "<meta property=\"og:image\" content=\"$src\"/>\n";
			}
		}
	}
}
add_action('wp_head', 'fb_thumbnail_config_header_action');