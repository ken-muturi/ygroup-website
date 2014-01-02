<?php

require( get_template_directory() . '/inc/util.php' );
require( get_template_directory() . '/inc/add_metabox.php' );
require( get_template_directory() . '/inc/jw_post_type.php' );

// Set up the content width value based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 625;

add_action( 'after_setup_theme', 'this_theme_setup' );
function this_theme_setup() {

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twentytwelve' ) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}

add_filter('request', 'this_theme_feed_request');
function this_theme_feed_request($feeds) 
{
	if (isset($feeds['feed']))
	{
		$feeds['post_type'] = get_post_types();
	}
	return $feeds;
}

function this_theme_content_nav( $html_id ) 
{
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="nav-single clearfix" role="navigation">
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}

/**
 * Attach a class to linked images' parent anchors
 * e.g. a img => a.img img
 */
add_filter('the_content','give_linked_images_class');
function give_linked_images_class($html)
{
	$classes = ' pull-right quote_sections_img '; // separated by spaces, e.g. 'img image-link'
	$rel = 'rel="prettyPhoto"';
	// check if there are already classes assigned to the anchor
	if ( preg_match('#<img.*? class=.+?>#', $html) ) 
	{
		$html = preg_replace('#(<img.+)(class\=[\".+\"|\'.+\'])(.*>)#', '$1 class="' . $classes .'$3', $html);
	} 
	else 
	{
		$html = preg_replace('#(<img.*?)>#', '$1 class="' . $classes .'" '. $rel.' >', $html);
	}
	return $html;
}


add_filter( 'excerpt_more', 'this_theme_excerpt_more' );
function this_theme_excerpt_more( $more ) 
{
	return ' <a class="more-link" href="'. get_permalink( get_the_ID() ) . '">Continue reading &rarr;</a>';
}

add_filter( 'excerpt_length', 'this_theme_excerpt_length' );
function this_theme_excerpt_length( $length = null) 
{
	return (is_front_page()) ? 30 : 90;
}

add_filter( 'pre_get_posts', 'this_theme_add_custom_types' );
function this_theme_add_custom_types( $query ) 
{
    if( is_tag() ) 
    {
        // this gets all post types:
        $post_types = get_post_types();

        // alternately, you can add just specific post types using this line instead of the above:
        // $post_types = array( 'post', 'your_custom_type' );
        $query->set( 'post_type', $post_types );
        return $query;
    }
}

//add metaboxes
$post_meta_box = new Add_Metabox("post");
$post_meta_box->add_meta_box('Meta Details', array(
	'Author' => 'text',
	// 'Release Date' => 'text',
	// 'Upload File' => 'file'
));
