<?php
/**
 * The template for displaying the 'nervetask' post type Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package control
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">Tasks</h1>

			</header><!-- .page-header -->

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

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
