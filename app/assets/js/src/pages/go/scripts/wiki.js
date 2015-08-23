(function(){
	Control.extend(
		'Pages.Go.Wiki',
		{
			template : 'widgets.go.wiki',
			render : function (wiki) {
				if ($.isPlainObject(wiki)) {
					wiki.parseWiki = function () {
						if (this.geosearch.length) {
							return ~this.geosearch[0].fullurl.indexOf('wikivoyage') ? '(wikivoyage)' : '(wikipedia)';
						}

						return '(wikipedia)';
					}

					this.html(this.template, wiki);
				}

				return this;
			}
		}
	);
})();