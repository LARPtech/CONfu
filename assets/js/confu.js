jQuery(document).ready(function() {
	
	if( jQuery('input.activity-torsdag-parent').is(':checked')){
		jQuery('input.activity-torsdag-child').removeAttr('disabled');
	} else {
		jQuery('input.activity-torsdag-child').attr('disabled','disabled');	
	}
	jQuery('input.activity-torsdag-parent').change(function() {
		if( jQuery(this).is(':checked')){
			jQuery('input.activity-torsdag-child').removeAttr('disabled');
		} else {
			jQuery('input.activity-torsdag-child').attr('disabled','disabled');	
		}
	});
	
	if( jQuery('input.activity-fredag-parent').is(':checked')){
		jQuery('input.activity-fredag-child').removeAttr('disabled');
	} else {
		jQuery('input.activity-fredag-child').attr('disabled','disabled');	
	}
	jQuery('input.activity-fredag-parent').change(function() {
		if( jQuery(this).is(':checked')){
			jQuery('input.activity-fredag-child').removeAttr('disabled');
		} else {
			jQuery('input.activity-fredag-child').attr('disabled','disabled');
		}
	});
	
	if( jQuery('input.activity-loerdag-parent').is(':checked')){
		jQuery('input.activity-loerdag-child').removeAttr('disabled');
		jQuery('input.activity-soendag-child').removeAttr('disabled');
	} else {
		jQuery('input.activity-loerdag-child').attr('disabled','disabled');
		jQuery('input.activity-soendag-child').attr('disabled','disabled');
	}
	jQuery('input.activity-loerdag-parent').change(function() {
		if( jQuery(this).is(':checked')){
			jQuery('input.activity-loerdag-child').removeAttr('disabled');
			jQuery('input.activity-soendag-child').removeAttr('disabled');
		} else {
			jQuery('input.activity-loerdag-child').attr('disabled','disabled');
			jQuery('input.activity-soendag-child').attr('disabled','disabled');
		}
	});
	
	jQuery('input.membership').change(function() {
		if( jQuery(this).val() == 1 ){
			jQuery('span.default_price').hide();
			jQuery('span.member_price').show();
		} else {
			jQuery('span.default_price').show();
			jQuery('span.member_price').hide();
		}
	});
});