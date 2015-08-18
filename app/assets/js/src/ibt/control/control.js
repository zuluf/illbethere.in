(function($, ibt, window){
	var controls, jClean;

	controls = {};

	jClean = $.cleanData;

	$.cleanData = function(elems) {
		for ( var i = 0, elem; (elem = elems[i]) !== undefined; i++ ) {
			$(elem).triggerHandler("destroyed");
		}
		// call the overwriten jQuery clean fn
		jClean(elems);
	};

	ibt.Control = function Control (name, element, options) {

		if (!controls[name]) {
			throw Error('Control [' + name + '] : not implemented');
		}

		if ((!element || !$(element).length) && !controls[name].element) {
			throw Error('Control [' + name + '] : add a control element you dumb fuck');
		}

		return Object.create(controls[name]).setup(element, options);
	}

	$.extend(ibt.Control.prototype, {

		getEvents : function getEvents () {
			return this.element ? $._data(this.element[0], 'events') : undefined;
		},

		isBound : function isBound () {
			return !!this.getEvents();
		},

		init: function init () {},

		setup : function setup (element, options) {

			this.element = element || this.element || null;
			this.options = options || this.options || {};

			return this.instance();
		},

		instance: function instance () {
			var events, callback;

			// b1ind events
			if (!this.isBound()) {
				for (var i in this) {

					callback = this[i];

					if (~i.indexOf(' ') && typeof callback === "function") {

						events = i.split(' ');

						this.element.on(events.pop(), events.pop(), function(callback, event) {
							callback.call(this, event, $(event.target));
						}.bind(this, callback));
					}
				}

				// bind element destroyed
				this.element.on('destroyed', this.destroy.bind(this));
			}

			this.init();

			return this;
		},

		destroy: function destroy () {
			if (this.element && this.element.length) {
				for (var i in this) {
					if (~i.indexOf(' ') && typeof this[i] === "function") {
						this.element.off(i.split(' ').pop(), this[i]);
					}
				}
			}
		},

		remove : function remove () {
			if (this.element) {
				this.element.remove();
			}
		},

		is : function is () {
			return $.fn.is.apply(this.element, arguments);
		},

		html : function html (template, options) {
			return this.element && this.element.html(ibt.render(template, options));
		},

		text : function text (text) {
			return this.element && this.element.text(text);
		},

		hide : function hide () {
			return this.element && this.element.addClass('dn');
		},

		show : function show () {
			return this.element && this.element.removeClass('dn');
		}
	});

	ibt.Control.extend = function Control (name, control) {
		return (
			controls[name] = controls[name] ||
			$.extend({
				name: function() {
					return name
				}
			}, Object.create(ibt.Control.prototype), control));
	}


	if (window) {
		window.Control = ibt.Control;
	}

	return ibt.Control;
})(jQuery, ibt, window);