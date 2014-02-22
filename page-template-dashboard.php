<?php
/**
 * Template Name: Dashboard
 *
 * @package control
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php

				$posts = get_posts( array(
					'post_type' => 'nervetask',
					'posts_per_page' => 14
				));
				if ( $posts ) : ?>

				<table class="table table-tasks">
					<thead>
						<tr>
							<th width="25%">Title</th>
							<th width="15%">Status</th>
							<th width="15%">Priority</th>
							<th width="15%">Assigned</th>
							<th width="15%">Due Date</th>
							<th width="15%">Created</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
					<tfoot>
						<tr>
							<th>Title</th>
							<th>Status</th>
							<th>Priority</th>
							<th>Assigned</th>
							<th>Due Date</th>
							<th>Created</th>
						</tr>
					</tfoot>
				</table>

				<?php endif; ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
