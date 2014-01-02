<?php
/**
 * The default template for displaying content
 *
 * Used for both index & archive.
 *
 */
?>
<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
	<div class="featured-post">
		<?php _e( 'Featured post'); ?>
	</div>
	<?php endif; ?>
	<?php $author = get_post_meta( $post->ID, 'post_meta_details_author', true );?>
	<time class="cbp_tmtime" datetime="<?php echo the_time("Y-m-d G:i"); ?>"><span><?php echo ! empty($author) ? 'Sang by' : 'Published on' ; ?></span> <span><?php echo ! empty($author) ? $author :the_time("Y-m-d");; ?></span></time>
	<div class="cbp_tmicon cbp_tmicon-phone"></div>
	<div class="cbp_tmlabel">
		<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<?php the_post_thumbnail(); ?>
		<?php the_excerpt(); ?>
	</div>
</li>
