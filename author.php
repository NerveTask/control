<?php
/**
 * The template for displaying an author's page.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package control
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<h1><?php printf( __( '%s\'s Tasks', 'control' ), '<span class="vcard">' . get_the_author() . '</span>' ); ?></h1>
		<meta id="userid" data-userid="<?php echo get_queried_object_id(); ?>">
		<table class="table table-striped table-bordered table-tasks">
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
			
		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
