(function($, ibt){
	ibt.templates = {

		templates: ibt.templates,

		render: function (template, data) {
			var compiled;
			data = $.extend({}, {ibt: ibt.config}, data);

			if (Handlebars && this.templates[template]) {
				compiled = Handlebars.compile(this.templates[template], data);
				return compiled(data);
			}

			return template;
		}
	}

	if (Handlebars) {
		Handlebars.registerHelper('_t', function (locale) {
			return _t(typeof locale.fn === "function" ? locale.fn() : locale);
		});
	}

	ibt.render = function renderTemplate() {
		return ibt.templates.render.apply(ibt.templates, $.makeArray(arguments));
	};
})(jQuery, ibt);