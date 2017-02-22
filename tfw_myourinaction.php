<?php
/**
 * @package tfw_myourinaction
 * @version 0.5
 */
/*
Plugin Name: tfw_myourinaction
Plugin URI: http://myourinaction.com
Description: The plugin to control myinaction.com
Author: Patricia Ryan Lee
Version: 0.5
Author URI: http://thefierywell.com/
*/
/**
 * Customize WordPress Toolbar
 *
 * @param obj $wp_admin_bar An instance of the global object WP_Admin_Bar
 */


if ( !defined( 'TFW_PLUGIN_DIR' ) ) {
    define( 'TFW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'TFW_PLUGIN_URL' ) ) {
    define( 'TFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'TFW_PLUGIN_FILE' ) ) {
    define( 'TFW_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'CAL_GREGORIAN' ) ) {
    define( 'CAL_GREGORIAN', 1 );
}
@require('tfw_cpt.php'); 
@require('tfw_review.php'); 
@require('tfw_bp_profile.php');	
@require('tfw_bp_events.php'); 
//@require('tfw_bp-templates.php'); 


function change_footer_admin () { 
   echo 'Made with lots of coffee and The Fiery Well.'; 
 } 
add_filter('admin_footer_text', 'change_footer_admin');


function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'high' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
        remove_meta_box( 'tribe_dashboard_widget', 'dashboard', 'normal');//since 3.8
	
}
add_action( 'admin_init', 'remove_dashboard_meta' );




function __my_registration_redirect(){
    wp_redirect( '/welcome' );
    exit;
}
add_filter( 'registration_redirect', '__my_registration_redirect' );
