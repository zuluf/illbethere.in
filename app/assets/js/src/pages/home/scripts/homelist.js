(function($, ibt, document){
	Control.extend(
		'Pages.Home.List',
		{
			template : 'widgets.home.locations',
			render : function (locations) {

				locations = locations || this.options;

				this.html(this.template, {
					locations : locations
				});
			}
		}
	);
})(jQuery, ibt, document);