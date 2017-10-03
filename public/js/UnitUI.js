/*******************************************************************************

	UNIT EXTENDED USER INTERFACE COMPONENTS

*******************************************************************************/

function UI_Menu(id) {
	var hash = Unit(id), area, menu, keys = [];														// link on the menu container
	var conf = hash.getAllAttributes('ui');															// get menu configuration from attributes
	var node = hash.getByAttribute('uiMenu');														// get menu child elements
	var mult = hash.getByAttribute('uiMenuField');													// get menu multiple value fields
	var init = function() {
		menu = Unit.filterMap(hash.getByAttribute('uiMenuOption'), function(k,v,n) {				// get menu options list
			k = v.getAttribute('uiMenuOption'), this[(n = k.substr(k.indexOf(':') + 1))] = {		// add menu item
				link:	(v = Unit(v)),																// set menu link
				body:	(k[0] == '#' ? Unit(k.substr(1)) : null),									// set menu target element
				text:	(v.innerHTML || k),															// set menu displayed value
				mult:	Unit.parse(v.getAttribute('uiMenuValues'))									// set menu multiple values
			}, keys.push(n);																		// store menu key
			v.addEvent('onclick', hash.selectMenu.bind(hash, n));									// attach onclick event on menu element
		});
		var s = hash.selectedList = ((node.value || {}).value || conf.selected || '').split(',');	// get menu default values
		for (var i = 0; i < s.length; i++) if (keys.indexOf(s[i]) < 0) s.splice(i--, 1);			// remove unavailable values from selection
		if (hash.hasClass('disabled')) return hash.selectMenu();
		if (node.group) Unit.addEvent(node.group, 'onclick', hash.selectMenu.bind(hash, null));		// add onclick event for group selector
		if (node.dropdown) {
			Unit.disableSelection(node.dropdown);
			area = Unit(hash.create('INPUT', {type:'text'}, {style:'position:absolute;left:-10000px'}));
			area.addEvent('onfocus', hash.openMenu.bind(hash,true));
			hash.addEvent('onclick', hash.openMenu.bind(hash,true));
			hash.addEvent('onkeydown', hash.searchMenu);											// add event for dropdown list
		}
		hash.selectMenu();																			// select default value and update childs
	};
	
	hash.openMenu = function(mode) {
		if ( ! Unit(node.dropdown)) return;
		if ((mode = (mode === true && node.dropdown.hasClass('hidden')))) {
			if (area && document.activeElement != area) return area.focus();
			setTimeout(Unit.addEvent.bind(hash, document, 'onclick', hash.openMenu), 1);
		}
		else Unit.removeEvent(document, 'onclick', hash.openMenu);
		node.dropdown.trigger(null, ! mode);
	};
	
	hash.searchMenu = function(event) {
		var i = Unit.indexOf(keys, hash.selected), k = event.keyCode;
		if (k == 38)		hash.selectMenu(keys[i - 1 < 0 ? keys.length - 1 : i - 1]);				// select previous menu
		else if (k == 40)	hash.selectMenu(keys[i * 1 + 1] || keys[0]);							// select next menu
		else if ([9,13,27].indexOf(k) >= 0) hash.openMenu();										// close menu on esc, enter, cancel
		if ((i = menu[hash.selected])) {															// scroll to the menu item
			var it = i.link.offsetTop * 1, ih = i.link.offsetHeight * 1;
			var nt = node.dropdown.scrollTop * 1, nh = node.dropdown.offsetHeight * 1;
			node.dropdown.scrollTop = it < nt ? it : (it + ih > nt + nh ? it + ih - nh : nt);
		}
		if (k != 9) Unit.cancelEvent(event);
	};
	
	hash.selectMenu = function(id) {
		var text = [], s = hash.selectedList, i = Unit.indexOf(s,id), l = keys.length, m;			// get current selection and short names
		if (menu[id])		conf.multiple ? (i >= 0 ? s.splice(i, 1) : s.push(id)) : s = [id];		// check and select required value
		if (id === null)	s = keys.slice(0, s.length == l && l ? 0 : undefined);					// select/unselect all values
		Unit.filterMap(menu, function(k,v,i) {
			if (conf.required && ! menu[s[0]]) s = [k];												// if nothing selected and field required - select first option
			if (i = (Unit.indexOf(s, k) >= 0)) text.push(v.text), m = v;							// check if menu is selected and get menu label
			if (v.link)	v.link.trigger('active', i);												// set className = 'active' for selected menu item
			if (v.body)	v.body.trigger('hidden', !i);												// display menu target
		});
		hash.previous			= hash.selected;
		hash.selected			= s.join(',');
		hash.selectedList		= s;
		if (m)					for (var i in m.mult) if (mult[i]) Unit.inner(mult[i], m.mult[i]);
		if (node.value)			Unit.inner(node.value, hash.selected);
		if (node.label)			Unit.inner(node.label, text.join(', '));
		if (node.group)			Unit.trigger(node.group, 'active', s.length === l && l > 0);
		if (conf.target)		Unit.set(conf.target, conf.multiple ? s : hash.selected);
		if (id !== undefined) {
			if (node.value)		Unit.emitEvent(node.value, 'onchange');
			if (conf.action)	Unit.exec(hash, conf.action, {hash:hash});
		}
	};
	
	return init(), hash;
}

function UI_Form(id) {
	var hash = Unit(id);														// create unit object by form id
	var form = hash.getByAttribute('uiForm');									// get form elements
	var init = setTimeout(function() {											// init method
		form.config		= hash.getAllAttributes('ui');							// get all form ai attributes
		form.action		= form.config.action;									// get form submit commands
		form.control	= eval(form.config.control);							// get form control method
		form.scope		= eval((form.config.control || '').split('.')[0]);		// control scope
		form.rules		= eval(form.config.rules);								// get form validation rules variable
		form.fields		= hash.getFields();										// get form fields
		form.parents	= hash.getByAttribute('uiField');						// get form field parents
		form.elements	= Unit.filterMap(form.parents, function(name, parent) {	// collect form elements with all parameters
			this[name]	= {
				parent:		Unit(parent),										// store field parent element link
				label:		parent.getChilds(null, 'label')[0],					// get field label
				rules:		parent.getAttribute('uiFieldRules'),				// get field validation rules
				comment:	parent.getChilds(null, 'comment')[0],				// get field comment
				field:		form.fields[name]									// get field element
			};
		});
		hash.addEvent('onchange', hash.onChange);
		hash.addEvent('onsubmit', hash.onSubmit);
		hash.validate(0);
	}, 1);
	
	hash.getFields = function() {
		for (var i = 0, f, e = {}; i < hash.elements.length; i++)
			if ((f = hash.elements[i]) && f.name && (f.type != 'radio' || f.checked)) e[f.name] = f;
		return e;
	};
	
	hash.getValues = function() {
		return Unit.filterMap(hash.getFields(), function(k,v) {
			if ( ! v.disabled) this[k] = v.value;
		});
	};
	
	hash.validate = function(level) { // 0-submit, 1-change, 2-keydown, 3-init
		if (form.control instanceof Function) {
			// validate form
			// call control with validation fields
			//hash.trigger('validation', true);
			form.control.call(form.scope, hash.getValues(), level, function(error) {
				//hash.trigger('validation', false);
				if ( ! error) return hash.complete();
				if (form.message) {
					Unit.trigger(form.message, 'error', error.type == 'ERROR');
					Unit.trigger(form.message, 'fatal', error.type == 'FATAL');
					Unit.inner(form.message, error.message || '');
				}
				Unit.filterMap(form.elements, function(name, element, fields) {
					Unit.trigger(element.parent, 'error', !!fields[name]);
					//console.log(element.comment);
					//Unit.inner(element.comment)
				}, (error.fields || {}));
			});
			//if (result === false) hash.trigger('validation', false);
			/*if (comp) comp(hash.getValues(), submit, function(messages) {
				var fields = hash.getFields();
				Unit.filterMap(messages, function(field, message) {
					//if (fields.field)
				});
			});*/
			/*if (report === true && messages.length) SA.alert(Unit.unique(messages).join('<br>'));
			if (node.submit) Unit.trigger(node.submit, 'disabled', messages.length > 0);
			return messages.length === 0;*/
		}
		
		if (level == 3 && form.action) {
			hash.exec(form.action, {values:hash.getValues()})
		}
	};
	
	hash.complete = function() {
		// call form action
	};
	
	hash.onChange = function(event) {
		hash.validate(2);
	};
	
	hash.onSubmit = function(event) {
		hash.validate(3);
		return Unit.cancelEvent(event);
	};
	
	return hash;
}

function UI_Table(id) {
	var hash = Unit(id);
	var init = setTimeout(function() {
		hash.action		= hash.getAttribute('uiAction');
		hash.options	= Unit.parse(hash.getAttribute('uiOptions'));
		hash.orderLinks	= hash.getByAttribute('uiOrder');
		hash.paginLinks	= hash.getByAttribute('uiPagin');
		
		if ( ! hash.options.index) hash.options.index = 0;
		
		Unit.filterMap(hash.orderLinks, function(key, link) {
			Unit.addEvent(link, 'onclick', hash.orderTable.bind(hash, key, undefined));
			if (key == hash.options.order) Unit.addClass(link, hash.options.drect == 1 ? 'orderAsc' : 'orderDesc')
		});
		
		Unit.filterMap(hash.paginLinks, function(key, link) {
			Unit.addEvent(link, 'onclick', hash.paginTable.bind(hash, key, undefined));
			if (key == hash.options.index) Unit.addClass(link, 'active')
		});
	}, 1);
	
	hash.orderTable = function(field, drect) {
		if (field == hash.options.order)
			hash.options.drect = drect === 0 || hash.options.drect == 1 ? 0 : 1;
		else {
			hash.options.order = field;
			hash.options.drect = drect == undefined ? 1 : drect;
		}
		hash.callAction();
	};
	
	hash.paginTable = function(index, limit) {
		if (index !== undefined) hash.options.index = index;
		if (limit !== undefined) hash.options.limit = limit;
		hash.callAction();
	};
	
	hash.callAction = function() {
		hash.exec(hash.action, {options:hash.options});
	};
	
	return hash;
}

function UI_Wysiwyg(id) {
	var hash = Unit(id);
	var wysy = null;
	var init = setTimeout(function() {
		var conf = hash.getByAttribute('uiWysy');
		var body = Unit(conf.content) || Unit(hash.create('DIV'));
		hash.value = conf.value;
		delete conf.content;
		delete conf.value;
		wysy = document.designMode ? body.create('IFRAME') : body.create('TEXTAREA');
		for (var i in conf) Unit.addEvent(conf[i], 'onmousedown', hash.set.bind(hash, i, null));
		Unit.onDomReady(hash.enable.bind(hash,true), wysy);
	}, 1);
	
	hash.enable = function(mode) {
		var docu = hash.getDocument();
		var body = hash.getBody();
		var wind = hash.getWindow();
		if (mode == undefined) mode = true;
		if (docu) docu.designMode = mode ? 'on' : 'off';
		if (body) body.contentEditable = mode ? true : false;
		if (body && hash.value) body.innerHTML = hash.value.value;
		if (wind) Unit.addEvent(wind, 'onblur', hash.update);
	};
	
	hash.getBody = function() {
		var docu = hash.getDocument();
		return docu.body || wysy.getElementsByTagName('body')[0]
	};
	
	hash.getDocument = function() {
		return wysy.document || wysy.contentDocument;
	};
	
	hash.getWindow = function() {
		return wysy.window || wysy.contentWindow;
	};
	
	hash.getContent = function() {
		return (hash.getBody() || wysy).innerHTML;
	};
	
	hash.setContent = function(html) {
		(hash.getBody() || wysy).innerHTML = (html || '');
	};
	
	hash.set = function(command, argument) {
		hash.getDocument().execCommand(command, false, argument);
	};
	
	hash.update = function() {
		if (hash.value) hash.value.value = hash.getContent();
	};
	
	return hash;
}

function UI_Date(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('ui');
	var node = hash.getByAttribute('uiDate');
	var days = hash.getByAttribute('uiDateDay');
	var date = new Date(((Unit(conf.target) || {}).value || Unit.time()) * 1000);
	var curM = date.getMonth();
	var curY = date.getFullYear();
	var curD = date.getDate();
	var init = setTimeout(function() {
		if (node.month)	Unit.addEvent(node.month, 'onchange', hash.update);
		if (node.year)	Unit.addEvent(node.year, 'onchange', hash.update);
		for (var i in days)  Unit.addEvent(days[i], 'onclick', hash.select.bind(hash,i));
		hash.update();
	}, 1);
	
	hash.update = function() {
		var m = node.month ? node.month.value : curM;
		var y = node.year ? node.year.value : curY;
		var d = new Date(y, m, 1);
		var c = 32 - new Date(y, m, 32).getDate();
		hash.setClassValue('firstDay', d.getDay());
		hash.setClassValue('monthDays', c);
		if (m == curM && y == curY) Unit.addClass(days[curD], 'active');
		else for (var i in days) Unit.removeClass(days[i], 'active');
	};
	
	hash.select = function(day) {
		var m = node.month ? node.month.value : curM;
		var y = node.year ? node.year.value : curY;
		var time = new Date(y, m, day).getTime() / 1000;
		if (conf.target) Unit.inner(conf.target, time);
		if (conf.action) Unit.exec(hash, conf.action, {time:time});

	};
	
	return hash;
}

function UI_Slider(id) {
	var hash = Unit(id);
	var conf = hash.getByAttribute('uiSlider');
	var step = parseInt(hash.getAttribute('uiSliderStep')) || 1;
	var time = parseInt(hash.getAttribute('uiSliderTime')) || 0;
	var init = setTimeout(function() {
		conf.links = hash.getByAttribute('uiSliderLink');
		conf.items = hash.getByAttribute('uiSliderItem');
		for (var i in conf.links) Unit.addEvent(conf.links[i], 'onclick', hash.moveScroll.bind(hash, i));
	 	if (conf.prev) Unit(conf.prev).addEvent('onclick', hash.moveScroll.bind(hash,false));
		if (conf.next) Unit(conf.next).addEvent('onclick', hash.moveScroll.bind(hash,true));
		if (Unit.keys(conf.items).length <= step) {
			Unit.trigger(conf.prev, 'hidden', true);
			Unit.trigger(conf.next, 'hidden', true);
		}
		else hash.moveScroll(0);
	}, 1);
	
	hash.moveScroll = function(to) {
		var keys = Unit.keys(conf.items);
		if (to === true) conf.index += step;
		else if (to === false) conf.index -= step;
		else conf.index = to;
		if (conf.index > keys.length - step) conf.index = 0;//keys.length - step;
		else if (conf.index <= 0) conf.index = 0;
		var item = keys[conf.index];
		if (conf.items[item] && conf.body) conf.body.style.left = -conf.items[item].offsetLeft + 'px';
		for (var i in conf.links) {
			if (item === i) Unit.addClass(conf.links[i], 'active');
			else Unit.removeClass(conf.links[i], 'active');
		}
		if (time) {
			if (conf.timeout) clearTimeout(conf.timeout);
			conf.timeout = setTimeout(hash.moveScroll.bind(hash,true), time);
		}
	};
	
	return hash;
}

function UI_Selecter(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('uiselecter');
	var node = hash.getByAttribute('uiSelecter');
	var drag = {};
	var init = setTimeout(function() {
		if (node.min) Unit.addEvent(node.min, 'onmousedown', hash.startDrag.bind(hash, node.min));
		if (node.max) Unit.addEvent(node.max, 'onmousedown', hash.startDrag.bind(hash, node.max));
		var min = conf.min, max = conf.max;
		if (node.minValue) min = Unit(node.minValue).value;
		if (node.maxValue) max = Unit(node.maxValue).value;
		drag.limit = node.bar ? node.bar.offsetWidth : 0;
		conf.all = conf.max - conf.min;
		hash.setDragPosition(min, max);
	}, 1);
	
	hash.startDrag = function(item, e) {
		drag.startX	= e.clientX - item.offsetLeft;
		drag.target = item;
		if (item == node.min) drag.min = 0, drag.max = node.max.offsetLeft;
		if (item == node.max) drag.min = node.min.offsetLeft, drag.max = drag.limit;
		Unit.addEvent(document, 'onmouseup', hash.stopDrag);
		Unit.addEvent(document, 'onmousemove', hash.processDrag);
		Unit.addClass(document.body, 'noselect');
		return Unit.cancelEvent(e);
	};
	
	hash.stopDrag = function(e) {
		Unit.removeEvent(document, 'onmouseup', hash.stopDrag);
		Unit.removeEvent(document, 'onmousemove', hash.processDrag);
		Unit.removeClass(document.body, 'noselect');
		hash.callAction();
	};
	
	hash.processDrag = function(e) {
		var l = e.clientX - drag.startX;
		if (l < drag.min) l = drag.min;
		if (l > drag.max) l = drag.max;
		var min = node.min.offsetLeft, max = node.max.offsetLeft;
		if (drag.target == node.min) min = l;
		if (drag.target == node.max) max = l;
		hash.setDragPositionPx(min, max);
	};
	
	hash.setDragPositionPx = function(min, max) {
		if (node.min) node.min.style.left = min + 'px';
		if (node.max) node.max.style.left = max + 'px';
		if (node.cur) {
			node.cur.style.left = min + 'px';
			node.cur.style.width = (max - min) + 'px';
		}
		if (node.minValue) Unit.inner(node.minValue, Math.round(conf.all / drag.limit * min));
		if (node.maxValue) Unit.inner(node.maxValue, Math.round(conf.all / drag.limit * max));
	};
	
	hash.setDragPosition = function(min, max) {
		hash.setDragPositionPx(
			Math.round(drag.limit / conf.all * min),
			Math.round(drag.limit / conf.all * max)
		);
	};
	
	hash.callAction = function() {
		var action = hash.getAttribute('uiAction');
		if (action) hash.exec(action, {});
	};
	
	return hash;
}

function UI_Trigger(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('ui');
	var init = setTimeout(function() {
		hash.addEvent('onclick', hash.collapse.bind(hash, undefined));
		var state = Unit.getCookie('trigger:' + conf.target);
		if (state !== undefined) hash.collapse(state === 'true');
	}, 1);
	
	hash.collapse = function(mode) {
		Unit.setCookie(
			'trigger:' + conf.target,
			Unit.trigger(hash, 'inactive', Unit.trigger(conf.target, 'hidden', mode))
		);
	};
	
	return hash;
}

function UI_Preview(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('ui');
	var node = hash.getByAttribute('uiPreview');
	var init = setTimeout(function() {
		for (var i in node) Unit.addEvent(node[i], 'onclick', hash.show.bind(hash,i))
	}, 1);
	
	hash.show = function(img, e) {
		if ( ! hash.root) hash.generatePopup();
		var keys = Unit.keys(hash.clip);
		if (img === true) hash.selected++;
		else if (img === false) hash.selected--;
		else hash.selected = keys.indexOf(img);
		if (hash.selected >= keys.length - 1) hash.selected = keys.length - 1;
		if (hash.selected <= 0) hash.selected = 0;
		Unit.trigger(hash.next, 'disabled', (hash.selected >= keys.length - 1));
		Unit.trigger(hash.prev, 'disabled', (hash.selected <= 0));
		img = keys[hash.selected];
		for (var i in hash.clip) {
			Unit.trigger(hash.clip[i], 'hidden', i != img);
		}
		return Unit.cancelEvent(e);
	};
	
	hash.close = function() {
		if (hash.root) {
			Unit.remove(hash.root);
			hash.root = null;
		}
	};
	
	hash.generatePopup = function() {
		var height = Math.round(Unit.pageSize().height * 0.8);
		
		hash.clip = {};
		hash.root = Unit.create(document.body, 'DIV', {className:'ui_preview'});
		hash.back = Unit.create(hash.root, 'DIV', {className:'back'});
		hash.body = Unit.create(hash.root, 'DIV', {className:'body',style:'max-height:'+height+'px'});
		hash.prev = Unit.create(hash.body, 'DIV', {className:'prev'});
		hash.next = Unit.create(hash.body, 'DIV', {className:'next'});
		hash.drop = Unit.create(hash.body, 'DIV', {className:'drop'});
		
		Unit.addEvent(hash.back, 'onclick', hash.close);
		Unit.addEvent(hash.drop, 'onclick', hash.close);
		Unit.addEvent(hash.prev, 'onclick', hash.show.bind(hash,false));
		Unit.addEvent(hash.next, 'onclick', hash.show.bind(hash,true));
		
		for (var i in node) {
			hash.clip[i] = Unit.create(hash.body, 'DIV', {
				className:	'item',
				innerHTML:	'<img src="' + i + '" style="max-height:' + height  + 'px;">'
			});
		}
	};
	
	return hash;
}