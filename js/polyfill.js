
/* POLYFILL */

/* polyfill indexOf pour IE < 9 */
if(!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(searchElement, fromIndex) {
		var k;

		// 1. Soit O le résultat de l'appel à ToObject avec
		//    this en argument.
		if (this == null) {
			throw new TypeError('"this" vaut null ou n est pas défini');
		}

		var O = Object(this);

		// 2. Soit lenValue le résultat de l'appel de la
		//    méthode interne Get de O avec l'argument
		//    "length".
		// 3. Soit len le résultat de ToUint32(lenValue).
		var len = O.length >>> 0;

		// 4. Si len vaut 0, on renvoie -1.
		if (len === 0) {
			return -1;
		}

		// 5. Si l'argument fromIndex a été utilisé, soit
		//    n le résultat de ToInteger(fromIndex)
		//    0 sinon
		var n = +fromIndex || 0;

		if (Math.abs(n) === Infinity) {
			n = 0;
		}

		// 6. Si n >= len, on renvoie -1.
		if (n >= len) {
			return -1;
		}

		// 7. Si n >= 0, soit k égal à n.
		// 8. Sinon, si n<0, soit k égal à len - abs(n).
		//    Si k est inférieur à 0, on ramàne k égal à 0.
		k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

		// 9. On répète tant que k < len
		while (k < len) {
			// a. Soit Pk égal à ToString(k).
			//    Ceci est implicite pour l'opérande gauche de in
			// b. Soit kPresent le résultat de l'appel de la
			//    méthode interne HasProperty de O avec Pk en
			//    argument. Cette étape peut être combinée avec
			//    l'étape c
			// c. Si kPresent vaut true, alors
			//    i.  soit elementK le résultat de l'appel de la
			//        méthode interne Get de O avec ToString(k) en
			//        argument
			//   ii.  Soit same le résultat de l'application de
			//        l'algorithme d'égalité stricte entre
			//        searchElement et elementK.
			//  iii.  Si same vaut true, on renvoie k.
			if (k in O && O[k] === searchElement) {
				return k;
			}
			k++;
		}
		return -1;
	};
}

/* polyfill isArray pour IE < 9 */
if(!Array.isArray) {
	Array.isArray = function(arg) {
		return Object.prototype.toString.call(arg) === '[object Array]';
	};
}

/* polyfill CustomEvent pour IE */
if ( typeof window.CustomEvent === undefined ) {
	function CustomEvent ( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };
		var evt = document.createEvent( 'CustomEvent' );
		evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;
	window.CustomEvent = CustomEvent;
}

/* polyfill JSON pour IE < 8 */
if (!window.JSON) {
	window.JSON = {
	parse: function(sJSON) { return eval('(' + sJSON + ')'); },
	stringify: (function () {
		var toString = Object.prototype.toString;
		var isArray = Array.isArray || function (a) { return toString.call(a) === '[object Array]'; };
		var escMap = {'"': '\\"', '\\': '\\\\', '\b': '\\b', '\f': '\\f', '\n': '\\n', '\r': '\\r', '\t': '\\t'};
		var escFunc = function (m) { return escMap[m] || '\\u' + (m.charCodeAt(0) + 0x10000).toString(16).substr(1); };
		var escRE = /[\\"\u0000-\u001F\u2028\u2029]/g;
		return function stringify(value) {
		if (value == null) {
			return 'null';
		} else if (typeof value === 'number') {
			return isFinite(value) ? value.toString() : 'null';
		} else if (typeof value === 'boolean') {
			return value.toString();
		} else if (typeof value === 'object') {
			if (typeof value.toJSON === 'function') {
			return stringify(value.toJSON());
			} else if (isArray(value)) {
			var res = '[';
			for (var i = 0; i < value.length; i++)
				res += (i ? ', ' : '') + stringify(value[i]);
			return res + ']';
			} else if (toString.call(value) === '[object Object]') {
			var tmp = [];
			for (var k in value) {
				if (value.hasOwnProperty(k))
				tmp.push(stringify(k) + ': ' + stringify(value[k]));
			}
			return '{' + tmp.join(', ') + '}';
			}
		}
		return '"' + value.toString().replace(escRE, escFunc) + '"';
		};
	})()
	};
}

// Pour IE (toutes versions)
// Ce fragment de code pour l'émulation ne supporte pas l'utilisation de symboles car ES5 ne possède pas les symboles 
if (typeof Object.assign != 'function') {
	Object.assign = function (target, varArgs) { // .length of function is 2
		'use strict';
		if (target == null) { // TypeError if undefined or null
			throw new TypeError('Cannot convert undefined or null to object');
		}

		var to = Object(target);

		for (var index = 1; index < arguments.length; index++) {
			var nextSource = arguments[index];

			if (nextSource != null) { // Skip over if undefined or null
				for (var nextKey in nextSource) {
					// Avoid bugs when hasOwnProperty is shadowed
					if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
						to[nextKey] = nextSource[nextKey];
					}
				}
			}
		}
		return to;
	};
}