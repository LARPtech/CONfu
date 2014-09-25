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

function outputLocalMonthNames($monthNum) {
	$monthNames = array(
		1 => 'Januar',
		2 => 'Februar',
		3 => 'Marts',
		4 => 'April',
		5 => 'Maj',
		6 => 'Juni',
		7 => 'Juli',
		8 => 'August',
		9 => 'September',
		10 => 'Oktober',
		11 => 'November',
		12 => 'December'
	);
	return $monthNames[$monthNum];
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
	$must_have_attendees = 60;
	$userCount = count_users(); 
	$count = $userCount["avail_roles"]["attendant"];
	$width = (100/$must_have_attendees)*$count;
	$output .= '<div class="barwrapper" style="width:100%;padding:2px;border:1px solid #777;border-radius:5px;">';
	$output .= '<div class="bar" style="height:15px;background:#777;width:'.$width.'%;border-radius:3px;"></div>';
	$output .= '</div>';
	return $output;
}

function html_email_content_replace($email_body, $userID) {
	$user = get_userdata( $userID );
	$search = array(
		'[firstname]',
		'[amount_owed]'
	);
	$replace = array(
		$user->user_firstname,
		getUserTotal($userID)
	);
	$content = str_replace($search, $replace, $email_body);
	return $content;
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

function getUserTotal($uid) {
	$activities = get_posts( array(
		'connected_type' => 'ATTENDEE_ACTIVITY_SIGNUP',
		'connected_items' => $uid,
		'suppress_filters' => false,
		'nopaging' => true
	) );
	$membership = get_user_meta($uid, 'confu_membership', true);
	if( $membership == 1 ) {
		$days = get_user_meta($uid, 'confu_tickets', true);
		if( !is_string($days) ) {
			if( count($days)>0 ) {
				$totalEntryFee = count($days) * 95;
				if( count($activities)>0 ) {
					foreach( $activities as $activity ) {
						$activityPrice[] = get_post_meta($activity->ID, 'hc_aktivitet_medlemspris', true);
					}
					$totalActivityPrice = array_sum( $activityPrice );
				}
				return $totalEntryFee + 75 + $totalActivityPrice + 50;
			} else {
				return 0;
			}
		} else {
			return 0; 	
		}
	} else {
		$days = get_user_meta($uid, 'confu_tickets', true);
		if( !is_string($days) ) {
			if( count($days)>0 ) {
				$totalEntryFee = count($days) * 145;
				if( count($activities)>0 ) {
					foreach( $activities as $activity ) {
						$activityPrice[] = get_post_meta($activity->ID, 'hc_aktivitet_medlemspris', true);
					}
					$totalActivityPrice = array_sum( $activityPrice );
				}
				return $totalEntryFee + $totalActivityPrice + 50;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
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