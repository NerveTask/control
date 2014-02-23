<?php

add_action( 'wp_ajax_nopriv_get_tasks', 'control_get_tasks' );
add_action( 'wp_ajax_get_tasks', 'control_get_tasks' );

function control_get_tasks() {

	if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {

		$count_tasks = wp_count_posts( 'nervetask' );

		// Order
		if( isset( $_GET['sSortDir_0'] ) ) {
			if( $_GET['sSortDir_0'] == 'desc' ) {
				$order = 'DESC';
			} else {
				$order = 'ASC';
			}
		}

		// Orderby
		if( isset( $_GET['iSortCol_0'] ) ) {
			if( $_GET['iSortCol_0'] == 0 ) {
				$orderby = 'title';
			} else {
				$orderby = 'date';
			}
		}

		$args = array(
			'offset'			=> $_GET['iDisplayStart'],
			'order'				=> $order,
			'orderby'			=> $orderby,
			'post_type'			=> 'nervetask',
			'posts_per_page'	=> $_GET['iDisplayLength'],
			's'					=> $_GET['sSearch']
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id = get_the_ID();

				$users = get_users( array(
					'connected_type' => 'nervetask_to_user',
					'connected_items' => $post_id
				));
				
				$assigned = '';
				foreach( $users as $user ) {
					$assigned .= '<a href="'. get_author_posts_url( $user->data->ID ) .'">'. $user->data->display_name .'</a>';
				}

				$rows[] = array(
					'<a href="'. get_permalink() .'">'. get_the_title() .'</a>',
					get_the_term_list( $post_id, 'nervetask_status', '<span class="task-status">', ', ', '</span>' ),
					get_the_term_list( $post_id, 'nervetask_priority', '<span class="task-priority">', ', ', '</span>' ),
					$assigned,
					get_post_meta( $post_id, 'nervetask_due_date', true),
					'<time datetime="'. get_the_time('c') .'">'. get_the_time('M j, Y') .' at '. get_the_time('g:ia') .'</time>'
				);

			}
		}

		$output = array(
			'get'					=> $_GET,
			'sEcho'					=> $_GET['sEcho'],
			'iTotalRecords'			=> $count_tasks->publish,
			'iTotalDisplayRecords'	=> $count_tasks->publish,
			'aaData'				=> $rows
		);

		die(json_encode( $output ));

	}
}

add_action( 'wp_ajax_nopriv_get_dashboard_tasks', 'control_get_dashboard_tasks' );
add_action( 'wp_ajax_get_dashboard_tasks', 'control_get_dashboard_tasks' );

function control_get_dashboard_tasks() {

	if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {
		
		$user = wp_get_current_user();
		
		$count_tasks = get_posts( array(
			'connected_type' => 'nervetask_to_user',
			'connected_items' => $user,
			'suppress_filters' => false,
			'nopaging' => true
		) );
		
		// Order
		if( isset( $_GET['sSortDir_0'] ) ) {
			if( $_GET['sSortDir_0'] == 'desc' ) {
				$order = 'DESC';
			} else {
				$order = 'ASC';
			}
		}

		// Orderby
		if( isset( $_GET['iSortCol_0'] ) ) {
			if( $_GET['iSortCol_0'] == 0 ) {
				$orderby = 'title';
			} else {
				$orderby = 'date';
			}
		}

		$args = array(
			'offset'			=> $_GET['iDisplayStart'],
			'order'				=> $order,
			'orderby'			=> $orderby,
			'post_type'			=> 'nervetask',
			'posts_per_page'	=> $_GET['iDisplayLength'],
			's'					=> $_GET['sSearch'],
			'connected_type'	=> 'nervetask_to_user',
			'connected_items'	=> $user,
			'suppress_filters'	=> false,
			'nopaging'			=> true
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id = get_the_ID();

				$users = get_users( array(
					'connected_type' => 'nervetask_to_user',
					'connected_items' => $post_id
				));
				
				$assigned = '';
				foreach( $users as $user ) {
					$assigned .= '<a href="'. get_author_posts_url( $user->data->ID ) .'">'. $user->data->display_name .'</a>';
				}

				$rows[] = array(
					'<a href="'. get_permalink() .'">'. get_the_title() .'</a>',
					get_the_term_list( $post_id, 'nervetask_status', '<span class="task-status">', ', ', '</span>' ),
					get_the_term_list( $post_id, 'nervetask_priority', '<span class="task-priority">', ', ', '</span>' ),
					$assigned,
					get_post_meta( $post_id, 'nervetask_due_date', true),
					'<time datetime="'. get_the_time('c') .'">'. get_the_time('M j, Y') .' at '. get_the_time('g:ia') .'</time>'
				);

			}
		}

		$output = array(
			'get'					=> $_GET,
			'sEcho'					=> $_GET['sEcho'],
			'iTotalRecords'			=> count( $count_tasks ),
			'iTotalDisplayRecords'	=> count( $count_tasks ),
			'aaData'				=> $rows
		);

		die(json_encode( $output ));

	}
}