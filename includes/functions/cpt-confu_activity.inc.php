<?php
function confu_activity_init() {
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
		'rewrite' => array( 'slug' => 'programmet' ),
		'capability_type' => 'post',
		'menu_icon' => 'dashicons-calendar',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor', 'thumbnail' )
	); 
	register_post_type('aktiviteter',$args);
	
	register_taxonomy(
		'days',
		'aktiviteter',
		array(
			'label' => __( 'Dage' ),
			'rewrite' => array( 'slug' => 'dag' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'confu_activity_type',
		'aktiviteter',
		array(
			'label' => __( 'Type' ),
			'rewrite' => array( 'slug' => 'aktivitetstype' ),
			'hierarchical' => true,
		)
	);
}
add_action( 'init', 'confu_activity_init' );