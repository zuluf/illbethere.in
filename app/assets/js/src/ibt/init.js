(function($, ibt, window){
	var init = {
		parseBrowser:  function parseBrowser() {
			var browserName;

			this.parser = new UAParser();
			this.browser = this.parser.getBrowser();
			this.device = this.parser.getDevice();

			if (this.device && this.device.type) {
				$('body').addClass(this.device.type);
			}

			if (this.browser && this.browser.name) {
				browserName = this.browser.name.toLowerCase();
				$('body').addClass(browserName + ' ' + browserName + '-' + this.browser.major);
			}
		},

		setMomentLocale: function setMomentLocale() {
			moment.locale('en-US');
		},

		setContentHeight: function setContentHeight() {
			$('.js-wrap').css('min-height', $(window).height() - 100);
		},

		resize: function resize() {
			$(window).on('resize', function () {
				this.setContentHeight();
			}.bind(this));
		}
	};

	ibt.init = function Init () {
		var app = ibt,
			self = $.extend(this.constructor, init);

		self.setContentHeight();
		self.parseBrowser();
		self.setMomentLocale();
		self.resize();
		self.router = new ibt.router();
	};

})(jQuery, ibt, window);