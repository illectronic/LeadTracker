<?php

/**
 * BuddyPress - Activity Post Form
 *
 * @package BuddyPress
 * @subpackage buddyboss
 */
?>

<?php global $buddyboss_wall, $bp; ?>

<?php if ( bp_is_group() || bp_is_my_profile() || bp_is_current_component( 'activity' ) ): ?>

	<?php if ( !is_user_logged_in() ) : ?>
		<div id="message">
			<p><?php printf( __( 'You need to <a href="%s" title="Log in">log in</a>', 'buddyboss' ), wp_login_url() ); ?><?php if ( bp_get_signup_allowed() ) : ?><?php printf( __( ' or <a class="create-account" href="%s" title="Create an account">create an account</a>', 'buddyboss' ), bp_get_signup_page() ); ?><?php endif; ?><?php _e( ' to post to this user\'s Wall.', 'buddyboss' ); ?></p>
		</div>

	<?php elseif (!bp_is_my_profile() && (!is_super_admin() && !buddyboss_is_admin()) && (bp_is_user() && (BUDDYBOSS_WALL_ENABLED && !$buddyboss_wall->is_friend($bp->displayed_user->id)) )):?>

		<div id="message" class="info">
			<p><?php printf( __( "You and %s are not friends. Request friendship to post to their Wall.", 'buddyboss' ), bp_get_displayed_user_fullname() ) ?></p>
		</div>

	<?php else:?>

		<?php if ( is_user_logged_in() ) : ?>

			<?php if ( isset( $_GET['r'] ) ) : ?>
				<div id="message" class="info">
					<p><?php printf( __( 'You are mentioning %s in a new update, this user will be sent a notification of your message.', 'buddyboss' ), bp_get_mentioned_user_display_name( $_GET['r'] ) ) ?></p>
				</div>
			<?php endif; ?>

			<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" role="complementary">

			<?php do_action( 'bp_before_activity_post_form' ); ?>

				<p class="activity-greeting">

  				Post update to:
				</p>


				<div id="whats-new-icons">
					<?php if (BUDDYBOSS_PICS_ENABLED): ?>
						<div id="whats-new-pic"><?php _e( 'Add Photo', 'buddyboss' ); ?></div>
						<div class="buddyboss-pics-progress">
							<div class="buddyboss-pics-progress-value">0%</div>
							<progress class="buddyboss-pics-progress-bar" value="0" max="100"></progress>
						</div>
					<?php endif; ?>
					<div id="whats-new-pic-uploader"></div>
				</div><!-- #whats-new-icons -->

				<div id="whats-new-content">
<div id ="whats-new-textarea">
   <div id="whats-new-post-in-box">

                                                                <div class="whats-new-select no-ajax" id="filter">
                                    <ul>
                                        <li id="members-order-select" class="last filter">
                                            <label for="members-order-by"><?php _e( 'Post in:', 'buddyboss' ); ?></label>
                                            <select id="whats-new-post-in" name="whats-new-post-in">
                                                <option value="">Select a Client</option>
                                               <?php


$conn = mysql_connect('localhost', constant('DB_USER'), constant('DB_PASSWORD'),'true','65536');
mysql_select_db(constant('DB_NAME')) or die("cannot use database");
if (!$conn) {

    die('Not connected : ' . mysql_error());

}
global $current_user;
get_currentuserinfo();

$result=mysql_query("select distinct wp_bp_groups.id,wp_bp_groups.name,wp_bp_groups.slug from wp_bp_groups,wp_bp_groups_members where wp_bp_groups_members.group_id=wp_bp_groups.id and wp_bp_groups_members.user_id='{$current_user->ID}' order by wp_bp_groups.name;",$conn);
$url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];   
$selected = ' selected';
while($row=mysql_fetch_array($result))
{echo "<option value=" . $row[0] . " data-href='" . $row[2] . "'" . ( false !== strpos($url, $row[2])  ? $selected : '' )  . " >";
echo $row[1] . "</option>"; 
}
if (false !== strpos($url,'?group_slug=')) {
mysql_query("UPDATE wp_users SET group_slug='{$_GET['group_slug']}' WHERE user_login= '{$current_user->user_login}';",$conn);
mysql_query("UPDATE wp_users SET group_id='{$_GET['group_id']}' WHERE user_login= '{$current_user->user_login}';",$conn);
}
else{
}

mysql_close($conn);
?>  
<script>$( document ).ready(function() {$("#whats-new-post-in").change(function(){
    var main = '<?php echo "https://".$_SERVER['SERVER_NAME']."/clients/" ?>';
	var link = $(this).find(":selected").attr("data-href");
        var id = $(this).find(":selected").attr("value");
	if (typeof link === 'undefined') { 
		return false; 
	} if (typeof id === 'undefined') {
                return false;
        }
        if ( id === ''){
               return false;
         }

	var href = main + link + '\?group_slug=' + link + '\&group_id=' + id;
	window.location.href = href;
});
});
</script>
</select>

                                   
</ul>
                                        </div> <!-- /.whats-new-select -->
                                                        </div> <!-- /.whats-new-post-in-box -->
<?php 
$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if($host != $_SERVER['SERVER_NAME']."/")
{
echo "<textarea name='whats-new' id='whats-new' cols='50' rows='10'>";
if ( isset( $_GET['r'] ) ){echo esc_attr( @$_GET['r'] );} 
echo "</textarea></div><div id='whats-new-options'><div id='whats-new-submit'>";
echo "<button type='submit' name='aw-whats-new-submit' id='aw-whats-new-submit'>";
_e( 'Post Update', 'buddyboss' );
echo "</button></div>";
}
?>
						<?php if ( bp_is_active( 'groups' ) && !bp_is_my_profile() && !bp_is_group() && !bp_is_user_activity() ) : ?>


							<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

						<?php elseif ( bp_is_group_home() ) : ?>

							<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
							<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

						<?php endif; ?>

						<?php do_action( 'bp_activity_post_form_options' ); ?>

					</div><!-- #whats-new-options -->

					<div class="clearfix" id="whats-new-pic-preview">
						<div class="clearfix" id="whats-new-pic-preview-inner"></div>
					</div><!-- #whats-new-pic-preview -->

				</div><!-- #whats-new-content -->

				<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
				<?php do_action( 'bp_after_activity_post_form' ); ?>

			</form><!-- #whats-new-form -->

		<?php endif; ?>

	<?php endif; ?>
<?php endif; ?>
