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
include(CONFU_PATH . 'includes/functions.php');


//==================================
//! 
//==================================
register_activation_hook( __FILE__, 'confu_activate' );
function confu_activate() {
	add_role( 
		'attendant', 
		'Deltager', 
		array(
			'read' => true
		) 
	);
	#remove_role( 'subscriber' );
}

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

function signupForm() {
	$date = date('Y-m-d');
	if( $date > '2013-10-04' ) {
		$output .= 'Tilmeldingen er lukket. Kontakt Hyggemester Cleo på <a href="mailto:hyggemester@eidolon.dk">hyggemester@eidolon.dk</a>, hvis du har nogen spørgsmål.';
		return $output;
	} else {
		ob_start();
		include( CONFU_PATH . 'includes/signupForm.tmpl.php');
		$content = ob_get_clean();
		return $content;
	}
}
add_shortcode( 'confu_tilmelding', 'signupForm' );

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

function confu_init() {
	$labels = array(
		'name' => _x('Aktiviteter', 'post type general name'),
		'singular_name' => _x('Aktivitet', 'post type singular name'),
		'add_new' => _x('Tilføj ny', ''),
		'add_new_item' => __('Tilføj ny aktivitet'),
		'edit_item' => __('Rediger aktivitet'),
		'new_item' => __('Ny aktivitet'),
		'all_items' => __('Alle aktiviteter'),
		'view_item' => __('Vis aktivitet'),
		'search_items' => __('Søg i aktiviteter'),
		'not_found' =>  __('Ingen aktiviteter fundet'),
		'not_found_in_trash' => __('Ingen aktiviteter fundet i papirkurven'), 
		'menu_name' => 'Aktiviteter'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor', 'thumbnail' )
	); 
	register_post_type('aktiviteter',$args);
}
add_action( 'init', 'confu_init' );
?>