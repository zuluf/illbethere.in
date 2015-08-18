(function($, ibt){
	var timer = {
		end: null,
		start: moment(),
		timeout: null,
		callback: null,
		init: function (callback) {

			this.callback = callback;
			this.start = moment();

			this.timeout = this.countdown();

			if (typeof this.callback == "function") {
				this.callback.call(this.callback, this.start);
			}

			return this;
		},

		countdown: function (callback) {
			if (typeof this.callback === "function") {

				this.start = moment();
				this.callback.call(this.callback, this.start);
				if (this.timeout) {
					clearTimeout(this.timeout);
				}

				return this.timeout = setTimeout(this.countdown.bind(this), 1000);
			}
		},

		getTime: function() {
			return this.start.format('dddd DD MM, HH:mm:ss');
		}
	}

	ibt.timer = function Timer () {
		var self = $.extend(this.constructor, timer);

		self.init.apply(timer, $.makeArray(arguments));

		return self;
	}

})(jQuery, ibt);