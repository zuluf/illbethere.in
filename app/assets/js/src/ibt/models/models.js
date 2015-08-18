(function($, ibt, window){
	var models, Model;

	models = {};

	Model = function Model (name) {

		if (!models[name]) {
			throw Error('Model [' + name + '] : not implemented');
		}

		return Model.prototype.instance(name);
	}

	$.extend(Model.prototype, {
		addInstance : function addInstance (name, model) {
			models[name] = models[name] || $.extend(Object.create(this), model);
			return models[name];
		},

		instance : function instance (name) {
			return models[name] || this;
		},

		request : function request (action, data, type) {
			return Api.get(action, data).then(
				function (response) {
					if (response.data && response.data.length && typeof this.parse === "function") {
						return this.parse(response.data);
					}

					return response.data;
				}.bind(this)
			);
		}
	});

	Model.extend = function (name, model) {
		return (models[name] || $.extend({}, Object.create(Model.prototype), model).addInstance(name, model));
	}

	if (window) {
		window.Model = Model;
	}

	return Model;
})(jQuery, ibt, window);