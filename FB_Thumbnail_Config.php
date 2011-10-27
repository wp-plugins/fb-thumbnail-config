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

function createAdminMenu()
{
	/**
	 * Include Rilwsi's Meta Boxes
	 */
	// get path to plugin
	include( 'meta-box.php' );

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
				'name' => 'Generate Description:',
				'desc' => 'Check to generate a description based on your content.',
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
}
add_action ( 'admin_init', 'createAdminMenu');

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
	
	// add meta for type, url, and title
	if($use_thumb || $use_desc):
	?><meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
<meta property="og:title" content="<?php wp_title('|', 1, 'right'); bloginfo('name'); ?>"/>

	<?php endif;
	
	$description = strip_tags ( $thispost->post_excerpt ? $thispost->post_excerpt : $thispost->post_content );
	// add meta for description
	if($use_desc)
		echo "<meta property=\"og:description\" content=\"$description\"/>\n";
	
	// add meta for image
	if($use_thumb)
	{
		echo "<meta property=\"og:image\" content=\"$the_thumb\"/>\n";
	} else {
		if ( function_exists ( 'has_post_thumbnail' ) AND has_post_thumbnail($post->ID) )
		{
				$attachment = wp_get_attachment_image_src ( get_post_thumbnail_id($thispost->ID) );
				$image = $attachment[0];
		}
		elseif ( preg_match ( '/<img\s[^>]*src=["\']?([^>"\']+)/i', $thispost->post_content, $match ) ) 
		{
			$image = $match[1];
		}
		echo "<meta property=\"og:image\" content=\"$image\"/>\n";
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