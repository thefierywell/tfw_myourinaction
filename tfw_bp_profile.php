<?php
/**
 * The tabs for the BuddyPress profile
 * @version 0.5
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


add_action( 'bp_setup_nav', 'add_videos_subnav_tab', 100 );

function add_videos_subnav_tab() {
	global $bp;

	bp_core_new_subnav_item( array(
		'name' => 'Videos',
		'slug' => 'videos',
		'parent_url' => trailingslashit( bp_loggedin_user_domain() . 'blogs' ),
		'parent_slug' => 'blogs',
		'screen_function' => 'profile_screen_video',
		'position' => 50
		)
	);
}

// redirect to videos page when 'Videos' tab is clicked
// assumes that the slug for your Videos page is 'videos' 
function profile_screen_video() {
	bp_core_redirect( get_option('siteurl') . "/sites/create/" );
}

function add_myourinaction_tabs() {
	global $bp;
	
	/*bp_core_new_nav_item( array(
		'name'                  => 'Account',
		'slug'                  => 'account',
		'parent_url'            => $bp->displayed_user->domain,
		'parent_slug'           => $bp->profile->slug,
		'screen_function'       => 'profile_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'profile',
		'show_for_displayed_user' => false
		
	) );
	*/
	
	bp_core_new_nav_item( array(
		'name'                  => 'My Reps',
		'slug'                  => 'myreps',
		'parent_url'            => $bp->displayed_user->domain,
		'parent_slug'           => $bp->profile->slug,
		'screen_function'       => 'myreps_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'myreps',
		'show_for_displayed_user' => false
		
	) );
	bp_core_new_subnav_item( array(
		'parent_id'				=> 'myreps',
		'slug'					=> 'senate',
		'name'                  => 'Senate',
		'parent_url'            => $bp->displayed_user->domain.'myreps/',
		'parent_slug'           => 'myreps',
		'screen_function'       => 'myreps_senate_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'senate'
	) );
	bp_core_new_subnav_item( array(
		'parent_id'				=> 'myreps',
		'slug'					=> 'senate_cabinet_votes',
		'name'                  => 'Key Administration Nominations',
		'parent_url'            => $bp->displayed_user->domain.'myreps/',
		'parent_slug'           => 'myreps',
		'screen_function'       => 'myreps_cabinet_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'senate_cabinet_votes'
	) );

	bp_core_new_subnav_item( array(
		'parent_id'				=> 'myreps',
		'slug'					=> 'townhalls',
		'name'                  => 'Townhalls',
		'parent_url'            => $bp->displayed_user->domain.'myreps/',
		'parent_slug'           => 'myreps',
		'screen_function'       => 'myreps_townhall_screen',			
		'position'              => 200,
		'default_subnav_slug'   => 'townhalls'
	) );
	

}
add_action( 'bp_setup_nav', 'add_myourinaction_tabs', 100 );



function mycallsinaction_screen() {
    add_action( 'bp_template_title', 	'mycallsinaction_screen_title' );
    add_action( 'bp_template_content', 'mycallsinaction_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function mycallsinaction_screen_title() { 
	echo '<h1>UNDER CONSTRUCTION</h1>';
	bp_show_blog_signup_form();
	//echo 'My Calls<br/>';
}
function mycallsinaction_screen_content() { 
	//gravity_form( 1, $display_title = true, $display_description = true, $display_inactive = false, $field_values = null, $ajax = false, $tabindex, $echo = true );
}









function myreps_senate_screen() {
    add_action( 'bp_template_title', 'myreps_senate_screen_title' );
    add_action( 'bp_template_content', 'myreps_senate_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function myreps_senate_screen_title() { 
	echo '<h1>My Senators</h1>';
}
function myreps_senate_screen_content() { 
	$useraddress = urlencode(xprofile_get_field_data( "Location" , bp_displayed_user_id()));

	$google_geocode = 'http://maps.google.com/maps/api/geocode/json?address='.$useraddress.'&sensor=false';
	$google_geocode_json_string = wp_remote_get($google_geocode);
	$google_geocode_json = json_decode(wp_remote_retrieve_body($google_geocode_json_string));
	$state = urlencode($google_geocode_json->results[0]->address_components[5]->short_name);
	
	$members_state_api = wp_remote_get("https://api.propublica.org/congress/v1/members/senate/".$state."/current.json", array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX")) or die('Could not reach API');
	$members_state = json_decode(wp_remote_retrieve_body($members_state_api), true)  or die(var_dump(json_last_error()));
	
	$members_state = $members_state['results'];
	//$senator = array();

	$offices_api = wp_remote_get('https://raw.githubusercontent.com/jlgoldman/membersofcongress/master/members.json');
	$offices = json_decode(wp_remote_retrieve_body($offices_api), true);
	$senator_office = array(); //declare array
	foreach($offices as $office){
		foreach($office['offices'] as $key=>$value){
			$senator_office[$office['bioguide_id']][$value['city']]['city'] = $value['city'];
			$senator_office[$office['bioguide_id']][$value['city']]['line1'] = $value['line1'];
			$senator_office[$office['bioguide_id']][$value['city']]['line2'] = $value['line2'];
			$senator_office[$office['bioguide_id']][$value['city']]['state'] = $value['state_code'];
			$senator_office[$office['bioguide_id']][$value['city']]['zip'] 	= $value['zip'];
			$senator_office[$office['bioguide_id']][$value['city']]['phone'] = $value['phone'];
		}
	}
	echo '<table>';
	foreach($members_state as $key=>$value){
		$senators_api = wp_remote_get($value['api_url'], array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX"));
		$senators = json_decode(wp_remote_retrieve_body($senators_api), true);
		$senators = $senators['results'];
		echo '<tr>';
		foreach($senators as $key=>$senator){
			echo '<td>';
			echo '<h4>'.$senator['first_name'].' '.$senator['last_name'].'</h4>';
			# @images https://github.com/unitedstates/images
			echo '<img src="https://theunitedstates.io/images/congress/225x275/'.$senator['member_id'].'.jpg">';
			echo '</td><td>';
			foreach($senator_office[$senator['member_id']] as $sen_key=>$sen_office){
				echo '<strong>'.$sen_office['city'].'</strong>';
				echo '<address>';
				if(!empty($sen_office['line1'])){ echo $sen_office['line1']; }
				if(!empty($sen_office['line2'])){ echo '<br>'.$sen_office['line2']; }
				if(!empty($sen_office['city'])){ echo '<br>'.$sen_office['city']; }
				if(!empty($sen_office['state'])){ echo '&nbsp;'.$sen_office['state']; }
				if(!empty($sen_office['zip'])){ echo '&nbsp;'.$sen_office['zip']; }
				if(!empty($sen_office['phone'])){ echo '<p>Phone: <a href="tel:'.$sen_office['phone'].'">'.$sen_office['phone'].'</a></p>'; }
				echo '</address>';
			}
			echo '</td>';

		}
		echo '</tr>';
	}
	echo '</table>';
}


function myreps_screen() {
    add_action( 'bp_template_title', 'myreps_screen_title' );
    add_action( 'bp_template_content', 'myreps_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function myreps_screen_title() { 
	echo '<h1>My Reps</h1>';
}
function myreps_screen_content() { 
	$useraddress = urlencode(xprofile_get_field_data( "Location" , bp_displayed_user_id()));

	$google_geocode = 'http://maps.google.com/maps/api/geocode/json?address='.$useraddress.'&sensor=false';
	$google_geocode_json_string = wp_remote_get($google_geocode);
	$google_geocode_json = json_decode(wp_remote_retrieve_body($google_geocode_json_string));
	$state = urlencode($google_geocode_json->results[0]->address_components[5]->short_name);
	
	if(empty($useraddress)){
		echo 'Please <a href="../profile/edit/group/1/">update your profile</a> with your location (at least your zip-code)';
	} else {
		$url = "https://www.googleapis.com/civicinfo/v2/voterinfo?key=AIzaSyAcDYLthzdgv8Iy-qEJY8yUNNniGl9C-eM&address=".$useraddress."&electionId=2000";
		$json_string =    wp_remote_get($url);
		$parsed_json = json_decode(wp_remote_retrieve_body($json_string), true);
		foreach($parsed_json['pollingLocations'] as $key => $location){
			//electionAdministrationBody
				echo '<h4>Polling Location</h4>';
				echo '<p>'.$location['address']['locationName'].'<br>';
				echo $location['address']['line1'].'<br>';
			if(!empty($location['address']['line2'])){
				echo $location['address']['line2'].'<br>';
			}
			if(!empty($location['address']['line3'])){
				echo $location['address']['line3'].'<br>';
			}
			echo $location['address']['city'].', '.$location['address']['state'].' '.$location['address']['zip'].'</p>';
			if(!empty($location['pollingHours'])){
			echo '<p>Polling Hours: '.$location['pollingHours'].'</p>';
			}
		}		
		foreach($parsed_json['state'] as $key => $state){
			echo '<h4>Election Administration Body</h4>';
			echo $state['electionAdministrationBody']['name'].'<br>';
			echo $state['electionAdministrationBody']['electionInfoUrl'].'<br>';			
			
			echo '<h4>Local Jurisdiction</h4>';
			echo $state['local_jurisdiction']['name'].'<br>';
			echo $state['local_jurisdiction']['electionAdministrationBody']['electionInfoUrl'];
		}
		echo '<hr>';

		$url = "https://www.googleapis.com/civicinfo/v2/representatives?key=AIzaSyAcDYLthzdgv8Iy-qEJY8yUNNniGl9C-eM&address=".$useraddress;
		$json_string =    wp_remote_get($url);
		$parsed_json = json_decode(wp_remote_retrieve_body($json_string), true);
		$members_state_api = wp_remote_get("https://api.propublica.org/congress/v1/members/senate/".$state."/current.json", array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX"));
		$members_state = json_decode(wp_remote_retrieve_body($members_state_api), true);
		$members_state = $members_state['results'];	
		foreach($parsed_json['offices'] as $key => $offices){
				echo '<h4>'.$offices['name'].'</h4>';
				foreach($offices['officialIndices'] as $indicies){
					foreach($parsed_json['officials'] as $k => $officials){
						if($k == $indicies){
						echo $officials['name'].', ';
							echo 'Party: '.$officials['party'].'<br>';
							if(!empty($officials['photoUrl'])){
								echo '<img src="'.$officials['photoUrl'].'" width="100"><br>';
							}
							foreach($officials['address'] as $address){
								echo $address['line1'].'<br>';
								echo $address['city'].', ';
								echo $address['state'].' ';
								echo $address['zip'].'<br>';

							}
							foreach($officials['phones'] as $phones){
								echo $phones.'<br>';
							}
							if(!empty($officials['emails'])){
								foreach($officials['emails'] as $emails){
									echo $emails.'<br>';
								}
							}
							//echo var_dump($officials['channels']);
							if(!empty($officials['channels'])){
								echo '<ul>';
								foreach($officials['channels'] as $channel_key => $channel_value){
									if($channel_value['type'] == 'Facebook'){
										echo '<li>Facebook: <a href="https://www.facebook.com/'.$channel_value['id'].'">'.$channel_value['id'].'</a></li>';
									}
									if($channel_value['type'] == 'Twitter'){
										echo '<li>Twitter: <a href="https://www.twitter.com/'.$channel_value['id'].'">'.$channel_value['id'].'</a></li>';
									}
									if($channel_value['type'] == 'GooglePlus'){
										echo '<li>Google+: <a href="https://plus.google.com/'.$channel_value['id'].'">'.$channel_value['id'].'</a></li>';
									}
									if($channel_value['type'] == 'YouTube'){
										echo '<li>YouTube: <a href="https://www.youtube.com/'.$channel_value['id'].'">'.$channel_value['id'].'</a></li>';
									}
								}
								echo '</ul>';
							}
							if(!empty($officials['urls'])){
								foreach($officials['urls'] as $urls){
									echo '<a href="'.$urls.'">'.$urls.'</a><br>';
								}
							}
						}

					}
				}
				echo '<hr>';
		}	
	}	
}



function myreps_townhall_screen() {
    add_action( 'bp_template_title', 'myreps_townhall_screen_title' );
    add_action( 'bp_template_content', 'myreps_townhall_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function myreps_townhall_screen_title() { 
	echo '<h1>Townhalls</h1>';
}

function myreps_townhall_screen_content() { 
	$members_state_api = wp_remote_get("https://actionnetwork.org/api/v2/people", array('headers' => "OSDI-API-Token: 4adc2aa8b6a92b9fabc6165c1f991e3b"));
	$members_state = json_decode(wp_remote_retrieve_body($members_state_api), true);
	echo 'link:'. $members_state['_embedded']['osdi:events'][0]['identifiers'][0];
	foreach($members_state['_embedded']['osdi:people'] as $people){
		foreach($people['email_addresses'] as $email){
			echo $email['address'];

		}
	}
	
}


function myreps_cabinet_screen() {
    add_action( 'bp_template_title', 'myreps_cabinet_screen_title' );
    add_action( 'bp_template_content', 'myreps_cabinet_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function myreps_cabinet_screen_title() { 
	echo '<h1>Key Administration Nominations</h1>';
	echo '<h2>Side by Side Vote Comparison</h2>';
	echo '<p>How did each of your Senators vote?</p>';
}
function myreps_cabinet_screen_content() { 
	//KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX //https://api.propublica.org/congress/{version}/

	$useraddress = urlencode(xprofile_get_field_data( "Location" , bp_displayed_user_id()));

	$google_geocode = 'http://maps.google.com/maps/api/geocode/json?address='.$useraddress.'&sensor=false';
	$google_geocode_json_string = wp_remote_get($google_geocode);
	$google_geocode_json = json_decode(wp_remote_retrieve_body($google_geocode_json_string));
	$state = urlencode($google_geocode_json->results[0]->address_components[5]->short_name);
	
	$members_state_api = wp_remote_get("https://api.propublica.org/congress/v1/members/senate/".$state."/current.json", array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX"));
	$members_state = json_decode(wp_remote_retrieve_body($members_state_api), true);
	$members_state = $members_state['results'];
	$senator = array();
	foreach($members_state as $key=>$value){
		$senator[$value['id']]['id'] = $value['id'];
		$senator[$value['id']]['name'] = $value['name'];
	}

	$nominations_api = wp_remote_get("https://api.propublica.org/congress/v1/115/nominations.json", array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX"));
	$nominations = json_decode(wp_remote_retrieve_body($nominations_api));
	$nomination = array();
	$vote = array();
		
	foreach($nominations->results[0]->votes as $nom){
		$nomination[$nom->roll_call]['id'] = $nom->roll_call;
		$nomination[$nom->roll_call]['description'] = $nom->description;
		$rollcall_api = wp_remote_get("https://api.propublica.org/congress/v1/115/senate/sessions/1/votes/".$nom->roll_call.".json", array('headers' => "X-API-Key: KPA5tOejtA48K9uItZdRo4g5lXl9vj6A53yAjxFX"));
		$rollcall = json_decode(wp_remote_retrieve_body($rollcall_api));
		foreach($rollcall->results->votes->vote->positions as $position){
			$vote[$position->member_id][$nom->roll_call]['position'] = $position->vote_position;
		}
	}
	
	echo '<table class="table"><thead><tr><th width="70%">Nominee</th>';
	foreach($senator as $senator_id){
		echo '<th>'.$senator_id['name'].'</th>';
	}
	echo '</tr></thead>';
	foreach($nomination as $nominee){
		echo '<tr>';
		echo ' <td>'.$nominee['description'].'</td>';
		foreach($senator as $senator_id){
			echo '<td>';
			echo $vote[$senator_id['id']][$nominee['id']]['position'];
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}