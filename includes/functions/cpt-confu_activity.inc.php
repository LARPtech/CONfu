<?php
function confu_activity_init() {
	$labels = array(
		'name' => _x('Aktiviteter', 'post type general name','confu' ),
		'singular_name' => _x('Aktivitet', 'post type singular name','confu' ),
		'add_new' => _x('Tilføj ny', '','confu' ),
		'add_new_item' => __('Tilføj ny aktivitet','confu' ),
		'edit_item' => __('Rediger aktivitet','confu' ),
		'new_item' => __('Ny aktivitet','confu' ),
		'all_items' => __('Alle aktiviteter','confu' ),
		'view_item' => __('Vis aktivitet','confu' ),
		'search_items' => __('Søg i aktiviteter','confu' ),
		'not_found' =>  __('Ingen aktiviteter fundet','confu' ),
		'not_found_in_trash' => __('Ingen aktiviteter fundet i papirkurven','confu' ), 
		'menu_name' => __('Aktiviteter','confu' )
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
			'label' => __( 'Dage', 'confu' ),
			'rewrite' => array( 'slug' => 'dag' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'confu_activity_type',
		'aktiviteter',
		array(
			'label' => __( 'Type' ,'confu' ),
			'rewrite' => array( 'slug' => 'aktivitetstype' ),
			'hierarchical' => true,
		)
	);
}
add_action( 'init', 'confu_activity_init' );