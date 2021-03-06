<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since BuddyBoss 3.0
 */
function buddyboss_child_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss' instances in all child theme files to 'buddyboss_child'.
  // load_theme_textdomain( 'buddyboss_child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_child_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since BuddyBoss 3.0
 */
function buddyboss_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'buddyboss-child-custom', get_stylesheet_directory_uri().'/css/custom.css' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here
function format_activity_date() {
 $activityDate=bp_get_activity_date_recorded();
  // Get GMT offset from root blog
  $root_blog_offset = get_blog_option( BP_ROOT_BLOG, 'gmt_offset' );
  // Calculate offset time
  $time_offset = $time + ( $root_blog_offset * 3600 );
  // Format the time using the offset and return it; date-i18n retrieves the date in localized format
  return 'on ' . date_i18n("n/d/y", strtotime($activityDate) + $time_offset) . '';
}
add_filter('bp_activity_time_since', 'format_activity_date');
function keep_me_logged_in_for_1_day( $expirein ) {

   return 86400; // 1 day in seconds

}

add_filter( 'auth_cookie_expiration', 'keep_me_logged_in_for_1_day' );

?>

