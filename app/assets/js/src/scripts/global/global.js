(function(){
	Script.extend(
		'Global',
		{
			init: function () {
				Control('Widgets.Menu', $('#js-user'));
			}
		}
	);

	Script('Global');
})();