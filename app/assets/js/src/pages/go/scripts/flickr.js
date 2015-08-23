(function(){
	Control.extend(
		'Pages.Go.Flickr',
		{
			template : 'widgets.go.flickr',
			render : function (flickr) {
				this.html(this.template, flickr);

				return this;
			}
		}
	);
})();