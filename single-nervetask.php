<?php
/**
 * The Template for displaying all single posts.
 *
 * @package control
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main row-fluid" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-sm-8 ' ); ?>>
				<header class="entry-header">
					<span class="post-id">#<?php the_ID(); ?></span>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php echo do_shortcode( '[nervetask_update_content]' ); ?>
					<?php
						wp_link_pages( array(
							'before' => '<div class="page-links">' . __( 'Pages:', 'control' ),
							'after'  => '</div>',
						) );
					?>
					
					<?php if( class_exists( 'NerveTask_Subtasks' ) ) { ?>
					
					<div class="subtask-container">
					
						<?php echo do_shortcode( '[nervetask_subtasks]' ); ?>
						<?php echo do_shortcode( '[nervetask_new_subtask]' ); ?>
						
					</div>
					
					<?php } ?>

				</div><!-- .entry-content -->

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template( '/comments-nervetask.php' );
					endif;
				?>

			</article><!-- #post-## -->

			<div class="entry-meta col-sm-4">

				<ul class="task-meta list-unstyled unstyled">
					
					<li class="task-sidebar-status <?php echo control_get_task_status( get_the_ID() ); ?>">
						<?php echo do_shortcode( '[nervetask_update_status]' ); ?>
					</li>
					<li>
						<?php echo do_shortcode( '[nervetask_update_assignees]' ); ?>
					</li>
					<li>
						<?php echo do_shortcode( '[nervetask_update_due_date]' ); ?>
					</li>
					<li>
						<?php echo do_shortcode( '[nervetask_update_priority]' ); ?>
					</li>
					<li>
						<?php echo do_shortcode( '[nervetask_update_category]' ); ?>
					</li>
					<li>
						<?php echo do_shortcode( '[nervetask_update_tags]' ); ?>
					</li>
					<li>
						<strong>Created: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></strong><br>
						by <?php the_author(); ?>
					</li>
					<?php if( class_exists( 'Email' ) ) { ?>
					<li>
						<?php echo do_shortcode( '[email_add_subsciber_form]' ); ?>
					</li>
					<?php } ?>
				</ul>

			</div><!-- .entry-meta -->

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
