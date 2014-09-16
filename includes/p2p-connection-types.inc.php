<?php
function confu_connection_types() {
	p2p_register_connection_type( array(
		'name' => 'attendee_signup',
		'from' => 'confu_signups',
		'to' => 'user',
		'reciprocal' => true,
		'title' => __('Deltager')
	) );
	p2p_register_connection_type( array(
		'name' => 'attendee_activities',
		'from' => 'confu_signups',
		'to' => 'aktiviteter',
		'reciprocal' => true,
		'title' => __('Aktiviteter')
	) );
}
add_action( 'p2p_init', 'confu_connection_types' );