<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>
<div class="pages">
<?php while ( have_posts() ) : the_post(); ?>
	<h2><?php the_title(); ?></h2>
				
	<?php the_content(); ?>

	<nav class="nav-single clearfix">
		<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link') . '</span> %title' ); ?></span>
		<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link') . '</span>' ); ?></span>
	</nav><!-- .nav-single -->

	<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>
</div>
<?php get_footer(); ?>