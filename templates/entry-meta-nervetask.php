<ul class="task-meta list-unstyled">

<?php
$users = get_users( array(
	'connected_type' => 'nervetask_to_user',
	'connected_items' => get_queried_object()
) );
if ( $users ) { ?>
	<li>
		<strong>Assigned to:</strong>
		<?php foreach ( $users as $user ) { ?>
			<a href="<?php echo get_author_posts_url( $user->data->ID ); ?>"><?php echo esc_html( $user->display_name ) ?></a>,
		<?php } ?>
	</li>
<?php } ?>

	<li>
		<?php echo get_the_term_list( get_queried_object(), 'nervetask_status', '<strong>Status: ', ', ', '</strong>' ); ?>
	</li>

	<li>
		<?php echo get_the_term_list( get_queried_object(), 'nervetask_priority', '<strong>Priority: ', ', ', '</strong>' ); ?>
	</li>

	<li>
		<?php echo get_the_term_list( get_queried_object(), 'category', '<strong>Category: ', ', ', '</strong>' ); ?>
	</li>

	<li>
		Created: <?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?>
	</li>

</ul>

<ul class="unstyled admin-nav">
	<li>
		<a href="<?php echo get_edit_post_link(); ?>" class="edit-task">
			<i class="fa fa-edit"></i>Edit Task</a>
	</li>
	<li>
		<a href="<?php echo bloginfo( 'url' ); ?>/tasks/">
			<i class="fa fa-arrow-left"></i>Back to all tasks</a>
	</li>
	<li>
		<a href="<?php echo bloginfo( 'url' ); ?>/new-task/">
			<i class="fa fa-plus"></i>New Task</a>
	</li>
</ul>