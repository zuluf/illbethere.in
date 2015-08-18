(function($, ibt){
	Page.extend(
		'Pages.Signin',
		{
			init : function init () {
				Script('Class.Validator', {
					form: this.element.find('.js-login-form')
				});

				Script('Class.Validator', {
					form: this.element.find('.js-register-form')
				});
			},

			'.js-signin click' : function (event, element) {
				event.preventDefault();

				element = element.is('a') ? element : element.parent();

				element.hasClass('btn-load') ? element.removeClass('btn-load') : element.addClass('btn-load');
			},

			'.js-register click' : function (event, element) {
				event.preventDefault();

				element = element.is('a') ? element : element.parent();

				element.hasClass('btn-load') ? element.removeClass('btn-load') : element.addClass('btn-load');
			}
		}
	);
})(jQuery, ibt);