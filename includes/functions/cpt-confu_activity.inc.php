<?php
function confu_activity_init() {
	$labels = array(
		'name' => _x('Activities', 'post type general name','confu' ),
		'singular_name' => _x('Activity', 'post type singular name','confu' ),
		'add_new' => _x('Add new', '','confu' ),
		'add_new_item' => __('Add new activity','confu' ),
		'edit_item' => __('Edit activity','confu' ),
		'new_item' => __('New activity','confu' ),
		'all_items' => __('All activities','confu' ),
		'view_item' => __('View activity','confu' ),
		'search_items' => __('Search activities','confu' ),
		'not_found' =>  __('No activities found.','confu' ),
		'not_found_in_trash' => __('No activities found in trash.','confu' ), 
		'menu_name' => __('Activities','confu' )
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
			'label' => __( 'Days', 'confu' ),
			'rewrite' => array( 'slug' => 'dag' ),
			'hierarchical' => true,
		)
	);
	register_taxonomy(
		'confu_activity_type',
		'aktiviteter',
		array(
			'label' => __( 'Activity Types' ,'confu' ),
			'rewrite' => array( 'slug' => 'aktivitetstype' ),
			'hierarchical' => true,
		)
	);
}
add_action( 'init', 'confu_activity_init' );