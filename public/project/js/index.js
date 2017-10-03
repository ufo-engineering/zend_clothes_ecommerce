/*******************************************************************************

	GLOBAL project METHODS

*******************************************************************************/

var project = {};

project.messages = {
	FORM_ERROR:			'Заполните форму'
};

/*project.currencies = {
	currencyUsd: 16, //13
	currencyRub: 0.32 //0.35
}*/

project.loginMessages = {
	EMPTY_FIELDS:		'Заполните форму',
	UNDEFINED_EMAIL:	'Не верный E-mail',
	UNDEFINED_PASSWORD:	'Не верный пароль',
	DISABLED_ACCESS:	'E-mail не активен',
	CANT_REGISTRATE:	'Указанный E-mail уже зарегистрирован.',
	PASSWORD_MISSMATCH:	'Пароль и подтверждение пароля не совпадают.',
	COMPLETE:			'Форма успешно сохранена'
};

project.emailRegExp = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;

project.HOST		= location.host.replace('www.','');
project.GA_CODES	= {'project.ru':'UA-54035450-2', 'project.com.ua':'UA-54035450-3', 'project.co.uk':'UA-54035450-4'};
project.GA_CODE	= project.GA_CODES[project.HOST] || project.GA_CODES['project.com.ua'] || '';
project.YA_CODES	= {'project.ru':'27024543', 'project.com.ua':'27024801'};
project.YA_CODE	= project.YA_CODES[project.HOST] || project.YA_CODES['project.com.ua'] || '';

project.request = function(url, post, callback) {
	Unit.requestAjax('api/' + (url || '') + '/', post, function(data, req) {
		if (callback instanceof Function)
			callback((data || {}).error, (data || {}).result);
	});
};

project.message = function(number, messages) {
	if ( ! number || ! (number = parseInt(number))) return  messages[0];
	else if (number%10 == 1 && (number%100 == 1 || number%100 > 20)) return messages[1].replace('%s', number);
	else if (number%10 >=2 && number%10 <= 4 && (number%100 < 10 || number%100 > 20)) return messages[2].replace('%s', number);
	else return messages[3].replace('%s', number);
};

project.discount = function(price, discount) {
	if (parseInt(discount)  > 0) {
		return discount.indexOf('%') > 0?
			price - (price / 100 * parseInt(discount)):
			price - discount;
	}
	return price;
};

project.price = function(price, discount, usd, rub) {
	var usd	= project.getConfig('currencyUsd', 20) * 1;//13;
	var rub	= project.getConfig('currencyRub', 0.385) * 1;//0.35;
	
	var html = '<span class="uah">' + Unit.price(price) + ' грн</span>'+
		'<span class="usd">' + Unit.price(price / usd) + ' $</span>'+
		'<span class="rub">' + Unit.price(price / rub) + ' р.</span>';
	
	if (discount && parseInt(discount) > 0) {
		discounted = discount.indexOf('%') >= 0?
			price - (price / 100 * parseInt(discount)):
			price - parseInt(discount);
		
		html = '<u class="discount">' + html + '</u> '+
			'<span class="uah">' + Unit.price(discounted) + '&nbsp;грн</span>'+
			'<span class="usd">' + Unit.price(discounted / usd) + '&nbsp;$</span>'+
			'<span class="rub">' + Unit.price(discounted / rub) + '&nbsp;р.</span>';
	}
	return html;
};

project.getConfig = function(id, value)
{
	return project.config && project.config[id] || value;
}

project.getCurrencyCodeOnlinePay= function(){
	var currency= Unit.getCookie('currency');
	switch (currency){
		case 'rub':
			var code= 643;
		break;
		case 'usd':
			var code= 840;
		break;
		default:
			//default currency is UAH
			var code= 980;
		break;
	}
	return code;
}

project.getCurrencyPrivat= function(){
	var currency= Unit.getCookie('currency');
	switch (currency){
		case 'rub':
			var code= 'RUB';
		break;
		case 'usd':
			var code= ' USD';
		break;
		default:
			//default currency is UAH
			var code= 'UAH';
		break;
	}
	return code;
}

project.getPriceOnlinePay= function(price){
	var usd	= project.getConfig('currencyUsd', 22) * 1;//13;
	var rub	= project.getConfig('currencyRub', 0.36) * 1;//0.35;

	var currency= Unit.getCookie('currency');
	switch (currency){
		case 'rub':
			wmiPrice= Unit.price(price / rub);
		break;
		case 'usd':
			wmiPrice= Unit.price(price / usd);
		break;
		default:
			wmiPrice= Unit.price(price);
		break;
	}
	return wmiPrice;
}

project.restore = function(form, level, callback) {
	if (level == 3) {
		var errors = {};
		if (form.email !== undefined && ! form.email) errors.email = 'required';
		if (Unit.toArray(errors).length) return callback({
			message:	project.messages.FORM_ERROR,
			fields:		errors,
			type:		'ERROR'
		});
		project.request('account/restore', form, function(err, res) {
			if ( ! err && res) {
				Unit.trigger('popupRestoreForm');
				Unit.trigger('popupRestoreComplete');
			}
			else
			{
				if (err && err.message) err.message = project.loginMessages[err.message] || project.loginMessages.EMPTY_FIELDS;
				if (callback instanceof Function) callback(err, res);
			}
		});
	}
};

project.login = function(form, level, callback) {
	if (level == 3) {
		project.request('account/login', form, function(err, res) {
			if ( ! err && res) {
				//console.log(res);
				Unit.setCookie('account', Unit.stringifyJson(res));
				document.location = '/account';
			}
			else
			{
				if (err && err.message) err.message = project.loginMessages[err.message] || project.loginMessages.EMPTY_FIELDS;
				if (callback instanceof Function) callback(err, res);
			}
		});
	}
};

project.logout = function() {
	project.request('account/logout', {}, function(err, res) {
		Unit.removeCookie('account');
		project.accountUpdate();
		document.location = '/';
	});
};

project.status = function(callback) {
	project.request('account/status', {}, callback);
};

project.accountUpdate = function() {
	var user = Unit.parseJson(Unit.getCookie('account'));
	var last = Unit.getCookie('accountLastCheck');
	var updt = function() {
		Unit.setClassValue('accountArea', 'account', user ? 'in' : 'out');
		if (user && user.email) Unit.inner('accountName', user.name);
	}
	if (user && ( ! last || last < Unit.time() - 600)) {
		Unit.setCookie('accountLastCheck', Unit.time());
		project.status(function(err, res) {
			user = res;
			if (res) Unit.setCookie('account', Unit.stringifyJson(res));
			else Unit.removeCookie('account');
			updt();
		});
	}
	
	updt();
};

project.setLanguage = function(language) {
	//
};

project.setCurrency = function(currency) {
	//
};

project.basketControl = function(form, level, callback) {
	if (level == 3)
	{
		var values = Unit.normalize(form);
		for (var i in values.sizeId) {
			if (values.sizeId[i] > 0) {
				project.basketAdd(form);
				return callback({
					type:		'SUCCESS',
					message:	'Товар добавлен в корзину.',
				}, false);
			}
		}
		return callback({
			type:		'ERROR',
			message:	'Выберите размер и количество.',
		}, false);
	}
};

project.registrationControl = function(form, level, callback) {
	if (level == 3)
	{
		var errors = {}, values = Unit.normalize(form);
		
		if (values.name !== undefined && ! values.name) errors.name = 'required';
		if (values.lastname !== undefined && ! values.lastname) errors.lastname = 'required';
		if (values.phone !== undefined && ! values.phone) errors.phone = 'required';
		if (values.email !== undefined && ! values.email) errors.email = 'required';
		if (values.password !== undefined && ! values.password) errors.password = 'required';
		if (values.confirm !== undefined && ! values.confirm) errors.confirm = 'required';
		
		if (Unit.toArray(errors).length) return callback({
			message:	project.messages.FORM_ERROR,
			fields:		errors,
			type:		'ERROR'
		});
		
		if (values.password !== values.confirm) return callback({
			message:	project.loginMessages.PASSWORD_MISSMATCH,
			fields:		{password: 'required', confirm: 'required'},
			type:		'ERROR'
		});
		
		delete form.confirm;
		
		project.request('account/save', {form:form}, function(err, res) {
			if ( ! err && res) {
				if (location.pathname.substr(0,8) == '/account') {
					location.reload();
					//document.location = '/account#information';
				}
				else {
					Unit.trigger('registrationForm');
					Unit.trigger('registrationSuccess');
				}
			}
			else
			{
				if (err && err.message) err.message = project.messages[err.message] || project.loginMessages.CANT_REGISTRATE;
				if (callback instanceof Function)	callback(err, res);
			}
		});
	}
};

project.subscribeControl = function(form, level, callback) {//name, email
	if (level == 3)
	{
		var errors = {}, values = Unit.normalize(form);
		
		if ( ! values.name) errors.name = 'required';
		if ( ! values.email) errors.email = 'required';
		if ( ! values.email.match(project.emailRegExp)) errors.email = 'required';
		
		if (Unit.toArray(errors).length) return callback({
			message:	project.messages.FORM_ERROR,
			fields:		errors,
			type:		'ERROR'
		});
		
		form.active = 1;
		
		project.request('subscribe/save', {form:form}, function(err, res) {
			if ( ! err && res > 0) {
				Unit.inner('subscribeName', '');
				Unit.inner('subscribeEmail', '');
				alert('Вы успешно подписаны на рассылку!');
			}
		});
	}
};

project.commentControl = function(form, level, callback) {//name, email
	if (level == 3)
	{
		var errors = {}, values = Unit.normalize(form);
		
		if ( ! values.message) errors.message = 'required';
		if ( ! values.name) errors.name = 'required';
		if ( ! values.email) errors.email = 'required';
		if ( ! values.rating) errors.rating = 'required';
		if ( ! values.email.match(project.emailRegExp)) errors.email = 'required';
		
		if (Unit.toArray(errors).length) return callback({
			message:	project.messages.FORM_ERROR,
			fields:		errors,
			type:		'ERROR'
		});
		
		project.request('comment/save', {form:form}, function(err, res) {
			if ( ! err && res > 0) {
				Unit.inner('commentName', '');
				Unit.inner('commentEmail', '');
				Unit.inner('commentMessage', '');
				Unit.trigger('commentPageForm');
				Unit.trigger('commentPageAdded');
			}
		});
	}
};

project.productCommentControl = function(form, level, callback) {//name, email
	if (level == 3)
	{
		var errors = {}, values = Unit.normalize(form);
		
		if ( ! values.message) errors.message = 'required';
		if ( ! values.authorName) errors.authorName = 'required';
		if ( ! values.authorEmail) errors.authorEmail = 'required';
		if ( ! values.authorEmail.match(project.emailRegExp)) errors.authorEmail = 'required';
		
		if (Unit.toArray(errors).length) return callback({
			message:	project.messages.FORM_ERROR,
			fields:		errors,
			type:		'ERROR'
		});
		
		project.request('productComment/save', {form:form}, function(err, res) {
			if ( ! err && res > 0) {
				Unit.inner('commentName', '');
				Unit.inner('commentEmail', '');
				Unit.inner('commentMessage', '');
				Unit.trigger('productCommentForm');
				Unit.trigger('productCommentAdded');
			}
		});
	}
};

project.basketAdd = function(form) {
	form = Unit.normalize(form);
	
	if ( ! form || typeof(form) != 'object') return false;
	//if ( ! form.sizeId) form.sizeId = {0:form.sizeId};
	
	var basket = project.basketLoad();
	
	/*for (var s in form.sizeId) {
		var k = [form.productId, s, form.colorId].join('/');
		if (form.sizeId[s] > 0) basket[k] = {
			productId:	form.productId,
			colorId:	form.colorId,
			sizeId:		s,
			amount:		form.sizeId[s],
			price:		form.price
		};
	}*/
	
	basket[form.id+'/'+(form.colorId || 0)] = form;
	
	console.log(basket);
	
	project.basketSave(basket);
	project.basketUpdate();
};

project.basketSet = function(product, color, size, amount) {
	var basket = project.basketLoad();
	
	if (basket[product+'/'+color] && basket[product+'/'+color].sizeId) {
		basket[product+'/'+color].sizeId[size] = amount;
	}
	
	project.basketSave(basket);
	project.basketUpdate();
};

project.basketGet = function(product, color) {
	var basket = project.basketLoad();
	return basket[product+'/'+color];
};

project.basketDel = function(id) {
	var basket = project.basketLoad();
	
	delete basket[id];
	
	project.basketSave(basket);
	project.basketUpdate();
};

project.basketLoad = function() {
	var json = Unit.normalize(Unit.parse(Unit.getCookie('basket')));
	var pack = Unit.getCookie('basketPacked');
	var data = {};

	if (pack) {
		pack = pack.split('&');
		for (var i in pack) {
			var prod = pack[i].split('/');
			prod = {
				id:			prod[0],
				price:		prod[2],
				colorId:	prod[1],
				sizeId:		Unit.parse(prod[3], '_', '-')
			};
			data[prod.id + '/' + prod.colorId] = prod;
		}
		return data;
	}

	//0[i]=5651&0[p]=290.00&0[c]=51&0[s][2]=0&0[s][3]=0&0[s][4]=1&1[i]=5649&1[p]=500.00&1[c]=15&1[s][2]=0&1[s][3]=0&1[s][4]=20
	//0=5651/290.00/51/2-0;3-0;4-1&1=5649/500.00/15/2-0;3-0;4-20
	//5651;51;290.00;2-0,3-0,4-1&5649;15;500.00;2-0,3-0,4-20

	for (var i in json) {
		var prod	= json[i];
		var id		= prod.i || prod.id;
		var price	= prod.p || prod.price;
		var color	= prod.c || prod.colorId || 0;
		var sizes	= prod.s || prod.sizeId;
		if (id) data[id+'/'+color] = {
			id:			id,
			price:		price,
			colorId:	color,
			sizeId:		sizes,
		};
	}

	return data;
};

project.basketSave = function(basket) {
	var data = [];
	
	for (var i in basket) {
		var prod = basket[i], size = [];
		//for (var i in prod.sizeId) if (prod.sizeId[i] > 0) size.push(i + '-' + prod.sizeId[i]);
		data.push([prod.id, prod.colorId, prod.price, Unit.stringify(prod.sizeId, '_', '-')].join('/'));
	}
	
	Unit.removeCookie('basket')
	return Unit.setCookie('basketPacked', data.join("&"));
};

project.basketSaveOld = function(basket) {
	var data = [];
	
	for (var i in basket) {
		var prod = basket[i];
		data.push({
			i: prod.id,
			p: prod.price,
			c: prod.colorId,
			s: prod.sizeId
		})
	}
	
	return Unit.setCookie('basket', Unit.stringify(data));
};

project.basketClean = function() {
	Unit.setCookie('basket', '{}');
	Unit.setCookie('basketPacked', '');
	project.basketUpdate();
};

project.basketUpdate = function() {
	var basket = project.basketLoad();
	var amount = 0;
	var price = 0;
	
	for (var i in basket) {
		for (var s in basket[i].sizeId) {
			amount += parseInt(basket[i].sizeId[s]) || 0;
			price += basket[i].sizeId[s] * basket[i].price;
		}
	}
	
	Unit('basketAmount').inner(amount);
	Unit('basketPrice').inner(project.price(price));
};

project.commentsUpdate = function() {
	var data = Unit.getCookie('commentsCounter');
	var last = Unit.getCookie('commentsCounterLastCheck');
	var updt = function() {
		var node = Unit('commentsCounter');
		node.trigger('hidden', false);
		node.inner(project.message(data, ['Нет отзывов', '%s отзыв', '%s отзыва', '%s отзывов']));
	}
	if (document.location.pathname.indexOf('/comments/') == 0 || ! last || last < Unit.time() - 600) {
		Unit.setCookie('commentsCounterLastCheck', Unit.time());
		project.request('comment/getCount', {form:{where:{active:1}}}, function(err, res) {
			data = res;
			if (res) Unit.setCookie('commentsCounter', res);
			else Unit.removeCookie('commentsCounter');
			updt();
		});
	}
	
	updt();
}

project.configUpdate = function(callback) {
	project.config = Unit.parse(Unit.getCookie('projectConfig'));
	//console.log(project.config);
	if ( ! project.config || ! project.config.indexMail) {
		project.request('config/getAll', {}, function(err, res) {
			if ( ! res) return callback();;
			project.config = {};
			for (var i in res) project.config[i] = res[i].value;
			Unit.setCookie('projectConfig', Unit.stringify(project.config));
			callback();
		});
	}
	else callback();
}

project.showLoginPopup = function(hash) {
	var code = Unit.templater(Unit('loginPopupTemplate').innerHTML, hash);
	var html = Unit('root').create('DIV',{
		id:	'loginPopup',
		innerHTML: '<div class="popupBackground" id="popupBackground" onclick="Unit.remove(\'loginPopup\')"></div>' + code
	});
	var height = Math.max(document.documentElement["clientHeight"], document.body["scrollHeight"], document.documentElement["scrollHeight"], document.body["offsetHeight"], document.documentElement["offsetHeight"]);
	Unit('popupBackground').style.height = height + 'px';
	Unit.reactivateAllElements(Unit('loginPopup'));
};

project.showProductPopup = function(hash) {
	var code = Unit.templater(Unit('productViewPopup').innerHTML, hash);
	var html = Unit('root').create('DIV',{
		id:	'productPopup',
		innerHTML: '<div class="popupBackground" id="popupBackground" onclick="Unit.remove(\'productPopup\')"></div>' + code
	});
	var height = Math.max(document.documentElement["clientHeight"], document.body["scrollHeight"], document.documentElement["scrollHeight"], document.body["offsetHeight"], document.documentElement["offsetHeight"]);
	Unit('popupBackground').style.height = height + 'px';
	Unit('item-popup').style.top = Math.round((window.pageYOffset || window.scrollY || 0) + Math.min(20,(window.innerHeight - 700) / 2 )) + 'px';
	Unit.reactivateAllElements(Unit('productPopup'));
};

/*******************************************************************************

	project BASKET CONTROLLER

*******************************************************************************/

var projectBasket = {data:{}};

projectBasket.messages = {
	FORM_ERROR:		'Заполнены не все обязательные поля.'
};

projectBasket.set = function(key, value, update) {
	projectBasket.data[key] = value;
	if (update !== false) projectBasket.updateBasket();
	return value;
};

projectBasket.get = function(key) {
	return projectBasket.data[key];
};

projectBasket.validateForm = function(form, level, callback) {
	projectBasket.set('form', form, false);
	if (level == 3) {
		var errors = {}, values = Unit.normalize(form), delivery = values.delivery || {};

		if ( ! values.email) errors.email = 'required';
		switch (values.order_type){
			case 'new':
				if ( ! values.name) errors.name = 'required';
				if ( ! values.lastname) errors.lastname = 'required';
				if ( ! values.phone) errors.phone = 'required';

				if (values.deliveryType == 0) errors.deliveryType = 'required';

				if (delivery.country !== undefined && ! delivery.country)	errors['delivery[country]'] = 'required';
				if (delivery.state !== undefined && ! delivery.state)		errors['delivery[state]'] = 'required';
				if (delivery.city !== undefined && ! delivery.city)			errors['delivery[city]'] = 'required';
				if (delivery.post !== undefined && ! delivery.post)			errors['delivery[post]'] = 'required';
				if (delivery.address !== undefined && ! delivery.address)	errors['delivery[address]'] = 'required';

				if (delivery.store !== undefined && ! delivery.store)		errors['delivery[store]'] = 'required';
				if (delivery.train !== undefined && ! delivery.train)		errors['delivery[train]'] = 'required';
				if (delivery.station !== undefined && ! delivery.station)	errors['delivery[station]'] = 'required';
				if (delivery.passport !== undefined && ! delivery.passport)	errors['delivery[passport]'] = 'required';
			break;
			case 'exist':
				if ( ! values.password) errors.password = 'required';
			break;
			case 'fast':
				if ( ! values.name) errors.name = 'required';
				if ( ! values.phone) errors.phone = 'required';
			break;
		}

		if (Unit.toArray(errors).length){
			if (values.order_type == 'new'){
				return callback({
					message:	projectBasket.messages.FORM_ERROR,
					fields:		errors,
					type:		'ERROR'
				});
			}else{
				alert (projectBasket.messages.FORM_ERROR);
				return false;
			}
		}

		project.request('order/send', {form:values,basket:project.basketLoad(), currency: project.getCurrencyPrivat()}, function(err, res) {
			if ( ! err && res && res.id) {
				//document.location = '/orderSuccess';
				Unit.trigger('orderSoccess', 'hidden', false);
				Unit.trigger('orderPage', 'hidden', true);
				projectBasket.sendAnalytics(res);
				project.basketClean();
				switch (values.paymentType){
					case 13:
					case '13':
						document.getElementById("wmiDescription").value= "Оплата заказа #"+res.id+" в магазине project.";
						document.getElementById("wmiPrice").value= project.getPriceOnlinePay(res.price);
						document.getElementById("onlinePayForm").submit();
					break;
					case 14:
					case '14':
						console.log(res.liqpay, values.paymentType);
						document.getElementById("liqData").value= res.liqpay.data;
						document.getElementById("liqSign").value= res.liqpay.signature;
						document.getElementById("liqpayForm").submit();
					break;
				}
			}
			else
			{
				Unit.trigger('orderFailed', 'hidden', false);
				Unit.trigger('orderPage', 'hidden', true);
				console.log('ERROR '+err);
				project.request('order/log_error', {err_msg:err,basket:project.basketLoad()}, function(result) {
				console.log('ERROR '+result);
				});
				if (err && err.message) err.message = projectBasket.messages[err.message] || projectBasket.messages.FORM_ERROR;
				if (callback instanceof Function)	callback(err, res);
			}
		});

		/*if (values.paymentType == 14){
			id= document.getElementById("liqOrderId").value;
			price= document.getElementById("liqAmount").value;
			console.log(id, price);
			project.request('privat/fields', {order_id: id, amount: price, currency: project.getCurrencyPrivat()}, function(err, res){
				console.log(res);
				document.getElementById("liqData").value= res.data;
				document.getElementById("liqSign").value= res.signature;
				document.getElementById("liqpayForm").submit();
			});
		}*/
	}
};
var _gaq = [];
projectBasket.sendAnalytics = function(order) {
	//var _gaq = _gaq || [];
	_gaq = _gaq || [];
	_gaq.push(['_setAccount', project.GA_CODE]);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_addTrans',
		order.id, // order ID - required
		'project', // affiliation or store name
		order.data.price, // total - required
		'0', // tax
		'0', // shipping
		order.data.city, // city
		order.data.state, // state or province
		order.data.country // country
	]);
	// add item might be called for every item in the shopping cart
	// where your ecommerce engine loops through each item in the cart and
	// prints out _addItem for each
	for (var i in order.items) {
		var item = order.items[i];
		_gaq.push(['_addItem',
			order.id, // order ID - required
			item.article, // SKU/code - required
			item.title, // product name
			item.variant, // category or variation
			item.price, // unit price - required
			item.amount // quantity - required
		]);
	}
	_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
};

projectBasket.updateBasket = function() {
	if ( ! projectBasket.productInfo) {
		var ids = [], basket = project.basketLoad();
		for (var i in basket) ids.push(basket[i].id);
		return project.request('order/getAllInfo', {ids:ids}, function(err, res) {
			if ( ! err && res)
			{
				projectBasket.productInfo = res;
				projectBasket.updateBasket();
			}
		});
	}
	
	Unit('basketCart').inner(
		Unit.templater(Unit('templateBasketCart').innerHTML, {})
	);
	Unit('basketForm').inner(
		Unit.templater(Unit('templateBasketForm').innerHTML, {})
	);
	Unit('basketPayments').inner(
		Unit.templater(Unit('templateBasketPayments').innerHTML, {})
	);
	Unit.reactivateAllElements(Unit('basketCart'));
	Unit.reactivateAllElements(Unit('basketForm'));
	Unit.reactivateAllElements(Unit('basketPayments'));
};

/*******************************************************************************

	USER INTERFACE COMPONENTS

*******************************************************************************/

function UI_Counter(id) {
	var hash = Unit(id);
	var conf = hash.getByAttribute('uiCounter');
	var init = setTimeout(function() {
		if (conf.add) Unit.addEvent(conf.add, 'onclick', hash.setValue.bind(hash,true))
		if (conf.del) Unit.addEvent(conf.del, 'onclick', hash.setValue.bind(hash,false))
		if (conf.val) Unit.addEvent(conf.val, 'onchange', hash.setValue)
	}, 1);
	
	hash.setValue = function(newValue) {
		if (conf.val) {
			var oldValue = hash.getValue();
			if (newValue === true)				newValue = oldValue + 1;
			else if (newValue === false)		newValue = oldValue - 1;
			else if (parseInt(newValue) >= 0)	newValue = parseInt(newValue);
			else								newValue = parseInt(oldValue);
			conf.val.value = newValue > 0 ? newValue : 0;
		}
		hash.callAction();
	};
	
	hash.getValue = function() {
		return parseInt((conf.val || {}).value) || 0;
	};
	
	hash.callAction = function() {
		var action = hash.getAttribute('uiAction');
		if (action) hash.exec(action, {});
	};
	
	return hash;
}

function UI_Rating(id) {
	var hash = Unit(id);
	var conf = hash.getAllAttributes('uirating');
	var node = hash.getByAttribute('uiRating');
	var item = hash.getByAttribute('uiRatingItem');
	var init = setTimeout(function() {
		for (var i in item) Unit.addEvent(item[i], 'onclick', hash.selectRating.bind(hash, i));
	}, 1);
	
	hash.selectRating = function(rating) {
		if ( ! item[rating]) return;
		for (var i in item) Unit.trigger(item[i], 'active', i == rating);
		if (node.value) Unit.inner(node.value, rating);
	};
	
	return hash;
}

/*******************************************************************************

	OTHER

*******************************************************************************/

Unit.onDomReady(function() {
	project.configUpdate(function(){
		project.basketUpdate();
		project.accountUpdate();
		project.commentsUpdate();
	});
});
