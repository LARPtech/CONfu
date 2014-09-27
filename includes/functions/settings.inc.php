<?php
add_action('admin_init', 'confu_initialize_theme_options');
function confu_initialize_theme_options() {

	add_settings_section(
		'confu_basic_settings',
		__( 'Important dates and times for CONfu.','confu' ),
		null,
		'confu_settings'
	);
	
	add_settings_field( 
		'signup_closes_date', 
		__( 'Signup closes on','confu' ), 
		'confu_signup_closes_field_output', 
		'confu_settings', 
		'confu_basic_settings', 
		array( 
			'label_for' => 'signup_closes_date' 
		) 
	);
	
	register_setting( 'confu_settings', 'signup_closes_date' );
    
}

function confu_signup_closes_field_output( $args ) {
		
	// Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field
	echo '<input type="text" id="signup_closes_date" class="confu_datepicker" name="signup_closes_date" value="'.get_option('signup_closes_date').'" />';
		
}	