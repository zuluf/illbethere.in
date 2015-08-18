(function($, ibt, window){
	var scripts;

	scripts = {};

	ibt.Script = function Script (name, options) {

		if (!scripts[name]) {
			throw Error('Script [' + name + '] : not implemented');
		}

		return Object.create(scripts[name]).setup(options);
	}

	$.extend(ibt.Script.prototype, {
		init: function init () {},

		setup : function setup (options) {

			this.options = options || this.options || {};

			return this.init(this.options);
		}
	});

	ibt.Script.extend = function Script (name, script) {
		return (
			scripts[name] = scripts[name] ||
			$.extend({
				name: function() {
					return name
				}
			}, Object.create(ibt.Script.prototype), script));
	}


	if (window) {
		window.Script = ibt.Script;
	}

	return ibt.Script;
})(jQuery, ibt, window);