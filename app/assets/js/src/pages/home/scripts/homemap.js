(function($, ibt, document){
	var mapMarkers, googleMap;

	mapMarkers = [];

	googleMap = null;

	Control.extend(
		'Pages.Home.Map',
		{
			template : 'widgets.home.map.map',
			label : 'widgets.home.map.label',
			zoom : 3,
			map: null,
			bounds: null,
			options : {
				locations : []
			},
			initMap : function initMap() {

				this.clearMarkers();

				if (typeof google !== "undefined") {

					this.bounds = new google.maps.LatLngBounds();

					if (!googleMap) {
						googleMap = new google.maps.Map(document.getElementById('js-home-map'), {
							zoom: this.zoom,
							center: new google.maps.LatLng(0, 0),
							mapTypeId: google.maps.MapTypeId.SATELLITE,
							disableDefaultUI: true,
							panControl: false
						});
					}

					this.options.locations.forEach(function (location) {
						var marker = new google.maps.Marker({
							position: new google.maps.LatLng(location.latitude, location.longitude),
							map: googleMap,
							title : location.label(),
							tooltip: location.label(),
							href: location.href(),
							locationId: parseInt(location.location_id)
						}), timeout;

						this.bounds.extend(marker.getPosition());

						google.maps.event.addListener(marker, 'click', function() {
							ibt.router.pushState(this.href);
						});

						google.maps.event.addListener(marker, 'hover', function() {
							this.setAnimation(google.maps.Animation.DROP);
						});

						mapMarkers.push(marker);
					}.bind(this));

					this.setCenter();
				}
			},

			setCenter : function setCenter() {
				var marker;

				if (googleMap && mapMarkers.length) {
					if (this.bounds) {
						googleMap.fitBounds(this.bounds);
					}
				}
			},

			setLabel : function setLabel() {
				setTimeout(function () {
					$('.gm-style a:first').parent().addClass('google-link');
				}, 500);
			},

			triggerMarkerAnimation : function triggerMarkerAnimation (locationId, animation) {
				animation = animation || 'hover';
				mapMarkers.forEach(function (marker) {
					if (marker.locationId == locationId) {
						google.maps.event.trigger(marker, animation);
					}
				});
			},

			getMarkers : function getMarkers () {
				return mapMarkers;
			},

			clearMarkers : function clearMarkers() {
				mapMarkers.forEach(function (marker) {
					google.maps.event.clearListeners(marker, 'click');
					google.maps.event.clearListeners(marker, 'hover');
					marker.setMap(null);
				});

				mapMarkers = [];
			},

			render : function render(options) {
				this.options = $.extend(this.options, (options || {}));

				if (!this.element.find('#js-home-map').length) {
					this.html(this.template);
				}

				this.element.show();
				this.initMap();
				this.setLabel();

				return this;
			},

			destroy : function destroy () {
				this.clearMarkers();
				this.bounds = null;
				this.options = {
					locations : []
				};

				if (google) {
					var element = document.getElementById('js-home-map');
					google.maps.event.clearInstanceListeners(window);
					google.maps.event.clearInstanceListeners(document);
					element && google.maps.event.clearInstanceListeners(element);
					googleMap && google.maps.event.clearListeners(googleMap);
				}

				googleMap = null;
				delete(googleMap);

				Control.prototype.destroy.call(this);
			}
		}
	);
})(jQuery, ibt, document);