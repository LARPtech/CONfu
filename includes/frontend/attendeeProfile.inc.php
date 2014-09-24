<?php if( !is_user_logged_in() ) { ?>
	<form role="form" class="login" method="post">
		<h3><?php _e('Login', 'confu'); ?></h3>
		<div class="form-group">
			<label for="attendeeEmail"><?php _e('Email address', 'confu'); ?></label>
			<input type="email" class="form-control" id="attendeeEmail" name="attendeeEmail" placeholder="<?php _e('Enter email', 'confu'); ?>">
		</div>
		<div class="form-group">
			<label for="attendeePassword"><?php _e('Password', 'confu'); ?></label>
			<input type="password" class="form-control" id="attendeePassword" name="attendeePassword" placeholder="<?php _e('Enter password', 'confu'); ?>">
		</div>
		<button type="submit" class="btn btn-success btn-block"><?php _e('Log me in', 'confu'); ?></button>
		<?php wp_nonce_field('attendee_login', 'confu_frondend_nonce'); ?>
	</form>
	
	<hr />
	
	<form role="form" class="reset-password" method="post">
		<h3><?php _e('Reset password', 'confu'); ?></h3>
		<div class="form-group">
			<label for="attendeeEmail"><?php _e('Email address', 'confu'); ?></label>
			<input type="email" class="form-control" id="attendeeEmail" name="attendeeEmail" placeholder="<?php _e('Enter email', 'confu'); ?>">
		</div>
		<button type="submit" class="btn btn-success btn-block"><?php _e('Send me a new password', 'confu'); ?></button>
		<?php wp_nonce_field('reset_attendee_password', 'confu_frondend_nonce'); ?>
	</form>
<?php } else {} ?>