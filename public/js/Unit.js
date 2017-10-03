/**********************************************************************

	JAVASCRIPT TOOLS LIBRARY

**********************************************************************/

(function() {

/**********************************************************************

	STRING METHODS

**********************************************************************/

var S = {

	html: function(str) {
		return S.replace(str, {'&':'&amp;', '<':'&lt;', '"':'&quot;'});
	},
	
	replace: function(str, map, pref) {
		if ( ! pref) pref = '';
		str = String(str);
		for (var i in map) str = str.split(pref + i).join(map[i]);
		return str;
	},
	
	stringify: function(map, separator, assignment, parent) {
		var sep = separator || '&', ass = assignment || '=', dump = [];
		for (var i in map) {
			var key = parent ? parent + '[' + i + ']' : i;
			dump.push(map[i] instanceof Object ?
				arguments.callee(map[i], sep, ass, key):
				key + ass + encodeURIComponent(map[i]));
		}
		return dump.join(sep);
	},
	
	parse: function(str, separator, assignment) {
		if (str instanceof Object) return str;
		if (typeof(str) != 'string' || str === '') return {};
		var sep = separator || '&', ass = assignment || '=', arr = str.split(sep);
		for (var i = 0, f, dump = {}; i < arr.length; i++)
			if ((f = arr[i].indexOf(ass)))
				dump[arr[i].substr(0,f)] = decodeURIComponent(arr[i].substr(f + 1));
		return dump;
	},
	
	normalize: function(post) {
		var dump = {}, p, f, l;
		for (var name in post) {
			var i = 0, d = dump, n = name;
			if (~(f = n.indexOf('[')) && n.substr(-1) == ']') {
				l = (p = n.slice(f + 1, -1).split('][')).unshift(n.substr(0, f));
				while((n = p[i]) && ++i < l) d = d[n] || (d[n] = {});
			}
			d[n] = post[name];
		}
		return dump;
	},
	
	parseJson: function(data) {
		try { return window.JSON ? JSON.parse(data) : eval(data); }
		catch (e) { return null; }
	},
	
	stringifyJson: function(data) {
		try { return window.JSON ? JSON.stringify(data) : ''; }
		catch (e) { return null; }
	},
	
	hereDoc: function(method) {
		return (method = method.toString()).slice(15, -4);//-3
	},
	
	findPart: function(source, start, end, index) {
		var result = {};
		if ((result.start = source.indexOf(start, index || 0)) == -1) return null;
		if ((result.endStriped = source.indexOf(end, result.start + start.length)) == -1) return null;
		result.startStriped = result.start + start.length;
		result.end = result.endStriped + end.length;
		result.found = source.substring(result.start, result.end),
		result.foundStriped = source.substring(result.startStriped, result.endStriped);
		return result;
	},
	
	repalceTag: function(html, options) {
		var result;
		while ((result = S.findPart(html, options.open, options.close))) {
			html =	html.substring(0, result.start, result.end) +
					options.toOpen + result.foundStriped + options.toClose +
					html.substring(result.end);
		}
		return html;
	},
	
	resolveXml: function(html) {
		var tags = {'<else>':'<% } else { %>', '<endif>':'<% } %>', '<endfor>':'<% } %>', '<endswitch>':'<% } %>'};
		var options = [
			{open:'{$',			close:'}',	toOpen:'<%= ',				toClose:' %>'},
			{open:'<var (',		close:')>',	toOpen:'<% var ',			toClose:'; %>'},
			{open:'<if (',		close:')>',	toOpen:'<% if (',			toClose:') { %>'},
			{open:'<elseif (',	close:')>',	toOpen:'<% } else if (',	toClose:') { %>'},
			{open:'<for (',		close:')>',	toOpen:'<% for (',			toClose:') { %>'},
			{open:'<switch (',	close:')>',	toOpen:'<% switch (',		toClose:') { %>'},
		];
		for (var i in options) html = S.repalceTag(html, options[i]);
		return S.replace(html, tags);
	},
	
	generate: function(html) {
		html = html.replace(/[\s\t\r\n]+/g, ' ');
		html = S.resolveXml(html);
		var s = "a.push(", e = ");", q = "'", p, c, r = {"'":"\\'"}, list = [];
		while ((p = S.findPart(html,'<%','%>')) && (c = p.foundStriped)) {
			list.push(s + q + S.replace(html.substr(0, p.start),r) + q + e);
			list.push(c[0] == '=' ? s + c.substr(1) + e : " " + c + " ");
			html = html.substr(p.end);
		}
		list.push(s + q + S.replace(html,r) + q + e);
		html = list.join('');
		return 'var a=[];with(hash){ ' + html + ' } return a.join("");';
	},
	
	/*generate: function(html) {
		var s = 0, e = 0, list = [], pref, code = '';
		html = html.replace(/[\s\t\r\n]+/g, ' ');
		html = S.resolveXml(html);
		do {
			s = html.indexOf('<%');
			if (s >= 0) {
				e = html.indexOf('%>', s);
				if (e == -1) e = html.length;
				pref = html.substr(0, s);
				code = html.substr(s + 2, e - s - 2);
				html = html.substr(e + 2);
				list.push("a.push('" + pref.replace(/\'/g, "\\'") + "');");
				if (code[0] == '=') list.push("a.push(" + code.substr(1) + ");");
				else list.push(" " + code + " ");
			}
			else {
				list.push("a.push('" + html.replace(/\'/g, "\\'") + "');");
			}
		} while (s >= 0);
		html = list.join('');
		return 'var a=[];with(hash){ ' + html + ' } return a.join("");';
	},*/
	
	templater: function(html, hash, scope) {
		var func = S.generate(html);
		return D.exec(scope, func, {hash:hash || {}});
	}

};

/**********************************************************************

	ARRAY METHODS

**********************************************************************/

var A = {
	
	sortByField: function(array, field, reverse) {
		return (array = A.toArray(array).sort(function(a, b, c) {
			return isNaN(c = (a[field] - b[field])) ? a[field] > b[field] : c;
		})) && reverse ? array.reverse() : array;
	},
	
	filterMap: function(object, callback) {
		var args = [].slice.call(arguments, 2), data = new Array(), r;
		for (var i in object)
			if ((r = callback.apply(data, [i, object[i] ].concat(args))) !== undefined) data.push(r);
		return data;
	},
	
	keys: function(object) {
		return A.filterMap(object, function(k,v) {
			this.push(k);
		});
	},
	
	toArray: function(object) {
		return A.filterMap(object, function(k,v) {
			this.push(v);
		});
	},
	
	assoc: function(object, key, val) {
		return A.filterMap(object, function(k,v) {
			this[ v[key] ] = val ? v[val] : v;
		});
	},
	
	indexOf: function(object, search) {
		for (var i in object) if (object[i] === search) return i;
		return -1;
	},
	
	concat: function() {
		var obj = {};
		for (var i = 0; i < arguments.length; i++)
			for (var j in arguments[i]) obj[j] = arguments[i][j];
		return obj;
	},
	
	group: function(object, field) {
		return Unit.filterMap(object, function(k, v) {
			var f = v[field];
			if ( ! this[f]) this[f] = {};
			this[f][k] = v;
		});
	},
	
	unique: function(object) {
		return Unit.filterMap(object, function(k, v) {
			if (A.indexOf(this, v) == -1) this[k] = v;
		});
	}
	
};

/**********************************************************************

	NUMERIC METHODS

**********************************************************************/

var N = {

	easing: function(distance, frames, duration) {
		return Math.round(frames > 1 ? distance / frames * duration : distance);
	},
	
	random: function(min, max) {
		return Math.round(Math.random() * (max - min)) + min;
	},
	
	limit: function(number, min, max) {
		return number < min ? min : number > max ? max : number;
	}

};

/**********************************************************************

	FORMATTING METHODS

**********************************************************************/

var F = {

	mtime: function() {
		return (new Date()).getTime();
	},
	
	time: function() {
		return Math.round(F.mtime() / 1000);
	},
	
	date: function(time, format) {
		format = format || 'd m, T', time = parseInt(time) * 1000 || time;
		var dubl = function(n) { return n < 10 ? '0' + n : n; };
		var mons = ['Jan','Fab','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var date = new Date(time);
		var code = {
			T: dubl(date.getHours()) + ':' + dubl(date.getMinutes()),
			D: dubl(date.getDay()),
			M: dubl(date.getMonth()),
			Y: date.getYear(),
			d: date.getDay(),
			m: mons[date.getMonth()],
			y: date.getFullYear()
		};
		for (var i in code) format = format.replace(i, code[i]);
		return format;
	},
	
	price: function(price) {
		var p = new Number(price).toFixed(2);
		while (p.match(/\d{4}/)) p = p.replace(/(\d{3}(\.|\,))/, ',$1');
		return p;
	},
	
	roundSize: function(p) {
		var s = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb'], t = 0, r = parseFloat(p);
		while (r > 1024 && s[t+1]) r /= 1024, t++;
		return (t == 0 ? r : (r).toFixed(2)) + ' ' + s[t];
	},
	
	roundTime: function(t) {
		var s = {ms:1000, s:60, m:60, h:24}, f = '', p;
		for (var i in s) if (t > 0) p = (t % s[i]), f = p + i + ' ' + f, t = (t - p) / s[i];
		return f || '0ms';
	}

};

/**********************************************************************

	COOKIES METHODS

**********************************************************************/

var C = {

	getCookie: function(name) {
		return S.parse(document.cookie, '; ', '=')[name] || undefined;
	},
	
	setCookie: function(name, value, params) {
		var cookie = name + '=' + encodeURIComponent(value), date = new Date();
		(params || (params = {})).path = params.path || '/';
		if (params.expires) {
			date.setTime(date.getTime() + parseInt(params.expires) * 1000);
			params.expires = date.toUTCString();
		}
		for (var p in params) cookie += '; ' + p + '=' + params[p];
		document.cookie = cookie;
	},
	
	removeCookie: function(name) {
		C.setCookie(name, null, {expires:-1});
	}

};

/**********************************************************************

	CONNECTION METHODS

**********************************************************************/

var R = {

	requestAjax: function(page, data, callback) {
		R.request(page, data, function(req) { 
			if (req.readyState == 4) callback(S.parseJson(req.responseText), req);
		});
	},
	
	request: function(page, data, callback) {
		var req = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
		//page = page + (page.indexOf('?') >= 0 ? '&' : '?') + 'r=' + N.random(10000, 99999);
		req.onreadystatechange = function() { callback(req); };
		req.open(data ? 'POST' : 'GET', '/' + page, true);
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		//req.setRequestHeader('Connection', 'close');
		req.send(data ? S.stringify(data) : null);
	},
	
	getParam: function(name) {
		return S.parse(window.location.search.replace('?',''))[name];
	}
	
};
	
/**********************************************************************

	DOM METHODS

**********************************************************************/

var D = {

	create: function(parent, tagName, properties, attributes) {
		var element = document.createElement(tagName);
		for (var i in properties) element[i] = properties[i];
		for (var i in attributes) element.setAttribute(i, attributes[i]);
		if (parent && parent.appendChild) parent.appendChild(element);
		return element;
	},
	
	remove: function(object) {
		if ( ! (object = Unit(object))) return;
		object.parentNode.removeChild(object);
	},
	
	append: function(object, methods, scope) {
		for (var i in methods) {
			if (methods instanceof Array) arguments.callee(object, methods[i], scope);
			else if ( ! object[i]) {
				object[i] = methods[i] instanceof Function && scope?
					methods[i].bind.apply(methods[i], scope):
					methods[i];
			}
		}
		return object;
	},
	
	inner: function(object, html, emit) {
		if ( ! (object = Unit(object))) return;
		if (object.value !== undefined) object.value = html;
		else if (object.innerHTML !== undefined) object.innerHTML = html;
		if (emit) E.emitEvent(object, 'onchange');
	},
	
	hasClass: function(object, name) {
		return (object = Unit(object)) && Unit.indexOf(object.className.split(' '), name) != -1;
	},
	
	addClass: function(object, name) {
		(object = Unit(object)) && ! D.hasClass(object, name) && (object.className += ' ' + name);
	},
	
	removeClass: function(object, name) {
		(object = Unit(object)) && (object.className = object.className.replace(new RegExp('(\\s|^)' + name + '(\\s|$)'), ' '));
	},
	
	setClassValue: function(object, name, value) {
		(object = Unit(object)) && (object.className = object.className.replace(new RegExp('(\\s|^)' + name + '-[a-zA-Z0-9]*(\\s|$)'), ' '));
		D.addClass(object, name + '-' + value);
	},
	
	trigger: function(object, name, mode) {
		if ( ! (object instanceof Array)) {
			name = typeof name == 'string' ? name : 'hidden';
			mode = mode === true || (mode !== false && ! D.hasClass(object, name));
			mode ? D.addClass(object, name) : D.removeClass(object, name);
		} else for (var i in object) D.trigger(object[i], name, mode);
		return mode;
	},
	
	/*get: function(parent, expression) {
		var matches = expression.match(/^([a-zA-Z]+)?(\.([^\s]+))?$/);
		return this.getChilds(parent, matches[3], matches[1]);
	},*/
	
	getChilds: function(parent, className, tagName) {
		var nodes = (parent || document).getElementsByTagName(tagName || '*');
		if ( ! className) return nodes;
		for (var i = 0, l = nodes.length, res = []; i < l; i++)
			if (D.hasClass(nodes[i], className)) res.push(nodes[i]);
		return res;
	},
	
	getByAttribute: function(parent, name, index) {
		var nodes = (parent || document).getElementsByTagName('*'), found = {}, param;
		for (var i in nodes)
			if (typeof (param = D.getAttribute(nodes[i], name)) == 'string')
				found[index ? i : param] = nodes[i];
		return found;
	},
	
	getAllAttributes: function(object, prefix) {
		return A.filterMap(Unit(object).attributes, function(k, v, p) {
			 if (v && (k = v.nodeName) && ( ! p || k.substr(0,p) === prefix))
			 	this[k.substr(p).toLowerCase()] = v.nodeValue;
		}, (prefix || '').length);
	},
	
	getAttribute: function(object, attribute) {
		return object && object.getAttribute ? object.getAttribute(attribute) : undefined;//Unit
	},
	
	setAttribute: function(object, attribute, value) {
		(object = Unit(object)) && object.setAttribute && object.setAttribute(attribute, value);
	},
	
	disableSelection: function(object) {
		if ( ! (object = Unit(object))) return;
		object.onselectstart = function(e) { return false; };
		object.unselectable = "on";
		object.style.MozUserSelect = "none";
	},
	
	exec: function(scope, func, hash) {
		var keys = [], values = [];
		for (var i in hash) keys.push(i), values.push(hash[i]);
		return keys.push(func), Function.apply(scope, keys).apply(scope, values);
	},
	
	getOffset: function(target, root) {//TODO: refactoring
		var top = 0, left = 0;
		if ( ! target || ! target.offsetParent) return;
		do { top += target.offsetTop - target.scrollTop;
			left += target.offsetLeft - target.scrollLeft; }
		while ((target = target.offsetParent) && target != root && target != document.body);
		return {top:top, left:left};
	},
	
	pageSize: function() {
		var w = window,
			d = document,
			e = d.documentElement,
			b = d.getElementsByTagName('body')[0],
			x = w.innerWidth || e.clientWidth || b.clientWidth,
			y = w.innerHeight|| e.clientHeight|| b.clientHeight;
		return {width:x, height:y};
	},
	
	move: function(object, top, left, time, easy) {//TODO: refactoring
		if ( ! object || ! object.style) return;
		time = time || 1000, easy = easy || 1;
		var ot = parseInt(object.style.top || object.offsetTop);
		var ol = parseInt(object.style.left || object.offsetLeft);
		for (var i = 0, n = 0, f = Math.round(time / 40), t = [], l = []; i < f; i++) {
			ot = t[i] = (ot + N.easing(top - ot, f - i, easy));
			ol = l[i] = (ol + N.easing(left - ol, f - i, easy));
		}
		if (object.moveInterval) {
			clearInterval(object.moveInterval);
			delete object.moveInterval;
		}
		object.moveInterval = setInterval(function() {
			object.style.left = l[n] + 'px';
			object.style.top = t[n] + 'px';
			if (++n > f) {
				clearInterval(object.moveInterval);
				delete object.moveInterval;
			}
		}, 40);
	},
	
	resize: function(object, width, height, time, easy) {//TODO: refactoring
		if ( ! object || ! object.style) return;
		time = time || 1000, easy = easy || 1;
		var ow = parseInt(object.style.width || object.offsetWidth);
		var oh = parseInt(object.style.height || object.offsetHeight);
		for (var i = 0, n = 0, f = Math.round(time / 40), w = [], h = []; i < f; i++) {
			ow = w[i] = (ow + N.easing(width - ow, f - i, easy));
			oh = h[i] = (oh + N.easing(height - oh, f - i, easy));
		}
		if (object.moveInterval) {
			clearInterval(object.moveInterval);
			delete object.moveInterval;
		}
		object.moveInterval = setInterval(function() {
			object.style.width = w[n] + 'px';
			object.style.height = h[n] + 'px';
			if (++n > f) {
				clearInterval(object.moveInterval);
				delete object.moveInterval;
			}
		}, 40);
	}

};

/**********************************************************************

	EVENT METHODS

**********************************************************************/

var E = {

	addEvent: function(object, event, callback) {
		if (object.addEventListener) object.addEventListener(event.replace(/^on/i,''), callback, false);
		else if (object.attachEvent) object.attachEvent(event, callback);
		else object[event] = callback;
	},
	
	removeEvent: function(object, event, callback) {
		if (object.removeEventListener) object.removeEventListener(event.replace(/^on/i,''), callback, false);
		else if (object.detachEvent) object.detachEvent(event, callback);
		else object[event] = null;
	},
	
	emitEvent: function(object, event) {
		try {
			var mouse = 'click,mouseup,mousedown,mousemove'.split(','), e;
			if (document.createEventObject) object.fireEvent(event, document.createEventObject());
			else if (document.createEvent && (event = event.replace(/^on/, '').toLowerCase())) {
				e = document.createEvent(Unit.indexOf(mouse,event) >= 0 ? 'MouseEvents' : 'HTMLEvents');
				e.initEvent(event, true, false);
				object.dispatchEvent(e);
			}
		} catch (e) {}
	},
	
	cancelEvent: function(event) {
		if ( ! (event = (event || window.event))) return false;
		if (event.stopPropagation) event.stopPropagation();
		if (event.preventDefault) event.preventDefault();
		if (event.cancelBubble != null) event.cancelBubble = true;
		return false;
	}

};

/**********************************************************************

	INTERFACE FUNCTIONS

**********************************************************************/

var I = {
	
	browser: function() {
		var m = navigator.userAgent.toLowerCase().match(/(msie|firefox|opera|chrome|safari)[\s\/]([\d\.]+)/);
		return m ? {name: m[1], version: parseInt((m[2] || '').split('.')[0]), full: m[2]} : {};
	},
	
	/*browser: function() {//notmy
		var a = navigator.userAgent.toLowerCase(), t,
			m = a.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*([\d\.]+)/) || [];
		if (/trident/.test(m[1])) return 'msie ' + ((/\brv[ :]+(\d+(\.\d+)?)/g.exec(a) || [])[1] || '');
		m = m[2] ? [m[1], m[2]] : [navigator.appName, navigator.appVersion, '-?'];
		if ((t = a.match(/version\/([\.\d]+)/)) != null) m[2] = t[1];
		return m.join(' ');
	},*/
	
	reactivateAllElements: function(parent) {
		var found = D.getByAttribute(parent, 'ui', true), ui;
		for (var i in found) {
			if (Unit(found[i]).getAttribute('uiIsActive')) continue;
			if ( ! window[(ui = 'UI_' + found[i].getAttribute('ui'))]) continue;
			found[i].setAttribute('uiIsActive', true), window[ui](found[i]);
		}
	},
	
	onDomReady: function(callback, target) {
		if (target) window = target.window || target.contentWindow;
		if (target) document = target.document || target.contentDocument;
		var check = function() {
			/loaded|complete/.test(document.readyState) && ready();
			try { document.documentElement && document.documentElement.doScroll('left'), ready(); }
			catch (error) {}
		}, ready = function() {
			if (timer == null) return;
			clearInterval(timer), timer = null, callback();
		}, timer = setInterval(check, 10);
		document.addEventListener && document.addEventListener('DOMContentLoaded', ready, false);
		E.addEvent(window, 'onload', ready);
	}
	
};

/**********************************************************************

	LIBRARY CONSTRUCTOR

**********************************************************************/

Unit = function(target) {
	if (typeof target == 'string') target = document.getElementById(target);
	if (typeof target != 'object' || ! target) return null;
	//if (target instanceof Array) target = target.map(arguments.callee);
	//if (target instanceof Array) target = Unit.filterMap(target, arguments.callee);
	if (target instanceof Array) for (var i in target) target[i] = Unit(target[i]);
	else if ( ! target.trigger) D.append(target, [D,E], [target,target]);
	return target;
};

D.append(Unit, [S,A,N,F,C,R,D,E,I]);
I.onDomReady(I.reactivateAllElements);

})();