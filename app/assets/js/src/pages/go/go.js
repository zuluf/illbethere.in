(function($, ibt){
	var Locations = Model('Locations');

	Page.extend(
		'Pages.Go',
		{
			init: function (element, options) {
				var params, locationId;

				this.picker = Control('Pages.Go.Picker', this.element.find('.js-picker'));
				this.wiki = Control('Pages.Go.Wiki', this.element.find('.js-wiki'));

				params = ibt.router.getParams();

				locationId = params[0] || false;

				if (locationId) {
					Locations.
						wiki(locationId).
						then(
							function (wiki) {
								this.wiki.render(wiki);
							}.bind(this)
						);
				}

				return this;
			}
		}
	);
})(jQuery, ibt);