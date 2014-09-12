jQuery(document).ready(function() {
	jQuery('input.helcon').change(function() {
		if( jQuery(this).is(':checked')){
			jQuery('input.activity').attr('disabled','disabled');
		} else {
			jQuery('input.activity').removeAttr('disabled');
		}
	});
	jQuery('input.activity').change(function() {
		if( jQuery(this).is(':checked')){
			jQuery('input.helcon').attr('disabled','disabled');
		} else {
			jQuery('input.helcon').removeAttr('disabled');
		}
	});
});