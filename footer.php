<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package control
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<a href="http://wordpress.org/" rel="generator"><?php printf( __( 'Proudly powered by %s', 'control' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'control' ), 'control', '<a href="http://developdaly.com" rel="designer">Patrick Daly</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="newTaskLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="newTaskLabel">New Task</h4>
			</div>
			<div class="modal-body">
				<?php echo do_shortcode( '[nervetask_new_task]' ); ?>
				
				<div id="new-task-user-feedback"></div>
			</div>
		</div>
	</div>
</div>

<?php wp_footer(); ?>

</body>
</html>