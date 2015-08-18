(function($){
	Control.extend(
		'Widgets.Messages',
		{
			element : $('<div class="js-messages messages">'),
			templates : {
				pop: 'widgets.messages.pop'
			},

			pop : function pop (message) {

				$('body').append(this.element);

				this.html(this.templates.pop, {message: message});

			},

			clear : function clear () {
				$('.js-messages').remove();
			}
		}
	);

})(jQuery);