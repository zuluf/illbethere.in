(function($){

	var Users = Model('Users');

	Control.extend(
		'Widgets.Menu',
		{
			template : 'widgets.user.menu.menu',
			init : function init () {
				this.html(this.template, {user: Users.getCurrent()});
			},

			'.js-login click' : function (event, element) {
				event.preventDefault();

				this.html(this.template, {user: {name: 'Joaninha', user_id: 123}});
			}
		}
	);

})(jQuery);