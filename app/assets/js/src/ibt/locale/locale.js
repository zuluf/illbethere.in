(function($, ibt, window){
	var locale = {
		jed: null,

		load: function () {
			if (Jed) {
				this.jed = new Jed(ibt.locales);

				return function() {
					return this.jed.translate.apply(this.jed, $.makeArray(arguments)).fetch();
				}.bind(this);
			}

			return function (key) {
				return key;
			}
		}
	}

	ibt.locale = new function Locale () {
		return $.extend(this.constructor, locale);
	}

	window._t = ibt.locale.load();

})(jQuery, ibt, window);