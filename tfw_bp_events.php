<?php

function add_tfw_bp_events_tabs() {
	global $bp;
	
	
	
	bp_core_new_nav_item( array(
		'name'                  => 'Calendar',
		'slug'                  => 'events',
		'parent_url'            => $bp->displayed_user->domain,
		'parent_slug'           => $bp->profile->slug,
		'screen_function'       => 'tfw_bp_events_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'events',
		'show_for_displayed_user' => false
		
	) );
	
	

}
add_action( 'bp_setup_nav', 'add_tfw_bp_events_tabs', 100 );




function tfw_bp_events_screen() {
    add_action( 'bp_template_title', 	'tfw_bp_events_screen_title' );
    add_action( 'bp_template_content', 'tfw_bp_events_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function tfw_bp_events_screen_title() { 
	echo '<h1>Events</h1>';
}
function tfw_bp_events_screen_content() { 
	echo do_shortcode('[tribe-user-event-confirmations]');
	//gravity_form( 1, $display_title = true, $display_description = true, $display_inactive = false, $field_values = null, $ajax = false, $tabindex, $echo = true );
}



