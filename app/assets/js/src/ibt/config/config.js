(function($){
	var timeout;

	$.ajaxSetup({
		beforeSend: function beforeSend (jqXHR, options) {
			Control('Widgets.Messages').clear();
		},

		error : function error (request, status, response) {
			if (!response) {
				Control('Widgets.Messages').pop(
					"It seams your internet connection is down. <br />Please move closer to the cafe's router, or go to a place with better wifi."
				)
			}
		}
	});
})(jQuery);