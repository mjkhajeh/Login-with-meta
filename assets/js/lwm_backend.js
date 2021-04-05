(function($) {
	$(document).ready(function(){
		if ( $('.lwm_login[value="fullname"]').attr('checked') ) {
			$('#lwm_fullname').show();
		} else {
			$('#lwm_fullname').hide();
		}
		if ( $('.lwm_login[value="website"]').attr('checked') ) {
			$('#lwm_website').show();
		} else {
			$('#lwm_website').hide();
		}
		if ( $('.lwm_login[value="woocommerce_phone"]').attr('checked') ) {
			$('#lwm_wc_phone').show();
		} else {
			$('#lwm_wc_phone').hide();
		}
		if ( $('.lwm_login[value="custom"]').attr('checked') ) {
			$('#lwm_custom').show();
		} else {
			$('#lwm_custom').hide();
		}
		if ( $('#lwm_create_reg_field_custom').prop("checked") ) {
			$('#lwm_reg_field').show();
		} else {
			$('#lwm_reg_field').hide();
		}
			
		$('.lwm_login').click(function(){
			if ( $(this).val() == 'fullname' ) {
				$('#lwm_fullname').slideDown();
			} else {
				$('#lwm_fullname').slideUp();
			}
			
			if ( $(this).val() == 'website' ) {
				$('#lwm_website').slideDown();
			} else {
				$('#lwm_website').slideUp();
			}
			
			if ( $(this).val() == 'woocommerce_phone' ) {
				$('#lwm_wc_phone').slideDown();
			} else {
				$('#lwm_wc_phone').slideUp();
			}
			
			if ( $(this).val() == 'custom' ) {
				$('#lwm_custom').slideDown();
			} else {
				$('#lwm_custom').slideUp();
			}
		});
		
		$('#lwm_create_reg_field_custom').click(function(){
			if ( $(this).prop("checked") ) {
				$('#lwm_reg_field').slideDown();
			} else {
				$('#lwm_reg_field').slideUp();
			}
		});
	});
})(jQuery)