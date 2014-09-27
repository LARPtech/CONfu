<?php
$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic_settings';
?>
<div class="wrap">
	<h2><?php _e('CONfu Settings', 'confu'); ?></h2>
	<?php settings_errors(); ?>
	
	<h2 class="nav-tab-wrapper">
    	<a href="?page=confu_settings&tab=basic_settings" class="nav-tab <?php echo $active_tab == 'basic_settings' ? 'nav-tab-active' : ''; ?>">Basic Settings</a>
		<a href="?page=confu_settings&tab=date_settings" class="nav-tab <?php echo $active_tab == 'date_settings' ? 'nav-tab-active' : ''; ?>">Dates and Times</a>
	</h2>
	
	<?php print_r($_POST); ?>

	<form method="post" action="admin.php?page=confu_settings">
	<?php
	if( $active_tab == 'basic_settings' ) {
		do_settings_sections( 'confu_settings' );
		settings_fields( 'confu_basic_settings' );
	} elseif(  $active_tab == 'date_settings'  ) {
		
	}
	submit_button(); ?>
	</form>
</div>
         
        