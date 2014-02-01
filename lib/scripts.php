<?php
/**
 * Enqueue scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.min.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.10.2.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr-2.7.0.min.js
 * 3. /theme/assets/js/main.min.js (in footer)
 */
function control_scripts() {
	wp_enqueue_style('control_main', get_template_directory_uri() . '/assets/css/main.min.css', false, '2692a0275396ffb258635f5bb48cc334');

	// jQuery is loaded using the same method from HTML5 Boilerplate:
	// Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
	// It's kept in the header instead of footer to avoid conflicts with plugins.
	if (!is_admin() && current_theme_supports('jquery-cdn')) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), null, false);
	add_filter('script_loader_src', 'control_jquery_local_fallback', 10, 2);
	}

	if (is_single() && comments_open() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
	}

	wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.7.0.min.js', array(), null, false);
	wp_register_script('control_scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), 'e8d77dbf49668910292f308827704f14', true);

	// all scripts
	wp_enqueue_script('wp-api');
	wp_enqueue_script('backbone');
	wp_enqueue_script('underscore');
	wp_enqueue_script('modernizr');
	wp_enqueue_script('jquery');
	wp_enqueue_script('control_scripts');
	wp_localize_script('control_scripts', 'control', array(
		'base' => home_url( '/' ),
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'loggedin' => is_user_logged_in() ? 'yes' : 'no'
	) );
}
add_action('wp_enqueue_scripts', 'control_scripts', 100);

// http://wordpress.stackexchange.com/a/12450
function control_jquery_local_fallback($src, $handle = null) {
  static $add_jquery_fallback = false;

  if ($add_jquery_fallback) {
    echo '<script>window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/js/vendor/jquery-1.10.2.min.js"><\/script>\')</script>' . "\n";
    $add_jquery_fallback = false;
  }

  if ($handle === 'jquery') {
    $add_jquery_fallback = true;
  }

  return $src;
}
add_action('wp_head', 'control_jquery_local_fallback');

function control_google_analytics() { ?>
<script>
  (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
  function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
  e=o.createElement(i);r=o.getElementsByTagName(i)[0];
  e.src='//www.google-analytics.com/analytics.js';
  r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
  ga('create','<?php echo GOOGLE_ANALYTICS_ID; ?>');ga('send','pageview');
</script>

<?php }
if (GOOGLE_ANALYTICS_ID && !current_user_can('manage_options')) {
  add_action('wp_footer', 'control_google_analytics', 20);
}
