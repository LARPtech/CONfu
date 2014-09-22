<?php
if( isset( $_POST[confu_nonce] ) && wp_verify_nonce( $_POST[confu_nonce], 'confu_submit_signup' ) ) {
	if( !isset($_POST['confu']['ticket']) && count($_POST['confu']['ticket'])==0 ) {
		
		$errormessages[] = __('Uhm. Du har ikke valgt nogen dage. Du kan ikke deltage uden at vælge en dag eller to, måske tre.', 'confu');
		echo '<div class="alert alert-danger" role="alert"><strong>FEJL!</strong>';
		foreach( $errormessages as $message ) {
			echo '<p>'.$message.'</p>';
		}
		echo '</div>';
		
	} elseif( !isset($_POST['confu']['attendee']['email']) ) {
	
		$errormessages[] = __('Hovsa. Du har vidst glemt at indtaste en emailadresse. Prøv igen, du!', 'confu');
		echo '<div class="alert alert-danger" role="alert"><strong>FEJL!</strong>';
		foreach( $errormessages as $message ) {
			echo '<p>'.$message.'</p>';
		}
		echo '</div>';
	
	} else {
	
		if( email_exists( $_POST['confu']['attendee']['email'] ) ) {
			$errormessages[] = __('Der er allerede tilmeldt en bruger med den mailadresse.', 'confu');
			echo '<div class="alert alert-danger" role="alert"><strong>FEJL!</strong>';
			foreach( $errormessages as $message ) {
				echo '<p>'.$message.'</p>';
			}
			echo '</div>';
		} else {
			$userdata = array(
				'first_name' => $_POST['confu']['attendee']['name'],
				'last_name' => $_POST['confu']['attendee']['surname'],
				'user_email' => $_POST['confu']['attendee']['email'],
				'user_login' => strtolower($_POST['confu']['attendee']['name'].$_POST['confu']['attendee']['surname']),
				'role' => 'attendant'
			);
			$attendeeID = wp_insert_user( $userdata );
			
			if( is_wp_error( $attendeeID ) ) {
				echo '<div class="alert alert-danger" role="alert"><strong>FEJL!</strong><p>'.$attendeeID->get_error_message().'</p></div>';
			} else {
				update_user_meta(
					$attendeeID, 
					'confu_attendee_notes', 
					$_POST['confu']['attendee']['notes']
				);
				update_user_meta(
					$attendeeID, 
					'confu_attendee_phone', 
					$_POST['confu']['attendee']['phone']
				);
				update_user_meta(
					$attendeeID, 
					'confu_attendee_birthdate', 
					$_POST['confu']['attendee']['age']['day'].'-'.
					$_POST['confu']['attendee']['age']['month'].'-'.
					$_POST['confu']['attendee']['age']['year']
				);
				update_user_meta(
					$attendeeID, 
					'confu_tickets', 
					$_POST['confu']['ticket']
				);
				update_user_meta(
					$attendeeID, 
					'confu_attendee_possible_gm', 
					$_POST['confu']['possible_gm']
				);
				update_user_meta(
					$attendeeID, 
					'confu_membership', 
					$_POST['confu']['membership']
				);
				if( isset( $_POST['confu']['activityID'] ) ) {
					foreach( $_POST['confu']['activityID'] as $actID ) {
						p2p_type( 'ATTENDEE_ACTIVITY_SIGNUP' )->connect( $attendeeID, $actID, array(
							'date' => current_time('mysql')
						) );
					}
				}
				
				update_user_meta(
					$attendeeID, 
					'confu_raw_signup_post', 
					$_POST['confu']
				);
				
				
				if( sendSignupReceipt( $attendeeID, $_POST['confu']['attendee']['email'] ) ) {
					echo '<div class="alert alert-success" role="alert">';
					echo '<p><strong>Du er nu tilmeldt!</strong></p>';
					echo '<p>Har jeg nogensinde fortalt dig hvor helt fantastisk awesome du er? Nej, nå, men så får jeg chancen på Hyggecon.</p>';
					echo '<p>Vi glæder os rigtig meget til at se dig!</p>';
					echo '<p>Tjek din email for at se din kvittering for tilmelding, og for at finde ud af hvordan du betaler. Husk, din tilmelding gælder ikke før vi har registreret din betaling.</p>';
					echo '<p class="pull-right">- Hyggemesteren og resten af crewet.</p>';
					echo '<br />';
					echo '</div>';
				} else {
					
				}
				unset($_POST);
				exit;
			}
			
		}
		
	}
}
?>
<form role="form" method="post">
	<h3>Stamdata</h3>
	<div class="form-group">
    	<label for="confu[attendee][name]">Fornavn</label>
		<input type="text" value="<?php echo $_POST['confu']['attendee']['name']; ?>" class="form-control" id="confu[attendee][name]" name="confu[attendee][name]"  placeholder="Indtast dit fornavn">
	</div>
	<div class="form-group">
    	<label for="confu[attendee][surname]">Efternavn</label>
		<input type="text" value="<?php echo $_POST['confu']['attendee']['surname']; ?>" class="form-control" id="confu[attendee][surname]" name="confu[attendee][surname]" placeholder="Indtast dit efternavn">
	</div>
	<div class="form-group">
    	<label for="confu[attendee][email]">Email address</label>
		<input type="email" value="<?php echo $_POST['confu']['attendee']['email']; ?>" class="form-control" id="confu[attendee][email]" name="confu[attendee][email]" placeholder="Indtast din email adresse">
	</div>
	<div class="form-group">
    	<label for="confu[attendee][phone]">Telefon</label>
		<input type="text" value="<?php echo $_POST['confu']['attendee']['phone']; ?>" class="form-control" id="confu[attendee][phone]" name="confu[attendee][phone]" placeholder="Indtast dit telefonnummer">
	</div>
	<div class="form-group">
    	<label for="confu[attendee][age]">Fødselsdato</label>
		<div class="row">
			<div class="col-xs-3">
				<select name="confu[attendee][age][day]" class="form-control">
				<?php 
				$dayCount = 1;
				while($dayCount < 32) { ?>
					<option value="<?php echo $dayCount; ?>"><?php echo $dayCount; ?></option>
				<?php $dayCount++; } ?>
				</select>
			</div>
			<div class="col-xs-6">
				<select name="confu[attendee][age][month]" class="form-control">
				<?php 
				$monthCount = 1;
				while($monthCount < 13) { ?>
					<option value="<?php echo $monthCount; ?>"><?php echo outputLocalMonthNames($monthCount); ?></option>
				<?php $monthCount++; } ?>
				</select>
			</div>
			<div class="col-xs-3">
				<select name="confu[attendee][age][year]" class="form-control">
				<?php 
				$year = date('Y');
				while($year > 1950) { ?>
					<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
				<?php $year--; } ?>
				</select>
			</div>
		</div>
	</div>
	<div class="form-group">
    	<label for="confu[attendee][notes]">Har du nogen kommentarer eller særlige bemærkninger?</label>
		<textarea name="confu[attendee][notes]" class="form-control" rows="3"><?php echo nl2br($_POST['confu']['attendee']['notes']); ?></textarea>
		<p class="help-block">Er du f.eks. vegetar? Er der nogen allergier vi skal tage hensyn til? Vil du bare gerne skrive et kærlighedsbrev til arrangørerne? Så er det her du gør det.</p>
	</div>
	
	<hr />
	
	<h3>Medlemsskab</h3>
	<div class="radio">
		<label>
			<input type="radio" name="confu[membership]" id="confu[membership]" class="membership" value="1" <?php if( isset($_POST['confu']['membership']) ) { checked($_POST['confu']['membership'], 1, true); } else { echo 'checked'; } ?>>
			Ja, jeg vil gerne melde mig ind i Eidolon. Kontingentet koster 75,- om året.
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="confu[membership]" id="confu[membership]" class="membership" <?php if( isset($_POST['confu']['membership']) ) { checked($_POST['confu']['membership'], 0, true); } ?>>
			Nej tak, jeg ønsker ikke at være medlem af Eidolon.
		</label>
	</div>
	
	<hr />

	<div class="alert alert-info" role="alert">
		<p><strong>OBS!</strong></p>
		Husk, dine ønsker er netop det - ønsker. Dit personlige program bliver lagt efterhånden som tilmeldingerne kommer ind og vi kan se hvordan fordelingen for de enkelte aktiviteter ender med at se ud.
	</div>

<?php
$days_args = array(
	'hide_empty' => false,
	'orderby' 	 => 'id',
	'order' 	 => 'ASC'
);
$days = get_terms('days', $days_args);
foreach($days as $day) { ?>
	<?php if( $day->slug != 'soendag' ) { ?>
	<h3><?php echo $day->name; ?></h3>
	<div id="accordion">
		<div id="activities">
			
			<article class="aktiviteter billet <?php echo $day->slug; ?>">
				<h4>
					<input type="checkbox" value="yes" name="confu[ticket][<?php echo $day->slug; ?>]" class="activity-<?php echo $day->slug; ?>-parent" <?php checked($_POST['confu']['ticket'][$day->slug],'yes', true); ?> />
					<a data-toggle="collapse" data-parent="#accordion" href="#activity-<?php echo $day->slug; ?>">
						Entré - <?php echo $day->name; ?> / 
						<span class="default_price" style="display:none;">145,-</span>
						<span class="member_price">95,-</span>
					</a>
				</h4>
				<section id="activity-<?php echo $day->slug; ?>" class="collapse in post-body"><small>
					<p>Denne billet giver dig adgang til Hyggecon om <?php echo strtolower( $day->name ); ?>en.</p>
					<p>Billetten giver adgang til de aktiviteter der er den pågældende dag, og garanterer dig både en varm seng at sove i og morgenmad dagen efter.</p>
				</small></section>
			</article>
			
		<?php
		$daily_activities_args = array(
			'posts_per_page' => -1,
			'post_type' => 'aktiviteter',
			'meta_key' => 'hc_aktivitet_tidspunkt',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'meta_query' => array( array(
				'key'     => 'confu_hide_from_signup',
				#'value'   => '1',
				#'type'    => 'numeric',
				'compare' => 'NOT EXISTS'
			) ),
			'tax_query' => array( array(
				'taxonomy' => 'days',
				'field'    => 'slug',
				'terms'    => $day->slug
			) )
		);
		$daily_activities = new WP_Query($daily_activities_args);
		if ( $daily_activities->have_posts() ) : while ( $daily_activities->have_posts() ) : $daily_activities->the_post();
			$term_list = wp_get_post_terms(get_the_ID(), 'confu_activity_type', array('fields' => 'all'));
		?>
			<article <?php post_class($term_list[0]->slug); ?>>
				<h4>
					<input type="checkbox" disabled="disabled" value="<?php the_ID(); ?>" name="confu[activityID][]" class="activity-<?php echo $day->slug; ?>-child" <?php if( isset($_POST['confu']['activityID']) && in_array(get_the_ID(), $_POST['confu']['activityID']) ) { echo 'checked'; } ?> />
					<a data-toggle="collapse" data-parent="#accordion" href="#activity-<?php the_ID(); ?>">
						<span class="time"><?php echo get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true); ?></span> 
						<?php the_title(); ?> / 
						<span class="default_price" style="display:none;"><?php the_field('hc_aktivitet_pris'); ?>,-</span>
						<span class="member_price"><?php the_field('hc_aktivitet_medlemspris'); ?>,-</span>
					</a>
				</h4>
				<section id="activity-<?php the_ID(); ?>" class="collapse post-body">
					<?php the_content(); ?>
				</section>
			</article>
		<?php endwhile; else : ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
		</div>
	</div>
	
	<hr />
<?php } ?>
<?php } ?>
	<h3>Diverse</h3>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="confu[possible_gm]" value="yes" /> Ja, jeg må gerne kontaktes vedr. at være spilleder.
		</label>
		<p class="help-block">Hyggecon vil være dig evigt taknemmelig, og fanger du Skyggemesteren i baren efter du har været spilleder så har vi hørt han giver en øl og fortæller dig spændende historier fra hans hjemland.</p>
	</div>
	
	<hr />
	
	<button type="submit" class="btn btn-success btn-block">Send tilmelding</button>
	<p>Hver tilmelding pålægges et rengøringsdepositum på 50,-</p>
<?php wp_nonce_field('confu_submit_signup','confu_nonce'); ?>
</form>