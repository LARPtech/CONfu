<?php
function confu_connection_types() {
	p2p_register_connection_type( array(
		'name' => 'ATTENDEE_ACTIVITY_SIGNUP',
		'from' => 'aktiviteter',
		'to' => 'user',
		'reciprocal' => true,
		'title' => __( 'Attendee', 'confu' )
	) );
}
add_action( 'p2p_init', 'confu_connection_types' );