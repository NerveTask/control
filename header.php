<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package control
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

	<div class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<h1 class="site-title"><a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		</div>
		<div class="navbar-collapse collapse">

			<?php
				wp_nav_menu( array(
					'menu'              => 'primary',
					'theme_location'    => 'primary',
					'depth'             => 2,
					'container'         => 'div',
					'container_class'   => '',
					'menu_class'        => 'nav navbar-nav',
					'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
					'walker'            => new wp_bootstrap_navwalker())
				);
			?>

			<?php if( is_user_logged_in() ) { $current_user = wp_get_current_user(); ?>
			
			<div class="navbar-right">
				<ul id="menu-secondary" class="nav navbar-nav">
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children dropdown">
						<a title="<?php echo esc_attr( $current_user->display_name ); ?>" href="#" data-toggle="dropdown" class="dropdown-toggle" aria-haspopup="true"><?php echo esc_attr( $current_user->display_name ); ?>
							<span class="caret"></span>
						</a>
						<ul role="menu" class=" dropdown-menu">
							<li id="menu-item-168" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-168">
								<a title="Logout" href="http://local.nervetask.com/wp-login.php?action=logout&amp;_wpnonce=06cf89f330">Logout</a>
							</li>
						</ul>
					</li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom">
						<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i> New Task</a>
					</li>
				</ul>
			</div>
			
			<?php } else { ?>
			
			<div class="navbar-right">
				<ul id="menu-secondary" class="nav navbar-nav">
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children dropdown">
						<?php wp_loginout(); ?>
					</li>
				</ul>
			</div>
			
			<?php } ?>
			
			

		</div><!-- .nav-collapse -->
	</div><!-- .navbar -->

	<div id="content" class="site-content">