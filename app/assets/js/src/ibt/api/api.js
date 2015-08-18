(function($, ibt, window){

	ibt.api = function Api () {
		return $.extend(true, {}, this.prototype, {
			request : function request (action, data, type) {
				return $.ajax({
					url : ibt.config.api + action,
					type : type,
					data: data
				});
			},

			get : function get (action, data) {
				return this.request(action, data, 'get');
			}
		})
	};

	if (window) {
		window.Api = new ibt.api();
	}
})(jQuery, ibt, window);