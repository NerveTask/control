<?php
/**
 * control functions and definitions
 *
 * @package control
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'control_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function control_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on control, use a find and replace
	 * to change 'control' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'control', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'control' ),
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'control_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );
}
endif; // control_setup
add_action( 'after_setup_theme', 'control_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function control_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'control' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'control_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function control_scripts() {

	wp_enqueue_style('control_main', get_template_directory_uri() . '/assets/css/main.min.css', false, '55f0a3798f9d897e05093c26b0df271f');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.7.0.min.js', array(), null, false);
	wp_register_script('control_scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), 'fc404e56062bf4ae375781a7f89cbcb4', true);
	wp_enqueue_script('modernizr');
	wp_enqueue_script('jquery');
	wp_enqueue_script('control-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true);
	wp_enqueue_script('control-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);
	wp_enqueue_script('control_scripts');
	wp_localize_script('control_scripts', 'ajaxurl', array('ajaxurl' => admin_url('admin-ajax.php')));

	wp_register_script('datatables',		get_template_directory_uri() .'/assets/datatables/js/jquery.dataTables.min.js' );
	wp_register_script('rotarydatatables',	get_template_directory_uri() .'/js/rotary.datatables.js',  array( 'jquery' ), null, true );
	wp_enqueue_style('rotary-datatables');
	wp_enqueue_script(array('datatables','datatablesreload', 'rotarydatatables', 'jquery-ui-dialog'));
	wp_localize_script( 'rotarydatatables', 'rotarydatatables', array(
		'ajaxURL' => admin_url('admin-ajax.php'),
		'tableNonce' => wp_create_nonce( 'rotary-table-nonce' )
	));

}
add_action( 'wp_enqueue_scripts', 'control_scripts' );

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


function control_task_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
	$args['avatar_size'] = 20;

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
	?>
	<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>

		<?php if ($comment->comment_approved == '0') : ?>
			<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
			<?php
			/* translators: 1: date, 2: time */
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
			?>
			<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>

		<div class="comment-author vcard">
			<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
			<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
		</div>

		<div class="comment-text">
			<?php comment_text() ?>
		</div>

	<?php if ( 'div' != $args['style'] ) : ?>
		</div>
	<?php endif; ?>
	<?php
}

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
