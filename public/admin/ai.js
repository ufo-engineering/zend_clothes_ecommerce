/*******************************************************************************

	GLOBAL ADMINISTRATION INTERFACE CLASS
	
*******************************************************************************/

/**
 * Administration Interface global class 
 */
var AI = {};

/**
 * List of installed administration panel modules
 */
AI.modules = {
	'config':			'Настройки сайта',
	'admin':			'Администраторы',
	'account':			'Клиенты',
	'subscribe':		'Подписчики',
	'comment':			'Отзывы',
	'order':			'Заказы',
	'product':			'Товары',
	'productSupplier':	'Поставщики товаров',
	'productType':		'Параметры товаров',
	'productImport':	'Импортирование товаров',
	'productExport':	'Экспортирование товаров',
	'page':				'Разделы сайта',
	'menu':				'Списки меню'
};

/**
 * Open target page
 */
AI.go = function() {
	document.location = AI.link.apply(AI, arguments);
};

/**
 * Generate link
 */
AI.link = function() {
	return '#' + Array.prototype.join.call(arguments, '/');
};

/**
 * Call associated request action
 */
AI.load = function(url) {
	var segment	= url.split('/');
	var module	= AI.module(segment[0]);
	var action	= 'action' + AI_Tools.name(segment[1]);
	
	if ( ! module)											return AI.message('Undefined module.');
	if ( ! module[action] && ! module[action = 'action'])	return AI.message('Undefined action.');
	if ( ! (module[action] instanceof Function))			return AI.message('Action is not a function.');
	
	AI.preloader(true);
	module[action].apply(module, segment.slice(1));
};

/**
 * Reload current page
 */
AI.reload = function() {
	AI.load(AI_Tools.hash());
}

/**
 * Get module instance by name
 */
AI.module = function(name) {
	return window['AI_' + AI_Tools.name(name)];
};

/**
 * Send api request
 */
AI.request = function(url, post, callback) {
	var timer = Unit.mtime();
	Unit.requestAjax('api/' + (url || '') + '/', post, function(data, req) {
		AI.lastRequestTime = Unit.mtime() - timer;
		if (callback instanceof Function)
			callback((data || {}).error, (data || {}).result);
	});
};

/**
 * Show/hide page preloader 
 */
AI.preloader = function(mode, element) {
	Unit.trigger(element || 'body', 'loading', mode);
};

/**
 * Set page content
 */
AI.content = function() {
	AI.preloader(false);
	Unit.inner('body', Array.prototype.join.call(arguments, ''));
	Unit.reactivateAllElements(Unit('body'));
};

/**
 * Show popup
 */
AI.popup = function(name, data, scope) {
	(data || (data = {})).id = name;
	Unit.reactivateAllElements(Unit(document.body).create('DIV', {
		id:			name,
		className:	'overlay',
		innerHTML:	AI.template(name, data, scope)
	}));
};

/**
 * Close popup
 */
AI.close = function(id) {
	Unit.remove(id);
};

/**
 * Generate template with passed data
 */
AI.template = function(template, data, scope) {
	if ( ! AI_Templates[template]) return 'Undefined template.';
	return Unit.templater(Unit.hereDoc(AI_Templates[template]), data, scope);
	//return AI_Templates[template].bind(scope)(data);
};

/**
 * Show message
 */
AI.message = function(text) {
	AI.content(
		AI.template('message', {text:text})
	);
};

/**
 * Administration panel initialization
 */
Unit.onDomReady(function() {
	var style = Unit.getCookie('interfaceStyle');
	if (style) Unit('style').href = '/public/admin/' + style;
	
	AI_Admin.status();
	Unit.addEvent(window, 'hashchange', function(event) {
		AI.load(AI_Tools.hash());
	});
});

window.onerror = function(msg, url, line, col, error) {
   // Note that col & error are new to the HTML 5 spec and may not be 
   // supported in every browser.  It worked for me in Chrome.
   var extra = !col ? '' : '\ncolumn: ' + col;
   extra += !error ? '' : '\nerror: ' + error;

   // You can view the information in an alert to see things working like this:
   alert("Error: " + msg + "\nurl: " + url + "\nline: " + line + extra);

   // TODO: Report this error via ajax so you can keep track
   //       of what pages have JS issues

   var suppressErrorAlert = true;
   // If you return true, then error alerts (like in older versions of 
   // Internet Explorer) will be suppressed.
   return suppressErrorAlert;
};

/*******************************************************************************

	ADMINISTRATION INTERFACE HELPERS

*******************************************************************************/

var AI_Tools = {
	
	/**
	 * Get current hash address
	 */
	hash: function() {
		return Unit.replace(window.location.hash, {'#':''});
	},
	
	/**
	 * Upper case first symbol
	 */
	name: function(name) {
		name = String(name || '');
		return (name[0] || '').toUpperCase() + name.slice(1);
	},
	
	discount: function(price, discount) {
		if (parseInt(discount)  > 0) {
			return discount.indexOf('%') > 0?
				price - (price / 100 * parseInt(discount)):
				price - discount;
		}
		return price;
	},
	
	/**
	 * Join several objects to one new
	 */
	join: function() {
		var blank = {};
		for (var i in arguments)
			for (var j in arguments[i])
				blank[j] = arguments[i][j];
		return blank;
	},
	
	/**
	 * Generate readable russian date string
	 */
	date: function(time) {
		var date = new Date(time * 1000);
		var dubl = function(n) { return n < 10 ? '0' + n : n; };
		var mons = 'Янв Фев Марта Апр Мая Июня Июля Авг Сен Окт Ноя Дек'.split(' ');
		return date.getDate() + ' ' + mons[date.getMonth()] + ', ' +
			dubl(date.getHours()) + ':' + dubl(date.getMinutes());
	},
	
	middleDate: function(time) {
		var date = new Date(time * 1000);
		var mons = 'Января Февраля Марта Апреля Мая Июня Июля Августа Сентября Октября Ноября Декабря'.split(' ');
		return date.getDate() + ' ' + mons[date.getMonth()];
	},
	
	bigDate: function(time) {
		var date = new Date(time * 1000);
		var mons = 'Января Февраля Марта Апреля Мая Июня Июля Августа Сентября Октября Ноября Декабря'.split(' ');
		return date.getDate() + ' ' + mons[date.getMonth()] + ' ' + date.getFullYear();
	},
	
	biggerDate: function(time) {
		var date = new Date(time * 1000);
		var mons = 'Янв Фев Мар Апр Май Июн Июл Авг Сен Окт Ноя Дек'.split(' ');
		return mons[date.getMonth()] + ' ' + date.getFullYear();
	},
	
	fullDate: function(time) {
		var date = new Date(time * 1000);
		var dubl = function(n) { return n < 10 ? '0' + n : n; };
		var mons = 'Января Февраля Марта Апреля Мая Июня Июля Августа Сентября Октября Ноября Декабря'.split(' ');
		return date.getDate() + ' ' + mons[date.getMonth()] + ' ' + date.getFullYear() + ', ' +
			dubl(date.getHours()) + ':' + dubl(date.getMinutes());
	},
	
	/**
	 * Generate template function
	 */
	template: function(html) {
		var func = null;
		return function() {
			if ( ! func) func = new Function('hash', Unit.generate(Unit.hereDoc(html)));
			return func.apply(this, arguments);
		};
	}

};

/*******************************************************************************

	BASIC MODULE

*******************************************************************************/

var AI_Module = {
	
	ACCESS: {
		DENY: 0,
		LIST: 1,
		EDIT: 2,
		DROP: 3
	},
	
	access: {
		0: 'Доступ запрещён',
		1: 'Просмотр списка записей',
		2: 'Добавление и редактирование',
		3: 'Полный доступ'
	},
	
	messages: {
		SUCCESS:			'Форма успешно сохранена.',
		EMPTY_FIELDS:		'Заполнены не все обязательные поля, пожалуйста, проверьте форму и заполните выделенные поля.',
		FORM_ERROR:			'Форма заполнена с ошибками. Пожалуйста, проверьте форму и заполните выделенные поля.',
		FORM_REQUIRED:		'<span>*</span> - Обязательные для заполнения поля.',
		
		NO_ACCESS:			'<b>Невозможно выполнить действие</b><br>'+
							'У вас недостаточно прав доступа для выполнения данного действия.<br>'+
							'Для получения прав доступа свяжитесь с администратором.',
		
		STATISTIC:			'Количество записей в таблице: <b>{$total}</b><br>'+
							'<if (total != found)>Количество записей соответствующих фильтру: <b>{$found}</b><br><endif>'+
							'Время выполнения запроса: <b>{$parseFloat(time).toFixed(5)} сек.</b><br>'+
							'Время ожидания сервера: <b>{$parseFloat(AI.lastRequestTime/1000).toFixed(5)} сек.</b><br>',
		
		READY_LIST:			'На странице расположен список записей, который может быть отфильтрован и отсортирован.<br>'+
							'Для добавления новой записи нажмите кнопку "добавить запись" в правом углу страницы.<br>'+
							'Для просмотра и редактирования существующих записей, нажмите кнопку "редактировать" у необходимой записи.<br>'+
							'Для удаления записей, нажмите кнопку "удалить" у ненужной записи.',
		
		EMPTY_LIST:			'В списке нет записей, для добавления новой записи, нажмите кнопку "добавить запись".',
		
		DROP_CONFIRM:		'Вы действительно хотите удалить запись ID{$id} из базы данных?'
	},
	
	actions: {
		list: {
			access:		1,
			request:	"this.requestList(form)",//{list:this.getCommand('findAll',{filters:Unit.parse(form)})}
			template:	'List'
		},
		
		edit: {
			access:		2,
			request:	"this.requestEdit(form)",//{form:this.getCommand('get',{value:form})}
			template:	'Edit'
		},
		
		drop: {
			access:		3,
			confirm:	"confirm(this.getMessage('DROP_CONFIRM',{id:form}))",
			request:	"{form:this.getCommand('drop',{value:form})}",
			action:		"AI.reload()",
			clear:		true
		}
	},
	
	controls: {
		edit: {
			command:	'save',
			request:	"{form:Unit.normalize(form)}",
			action:		"AI.go(this.name,'edit',form)",
			clear:		true
		}
	},
	
	checkAccess: function(action) {
		return AI.adminInfo && (AI.adminInfo['permissions_' + (this.parent || this.name)] || 0) >= action;
	},
	
	isLocked: function(id) {
		for (var i in this.locked)
			if (this.locked[i] == id) return true;
		return false;
	},
	
	callMethod: function(command, data, callback) {
		AI.preloader(true);
		AI.request(this.name + '/' + command, data, function(err, result) {
			AI.preloader(false);
			if (callback instanceof Function) callback(err, result);
		});
	},
	
	getCommand: function(method, data) {
		return {
			module:		this.name,
			method:		method,
			arguments:	data || {}
		};
	},
	
	getTemplate: function(name, data) {
		return AI.template(this.name + name, data, this);
	},
	
	getMessage: function(name, data) {
		return this.messages[name]?
			Unit.templater(this.messages[name], data || {}, this):
			false;
	},
	
	getLink: function() {
		return '#' + this.name + '/' + Array.prototype.join.call(arguments, '/');
	},
	
	action: function(action, form) {
		if ( ! action || action == '') action = this.defaultAction || 'list';
		if ( ! (action = this.actions[action])) return alert('Undefined action.');
		if ( ! this.checkAccess(action.access)) return alert('You don\'t have access.');
		if (action.confirm && ! Unit.exec(this, 'return ' + action.confirm, {form:form})) return false;
		
		if (action.request) {
			var self = this;
			var data = typeof(action.request) == 'string'?
				Unit.exec(this, 'return ' + action.request, {form:form}):
				action.request;
			
			if (action.loader) AI.preloader(true, action.loader);
			AI.request(null, data, function(error, result) {
				action.result = result;
				if (action.loader)			AI.preloader(false, action.loader);
				if (action.template)		AI.content(self.getTemplate(action.template, result));
				if (action.popup)			AI.popup(self.name + action.popup, result, self);
				if (action.action)			Unit.exec(self, action.action, result);
				if (action.clear && result)	AI.request('cache/clear');
			});
		}
		
		else if (action.action) {
			var result = Unit.exec(this, 'return ' + action.action, {form:form});
			if (action.template)	AI.content(this.getTemplate(action.template, result));
			if (action.popup)		AI.popup(this.name + action.popup, result, this);
		}
		
		else if (action.popup)		AI.popup(this.name + action.popup, {}, this);
	},
	
	control: function(control, form, callback) {
		if ( ! control) control = this.defaultControl || 'edit';
		
		if (control.request) {
			var self = this;
			var data = Unit.exec(this, 'return ' + control.request, {form:form});
			
			if (control.loader) AI.preloader(true, control.loader);
			self.callMethod(control.command, data, function(error, result) {
				if (error) error.message = self.getMessage(error.message) || error.message;
				else error = {message:self.getMessage('SUCCESS')};
				
				if (control.loader) AI.preloader(false, control.loader);
				if (callback instanceof Function)	callback(error, result);
				if (result && control.action)		Unit.exec(self, control.action, {form:result});
				if (control.clear && result)		AI.request('cache/clear');
				if (error.fields) {}
			});
		}
		
		else if (control.action) {
			Unit.exec(this, control.action, {form:form});
		}
	},
	
	requestList: function(form) {
		this.lastRequestFilters = form || '';
		return {
			list: this.getCommand('findAll', {filters:Unit.normalize(Unit.parse(form))})
		};
	},
	
	requestEdit: function(form) {
		return {
			form: this.getCommand('get', {value: form})
		};
	},
	
	controlEdit: function(form, level, callback) {
		if (level == 3) this.control(this.controls.edit, form, callback);
	},
	
	controlFilter: function(form, level, callback) {
		if (level != 3) return;
		for (var i in form) if (form[i] === '') delete form[i];
		AI.go(this.name, 'list', Unit.stringify(form));
	}
	
};

/*******************************************************************************

	UPLOADER

*******************************************************************************/

AI_Uploader = function(id, path, input, callback) {
	if ( ! input || ! input.files) return;
	
	var uploading = 0, complete = 0;
	
	var upload = function(file) {
		var xhr = new XMLHttpRequest();
		var fd = new FormData();
		fd.append("id", id);
		fd.append("file", file);
		
		xhr.addEventListener("error", console.log, false);
		xhr.addEventListener("abort", console.log, false);
		xhr.upload.addEventListener("progress", function(e) {
			if (e.lengthComputable) {
				//console.log(100 / e.total * e.loaded);
			}
		}, false);
		
		xhr.addEventListener("load", function(e) {
			if (++complete >= uploading) AI.preloader(false);
			if (e.target.responseText) {
				if (callback instanceof Function) {
					var parsed = Unit.parseJson(e.target.responseText) || {};
					callback(parsed.error, parsed.result);
				}
			}
		}, false);
		
		xhr.open("POST", path);
		xhr.send(fd);
		AI.preloader(true);
		uploading++
	};
	
	for (var i = 0; i < input.files.length; i++)
		upload(input.files[i]);
};

UI_Color = function(id) {
	var hash = Unit(id);
	var conf = hash.getByAttribute('uiColor');
	var init = setTimeout(function() {
		if (jscolor) jscolor.init();
	}, 1);
	
	return hash;
};

UI_Drag = function(id) {
	var hash = Unit(id);
	var node = {};
	var drag = {};
	var init = setTimeout(function() {
		node = Unit.filterMap(hash.getByAttribute('uiDrag', true), function(k, v) {
			var conf = Unit(v).getAllAttributes('ui');
			if (this[conf.drag] == undefined) this[conf.drag] = [];
			var index = this[conf.drag].length;
			var item = this[conf.drag][index] = {
				index:	index,
				id:		conf.dragid,
				group:	conf.drag,
				object:	v,
			};
			Unit.addEvent(v, 'onmousedown', hash.startDrag.bind(hash, item));
		});
	}, 1);
	
	hash.startDrag = function(item, e) {
		drag.group	= item.group;
		drag.target	= item.object;
		drag.index	= item.index;
		drag.startY	= e.clientY;
		drag.change = false;
		Unit.addEvent(document, 'onmouseup', hash.stopDrag);
		Unit.addEvent(document, 'onmousemove', hash.processDrag);
		Unit.addClass(document.body, 'noselect');
		Unit.addClass(document.body, 'dragging');
		return Unit.cancelEvent(e);
	};
	
	hash.stopDrag = function(e) {
		Unit.removeEvent(document, 'onmouseup', hash.stopDrag);
		Unit.removeEvent(document, 'onmousemove', hash.processDrag);
		Unit.removeClass(document.body, 'noselect');
		Unit.removeClass(document.body, 'dragging');
		if (drag.change) hash.callAction();
	};
	
	hash.processDrag = function(e) {
		var parent = drag.target.parentNode;
		var current = node[drag.group][drag.index];
		var previous = node[drag.group][drag.index - 1];
		var next = node[drag.group][drag.index + 1];
		var after = node[drag.group][drag.index + 2];
		
		//console.log(drag.target.offsetTop);
		
		var pArea = previous ? previous.object.offsetHeight : drag.target.offsetHeight/2;
		var nArea = next ? next.object.offsetHeight : drag.target.offsetHeight/2;
		
		if ((drag.startY - e.clientY) > pArea) {
			if (parent && previous && previous.object) {
				node[drag.group][drag.index] = previous;
				node[drag.group][drag.index - 1] = current;
				drag.index--, current.index--, previous.index++;
				drag.startY -= previous.object.offsetHeight, drag.change = true;
				parent.removeChild(drag.target);
				parent.insertBefore(drag.target, previous.object);
			}
		}
		else if ((drag.startY - e.clientY) < (-drag.target.offsetHeight/2)) {
			if (parent && next && next.object) {
				node[drag.group][drag.index] = next;
				node[drag.group][drag.index + 1] = current;
				drag.index++, current.index++, next.index--;
				drag.startY += next.object.offsetHeight, drag.change = true;
				parent.removeChild(drag.target);
				if (after) parent.insertBefore(drag.target, after.object);
				else parent.appendChild(drag.target);
			}
		}
	};
	
	hash.callAction = function() {
		var action = hash.getAttribute('uiAction');
		var list = Unit.filterMap(node[drag.group], function(k, v) {
			this[v.id] = v.index;
		});
		if (action) hash.exec(action, {list:list});
		return list;
	};
	
	return hash;
};

UI_Selector = function(id) {
	var hash = Unit(id);
	var list = hash.getByAttribute('uiSelector');
	var init = setTimeout(function() {
		for (var i in list)
			Unit.addEvent(list[i], 'onclick', hash.check.bind(hash,i));
	}, 1);
	
	hash.check = function(key, value) {
		if (key == 'all') {
			if (value !== true && value !== false) value = list[key].checked;
			for (var i in list) list[i].checked = value;
		}
		else {
			var checked = true;
			for (var i in list)
				if (i != 'all' && ! list[i].checked) { checked = false; break; }
			if (list.all) list.all.checked = checked;
		}
	};
	
	hash.getSelected = function() {
		return Unit.filterMap(list, function(k, v) {
			if (k !== 'all' && v.checked) this.push(k);
		});
	};
	
	return hash;
};

UI_NicWysy = function(id) {
	var hash = Unit(id);
	new nicEditor({fullPanel : true}).panelInstance(hash.id);
	var body = hash.previousSibling;
	var menu = body.previousSibling;
	if (body) body.style.width = "100%";
	if (menu) menu.style.width = "100%";
	//nicEditors.findEditor(hash.id);
	//nicEditors.allTextAreas();
	return hash;
};

UI_Statistic = function(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('ui');
	var ctxt = hash.getContext("2d");
	var info = Unit.normalize(Unit.parse(conf.data || ''));
	var dubl = function(n) { return n < 10 ? '0' + n : n; };
	var time = 0, step = 0, peri = conf.period;
	var data = {labels:[],datasets:[
		{
				label: "Hits",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : []
			},
			{
				label: "Host",
				fillColor : "rgba(151,187,205,0.2)",
				strokeColor : "rgba(151,187,205,1)",
				pointColor : "rgba(151,187,205,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(151,187,205,1)",
				data : []
			}
	]};
	
	switch (peri)
	{
		case '1':	time = 3600, step = 23; break;		// 1 day = 24 / 6 = 4h
		case '7':	time = 86400, step = 6; break;		// 7 days = 7 * 24 / 7 = 24h
		case '30':	time = 259200, step = 10; break;		// 30 days = 30 * 24 / 6 = 120h
		case '365':	time = 2628000, step = 11; break;		// 365 days = 365 * 24 / 6 = 1460h
		default:	time = 3600, step = 23;
	}
	
	var month = [];
	
	var max = Math.floor((new Date()).getTime() / 1000 / time);
	var min = max - step;
	
	info = Unit.assoc(info, 'date');
	
	for (var i = min; i <= max; i++) { 
		var item = info[i] || {};
		var date = new Date(i * time * 1000);
		//data.labels.push(item.day + '.' + item.month + '.' + (2000+parseInt(item.year)));
		switch (peri)
		{
			case '365':	data.labels.push(AI_Tools.biggerDate(i * time)); break;
			case '30': 	data.labels.push(AI_Tools.middleDate(i * time)); break;
			case '7': 	data.labels.push(AI_Tools.middleDate(i * time)); break;
			default:	data.labels.push(dubl(date.getHours()) + ':' + dubl(date.getMinutes())); 
		}
		data.datasets[0].data.push(item.hits || 0);
		data.datasets[1].data.push(item.host || 0);
	}
	
	//console.log(info);
	
	hash.chart = new Chart(ctxt).Line(data, {
		responsive: true
	});
	
	return hash;
};