/*******************************************************************************

	CONFIGURATION MODULE

*******************************************************************************/

var AI_Config = Unit.append({

	name:			'config',
	module:			'AI_Config',
	
	ACCESS: {
		DENY: 0,
		LIST: 1,
		EDIT: 2
	},
	
	access: {
		0: 'Доступ запрещён',
		1: 'Просмотр настроек',
		2: 'Изменение настроек'
	},
	
	messages: 		{
		TITLE:		'Настройки сайта',
		MESSAGE:	'Внесите необходимые изменения в настройки сайта и нажмите "сохранить".'
	},
	
	controls: {
		edit: {
			command:	'setAll',
			request:	"{form:Unit.normalize(form)}",
			clear:		true
			//action:	"AI.go(this.name,'edit',form)"
		}
	},
	
	getGroups: function() {
		return {
			currency: {
				title:	'Котировки валют сайта',
				fields:	{
					currencyUsd:		'Стоимость доллара относительно гривны',
					currencyRub:		'Стоимость рубля относительно гривны'
				}
			},
			
			index: {
				title:	'Стандартные элементы страницы',
				fields:	{
					indexPhoneFirst:	'Контактный номер телефона 1 (project.com.ua)',
					indexPhoneSecond:	'Контактный номер телефона 2 (project.com.ua)',
					indexRuPhoneFirst:	'Контактный номер телефона 1 (project.ru)',
					indexRuPhoneSecond:	'Контактный номер телефона 2 (project.ru)',
					indexWwPhoneFirst:	'Контактный номер телефона 1 (project.co.uk)',
					indexWwPhoneSecond:	'Контактный номер телефона 2 (project.co.uk)',
					indexSearchLabel:	'Подсказка в строке поиска',
					indexMail:			'Почтовый адресс',
					indexCopy:			'Копирайт сайта'
				}
			},
			
			social: {
				title:	'Социальные ссылки',
				fields:	{
					socialFb:			'Ссылка на страницу в Facebook',
					socialVk:			'Ссылка на страницу в Контакте'
				}
			},
			
			product: {
				title:	'Сообщения вкладок на товарной странице',
				fields:	{
					productShipping:	{title:'Доставка',type:'textarea'},
					productPayment:		{title:'Оплата',type:'textarea'},
					productQuestions:	{title:'Вопросы',type:'textarea'}
				}
			}
		};
	},
	
	requestList: function(id) {
		var data		= {};
		data.config 	= this.getCommand('getAll');
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	DASHBOARD MODULE

*******************************************************************************/

var AI_Dashboard = Unit.append({

	name:			'dashboard',
	module:			'AI_Dashboard',
	
	messages: 		{
		//
	},
	
	actions: {
		list: {
			access:		0,
			request:	"this.requestList(form)",
			template:	'List'
		}
	},
	
	controls: {
		//
	},
	
	requestList: function(id) {
		var data			= {};
		data.order	 		= AI_Order.getCommand('findAll',{filters:{order:'added',drect:0,where:{'status in':'0,1,2,3'}}});
		data.prodComment	= {module:'productComment', method:'findAll', arguments:{filters:{order:'added',drect:0,where:{active:'2'}}}};
		data.siteComment 	= {module:'comment', method:'findAll', arguments:{filters:{order:'added',drect:0,where:{active:'2'}}}};
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	STATISTIC MODULE

*******************************************************************************/

var AI_Statistic = Unit.append({

	name:			'statistic',
	module:			'AI_Statistic',
	
	actions: {
		list: {
			access:		0,
			request:	"this.requestList(form)",
			template:	'List'
		}
	},
	
	requestList: function(id) {
		AI_Statistic.info = Unit.parse(id);
		var data		= {};
		data.statAll 	= this.getCommand('findAll', AI_Statistic.info);
		//data.statRu 	= this.getCommand('findAll', {domain:'1'});
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	ADMINISTRATOR MODULE

*******************************************************************************/

var AI_Admin = Unit.append({

	name:			'admin',
	module:			'AI_Admin',
	defaultPage:	'dashboard',
	
	messages: AI_Tools.join(AI_Module.messages, {
		UNDEFINED_LOGIN:	'Неверный логин. Если у вас нет логина, свяжитесь с администратором для получения учётной записи.',
		UNDEFINED_PASSWORD:	'Неверный пароль. Если вы забыли пароль, попробуйсте снова или свяжитесь с администратором.',
		DISABLED_ACCESS:	'Ваша учётная запись не активна. Для активации учётной записи свяжитесь с администратором.',
		
		TITLE:				'Список менеджеров и администраторов'
	}),
	
	controls: AI_Tools.join(AI_Module.controls, {
		login: {
			command:	'login',
			request:	'form',
			loader:		'popupLogin',
			action:		"this.activate(form);AI.load(AI_Tools.hash() || this.defaultPage)"
		}
	}),
	
	locked: [1],
	
	controlLogin: function(form, level, callback) {
		if (level == 3) this.control(this.controls.login, form, callback);
	},
	
	activate: function(data) {
		AI.adminInfo = data;
		Unit.inner('head', this.getTemplate('Navigation', data));
		data ? AI.close('popupLogin') : AI.popup('popupLogin', null, this);
	},
	
	status: function(callback) {
		var self = this;
		self.callMethod('status', null, function(error, result) {
			self.activate(result);
			if (callback instanceof Function) callback(error, result);
			if (result) AI.load(AI_Tools.hash() || self.defaultPage);
		});
	},

	login: function(data, callback) {
		var self = this;
		self.callMethod('login', data, function(error, result) {
			if (result) self.activate(result);
			if (callback instanceof Function) callback(error, result);
			if (result) AI.load(AI_Tools.hash() || self.defaultPage);
		});
	},
	
	logout: function() {
		var self = this;
		self.callMethod('logout', null, function(error, result) {
			self.activate();
			AI.content('');
		});
	}
	
}, AI_Module);

/*******************************************************************************

	ACCOUNTS MODULE

*******************************************************************************/

var AI_Account = Unit.append({

	name:			'account',
	module:			'AI_Account',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Список учётных записей клиентов'
	}),
	
	requestEdit: function(id) {
		var data		= AI_Module.requestEdit.call(this, id);
		data.orders 	= AI_Order.getCommand('find',{filters:{where:{accountId:id}}})
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	SUBSCRIBE MODULE

*******************************************************************************/

var AI_Subscribe = Unit.append({

	name:			'subscribe',
	module:			'AI_Subscribe',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Список подписчиков на рассылку'
	})
	
}, AI_Module);

/*******************************************************************************

	COMMENT MODULE

*******************************************************************************/

var AI_Comment = Unit.append({

	name:			'comment',
	module:			'AI_Comment',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Список отзывов о сайте'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		confirmComment: {
			access:		2,
			request:	"{comment:{module:'comment',method:'confirm',arguments:form}}",
			clear:		true
		}
	}),
	
	requestList: function(id) {
		if ( ! id) id = 'order=added&drect=0';
		return AI_Module.requestList.call(this, id);
	}
	
}, AI_Module);

/*******************************************************************************

	PAGES MODULE

*******************************************************************************/

var AI_Page = Unit.append({

	name:			'page',
	module:			'AI_Page',
	
	locked: [1,2,3,4,5,6,7,8,9,19,20,21],
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Cписок разделов сайта'
	})
	
}, AI_Module);

/*******************************************************************************

	MENUS MODULE

*******************************************************************************/

var AI_Menu = Unit.append({

	name:			'menu',
	module:			'AI_Menu',
	
	actions: AI_Tools.join(AI_Module.actions, {
		edit: {
			access:		2,
			action:		"{form:form}",
			popup:		'Edit'
		}
	}),
	
	controls: {
		edit: {
			command:	'save',
			loader:		'recordEdit',
			request:	"{form:Unit.normalize(form)}",
			action:		"AI.reload()",
			clear:		true
		}
	},
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Cписки элементов меню сайта'
	}),
	
	getGroupedMenus: function(records) {
		return Unit.filterMap(records, function(k,v) {
			(this[v.parentId] || (this[v.parentId] = [])).push(v); 
		});
	},
	
	getMenuInfo: function(id) {
		var list = (this.actions.list.result || {}).list || [];
		for (var i in list) if (list[i].id == id) return list[i];
		return null;
	},
	
	requestList: function(id) {
		return {
			list: this.getCommand('find')
		};
	}
	
}, AI_Module);

/*******************************************************************************

	BANNERS MODULE

*******************************************************************************/

var AI_Banner = Unit.append({

	name:			'banner',
	module:			'AI_Banner',
	
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Cписок баннеров'
	}),
	
	getGroupedBanners: function(records) {
		return Unit.filterMap(records, function(k,v) {
			(this[v.type] || (this[v.type] = [])).push(v); 
		});
	},
	
	uploadedFile: function(error, result) {
		//console.log(error);
		//console.log(result);
		//AI.reload();
		Unit(AI_Banner.module + 'Image').inner((result || {}).file || '');
	}
	
}, AI_Module);

/*******************************************************************************

	ORDERS MODULE

*******************************************************************************/

var AI_Order = Unit.append({

	name:			'order',
	module:			'AI_Order',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:				'Список заказов',
		EMPTY_LIST:			'В списке нет заказов, новые заказы будут добавлены в список после оформления на сайте.'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		productStatus: {
			access:		2,
			loader:		'orderOverallContent',
			request:	"{form:this.getCommand('setProductStatus',form)}"
		},
		productAmount: {
			access:		2,
			loader:		'orderOverallContent',
			request:	"{form:this.getCommand('setProductAmount',form)}",
			action:		"this.updateOverall(form)"
		},
		dropProduct: {
			access:		3,
			loader:		'orderOverallContent',
			request:	"{form:this.getCommand('dropProduct',form)}",
			action:		"this.updateOverall(form)"
		}
	}),
	
	updateOverall: function(form) {
		//console.log(form);
		AI.preloader(false, 'orderOverall');
		Unit.inner('orderOverall', this.getTemplate('EditOverall', {form:form}));
	},
	
	getCountries: function() {
		return {
			0: 'Не указано',
			1: 'Украина',
			2: 'Россия',
			3: 'Белоруссия',
			4: 'Казахстан',
			5: 'Англия',
			99: 'Другая страна'
		};
		
	},
	
	getPaymentTypes: function() {
		return {
			0: 'Не указано',
			1: 'MoneyGram',
			2: 'Contact',
			3: 'UniStream',
			4: 'Колибри',
			5: 'Золотая Корона',
			6: 'Приват24'
		};
	},
	
	getDeliveryTypes: function() {
		return {
			0: 'Не указано',
			1: 'Новая Почта',
			2: 'Укр Почта',
			3: 'EMC',
			4: 'DIMEX',
			5: 'Helios Express',
			6: 'Доставка поездом',
			7: 'Экспресс доставка',
			8: 'Курьер Сервис Экспресс'
		};
	},
	
	getExpressTypes: function() {
		return {
			0: 'Не указано',
			1: 'ПЭК',
			2: 'ЖелДорЭкспедиция',
			3: 'Деловые Линии',
			4: 'Байкал-Сервис',
			5: 'КурьерСервисЭкспресс',
			6: 'Почта России'
		};
	},
	
	getCurrencys: function() {
		return {
			1: 'Гривна',
			2: 'Рубль',
			3: 'Доллар'
		};
	},
	
	getProductStatuses: function() {
		return {
			0: 'Ожидает',
			1: 'Принят',
			5: 'Отменён',
			6: 'Отклонен'
		};
	},
	
	getOrderStatuses: function() {
		return {
			0: 'Не подтверждён',
			1: 'Обрабатывается',
			2: 'Комплектуется',
			3: 'Доставляется',
			4: 'Доставлен',
			5: 'Отменен',
			6: 'Отклонен'
		};
	},
	
	requestList: function(id) {
		if ( ! id) id = 'order=added&drect=0';
		return AI_Module.requestList.call(this, id);
	}
	
}, AI_Module);

/*******************************************************************************

	CATEGORY MODULE

*******************************************************************************/

var AI_ProductCategory = Unit.append({

	name:			'productCategory',
	module:			'AI_ProductCategory',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список категорий товаров'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		list: {
			access:		1,
			request:	"this.requestList({order:'position'})",//{list:this.getCommand('findAll',{filters:Unit.parse(form)})}
			template:	'List'
		},
		setPosition: {
			access:		2,
			//loader:		'body',
			request:	"{form:this.getCommand('setPosition',{form:form})}",
			clear:		true/*,
			action:		'AI.reload()'*/
		}
	}),
	
	getGroupedCategories: function(records) {
		return Unit.filterMap(records, function(k,v) {
			(this[v.parentId] || (this[v.parentId] = [])).push(v); 
		});
	},
	
	getCategoriesOptions: function(records, excluded, result, parent, prefix) {
		if ( ! result)	result = {};
		if ( ! parent)	parent = 0;
		if ( ! prefix)	prefix = ' ';
		
		var category	= records[parent] || []; 
		
		for (var i in category) {
			if (i == excluded) continue;
			result[' ' + i] = prefix + (category[i].titleRu || category[i].title);
			arguments.callee(records, excluded, result, i, '---' + prefix);
		}
		
		return result;
	},
	
	requestEdit: function(id) {
		var data		= AI_Module.requestEdit.call(this, id);
		data.categories = this.getCommand('getGrouped')
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	SUPPLIER MODULE

*******************************************************************************/

var AI_ProductSupplier = Unit.append({

	name:			'productSupplier',
	module:			'AI_ProductSupplier',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список поставщиков товаров'
	}),
	
	getTypesOptions: function(records, result) {
		if ( ! result)	result = {};
		
		for (var i in records) {
			result[records[i].id] = records[i].title;
		}
		
		return result;
	}
	
}, AI_Module);

/*******************************************************************************

	PRODUCT TYPES MODULE

*******************************************************************************/

var AI_ProductType = Unit.append({

	name:			'productType',
	module:			'AI_ProductType',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список типов товаров'
	}),
	
	getTypesOptions: function(records, result) {
		if ( ! result)	result = {};
		
		for (var i in records) {
			result[records[i].id] = records[i].title;
		}
		
		return result;
	},
	
	requestEdit: function(id) {
		var data		= AI_Module.requestEdit.call(this, id);
		data.groups		= AI_ProductParam.getCommand('getGroups');
		return data;
	}
	
}, AI_Module);

/*******************************************************************************

	PRODUCT PARAM MODULE

*******************************************************************************/

var AI_ProductParam = Unit.append({

	name:			'productParam',
	module:			'AI_ProductParam',
	parent:			'productType',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Параметры и группы параметров товаров'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		editGroup: {
			access:		2,
			action:		"{form:this.getGroup(form)}",
			popup:		'GroupEdit',
			clear:		true
		},
		dropGroup: {
			access:		3,
			confirm:	"confirm(this.getMessage('DROP_CONFIRM',{id:form}))",
			request:	"{form:this.getCommand('dropGroup',{id:form})}",
			action:		"AI.reload()",
			clear:		true
		},
		editKey: {
			access:		2,
			action:		"{form:this.getKey(form)}",
			popup:		'KeyEdit',
			clear:		true
		},
		dropKey: {
			access:		3,
			confirm:	"confirm(this.getMessage('DROP_CONFIRM',{id:form}))",
			request:	"{form:this.getCommand('dropKey',{id:form})}",
			action:		"AI.reload()",
			clear:		true
		},
		editValue: {
			access:		2,
			action:		"{form:this.getValue(form)}",
			popup:		'ValueEdit',
			clear:		true
		},
		dropValue: {
			access:		3,
			confirm:	"confirm(this.getMessage('DROP_CONFIRM',{id:form}))",
			request:	"{form:this.getCommand('dropValue',{id:form})}",
			action:		"AI.reload()",
			clear:		true
		}
	}),
	
	requestList: function(id) {
		return {
			groups:	this.getCommand('getGroups'),
			keys:	this.getCommand('getKeys'),
			values:	this.getCommand('getValues')
		};
	},
	
	getGroup: function(form) {
		var result = this.actions.list.result || {};

		if (result.groups) {
			var list = result.groups;
			for (var i in list) if (list[i].id == form.groupId) return list[i];
		}
		
		return form;
	},
	
	getKey: function(form) {
		var result = this.actions.list.result || {};

		if (result.keys && result.keys[form.groupId]) {
			var list = result.keys[form.groupId];
			for (var i in list) if (list[i].id == form.keyId) return list[i];
		}
		
		return form;
	},
	
	getValue: function(form) {
		var result = this.actions.list.result || {};

		if (result.values && result.values[form.groupId] && result.values[form.groupId][form.keyId]) {
			var list = result.values[form.groupId][form.keyId];
			for (var i in list) if (list[i].id == form.valueId) return list[i];
		}
		
		return form;
	},
	
	groupControl: function(form, level, callback) {
		var self = this;

		if (level != 3) return;
		
		AI.preloader(true, self.name + 'GroupEdit');
		self.callMethod('saveGroup', form, function(error, result) {
			AI.preloader(false, self.name + 'GroupEdit');
			if (error && error.message) error.message = self.getMessage(error.message) || error.message;
			else if ( ! error) Unit.remove('productParamGroupEdit');//error = {message:self.getMessage('SUCCESS')};
			if (callback instanceof Function) callback(error, result);
			AI.reload();
		});
	},
	
	keyControl: function(form, level, callback) {
		var self = this;

		if (level != 3) return;
		
		AI.preloader(true, self.name + 'KeyEdit');
		self.callMethod('saveKey', form, function(error, result) {
			AI.preloader(false, self.name + 'KeyEdit');
			if (error && error.message) error.message = self.getMessage(error.message) || error.message;
			else if ( ! error) Unit.remove('productParamKeyEdit');//error = {message:self.getMessage('SUCCESS')};
			if (callback instanceof Function) callback(error, result);
			AI.reload();
		});
	},
	
	valueControl: function(form, level, callback) {
		var self = this;

		if (level != 3) return;
		
		AI.preloader(true, self.name + 'ValueEdit');
		self.callMethod('saveValue', form, function(error, result) {
			AI.preloader(false, self.name + 'ValueEdit');
			if (error && error.message) error.message = self.getMessage(error.message) || error.message;
			else if ( ! error) Unit.remove('productParamValueEdit');//error = {message:self.getMessage('SUCCESS')};
			if (callback instanceof Function) callback(error, result);
			AI.reload();
		});
	},
	
	getGroupedKeys: function(keys) {
		var result = {};
		
		for (var i in keys) {
			if ( ! result[keys[i].groupId]) result[keys[i].groupId] = [];
			result[keys[i].groupId].push(keys[i]);
		}
		
		return result;
	},
	
	getGroupedValues: function(values) {
		var result = {};
		
		for (var i in values) {
			if ( ! result[values[i].groupId]) result[values[i].groupId] = {};
			if ( ! result[values[i].groupId][values[i].keyId])
				result[values[i].groupId][values[i].keyId] = [];
			result[values[i].groupId][values[i].keyId].push(values[i]);
		}
		
		return result;
	},
	
	getValuesOptions: function(values, result) {
		if ( ! result) result = {};
		
		for (var i in values)
			result[values[i].id] = values[i].title; 
		
		return result;
	}
	
}, AI_Module);

/*******************************************************************************

	PRODUCT IMPORT

*******************************************************************************/

var AI_ProductImport = Unit.append({

	name:			'productImport',
	module:			'AI_ProductImport',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список импортируемых товаров',
		EMPTY_LIST:	'В списке нет записей, для импортирования записей, нажмите кнопку "импортировать файл".'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		accept: {
			access:		2,
			//loader:		'orderOverallContent',
			request:	"{form:this.getCommand('accept',{id:form})}",
			clear:		true
		},
		acceptAll: {
			access:		2,
			//loader:		'orderOverallContent',
			request:	"{form:this.getCommand('acceptAll',{})}",
			clear:		true
		}
	}),
	
	controls: {
		//
	},
	
	ACCESS: {
		DENY: 0,
		LIST: 1,
		EDIT: 3
	},
	
	access: {
		0: 'Доступ запрещён',
		3: 'Доступ разрешен'
	},
	
	uploadedFile: function(error, result) {
		//console.log(error);
		//console.log(result);
		AI.reload();
		//var target = Unit(AI_Product.name + 'Images');
		//if (target) target.innerHTML += AI_Product.getTemplate('Images', {images:result});
	}
	
}, AI_Module);

/*******************************************************************************

	PRODUCT EXPORT

*******************************************************************************/

var AI_ProductExport = Unit.append({

	name:			'productExport',
	module:			'AI_ProductExport',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список импортируемых товаров',
		EMPTY_LIST:	'В списке нет записей, для экспортирования товаров, нажмите кнопку "экспортировать файл".',
		NO_RECORDS:	'В указанный период нет товаров для экспортирования',
		NO_FILES:	'В указанный период нет файлов изображений для экспортирования.'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		selectDate: {
			access:		2,
			popup:		'SelectDate'
		}/*,
		generate: {
			access:		2,
			//loader:		'orderOverallContent',
			request:	"{form:this.getCommand('accept',{id:form})}"
		}*/
	}),
	
	controls: {
		generate: {
			command:	'generate',
			request:	'form',
			loader:		'productExportSelectDate',
			action:		"Unit.remove('productExportSelectDate');AI.reload();"
		}
	},
	
	ACCESS: {
		DENY: 0,
		LIST: 1,
		EDIT: 3
	},
	
	access: {
		0: 'Доступ запрещён',
		3: 'Доступ разрешен'
	},
	
	controlGenerate: function(form, level, callback) {
		if (level == 3) this.control(this.controls.generate, form, callback);
	},
	
}, AI_Module);

/*******************************************************************************

	PRODUCT EXPORT XLS

*******************************************************************************/

var AI_ProductExportXls = Unit.append({

	parent:			'productExport',
	name:			'productExportXls',
	module:			'AI_ProductExportXls',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Экспортирование товаров для Prom.ua',
		EMPTY_LIST:	'В списке нет записей, для экспортирования товаров, нажмите кнопку "экспортировать файл".',
		NO_RECORDS:	'В указанный период нет товаров для экспортирования',
		NO_FILES:	'В указанный период нет файлов изображений для экспортирования.'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		selectDate: {
			access:		2,
			popup:		'SelectDate'
		}/*,
		generate: {
			access:		2,
			//loader:		'orderOverallContent',
			request:	"{form:this.getCommand('accept',{id:form})}"
		}*/
	}),
	
	controls: {
		generate: {
			command:	'generate',
			request:	'form',
			loader:		'productExportXlsSelectDate',
			action:		"Unit.remove('productExportXlsSelectDate');AI.reload();"
		}
	},
	
	ACCESS: {
		DENY: 0,
		LIST: 1,
		EDIT: 3
	},
	
	access: {
		0: 'Доступ запрещён',
		3: 'Доступ разрешен'
	},
	
	controlGenerate: function(form, level, callback) {
		if (level == 3) this.control(this.controls.generate, form, callback);
	},
	
}, AI_Module);

/*******************************************************************************

	PRODUCTS MODULE

*******************************************************************************/

var AI_Product = Unit.append({

	name:			'product',
	module:			'AI_Product',
	
	messages: AI_Tools.join(AI_Module.messages, {
		TITLE:		'Список товаров',
		NO_IMAGES:	'Для сохранения товара необходимо добавить его изображения.'
	}),
	
	actions: AI_Tools.join(AI_Module.actions, {
		recalculatePrice: {
			access:		2,
			action:		"{}",
			popup:		'RecalculatePrice',
			clear:		true
		},
		recalculateDiscount: {
			access:		2,
			action:		"{}",
			popup:		'RecalculateDiscount',
			clear:		true
		},
		productPrice: {
			access:		2,
			request:	"{form:this.getCommand('save',{form:form})}",
			clear:		true
		},
		todoAction: {
			access:		2,
			loader:		'productList',
			request:	"{form:this.getCommand(form.action,{products:form.products})}",
			action:		'AI.reload()',
			clear:		true
		},
		setCategory: {
			access:		2,
			loader:		'productList',
			request:	"{form:this.getCommand('setCategory',form)}",
			action:		'AI.reload()',
			clear:		true
		},
		/*productPriority: {
			access:		2,
			request:	"{form:this.getCommand('save',{form:form})}"
		},*/
		setImageColor: {
			access:		2,
			request:	"{form:this.getCommand('setImageColor',form)}"
		},
		dropImage: {
			access:		3,
			request:	"{form:this.getCommand('dropImage',{id:form})}"
		},
		dropComment: {
			access:		3,
			request:	"{form:this.getCommand('dropComment',{id:form})}"
		},
		confirmComment: {
			access:		2,
			request:	"{comment:{module:'productComment',method:'confirm',arguments:form}}"
		}
	}),
	
	controls: AI_Tools.join(AI_Module.controls, {
		recalculate: {
			command:	'recalculate',
			request:	'{filters:Unit.normalize(Unit.parse(this.lastRequestFilters)),form:form}',
			loader:		'productRecalculatePrice',
			action:		"Unit.remove('productRecalculatePrice');AI.reload()",
			clear:		true
		},
		discount: {
			command:	'recalculate',
			request:	'{filters:Unit.normalize(Unit.parse(this.lastRequestFilters)),form:form}',
			loader:		'productRecalculateDiscount',
			action:		"AI.reload()",
			clear:		true
		}
	}),
	
	getAvailabilityOptions: function() {
		return {
			0: 'Снято с производства',
			1: 'Ожидается поступление',
			2: 'Под заказ',
			3: 'Есть в наличии'
		};
	},
	
	getColors: function() {
		var result = this.actions.edit.result || {};
		if ( ! result.values || ! result.values[1] || ! result.values[1][1]) return;
		if (this.colors) return this.colors;
		return this.colors = Unit.assoc(result.values[1][1], 'id');
	},
	
	controlRecalculate: function(form, level, callback) {
		if (level == 3) this.control(this.controls.recalculate, form, callback);
	},
	
	controlDiscount: function(form, level, callback) {
		if (level == 3) this.control(this.controls.discount, form, callback);
	},
	
	uploadedImage: function(error, result) {
		Unit.reactivateAllElements(Unit(AI_Product.name + 'Images').create('SPAN', {
			innerHTML: AI_Product.getTemplate('Images', {images:result})
		}));
	},
	
	requestEdit: function(id) {
		var data		= AI_Module.requestEdit.call(this, id);
		//if (id === '') data.blank = AI_Module.getCommand('blank');
		data.categories	= AI_ProductCategory.getCommand('getGrouped');
		data.supplier	= AI_ProductSupplier.getCommand('find');
		data.types		= AI_ProductType.getCommand('find');
		data.groups		= AI_ProductParam.getCommand('getGroups');
		data.keys		= AI_ProductParam.getCommand('getKeys');
		data.values		= AI_ProductParam.getCommand('getValues');
		data.comments	= this.getCommand('getComments', {id:id});
		data.images		= this.getCommand('getImages', {id:id});
		return data;
	},
	
	requestList: function(id) {
		if ( ! id) id = 'where[active]=1&order=added&drect=0';
		var data		= AI_Module.requestList.call(this, id);
		data.categories	= AI_ProductCategory.getCommand('getGrouped');
		data.supplier	= AI_ProductSupplier.getCommand('find');
		data.types		= AI_ProductType.getCommand('find');
		return data;
	},
	
	controlEdit: function(form, level, callback) {
		var values = Unit.normalize(form);
		if (level == 3) {
			if ( ! values.image) return callback({message:AI_Product.messages.NO_IMAGES, type:'ERROR'});
			this.control(this.controls.edit, form, callback);
		}
	}
	
}, AI_Module);