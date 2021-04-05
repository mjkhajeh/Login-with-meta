(function($) {
	$(document).ready(function() {
		// Set fields in dokan registration form
		$("#register-form").append( $('label[for="shop-phone"]') ); // Move Label
		$("#register-form").append( $('#shop-phone') ); // Move Label

		$( $('label[for="shop-phone"]') ).insertBefore(".show_if_seller");
		$( $('input#shop-phone') ).insertBefore(".show_if_seller");
		$('input#shop-phone').attr("disabled", "");
		$('input#shop-phone').removeAttr("disabled");
	});
})(jQuery);
