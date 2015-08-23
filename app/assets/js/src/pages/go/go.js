(function($, ibt){
	var Locations = Model('Locations');

	Page.extend(
		'Pages.Go',
		{
			init: function (element, options) {
				var params, locationId;

				this.picker = Control('Pages.Go.Picker', this.element.find('.js-picker'));
				this.wiki = Control('Pages.Go.Wiki', this.element.find('.js-wiki'));
				this.flickr = Control('Pages.Go.Flickr', this.element.find('.js-flickr'));
				this.panoramio = Control('Pages.Go.Panoramio', this.element.find('.js-panoramio'));

				params = ibt.router.getParams();

				locationId = params[0] || false;

				if (locationId) {
					Locations.wiki(locationId).then(
						function (wiki) {
							this.wiki.render(wiki);
						}.bind(this)
					);

					Locations.panoramio(locationId).then(
						function (panoramio) {
							this.panoramio.render(panoramio);
						}.bind(this)
					);
				}

				return this;
			}
		}
	);
})(jQuery, ibt);