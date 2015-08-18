(function($, ibt, window){
	var timeout, router;

	router = {

		loaded: {},

		current : '',

		init: function init () {
			this.bind();
			Page('Pages.' + ibt.page, $('.js-content'));
		},

		bind: function () {
			var self;

			self = this;

			self.current = window.location.href;

			$('body').on('click', '.js-page', function(event) {
				var href, current;

				href = $(this).attr('href');

				if (history.pushState && href) {
					event.preventDefault();
					self.pushState(href);
				}
			});

			window.onpopstate = function () {
				self.load();
			}
		},

		pushState : function pushState (href) {
			var current = window.location.href;

			if (history.pushState && href) {
				if (href != current) {
					history.pushState({}, '', href);
				}
				this.load();
			} else {
				window.location.href = href;
			}
		},

		load : function load (href) {
			var Loaded;

			Control('Widgets.Messages').clear();

			href = href || window.location.href;

			if (~href.indexOf('#') && this.current === (href).replace('#', '')) {
				return;
			}

			this.current = href.replace('#', '');

			this.loader.start();

			if (!this.getLoaded().page) {
				$.ajax({
					url : this.current,
					contentType: 'application/json',
					success: function(response, status, request) {
						if (parseInt(request.status, 10) === 200) {
							this.loaded[this.current] = {
								content: response.content,
								page: response.app.page
							};
						}

						this.renderPage(response.app.page, response.content);
					}.bind(this)
				});
			} else {
				this.renderPage();
			}
		},

		renderPage: function renderPage(page, content) {
			Loaded = this.getLoaded();

			page = page || Loaded.page;
			content = content || Loaded.content;
			// replace page content with the new one
			$('.js-content').replaceWith(content);

			// trigger page controler
			Page('Pages.' + page, $('.js-content'));

			this.loader.stop();
		},

		loader: {
			start: function removeLoader() {
				$('.js-page-ldr').addClass('loadeit');
			},

			stop: function () {
				if (timeout) {
					clearTimeout(timeout);
				}

				timeout = setTimeout(function () {
					$('.js-page-ldr').removeClass('loadeit');
				}, 350);
			}
		},

		getLoaded : function (href) {
			return this.loaded[(href || this.current)] || {
				page: '',
				content: ''
			};
		},

		getParams : function getParams () {
			return window.location.pathname.split('/').slice(2);
		}
	}

	ibt.router = function Router () {
		$.extend(this.constructor, router).init();
	};
})(jQuery, ibt, window);