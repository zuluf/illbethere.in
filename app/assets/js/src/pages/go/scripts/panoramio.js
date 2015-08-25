(function(){
	Control.extend(
		'Pages.Go.Panoramio',
		{
			template : 'widgets.go.panoramio',
			render : function (panoramio) {
				this.html(this.template, panoramio);
				return this;
			}
		}
	);
})();