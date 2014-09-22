<?php if( isset( $_GET["participantID"] ) AND is_numeric( $_GET["participantID"] ) ) { 
	$user = get_userdata( $_GET["participantID"] );
?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div><h2><?php echo $user->user_firstname . ' ' . $user->user_lastname; ?></h2>
		
		<br class="clear">
		
		<table width="100%">
			<tr>
				<td valign="top">
					
					<table class="wp-list-table widefat fixed" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2">STAMDATA</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Navn</th>
								<td><?php echo $user->user_firstname . ' ' . $user->user_lastname; ?></td>
							</tr>
							<tr>
								<th>Pris</th>
								<td><?php echo getUserTotal($_GET["participantID"]); ?>,-</td>
							</tr>
							<tr>
								<th>Telefon</th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_phone', true); ?></td>
							</tr>
							<tr>
								<th>Email</th>
								<td><?php echo $user->user_email; ?></td>
							</tr>
							<tr>
								<th>Fødselsdag</th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_birthdate', true); ?></td>
							</tr>
							<tr>
								<th>Spilleder</th>
								<td><?php if( get_user_meta($_GET["participantID"], 'confu_attendee_possible_gm', true) == 1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
							</tr>
							<tr>
								<th>Medlem</th>
								<td><?php if( get_user_meta($_GET["participantID"], 'confu_membership', true) == 1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
							</tr>
							<tr>
								<th>Besked</th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_notes', true); ?></td>
							</tr>
						</tbody>
					</table>

				</td>
				<td valign="top">
					
					<table class="wp-list-table widefat fixed" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2">PROGRAM</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					
				</td>
			</tr>
		</table>
						
	</div>

<?php } else { ?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div><h2>CONfu</h2>

		<br class="clear">

		<table class="wp-list-table widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th>Navn</th>
					<th>Tilmeldingsdato</th>
					<th>Telefonnummer</th>
					<th>Email</th>
					<th>Spilleder</th>
					<th>Medlem</th>
				</tr>
			</thead>
				
			<tfoot>
				<tr>
					<th>Navn</th>
					<th>Tilmeldingsdato</th>
					<th>Telefonnummer</th>
					<th>Email</th>
					<th>Spilleder</th>
					<th>Medlem</th>
				</tr>
			</tfoot>
	
			<tbody id="the-list">
			<?php
			$attendees_args = array(
				'role' => 'attendant',
				'orderby' => 'registered',
				'order' => 'DESC'
			);
			$attendees = get_users( $attendees_args );
			foreach($attendees as $attendee) { ?>
				<tr>
					<th><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>&participantID=<?php echo $attendee->ID; ?>"><?php echo $attendee->display_name; ?></a></th>
					<td><?php echo $attendee->user_registered; ?></td>
					<td><?php echo get_user_meta($attendee->ID, 'confu_attendee_phone', true); ?></td>
					<td><a href="maito:<?php echo $attendee->user_email; ?>"><?php echo $attendee->user_email; ?></a></td>
					<td><?php if ( get_user_meta($attendee->ID, 'confu_attendee_possible_gm', true)==1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
					<td><?php if ( get_user_meta($attendee->ID, 'confu_membership', true)==1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

<?php } ?>