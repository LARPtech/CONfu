<?php
function convertToHoursMins($time, $format = '%d timer og %d minutter') {
	settype($time, 'integer');
	if ($time < 1) {
		return;
	}
	$hours = floor($time/60);
	$minutes = $time%60;
	if( $hours == 1 ) {
		$format = '%d time';
	} else {
		$format = '%d timer';
	}
	return sprintf($format, $hours, $minutes);
}

function getTicketAttendeeCount($ticketID) {
	global $wpdb;
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key='confu_attendee_ticket' AND meta_value ='$ticketID'" );
	return $count;
}

function countActivityAttendees($activityID) {
	global $wpdb;
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM hyggecon_wp_confu_activity_attendees WHERE activityID='$activityID'" );
	return $count;
}

function getSuccessBar() {
	$must_have_attendees = 75;
	$userCount = count_users(); 
	$count = $userCount["avail_roles"]["attendant"];
	$width = ($must_have_attendees/100)*$count;
	$output .= '<div class="barwrapper" style="width:100%;padding:2px;border:1px solid #777;border-radius:5px;">';
	$output .= '<div class="bar" style="height:15px;background:#777;width:'.$width.'%;border-radius:3px;"></div>';
	$output .= '</div>';
	return $output;
}

function html_email_content_replace($email_body, $userID) {
	$user = get_userdata( $userID );
	$search = array(
		'[firstname]',
		'[amount_owed]',
		'[attendee_programme_rows]'
	);
	$replace = array(
		$user->user_firstname,
		get_user_meta($userID, 'confu_attendee_total_owed', true),
		attendeeProgrammeForEmail($userID)
	);
	$content = str_replace($search, $replace, $email_body);
	return $content;
}

function attendeeProgrammeForEmail($userID) {
	global $wpdb;
	$activities_ids = $wpdb->get_col( "SELECT activityID FROM ".$wpdb->prefix."confu_activity_attendees WHERE attendeeID = '{$userID}'" );
	
	if( count($activities_ids)==0 ) {
		
		$output .= '<tr>';
		$output .= '<td colspan="2" style="padding:.5em;">Du har ikke tilmeldt dig nogen aktiviteter.</td>';
		$output .= '</tr>';
		
	} else {
	
		$torsdag_activity_args = array(
			'meta_key' => 'hc_aktivitet_tidspunkt',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'hc_aktivitet_dag',
					'value' => 'torsdag',
					'compare' => '='
				)
			),
			'post_type' => 'aktiviteter',
			'post__in' => $activities_ids
		);
		$activities = new WP_Query($torsdag_activity_args);
		$output .= '<tr><th colspan="2" style="text-align:left;padding:.5em;">Torsdag</th></tr>';
		if ( $activities->have_posts() ) : while ( $activities->have_posts() ) : $activities->the_post();
			$output .= '<tr>';
			$output .= '<th style="padding:.5em;">'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).'</th>';
			$output .= '<td style="padding:.5em;">'.get_the_title().'</td>';
			$output .= '</tr>';
		endwhile; else:
			$output .= '<tr>';
			$output .= '<td colspan="2" style="padding:.5em;">Du har ikke tilmeldt dig nogen aktiviteter om torsdagen.</td>';
			$output .= '</tr>';
		endif;
		
		$fredag_activity_args = array(
			'meta_key' => 'hc_aktivitet_tidspunkt',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'hc_aktivitet_dag',
					'value' => 'fredag',
					'compare' => '='
				)
			),
			'post_type' => 'aktiviteter',
			'post__in' => $activities_ids
		);
		$activities = new WP_Query($fredag_activity_args);
		$output .= '<tr><th colspan="2" style="text-align:left;padding:.5em;">Fredag</th></tr>';
		if ( $activities->have_posts() ) : while ( $activities->have_posts() ) : $activities->the_post();
			$output .= '<tr>';
			$output .= '<th style="padding:.5em;">'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).'</th>';
			$output .= '<td style="padding:.5em;">'.get_the_title().'</td>';
			$output .= '</tr>';
		endwhile; else:
			$output .= '<tr>';
			$output .= '<td colspan="2" style="padding:.5em;">Du har ikke tilmeldt dig nogen aktiviteter om fredagen.</td>';
			$output .= '</tr>';
		endif;
		
		$lordag_activity_args = array(
			'meta_key' => 'hc_aktivitet_tidspunkt',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'hc_aktivitet_dag',
					'value' => 'lørdag',
					'compare' => '='
				)
			),
			'post_type' => 'aktiviteter',
			'post__in' => $activities_ids
		);
		$activities = new WP_Query($lordag_activity_args);
		$output .= '<tr><th colspan="2" style="text-align:left;padding:.5em;">Lørdag</th></tr>';
		if ( $activities->have_posts() ) : while ( $activities->have_posts() ) : $activities->the_post();
			$output .= '<tr>';
			$output .= '<th style="padding:.5em;">'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).'</th>';
			$output .= '<td style="padding:.5em;">'.get_the_title().'</td>';
			$output .= '</tr>';
		endwhile; else:
			$output .= '<tr>';
			$output .= '<td colspan="2" style="padding:.5em;">Du har ikke tilmeldt dig nogen aktiviteter om lørdagen.</td>';
			$output .= '</tr>';
		endif;
	
	}
	
	return $output;
}

function sendSignupReceipt($uid,$email) {
	$email_template = file_get_contents(CONFU_PATH . '/assets/email_templates/signup_receipt.tmpl.html');
	$real_email_content = html_email_content_replace($email_template, $uid);
	
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	$return = wp_mail( $email, '[Hyggecon] Tilmeldingsbekræftelse', $real_email_content, $headers );
	if( $return ) {
		return TRUE;
	} else {
		return FALSE;
	}
	
}

function calculateActivityPrice($uid, $activities) {
	$price_fetching_args = array(
		'post_type' => 'aktiviteter',
		'post__in' => $activities,
		'meta_key' => 'hc_aktivitet_pris',
		'posts_per_page' => -1
	);
	$price_fetching = new WP_Query($price_fetching_args);
	if ( $price_fetching->have_posts() ) : while ( $price_fetching->have_posts() ) : $price_fetching->the_post();
		$activitySum = get_post_meta( get_the_ID(), 'hc_aktivitet_pris', true ) + $activitySum;
	endwhile; else: 
		$activitySum = 0;
	endif;
	
	return $activitySum;
}
?>