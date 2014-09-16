<?php
function confu_signup_init() {
	$labels = array(
		'name' => _x('Tilmeldinger', 'post type general name'),
		'singular_name' => _x('Tilmelding', 'post type singular name'),
		'add_new' => _x('Tilføj ny', 'tilmelding'),
		'add_new_item' => __('Tilføj ny tilmelding'),
		'edit_item' => __('Rediger tilmelding'),
		'new_item' => __('Ny tilmelding'),
		'all_items' => __('Alle tilmeldinger'),
		'view_item' => __('Vis tilmelding'),
		'search_items' => __('Søg i tilmeldinger'),
		'not_found' =>  __('Ingen tilmeldinger fundet'),
		'not_found_in_trash' => __('Ingen tilmeldinger fundet i papirkurven'), 
		'menu_name' => 'Tilmeldinger'
	);
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => array( 'slug' => 'programmet' ),
		'capability_type' => 'post',
		'menu_icon' => 'dashicons-tickets',
		'has_archive' => false, 
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor', 'thumbnail' )
	); 
	register_post_type('confu_signup',$args);
}
add_action( 'init', 'confu_signup_init' );