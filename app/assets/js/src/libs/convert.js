if (typeof console === "undefined") {
	var console = {
		log: function (log) {
			// alert(log);
		}
	}
}

if (typeof Object.create !== 'function') {
	Object.create = function(o, props) {
		function F() {}
		F.prototype = o;

		if (typeof(props) === "object") {
			for (prop in props) {
				if (props.hasOwnProperty((prop))) {
					F[prop] = props[prop];
				}
			}
		}

		return new F();
	};
}

if (typeof Array.prototype.clone !== 'function') {
	Array.prototype.clone = function () {
		return $.extend([], this);
	}
}