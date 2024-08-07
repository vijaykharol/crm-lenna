<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package CRM
 */

?>

	<footer id="colophon" class="site-footer">
		<?php 
		if(is_user_logged_in()){
			?>
			<div class="site-info"></div><!-- .site-info -->
			<?php
		}
		?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
