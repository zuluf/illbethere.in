(function($, ibt){
	Model.extend(
		'Locations',
		{
			build: function (location) {
				location.label = function () {
					return this.name + ', ' + this.country_long +
						(this.region && !isFinite(this.region) ? ' (' + this.region + ')' : '');
				}

				location.href = function () {
					var name, country;

					name = this.name.replace(/\s/g, '.').toLowerCase();
					country = this.country_long.replace(/\s/g, '.').toLowerCase();

					return ibt.config.app + 'go/' + this.location_id + '/' + name + ',' + country;
				}

				return location;
			},

			parse : function (locations) {
				return $.makeArray(locations.map(function (location) {
					return this.build(location);
				}.bind(this)));
			},

			find : function (query) {
				return this.request('locations/find/' + query);
			},

			wiki : function (locationId) {
				return this.request('wiki/location/' + locationId);
			},

			flickr : function (locationId) {
				return this.request('flickr/location/' + locationId);
			},

			panoramio : function (locationId) {
				return this.request('panoramio/location/' + locationId);
			}
		}
	);
})(jQuery, ibt);