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
			'post_status'		=> 'publish',
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
				
				$due_date_object = get_post_meta( $post_id, 'nervetask_due_date', true );
				$due_date_object_decoded = json_decode( $due_date_object );
				
				if( $due_date_object_decoded ) {
					$due_date = new DateTime($due_date_object_decoded->due_date);
					$due_date = $due_date->format(get_option('date_format')) .' '. $due_date->format(get_option('time_format'));
				} else {
					$due_date = '';
				}
				
				$rows[] = array(
					'<a href="'. get_permalink() .'">'. get_the_title() .'</a>',
					get_the_term_list( $post_id, 'nervetask_status', '<span class="task-status '. control_get_task_status( $post_id ) .'">', ', ', '</span>' ),
					get_the_term_list( $post_id, 'nervetask_priority', '<span class="task-priority">', ', ', '</span>' ),
					$assigned,
					$due_date,
					'<time datetime="'. get_the_time('c') .'">'. get_the_time(get_option('date_format')) .' '. get_the_time(get_option('time_format')) .'</time>'
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

add_action( 'wp_ajax_nopriv_get_user_tasks', 'control_get_user_tasks' );
add_action( 'wp_ajax_get_user_tasks', 'control_get_user_tasks' );

function control_get_user_tasks() {

	if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {
		
		if( !is_user_logged_in() ) {
			die(
				json_encode(
					array(
						'success' => false,
						'message' => __( 'You must be logged in to view this information.' )
					)
				)
			);
		}

		// Order
		if( isset( $_GET['user'] ) ) {
			$user = $_GET['user'];
		} else {
			die(
				json_encode(
					array(
						'success' => false,
						'message' => __( 'You must specify a user ID.' )
					)
				)
			);
		}
				
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
			'post_status'		=> 'publish',
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

				$due_date_object = get_post_meta( $post_id, 'nervetask_due_date', true );
				$due_date_object_decoded = json_decode( $due_date_object );
				
				if( $due_date_object_decoded ) {
					$due_date = new DateTime($due_date_object_decoded->due_date);
					$due_date = $due_date->format(get_option('date_format')) .' '. $due_date->format(get_option('time_format'));
				} else {
					$due_date = '';
				}
				
				$rows[] = array(
					'<a href="'. get_permalink() .'">'. get_the_title() .'</a>',
					get_the_term_list( $post_id, 'nervetask_status', '<span class="task-status '. control_get_task_status( $post_id ) .'">', ', ', '</span>' ),
					get_the_term_list( $post_id, 'nervetask_priority', '<span class="task-priority">', ', ', '</span>' ),
					$assigned,
					$due_date,
					'<time datetime="'. get_the_time('c') .'">'. get_the_time(get_option('date_format')) .' '. get_the_time(get_option('time_format')) .'</time>'
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

add_action( 'wp_ajax_nopriv_get_tax_tasks', 'control_get_tax_tasks' );
add_action( 'wp_ajax_get_tax_tasks', 'control_get_tax_tasks' );

function control_get_tax_tasks() {

	if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {
//die(print_r($_GET));
		if( !is_user_logged_in() ) {
			die(
				json_encode(
					array(
						'success' => false,
						'message' => __( 'You must be logged in to view this information.' )
					)
				)
			);
		}
		
		

		// Order
		if( isset( $_GET['tax'] ) ) {
			$tax = $_GET['tax'];
		} else {
			die(
				json_encode(
					array(
						'success' => false,
						'message' => __( 'You must specify a taxonomy.' )
					)
				)
			);
		}
				
		$count_tasks = get_posts( array(
			'suppress_filters' => false,
			'nopaging' => true,
			'tax_query'			=> array(
				array(
					'taxonomy'	=> $_GET['tax'],
					'field'		=> 'id',
					'terms'		=> $_GET['term']
				)
			)
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
			'post_status'		=> 'publish',
			's'					=> $_GET['sSearch'],
			'suppress_filters'	=> false,
			'nopaging'			=> true,
			'tax_query'			=> array(
				array(
					'taxonomy'	=> $_GET['tax'],
					'field'		=> 'id',
					'terms'		=> $_GET['term']
				)
			)
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

				$due_date_object = get_post_meta( $post_id, 'nervetask_due_date', true );
				$due_date_object_decoded = json_decode( $due_date_object );
				
				if( $due_date_object_decoded ) {
					$due_date = new DateTime($due_date_object_decoded->due_date);
					$due_date = $due_date->format(get_option('date_format')) .' '. $due_date->format(get_option('time_format'));
				} else {
					$due_date = '';
				}
				
				$rows[] = array(
					'<a href="'. get_permalink() .'">'. get_the_title() .'</a>',
					get_the_term_list( $post_id, 'nervetask_status', '<span class="task-status '. control_get_task_status( $post_id ) .'">', ', ', '</span>' ),
					get_the_term_list( $post_id, 'nervetask_priority', '<span class="task-priority">', ', ', '</span>' ),
					$assigned,
					$due_date,
					'<time datetime="'. get_the_time('c') .'">'. get_the_time(get_option('date_format')) .' '. get_the_time(get_option('time_format')) .'</time>'
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