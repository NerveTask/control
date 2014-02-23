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
		'secondary' => __( 'Secondary Menu', 'control' ),
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

	wp_enqueue_style('google_fonts', 'http://fonts.googleapis.com/css?family=Varela+Round' );
	wp_enqueue_style('control_main', get_template_directory_uri() . '/assets/css/main.min.css', false, '3155d153012c3c2d7218044bcc79b1f1');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.7.0.min.js', array(), null, false);
	wp_register_script('control_scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), 'e53ccad1c53a0370df6bb3364651fd55', true);
	wp_enqueue_script('modernizr');
	wp_enqueue_script('jquery');
	wp_enqueue_script('control-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true);
	wp_enqueue_script('control-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);
	wp_enqueue_script('control_scripts');
	wp_localize_script('control_scripts', 'ajaxurl', array('ajaxurl' => admin_url('admin-ajax.php')));

	wp_register_script('datatables',		get_template_directory_uri() .'/assets/datatables/js/jquery.dataTables.min.js' );
	wp_enqueue_script(array('datatables','datatablesreload', 'jquery-ui-dialog'));
	wp_localize_script( 'control_scripts', 'control', array(
		'ajaxURL' => admin_url('admin-ajax.php'),
		'tableNonce' => wp_create_nonce( 'rotary-table-nonce' )
	));

}
add_action( 'wp_enqueue_scripts', 'control_scripts' );

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

// TODO: Run this function on theme activate only
function control_custom_menus() {

	// Check if the menu exists
	$menu_exists = wp_get_nav_menu_object( 'secondary' );

	// If it doesn't exist, let's create it.
	if( !$menu_exists){
		$menu_id = wp_create_nav_menu( 'secondary' );
		
		$user = wp_get_current_user();

		// Set up default menu items
		$menu_author_id = wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  __( $user->display_name ),
			'menu-item-url' => get_author_posts_url( $user->ID ), 
			'menu-item-status' => 'publish'));
		
		// Set up default menu items
		$menu_logout_id = wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  __( 'Logout' ),
			'menu-item-parent-id' => $menu_author_id,
			'menu-item-url' => wp_logout_url(), 
			'menu-item-status' => 'publish'));

	}
}
add_action( 'init', 'control_custom_menus' );


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
 * Load WP Bootstrap Nav Walker file.
 */
require get_template_directory() . '/inc/bootstrap-nav-walker.php';

/**
 * Load ajax functions.
 */
require get_template_directory() . '/inc/ajax.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
