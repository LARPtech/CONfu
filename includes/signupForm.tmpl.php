<?php

if( wp_verify_nonce( $_POST['_wpnonce'], 'confu_signup_submission' ) AND $_POST["_wp_http_referer"] == "/tilmelding/" ) {

	if( !isset($_POST["ticket"]) ) {
	
		$output .= '<div class="alert alert-error"><strong>Fejl!</strong> Du skal vælge en billet. Ellers kan du ikke deltage, duh!</div>';
	
	} else {
		
		$user_args = array(
			'ID'			=> null,
			'user_login' 	=> $_POST["email"],
			'user_pass' 	=> wp_generate_password(15, true, false),
			'user_email' 	=> $_POST["email"],
			'first_name' 	=> $_POST["fornavn"],
			'last_name' 	=> $_POST["efternavn"]
		);
		$attendeeID = wp_insert_user($user_args);
		if( !is_wp_error($attendeeID) ) {
			
			update_user_meta( $attendeeID, 'confu_attendee_address', $_POST["address"] );
			update_user_meta( $attendeeID, 'confu_attendee_zipcode', $_POST["zipcode"] );
			update_user_meta( $attendeeID, 'confu_attendee_town', $_POST["town"] );
			update_user_meta( $attendeeID, 'confu_attendee_phone_no', $_POST["phone_no"] );
			update_user_meta( $attendeeID, 'confu_attendee_birthdate', $_POST["birthday"].'-'.$_POST["birthmonth"].'-'.$_POST["birthyear"] );
			update_user_meta( $attendeeID, 'confu_attendee_ticket', $_POST["ticket"] );
			update_user_meta( $attendeeID, 'confu_attendee_membership', $_POST["membership"] );
			update_user_meta( $attendeeID, 'confu_attendee_message', $_POST["message"] );
			update_user_meta( $attendeeID, 'confu_attendee_possible_gm', $_POST["possible_gm"] );
			
			$activities = array_merge((array)$_POST["helcon"],(array)$_POST["activity"]);
			$activitySum = calculateActivityPrice( $attendeeID, $activities );
			
			$ticket_price = get_post_meta( $_POST["ticket"] ,'hc_ticket_price', true );
			$amount_owed = $ticket_price + 50 + ($_POST["membership"]==1 ? 75 - 100 : 0) + $activitySum;
			
			update_user_meta( $attendeeID, 'confu_attendee_total_owed', $amount_owed );
			
			#$output .= "<pre>" .$activities. "</pre>";
			
			global $wpdb;
			foreach($activities as $activity) {
				$insert = $wpdb->query("INSERT INTO " .$wpdb->prefix. "confu_activity_attendees (activityID, attendeeID) VALUES (".$activity.", ".$attendeeID.")");
				$output .= "<pre>" .$insert. "</pre>";
				$wpdb->print_error();
			}
			
			sendSignupReceipt($attendeeID,$_POST["email"]);
			$output .= '<div class="alert alert-success"><strong>SUCCES!</strong> Du er nu tilmeldt Hyggecon. Det gør dig til et af de sejeste mennesker på planeten.</div>';
			unset($_POST);
			
		}
		
	}
	
	
} else {

	$output .= '<form id="confu-form" class="form-horizontal" method="post" action="'.$_SERVER["REQUEST_URI"].'">';
	
	###
	#
	# STAMDATA
	#
	###
	$output .= '	<fieldset>';
	$output .= '		<legend>Stamdata</legend>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="fornavn">Fornavn</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="fornavn" name="fornavn" placeholder="Fornavn" value="'.$_POST["fornavn"].'" class="input-xlarge">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="efternavn">Efternavn</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="efternavn" name="efternavn" placeholder="Efternavn" value="'.$_POST["efternavn"].'" class="input-xlarge">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="adresse">Adresse</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="adresse" name="address" placeholder="Adresse" value="'.$_POST["address"].'" class="input-xlarge">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	<div class="control-group">';
	$output .= '			<label class="control-label" for="zipcode">Postnummer og by</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="zipcode" name="zipcode" placeholder="9500" value="'.$_POST["zipcode"].'" size="4" class="input-mini">';
	$output .= '				<input type="text" id="town" name="town" value="'.$_POST["town"].'" placeholder="By">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="phone_no">Telefon</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="phone_no" name="phone_no" value="'.$_POST["phone_no"].'" placeholder="Telefon" class="input-xlarge">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="email">E-mail</label>';
	$output .= '			<div class="controls">';
	$output .= '				<input type="text" id="email" name="email" value="'.$_POST["email"].'" placeholder="E-mail" class="input-xlarge">';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="birthdate">Fødselsdag</label>';
	$output .= '			<div class="controls">';
	$output .= '				<select name="birthday" class="input-small">';
	$i = 1;
	while($i < 32) {
		if( strlen($i)==1 ) {
	$output .= '					<option value="0'.$i.'">0'.$i.'</option>';			
		} else {
	$output .= '					<option value="'.$i.'">'.$i.'</option>';	
		}
	$i++;
	}
	$output .= '				</select> - ';
	$output .= '				<select name="birthmonth" class="input-small">';
	$i = 1;
	while($i < 13) {
		if( strlen($i)==1 ) {
	$output .= '					<option value="0'.$i.'">0'.$i.'</option>';			
		} else {
	$output .= '					<option value="'.$i.'">'.$i.'</option>';	
		}
	$i++;
	}
	$output .= '				</select> - ';
	$output .= '				<select name="birthyear" class="input-small">';
	$i = 2013;
	while($i > 1950) {
	$output .= '					<option value="'.$i.'">'.$i.'</option>';	
	$i--;
	}
	$output .= '				</select>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</fieldset>';
	
	###
	#
	# BILLET
	#
	###
	$output .= '	<fieldset>';
	$output .= '		<legend>Billet</legend>';
	
	$tickets = new WP_Query( 'post_type=tickets' );
	if ( $tickets->have_posts() ) : while ( $tickets->have_posts() ) : $tickets->the_post();
	
	$output .= '		<div class="'.array_shift(get_post_class()).' control-group">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="radio">';
	$output .= '					<input type="radio" name="ticket" id="ticket-'.get_the_ID().'" value="'.get_the_ID().'">';
	$output .= '					<h4>'. get_the_title() . ' &#8212; ' . get_post_meta(get_the_ID(), 'hc_ticket_price', true) . ' dkr.</h4>';
	$output .= 						get_the_content();
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	
	endwhile; endif;
	
	$output .= '	</fieldset>';
	
	###
	#
	# HELCON
	#
	###
	$helcon_args = array(
		'post_type' => 'aktiviteter',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'hc_aktivitet_dag',
				'value' => 'helcon',
				'compare' => '='
			)
		)
	);
	$helcon = new WP_Query( $helcon_args );
	if ( $helcon->have_posts() ) : 
	
	$output .= '	<fieldset>';
	$output .= '		<legend>Helcon</legend>';
	$output .= '		<p>Hvis du tilmelder dig en helcon aktivitet, vil du ikke kunne tilmelde dig andre aktiviteter. Det betyder ikke at du ikke må være med til en af de andre aktiviteter, men det må koordineres på dagen, hvis der mangler spillere eller deltagere.</p>';
	while ( $helcon->have_posts() ) : $helcon->the_post();
	
	$type = get_the_terms( get_the_ID(), 'aktivitetstype' );
	
	$output .= '		<div class="'.array_shift(get_post_class()).' control-group ' . $type->slug  . '">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="helcon[]" id="helcon-'.get_the_ID().'" value="'.get_the_ID().'" class="helcon">';
	$output .= '					<h4>'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).' &#8212; '. get_the_title() . ' <small><a href="'.get_permalink().'">Læs mere</a></small></h4>';
	
	$output .= '					<p class="metadata"><small>';
	$price = get_post_meta(get_the_ID(), 'hc_aktivitet_pris', true);
	$output .=							($price == 0 ? 'Gratis &bull; ' : $price . 'dkr &bull;' );
	$no_of_slots = get_post_meta(get_the_ID(), 'hc_aktivitet_deltagertal', true);
	$output .= 							($no_of_slots == 0 ? 'Ubegrænsede pladser' : $no_of_slots . ' pladser');
	$output .= 						'</small></p>';
	
	$output .= '					<p>'.get_the_excerpt().'</p>';
	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	endwhile;
	$output .= '	</fieldset>';
	
	endif;
	
	###
	#
	# TORSDAG
	#
	###
	$torsdag_args = array(
		'post_type' => 'aktiviteter',
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
		)
	);
	$torsdag = new WP_Query( $torsdag_args );
	if ( $torsdag->have_posts() ) : 
	
	$output .= '	<fieldset>';
	$output .= '		<legend>Torsdag</legend>';
	
	while ( $torsdag->have_posts() ) : $torsdag->the_post();
	
	$types = get_the_terms( get_the_ID(), 'aktivitetstype' );
	foreach($types as $type){}
	
	$output .= '		<div class="'.array_shift(get_post_class()).' control-group ' . $type->slug  . '">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="activity[]" id="activity-'.get_the_ID().'" value="'.get_the_ID().'" class="activity">';
	$output .= '					<h4>'. get_the_title() . ' <small><a href="'.get_permalink().'">Læs mere</a></small></h4>';
	
	$output .= '					<p class="metadata"><small>';
	$output .= 							$type->name  . ' &bull; ';
	$price = get_post_meta(get_the_ID(), 'hc_aktivitet_pris', true);
	$output .=							($price == 0 ? 'Gratis &bull; ' : $price . 'dkr &bull;' );
	$no_of_slots = get_post_meta(get_the_ID(), 'hc_aktivitet_deltagertal', true);
	$output .= 							($no_of_slots == 0 ? 'Ubegrænsede pladser &bull; ' : $no_of_slots . ' pladser &bull; ');
	$duration = get_post_meta(get_the_ID(), 'hc_aktivitet_varighed', true);
	$output .=							convertToHoursMins($duration);
	$output .= 						'</small></p>';
	
	$output .= '					<p>'.get_the_excerpt().'</p>';
	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	endwhile;
	
	$output .= '	</fieldset>';
	
	endif;
	
	###
	#
	# FREDAG
	#
	###
	$fredag_args = array(
		'post_type' => 'aktiviteter',
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
		)
	);
	$fredag = new WP_Query( $fredag_args );
	if ( $fredag->have_posts() ) : 
	
	$output .= '	<fieldset>';
	$output .= '		<legend>Fredag</legend>';
	
	while ( $fredag->have_posts() ) : $fredag->the_post();
	
	$types = get_the_terms( get_the_ID(), 'aktivitetstype' );
	foreach($types as $type){}
	
	$output .= '		<div class="'.array_shift(get_post_class()).' control-group ' . $type->slug  . '">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="activity[]" id="activity-'.get_the_ID().'" value="'.get_the_ID().'" class="activity">';
	$output .= '					<h4>'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).' &#8212; '. get_the_title() . ' <small><a href="'.get_permalink().'">Læs mere</a></small></h4>';
	
	$output .= '					<p class="metadata"><small>';
	$output .= 							$type->name  . ' &bull; ';
	$price = get_post_meta(get_the_ID(), 'hc_aktivitet_pris', true);
	$output .=							($price == 0 ? 'Gratis &bull; ' : $price . 'dkr &bull;' );
	$no_of_slots = get_post_meta(get_the_ID(), 'hc_aktivitet_deltagertal', true);
	$output .= 							($no_of_slots == 0 ? 'Ubegrænsede pladser &bull; ' : $no_of_slots . ' pladser &bull; ');
	$duration = get_post_meta(get_the_ID(), 'hc_aktivitet_varighed', true);
	$output .=							convertToHoursMins($duration);
	$output .= 						'</small></p>';
	
	$output .= '					<p>'.get_the_excerpt().'</p>';
	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	endwhile;
	
	$output .= '	</fieldset>';
	
	endif;
	
	###
	#
	# LØRDAG
	#
	###
	$lordag_args = array(
		'post_type' => 'aktiviteter',
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
		)
	);
	$lordag = new WP_Query( $lordag_args );
	if ( $lordag->have_posts() ) : 
	
	$output .= '	<fieldset>';
	$output .= '		<legend>Lørdag</legend>';
	
	while ( $lordag->have_posts() ) : $lordag->the_post();
	
	$types = get_the_terms( get_the_ID(), 'aktivitetstype' );
	
	foreach($types as $type){}
	
	$output .= '		<div class="'.array_shift(get_post_class()).' control-group ' . $type->slug  . '">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="activity[]" id="activity-'.get_the_ID().'" value="'.get_the_ID().'" class="activity">';
	$output .= '					<h4>'.get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true).' &#8212; '. get_the_title() . ' <small><a href="'.get_permalink().'">Læs mere</a></small></h4>';
	
	$output .= '					<p class="metadata"><small>';
	$output .= 							$type->name  . ' &bull; ';
	$price = get_post_meta(get_the_ID(), 'hc_aktivitet_pris', true);
	$output .=							($price == 0 ? 'Gratis &bull; ' : $price . 'dkr &bull;' );
	$no_of_slots = get_post_meta(get_the_ID(), 'hc_aktivitet_deltagertal', true);
	$output .= 							($no_of_slots == 0 ? 'Ubegrænsede pladser &bull; ' : $no_of_slots . ' pladser &bull; ');
	$duration = get_post_meta(get_the_ID(), 'hc_aktivitet_varighed', true);
	$output .=							convertToHoursMins($duration);
	$output .= 						'</small></p>';
	
	$output .= '					<p>'.get_the_excerpt().'</p>';
	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	endwhile;
	
	$output .= '	</fieldset>';
	
	endif;
	
	$output .= '	<fieldset>';
	$output .= '		<legend>Andet</legend>';
	$output .= '		<div class="control-group">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="possible_gm" id="possible_gm" value="1" class="possible_gm"> Jeg vil gerne være spilleder på Hyggecon.</small>';	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	
	$output .= '		<div class="control-group">';
	$output .= '			<div class="controls">';
	$output .= '				<label class="checkbox">';
	$output .= '					<input type="checkbox" name="membership" id="membership" value="1" class="membership"> Ja, jeg vil gerne være medlem af Eidolon resten af medlemsskabsåret.<br /> <small>Medlemsskab koster 75,- og udløser en rabat på 100,- på din deltagerpris til Hyggecon.</small>';	
	$output .= '				</label>';
	$output .= '			</div>';
	$output .= '		</div>';
	
	$output .= '		<div class="control-group">';
	$output .= '			<label class="control-label" for="message">Besked</label>';
	$output .= '			<div class="controls">';
	$output .= '				<textarea name="message" class="input-xlarge" rows="6"></textarea>';
	$output .= '				<span class="help-block">F.eks. mad-allergier, eller navnene på dem du gerne vil dele værelse med.</span>';
	$output .= '			</div>';
	$output .= '		</div>';
	$output .= '	</fieldset>';
	
	$output .= '	<div class="form-actions">';
	$output .= '		<button type="submit" class="btn btn-success">Tilmeld mig!</button>';
	$output .= '		<a href="/" class="btn btn-link">Annuller</a>';
	$output .= '	</div>';
	$output .= wp_nonce_field('confu_signup_submission','_wpnonce',true,false);
	$output .= '</form>';

}

echo $output;
?>