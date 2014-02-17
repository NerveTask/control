<?php

$args = array(
	'post_type'			=> 'nervetask',
	'posts_per_page'	=> 20,
);

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) { ?>

	<table class="table table-striped table-condensed table-bordered table-hover">

		<thead>
			<tr>
				<th class="column-title">
					<?php if( ( get_query_var( 'order' ) == 'DESC' ) && ( get_query_var( 'orderby' ) == 'title' ) ): ?>
						<a class="btn-block active" style="text-decoration: underline;" href="<?php echo add_query_arg( array( 'post_type' => 'nervetask', 'orderby' => 'title', 'order' => 'ASC' ) ); ?>">Title <span class="caret" style="vertical-align: middle;"></span></a>
					<?php elseif( ( get_query_var( 'order' ) == 'ASC' ) && ( get_query_var( 'orderby' ) == 'title' ) ): ?>
						<a class="btn-block dropup active" style="text-decoration: underline;" href="<?php echo add_query_arg( array( 'post_type' => 'nervetask', 'orderby' => 'title', 'order' => 'DESC' ) ); ?>">Title <span class="caret" style="vertical-align: middle;"></span></a>
					<?php else: ?>
						<a class="btn-block active" style="text-decoration: underline;" href="<?php echo add_query_arg( array( 'post_type' => 'nervetask', 'orderby' => 'title', 'order' => 'DESC' ) ); ?>">Title</a>
					<?php endif; ?>
				</th>

				<th class="column-status">Status</th>
				<th class="column-priority">Priority</th>
				<th class="column-assigned">Assigned</th>
			</tr>
		</thead>

		<tbody>

		<?php while ( $the_query->have_posts() ) { $the_query->the_post(); ?>

			<tr id="post-<?php the_ID(); ?>" class="">

				<td class="column-title title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>

				<td class="column-status"></td>

				<td class="column-priority"></td>

				<td class="column-assigned"></td>

			</tr>

		<?php } ?>

		</tbody>

	</table>

<?php } else { ?>

	No tasks

<?php } wp_reset_postdata(); ?>