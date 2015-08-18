(function($, ibt){
	Model.extend(
		'Users',
		{
			build: function build (user) {

				return user;
			},

			parse : function parse (users) {
				return $.makeArray(users.map(function (user) {
					return this.build(user);
				}.bind(this)));
			},

			getCurrent : function getCurrent() {
				return ibt.user;
			},

			login : function login (username, password) {
				return this.request('login', {
					username: username,
					password: password
				});
			}
		}
	);
})(jQuery, ibt);