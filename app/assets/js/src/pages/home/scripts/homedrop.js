(function($, ibt, document){
	Control.extend(
		'Pages.Home.Drop',
		{
			template : 'widgets.home.drop.list',
			render : function (locations) {
				locations = locations || this.options

				this.html(this.template, {
					locations : locations
				});

				$(document).off('mousedown.homedrop', this.close.bind(this));
				$(document).on('mousedown.homedrop', this.close.bind(this));

				this.show();
				return this;
			},

			close : function (event) {
				if (!this.element.is(event.target) &&
					!this.element.has(event.target).length &&
					!$(event.target).hasClass('js-where')) {
						this.hide();
				}
			},

			'a.js-location mouseover' : function (event, element) {
				if (this.options.homemap) {
					this.options.homemap.triggerMarkerAnimation(element.data('id'));
				}
			},

			destroy : function (event) {
				$(document).off('mousedown.homedrop', this.close.bind(this));

				Control.prototype.destroy.call(this);
			}
		}
	);
})(jQuery, ibt, document);