<?php while (have_posts()) : the_post(); ?>
<article <?php post_class( 'col-sm-9'); ?>>
	<header>
		<a href="<?php the_permalink(); ?>" class="post-id"><?php the_ID(); ?></a>
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
	</header>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<footer>
		<?php wp_link_pages(array( 'before'=>'
		<nav class="page-nav">
			<p>' . __('Pages:', 'roots'), 'after' => '</p>
		</nav>')); ?>
	</footer>
	<?php comments_template( '/templates/comments-nervetask.php'); ?>
</article>
<aside class="nervetask-sidebar col-sm-3">
	<?php get_template_part( 'templates/entry-meta-nervetask'); ?>
</aside>
<?php endwhile; ?>