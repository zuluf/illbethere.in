(function($, ibt, document){

	Control.extend(
		'Pages.Go.Picker',
		{
			template : 'widgets.scripts.picker',
			render : function () {
				this.html(this.template);
				return this;
			}
		}
	);

})(jQuery, ibt, document);