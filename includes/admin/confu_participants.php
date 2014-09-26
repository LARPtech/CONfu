<?php if( isset( $_GET["participantID"] ) AND is_numeric( $_GET["participantID"] ) ) { 
	$user = get_userdata( $_GET["participantID"] );function my_admin_notice() { ?>
    <div class="updated">
        <p><?php _e( 'Updated!', 'confu' ); ?></p>
    </div>
    <?php
}
?>

	<div class="wrap">
		<h2><?php _e( 'CONfu Participant', 'confu' ); ?>: <?php echo $user->user_firstname . ' ' . $user->user_lastname; ?></h2>
		<?php if( isset( $_POST['confu_payment_update_nonce'] ) && wp_verify_nonce( $_POST['confu_payment_update_nonce'], 'update_confu_participant_payment' ) ) {
			update_user_meta( $_GET["participantID"], 'confu_payment_received', $_POST['confuAmountPaid'] );
		} ?>
		<br class="clear">
		
		<table width="100%">
			<tr>
				<td valign="top">
					
					<table class="wp-list-table widefat fixed" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2"><strong><?php _e( 'Userdata', 'confu' ); ?></strong></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th><?php _e( 'Name', 'confu' ); ?></th>
								<td><?php echo $user->user_firstname . ' ' . $user->user_lastname; ?></td>
							</tr>
							<tr>
								<th><?php _e( 'Phone', 'confu' ); ?></th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_phone', true); ?></td>
							</tr>
							<tr>
								<th><?php _e( 'Email', 'confu' ); ?></th>
								<td><?php echo $user->user_email; ?></td>
							</tr>
							<tr>
								<th><?php _e( 'Birthdate', 'confu' ); ?></th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_birthdate', true); ?></td>
							</tr>
							<tr>
								<th><?php _e( 'GM?', 'confu' ); ?></th>
								<td><?php if( get_user_meta($_GET["participantID"], 'confu_attendee_possible_gm', true) == 1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
							</tr>
							<tr>
								<th><?php _e( 'Member?', 'confu' ); ?></th>
								<td><?php if( get_user_meta($_GET["participantID"], 'confu_membership', true) == 1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
							</tr>
							<tr>
								<th><?php _e( 'Note', 'confu' ); ?></th>
								<td><?php echo get_user_meta($_GET["participantID"], 'confu_attendee_notes', true); ?></td>
							</tr>
						</tbody>
					</table>

				</td>
				<td valign="top">
					<?php 
					$tickets = get_user_meta( $_GET["participantID"], 'confu_tickets', true ); 
					foreach( $tickets as $day => $ticket ) {
						$days[] = $day;
					} 
					$activities = get_posts( array(
						'connected_type' => 'ATTENDEE_ACTIVITY_SIGNUP',
						'connected_items' => $_GET["participantID"],
						'suppress_filters' => false,
						'nopaging' => true
					) );
					foreach( $activities as $activity ) {
						$activityIDs[] = $activity->ID;
					} ?>
					<table class="wp-list-table widefat fixed" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2"><strong><?php _e( 'Programme', 'confu' ); ?></strong></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach( $days as $day ) { ?>
							<tr>
								<th valign="top" width="20%"><strong><?php echo get_term_by( 'slug', $day, 'days' )->name; ?></strong></th>
								<td width="80%">
								<?php
								$single_day_activities_args = array(
									'posts_per_page' => -1,
									'post_type' => 'aktiviteter',
									'meta_key' => 'hc_aktivitet_tidspunkt',
									'orderby' => 'meta_value',
									'order' => 'ASC',
									'post__in' => $activityIDs,
									'tax_query' => array( array(
										'taxonomy' => 'days',
										'field'    => 'slug',
										'terms'    => $day
									) )
								);
								$single_day_activities = new WP_Query($single_day_activities_args);
								if ( $single_day_activities->have_posts() ) : while ( $single_day_activities->have_posts() ) : $single_day_activities->the_post(); ?>
									<p><strong class="time"><?php echo get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true); ?></strong> <?php the_title(); ?></p>
								<?php endwhile; else : ?>
									<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
								<?php endif; ?>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					
				</td>
			</tr>
			<tr>
				<td valign="top">
					
					<table class="wp-list-table widefat fixed" cellspacing="0">
						<thead>
							<tr>
								<th colspan="2"><strong><?php _e( 'Payment', 'confu' ); ?></strong></th>
							</tr>
						</thead>
						<form method="post">
						<tbody>
							<tr>
								<th><?php _e( 'Total price', 'confu' ); ?></th>
								<td><?php $amountOwed = getUserTotal($_GET["participantID"]); echo $amountOwed; ?>,-</td>
							</tr>
							<tr>
								<th><?php _e( 'Payment Received', 'confu' ); ?></th>
								<td><input type="text" name="confuAmountPaid" value="<?php $amountPaid = get_user_meta($_GET["participantID"], 'confu_payment_received', true); if( is_numeric($amountPaid) ) { echo $amountPaid; } else { echo 0; } ?>" class="widefat" /></td>
							</tr>
							<tr>
								<th><?php _e( 'Amount owed', 'confu' ); ?></th>
								<td><?php echo $amountOwed - $amountPaid; ?>,-</td>
							</tr>
							<tr>
								<th></th>
								<td>
									<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save changes', 'confu' ); ?>">
								</td>
							</tr>
						</tbody>
						<?php wp_nonce_field('update_confu_participant_payment', 'confu_payment_update_nonce'); ?>
						</form>
					</table>
					
				</td>
			</tr>
		</table>
						
	</div>

<?php } else { ?>

	<div class="wrap">
		<h2>CONfu</h2>

		<br class="clear">

		<table class="wp-list-table widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th><?php _e( 'Name', 'confu' ); ?></th>
					<th><?php _e( 'Signup Date', 'confu' ); ?></th>
					<th><?php _e( 'Phone', 'confu' ); ?></th>
					<th><?php _e( 'Email', 'confu' ); ?></th>
					<th><?php _e( 'GM?', 'confu' ); ?></th>
					<th><?php _e( 'Member?', 'confu' ); ?></th>
				</tr>
			</thead>
				
			<tfoot>
				<tr>
					<th><?php _e( 'Name', 'confu' ); ?></th>
					<th><?php _e( 'Signup Date', 'confu' ); ?></th>
					<th><?php _e( 'Phone', 'confu' ); ?></th>
					<th><?php _e( 'Email', 'confu' ); ?></th>
					<th><?php _e( 'GM?', 'confu' ); ?></th>
					<th><?php _e( 'Member?', 'confu' ); ?></th>
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
					<td><a href="mailto:<?php echo $attendee->user_email; ?>"><?php echo $attendee->user_email; ?></a></td>
					<td><?php if ( get_user_meta($attendee->ID, 'confu_attendee_possible_gm', true)==1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
					<td><?php if ( get_user_meta($attendee->ID, 'confu_membership', true)==1 ) { echo 'Ja'; } else { echo 'Nej'; } ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

<?php } ?>