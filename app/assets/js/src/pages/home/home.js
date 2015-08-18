(function($, ibt){
	var Locations = Model('Locations');

	Page.extend(
		'Pages.Home',
		{
			timeout: null,
			query : '',
			codes : [9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,91,92,93,111,112,113,114,115,116,117,118,119,120,121,122,123,144,145,186,187,188,191,219,221],
			locations : [],
			init: function (element, options) {

				this.homemap = Control('Pages.Home.Map', this.element.find('.js-map'));
				this.homedrop = Control('Pages.Home.Drop', this.element.find('.js-drop'), {
					homemap: this.homemap
				});

				this.element.find('.js-where').focus();

				return this;
			},

			'.js-where keyup' : function (event, element) {
				var query = $.trim(element.val());

				if (!~this.codes.indexOf(event.keyCode)) {
					if (query.length && query != self.query) {
						this.element.find('.js-location').addClass('has-content');
						this.find(query);
					}
				}
			},

			'.js-where focus' : function (event, element) {
				this.homedrop.render(this.locations);
			},

			find : function (query) {
				if (query.length > 2) {

					if (this.timeout) {
						clearTimeout(this.timeout);
					}

					this.timeout = setTimeout( function() {
						if (this.query !== query) {
							this.findLocation( query );
						}
					}.bind(this), 300);
				} else {
					this.homedrop.hide();
				}
			},

			findLocation : function (query) {
				var data = [];

				if (query && this.query !== query) {

					this.query = query;

					Locations.find(query).then(
						function(locations) {
							this.locations = locations.clone();

							this.homemap.render({locations: this.locations});
							this.homedrop.render(this.locations);

							if (this.query !== query) {
								this.query = '';
								this.homedrop.hide();
							}
						}.bind(this)
					);
				}
			}
		}
	);
})(jQuery, ibt);