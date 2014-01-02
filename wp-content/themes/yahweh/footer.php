<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */
?>
	<footer id="colophon" role="contentinfo">
		<div class="copyright">
			<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> <br/> &copy; <?php echo date('Y', time());?>
			&nbsp; 
		</div>
	</footer><!-- #colophon -->
</div><!-- #main -->

<?php wp_footer(); ?>
</body>
</html>