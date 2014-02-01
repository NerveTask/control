<?php

/**
 * _bootstraps - 2013 functions and definitions
 *
 * @package _bootstraps
 * @package _bootstraps - 2013 1.0
 */

/**
 * Bootstrap Theme Class
 *
 * @package _bootstraps - 2013 1.0
 */
class WeDevs_Bootstrap {

    function __construct() {
        add_action( 'after_setup_theme', array($this, 'setup_theme') );
        remove_action( 'wp_head', 'wp_generator' );

        add_filter( 'post_link', array($this, 'filter_post_link' ) );
        add_filter( 'page_link', array($this, 'filter_page_link' ), 10, 2 );
		add_filter( 'post_type_link', array($this, 'filter_post_type_link' ), 10, 2 );

        add_action( 'wp_ajax_wpbb_new_comment', array($this, 'add_new_comment' ) );
        add_action( 'wp_ajax_nopriv_wpbb_new_comment', array($this, 'add_new_comment' ) );
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which runs
     * before the init hook. The init hook is too late for some features, such as indicating
     * support post thumbnails.
     *
     * @package _bootstraps - 2013 1.0
     */
    function setup_theme() {

        /**
         * Add support for the Aside Post Formats
         */
        add_theme_support( 'post-formats', array('aside',) );
    }

    /**
     * Filter post links to fit our permalink structure
     *
     * @param string $permalink
     * @return string permalink
     */
    function filter_post_link( $url ) {
        $url =  str_replace( home_url(), home_url('/#'), $url );

        return $url;
    }

    /**
     * Filter page links to fit our permalink structure
     *
     * @param string $permalink
     * @param int $page_id
     * @return string permalink
     */
    function filter_page_link( $url, $page_id ) {
		error_log( $url );
        $url =  str_replace( home_url(), home_url( '/#/page/' . $page_id ), $url );

        return $url;
    }

    /**
     * Filter post type links to fit our permalink structure
     *
     * @param string $permalink
     * @return string permalink
     */
    function filter_post_type_link( $url, $post ) {
		error_log( $post->id );
		$url =  str_replace( home_url(), home_url( '/#/task/' . $post->ID ), $url );

        return $url;
    }

    function add_new_comment() {

        $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
        $post = get_post($comment_post_ID);
        $status = get_post_status($post);
        $status_obj = get_post_status_object($status);

        if ( !comments_open($comment_post_ID) ) {
            do_action('comment_closed', $comment_post_ID);
            wp_send_json_error( __('Sorry, comments are closed for this item.') );
        } elseif ( 'trash' == $status ) {
            do_action('comment_on_trash', $comment_post_ID);
            wp_send_json_error();
        } elseif ( !$status_obj->public && !$status_obj->private ) {
            do_action('comment_on_draft', $comment_post_ID);
            wp_send_json_error();
        } elseif ( post_password_required($comment_post_ID) ) {
            do_action('comment_on_password_protected', $comment_post_ID);
            wp_send_json_error();
        } else {
            do_action('pre_comment_on_post', $comment_post_ID);
        }

        $comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
        $comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
        $comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
        $comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;

        // If the user is logged in
        $user = wp_get_current_user();
        if ( $user->exists() ) {
            if ( empty( $user->display_name ) ) {
                $user->display_name = $user->user_login;
            }

            $comment_author       = wp_slash( $user->display_name );
            $comment_author_email = wp_slash( $user->user_email );
            $comment_author_url   = wp_slash( $user->user_url );
            if ( current_user_can('unfiltered_html') ) {
                if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
                    kses_remove_filters(); // start with a clean slate
                    kses_init_filters(); // set up the filters
                }
            }
        } else {
            if ( get_option('comment_registration') || 'private' == $status )
                wp_send_json_error( __('Sorry, you must be logged in to post a comment.') );
        }

        $comment_type = '';

        if ( get_option('require_name_email') && !$user->exists() ) {
            if ( 6 > strlen($comment_author_email) || '' == $comment_author )
                wp_send_json_error( __('<strong>ERROR</strong>: please fill the required fields (name, email).') );
            elseif ( !is_email($comment_author_email))
                wp_send_json_error( __('<strong>ERROR</strong>: please enter a valid email address.') );
        }

        if ( '' == $comment_content )
            wp_send_json_error( __('<strong>ERROR</strong>: please type a comment.') );

        $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

        $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

        $comment_id = wp_new_comment( $commentdata );

        $comment = get_comment($comment_id);
        do_action('set_comment_cookies', $comment, $user);

        $location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id;
        $location = apply_filters('comment_post_redirect', $location, $comment);

        wp_send_json_success(array(
            'location' => $location,
            'comment_id' => $comment_id,
            'post_id' => $comment_post_ID
        ));
        exit;
    }

}

$wedevs_bootstrap = new WeDevs_Bootstrap();