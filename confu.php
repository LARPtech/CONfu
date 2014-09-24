<?php
/**
 * Plugin Name: CONfu
 * Plugin URI: http://mertzmedia.dk/projects/confu
 * Description: A simple plugin for taking signups for a roleplaying convention.
 * Version: 0.8
 * Author: Thomas Mertz
 * Author URI: http://mertzmedia.dk/
 */

define('CONFU_PATH', plugin_dir_path( __FILE__ ) );
include(CONFU_PATH . 'includes/p2p-connection-types.inc.php');
include(CONFU_PATH . 'includes/functions.php');

//==================================
//! ACTIVATION FUNCTIONS
//==================================
function confu_activate() {
	add_role( 
		'attendant', 
		'Deltager', 
		array(
			'read' => true
		) 
	);
	remove_role( 'subscriber' );
}
register_activation_hook( __FILE__, 'confu_activate' );

//==================================
//! LOADING TRANSLATIONS
//==================================
function confu_lang_init() {
	load_plugin_textdomain( 'confu', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
}
add_action( 'init', 'confu_lang_init' );

//==================================
//! SHORTCODES
//==================================
function confu_attendees_shortcode(){
	$attendees = get_users('role=attendant&orderby=display_name&order=ASC');
	$output .= '<ul>'; 
	foreach($attendees as $attendee) {
		$output .= '<li>' . $attendee->display_name . '</li>';
	}
	$output .= '</ul>';
	return $output;
}
add_shortcode( 'confu_attendees', 'confu_attendees_shortcode' );

function confu_signup_shortcode(){
	include( CONFU_PATH . 'includes/frontend/signupForm.inc.php');
}
add_shortcode( 'confu_signup', 'confu_signup_shortcode' );

function confu_profile_shortcode(){
	include( CONFU_PATH . 'includes/frontend/attendeeProfile.inc.php');
}
add_shortcode( 'confu_profile', 'confu_profile_shortcode' );

//==================================
//! 
//==================================
add_action( 'admin_menu', 'confu_menu_pages' );
function confu_menu_pages(){
	add_menu_page( 'CONfu', 'CONfu', 'moderate_comments', CONFU_PATH.'includes/admin/confu_overview.php', '', plugins_url( 'confu/assets/images/cake.png' ), 3 );
	add_submenu_page( CONFU_PATH.'includes/admin/confu_overview.php', 'CONfu Deltagere', 'CONfu Deltagere', 'moderate_comments', 'confu_deltagere', 'confu_admin_participants_page_callback' );
	add_submenu_page( CONFU_PATH.'includes/admin/confu_overview.php', 'CONfu Aktiviteter', 'CONfu Aktiviteter', 'moderate_comments', 'confu_activities', 'confu_admin_activities_page_callback' );
}
function confu_admin_participants_page_callback() {
	include(CONFU_PATH.'includes/admin/confu_participants.php');
}
function confu_admin_activities_page_callback() {
	include(CONFU_PATH.'includes/admin/confu_activities.php');
}


//==================================
//! DASHBOARD WIDGET
//==================================
function confu_core_numbers_widget() {
	include( CONFU_PATH . 'includes/admin/confu_dashboard_widget.php');
}
function add_confu_dashboard_widgets() {
	wp_add_dashboard_widget('confu_core_stats', 'CONfu Kernetal', 'confu_core_numbers_widget');
}
add_action('wp_dashboard_setup', 'add_confu_dashboard_widgets' );


//==================================
//! CSS AND JS QUEUE
//==================================
function confu_queued() {
	wp_enqueue_style(
		'confu_css',
		plugin_dir_url(__FILE__) . 'assets/css/confu.css',
		'1.0'
	);
	wp_enqueue_script( 
		'script-name', 
		plugin_dir_url(__FILE__) . 'assets/js/confu.js', 
		array('jquery'), 
		'1.0.0', 
		true 
	);
}
add_action('wp_enqueue_scripts', 'confu_queued');


//==================================
//! CUSTOM POST TYPES
//==================================
include(CONFU_PATH . 'includes/functions/cpt-confu_activity.inc.php');