<?php
/**
 * The main posts template file
 *
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>
<ul class="cbp_tmtimeline">
		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', get_post_format() ); ?>
		<?php endwhile; ?>
</ul> 
<?php this_theme_content_nav( 'nav-below' ); ?>

<?php else : ?>
<ul class="cbp_tmtimeline">
<li>
	<?php $author = get_post_meta( $post->ID, 'post_meta_details_author', true );?>

	<time class="cbp_tmtime" datetime="<?php $time = time(); echo date("Y-m-d G:i", $time); ?>"><span><?php echo date("Y-m-d", $time); ?></span> 
		<span><?php echo ! empty($author) ? $author : date("G:i", $time); ?></span>
	</time>
	<div class="cbp_tmicon cbp_tmicon-phone"></div>
	<div class="cbp_tmlabel">
		<h2><?php the_title(); ?></h2>
		<?php the_post_thumbnail(); ?>
		<?php the_excerpt(); ?>
	</div>
</li>
</ul> 
<?php endif; // end have_posts() check ?>

<?php get_footer(); ?>