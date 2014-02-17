<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?> data-id="<?php the_ID(); ?>">

  <!--[if lt IE 8]>
    <div class="alert alert-warning">
      <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
    </div>
  <![endif]-->

	<?php if (roots_display_sidebar()) : ?>
	<aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
		<?php include roots_sidebar_path(); ?>
	</aside><!-- /.sidebar -->
	<?php endif; ?>

	<div class="wrap" role="document">

		<?php
		do_action('get_header');
		?>
		<div class="content row">
			<main class="main <?php echo roots_main_class(); ?>" role="main">

				<?php include roots_template_path(); ?>

			</main><!-- /.main -->

		</div><!-- /.content -->

		<?php get_template_part('templates/footer'); ?>

	</div><!-- /.wrap -->

</body>
</html>
