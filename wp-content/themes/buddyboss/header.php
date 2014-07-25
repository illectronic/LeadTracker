<?php

$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if ($host == $_SERVER['SERVER_NAME']."/"){

$conn = mysql_connect('localhost', constant('DB_USER'), constant('DB_PASSWORD'),'true','65536');
mysql_select_db(constant('DB_NAME')) or die("cannot use database");
if (!$conn) {

    die('Not connected : ' . mysql_error());

}
global $current_user;
get_currentuserinfo();
$group_slug=mysql_query("select group_slug from wp_users WHERE user_login= '{$current_user->user_login}';",$conn);
$slug = mysql_fetch_row($group_slug);
$currentgroups=mysql_query("select group_id from wp_bp_groups_members where user_id= '{$current_user->ID}';",$conn);
$group_id=mysql_query("select group_id from wp_users WHERE user_login= '{$current_user->user_login}';",$conn);
$id= mysql_fetch_row($group_id);

$i=0;
while($row=mysql_fetch_array($currentgroups)){
if ($row['group_id'] == $id[0]){
wp_redirect("https://".$_SERVER['SERVER_NAME']."/clients/".$slug[0]);
exit;
}
else{
$i=$i+1;
}
}
mysql_close($conn);


}



/**
 * The Header for your theme.
 *
 * Displays all of the <head> section and everything up until <div id="main">
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */
?><!DOCTYPE html>
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE > 8]>
<html class="ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if ! IE  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico" type="image/x-icon">
<!-- BuddyPress and bbPress Stylesheets are called in wp_head, if plugins are activated -->
<?php
wp_head(); 
?>
</head>

<body <?php if ( current_user_can('manage_options') ) : ?>id="role-admin"<?php endif; ?> <?php body_class(); ?>>
<?php do_action( 'buddyboss_before_header' ); ?>

<header id="masthead" class="site-header" role="banner">

	<div class="header-inner">

        <!-- Look for uploaded logo -->
        <?php if ( get_theme_mod( 'buddyboss_logo' ) ) : ?>           
            <div id="logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_theme_mod( 'buddyboss_logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a>
            </div>

        <!-- If no logo, display site title and description -->
        <?php else: ?>          
            <div class="site-name">
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </h1>
                <p class="site-description"><?php bloginfo( 'description' ); ?></p>
            </div>
        <?php endif; ?>

        <!-- Register/Login links for logged out users -->
        <?php if ( !is_user_logged_in() && buddyboss_is_bp_active() && !bp_hide_loggedout_adminbar( false ) ) : ?>

            <div class="header-account">
                <?php if ( buddyboss_is_bp_active() && bp_get_signup_allowed() ) : ?>
                    <a href="<?php echo bp_get_signup_page(); ?>"><?php _e( 'Register', 'buddyboss' ); ?></a>
                <?php endif; ?>

                <a href="<?php echo wp_login_url(); ?>" class="button"><?php _e( 'Login', 'buddyboss' ); ?></a>
            </div>

        <?php endif; ?>

	</div>

	<nav id="site-navigation" class="main-navigation" role="navigation">
		<div class="nav-inner">

			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'buddyboss' ); ?>"><?php _e( 'Skip to content', 'buddyboss' ); ?></a>
			<?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'nav-menu clearfix' ) ); ?>
		</div>
	</nav><!-- #site-navigation -->
</header><!-- #masthead -->

<?php do_action( 'buddyboss_after_header' ); ?>

<div id="main-wrap"> <!-- Wrap for Mobile -->
    <div id="mobile-header">
    	<div class="mobile-header-inner">
        	<div class="left-btn">
				<?php if ( is_user_logged_in() ) : ?>
                    <a href="#" id="user-nav" class="closed "></a>
                <?php elseif ( !is_user_logged_in() && buddyboss_is_bp_active() && !bp_hide_loggedout_adminbar( false ) ) : ?>
                    <a href="#" id="user-nav" class="closed "></a>
                <?php endif; ?>
            </div>

            <div class="mobile-header-content">
            	<a class="mobile-site-title" href="#" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
            </div>

            <div class="right-btn">
                <a href="#" id="main-nav" class="closed"></a>
            </div>
        </div>
    </div>
<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<label>
		<input type="search" class="search-field" placeholder="Search..." value="" name="s" title="Search for:" />
	</label>
	<input type="submit" class="search-submit" value="Search" />
</form>
<!-- #mobile-header -->

    <div id="inner-wrap"> <!-- Inner Wrap for Mobile -->
    	<?php do_action( 'buddyboss_inside_wrapper' ); ?>
        <div id="page" class="hfeed site">
			<div id="swipe-area">
            </div>
            <div id="main" class="wrapper">
