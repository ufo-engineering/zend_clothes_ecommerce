var AI_Templates = {};

/*******************************************************************************

	POPUPS

*******************************************************************************/

AI_Templates.popupLogin = function() {/*
	<div class="popup popupOneRow">
		<form class="form" ui="Form" uiControl="{$this.module}.controlLogin" uiRules="{$this.module}.schema">
		
			<%= AI.template('simpleForm', {
				title: 		'Вход в панель управления',
				message:	'Для входа в панель управления необходимо ввести логин и пароль Вашей персональной учётной записи.',
				fields:	{
					'field30': [
						AI.template('formTextField', {
							id:				hash.id + 'Login',
							name:			'login',
							title:			'Логин',
							description:	'Имя Вашей персональной учётной записи.'
						}),
						
						AI.template('formTextField', {
							id:				hash.id + 'Password',
							name:			'password',
							type:			'password',
							title:			'Пароль',
							description:	'Пароль Вашей персональной учётной записи.'
						}),
						
						AI.template('formSelectField', {
							id:				hash.id + 'Style',
							name:			'style',
							type:			'style',
							title:			'Стиль',
							description:	'Цвет интерфейса панели управления.',
							options:		{'index.css':'Тёмный','indexLight.css':'Светлый'},
							value:			Unit.getCookie('interfaceStyle'),
							action:			"Unit.setCookie('interfaceStyle',this.selected);Unit('style').href='/public/admin/'+this.selected;"
						})
					]
				}
			}) %>
		
			<div class="actions">
				<input type="submit" value="войти" class="bigButton" uiForm="submit">
			</div>
				
		</form>
	</div>
*/};

AI_Templates.popupSelectDate = function() {/*
	<div class="popup formDate">
		<div class="form" ui="Date" uiTarget="{$hash.target || ''}" uiAction="Unit.remove('{$hash.id}');Unit.inner('{$hash.label}',AI_Tools.bigDate(time))">
		
			<div class="headSmall">
				<span class="title">Выбор даты</span>
				<input class="miniButton" type="button" onclick="Unit.remove('{$hash.id}')" value="закрыть">
			</div>
			
			<div class="bodySmall">
				<div class="fdMenu">
					<div class="fdMonth">
						<%= AI.template('formSelectItem', {
							id:				hash.id + 'DateMonth',
							name:			'dateMonth',
							value:			(new Date()).getMonth(),
							attributes:		'uiDate="month"',
							options:		'Январь Февраль Март Апрель Май Июнь Июль Август Сентябрь Октябрь Ноябрь Декабрь'.split(' ')
						}) %>
					</div>
					<div class="fdYear">
						<%= AI.template('formTextItem', {
							id:				hash.id + 'DateYear',
							name:			'dateYear',
							value:			(new Date()).getFullYear(),
							attributes:		'uiDate="year"',
							length:			4
						}) %>
					</div>
				</div>
				
				<div class="fdBody">
					<for (var i = 1; i <= 31; i++)>
						<span uiDateDay="{$i}" class="fdDay">{$i}</span>
					<endfor>
				</div>
					
			</div>
		</div>
	</div>
*/};

/*******************************************************************************

	FORM COMPONENTS

*******************************************************************************/

AI_Templates.message = function() {/*
	<div id="message">
		{$hash.text || ''}
	</div>
*/};

AI_Templates.formStaticField = function() {/*
	<div>
		<label>{$hash.title||''}</label>
		<dl>
			<dt>{$hash.value||''}</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formImageField = function() {/*
	<div uiField="{$hash.name}" class="{$hash.required ? 'formRequired' : ''}">
		<label>{$hash.title||''}</label>
		<dl>
			<dt class="imageField">
				<input type="text" id="{$hash.id}" name="{$hash.name}" value="{$hash.value}" class="input" {$hash.length ? 'maxlength="' + hash.length + '"' : ''} {$hash.attributes || ''}>
				<input type="file" id="{$this.id}Uploader" onchange="AI_Uploader('', '/api/banner/upload/', this, {$this.module}.uploadedFile)" min="1" max="1" multiple="true" accept="image/*" class="hidden">
				<input type="button" onclick="Unit('{$this.id}Uploader').click()" value="загрузить изображение" class="miniButton" style="position:absolute;right:-27px;top:3px" />
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formTextField = function() {/*
	<var (langs = hash.values || {'':hash.value})>
	<for (var lang in langs)>
		<div uiField="{$name+AI_Tools.name(lang)}" uiFieldRules="{$hash.rules||''}" class="lang{$lang}{$hash.required ? ' formRequired' : ''}">
			<label for="{$id+lang}">{$title}</label>
			<dl>
				<dt>
					<input type="{$hash.type||'text'}" id="{$id+lang}" name="{$name+AI_Tools.name(lang)}" value="{$langs[lang] || ''}" class="input" {$hash.disabled ? 'disabled' : ''} {$hash.length ? 'maxlength="' + hash.length + '"' : ''} {$hash.attributes || ''}>
					<if (hash.configurable)>
						<div class="inputCheckbox {$!hash.disabled ? 'active' : ''}" onclick="Unit(this).trigger('active',!(Unit('{$id+lang}').disabled=!Unit('{$id+lang}').disabled))" title="Кликните для разблокировки поля"></div>
					<endif>
				</dt>
				<dd><comment>{$hash.description||''}</comment></dd>
			</dl>
		</div>
	<endfor>
*/};

AI_Templates.formTextItem = function() {/*
	<input type="{$hash.type || 'text'}" id="{$hash.id}" name="{$hash.name}" value="{$hash.value || ''}" class="input" {$hash.disabled ? 'disabled' : ''} {$hash.length ? 'maxlength="' + hash.length + '"' : ''} {$hash.attributes || ''}>
*/};

AI_Templates.formTextareaItem = function() {/*
	<textarea id="{$hash.id}" name="{$hash.name}" class="textarea">{$hash.value || ''}</textarea>
*/};

AI_Templates.formTwinField = function() {/*
	<div uiField="{$name}" uiFieldRules="{$hash.rules||''}">
		<label for="{$id}">{$title}</label>
		<dl>
			<dt>
				<div class="inputTwin">
					<input type="{$hash.type||'text'}" id="{$id}" name="{$nameA}" value="{$hash.valueA || ''}" class="input" {$hash.disabled ? 'disabled' : ''} {$hash.length ? 'maxlength="' + hash.length + '"' : ''}>
				</div><div class="inputTwin">
					<input type="{$hash.type||'text'}" name="{$nameB}" value="{$hash.valueB || ''}" class="input" {$hash.disabled ? 'disabled' : ''} {$hash.length ? 'maxlength="' + hash.length + '"' : ''}>
				</div>
				<if (hash.configurable)>
					<div class="inputCheckbox {$!hash.disabled ? 'active' : ''}" onclick="Unit(this).trigger('active',!(Unit('{$id}').disabled=!Unit('{$id}').disabled))" title="Кликните для разблокировки поля"></div>
				<endif>
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formColorField = function() {/*
	<div>
		<label for="{$id}">{$title}</label>
		<dl ui="Color">
			<dt>
				<input type="{$hash.type||'text'}" id="{$id}" name="{$name}" value="{$hash.value || ''}" class="input color" {$hash.attributes || ''} uiColor="value">
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formDateField = function() {/*
	<div uiField="{$name}" uiFieldRules="{$hash.rules||''}">
		<label for="{$id}">{$title}</label>
		<dl>
			<dt>
				<div class="input">
					<span class="link" id="{$hash.id}selectDateLabel{$name}" onclick="AI.popup('popupSelectDate',{target:'{$hash.id}selectDate{$name}',label:'{$hash.id}selectDateLabel{$name}',value:'{$hash.value }'})">
						{$hash.value ? AI_Tools.bigDate(hash.value) : 'выбрать дату'}
					</span>
					<input type="hidden" id="{$hash.id}selectDate{$name}" name="{$name}" value="{$hash.value || ''}">
				</div>
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formDoubleDateField = function() {/*
	<div uiField="{$name}" uiFieldRules="{$hash.rules||''}">
		<label for="{$id}">{$title}</label>
		<dl>
			<dt>
				<div class="inputTwin">
					с <span class="link" id="{$hash.id}selectDateLabel{$nameA}" onclick="AI.popup('popupSelectDate',{target:'{$hash.id}selectDate{$nameA}',label:'{$hash.id}selectDateLabel{$nameA}',value:'{$hash.valueA }'})">
						{$hash.valueA ? AI_Tools.bigDate(hash.valueA) : 'выбрать дату'}
					</span>
					<input type="hidden" id="{$hash.id}selectDate{$nameA}" name="{$nameA}" value="{$hash.valueA || ''}">
				</div><div class="inputTwin">
					до <span class="link" id="{$hash.id}selectDateLabel{$nameB}" onclick="AI.popup('popupSelectDate',{target:'{$hash.id}selectDate{$nameB}',label:'{$hash.id}selectDateLabel{$nameB}',value:'{$hash.valueB}'})">
						{$hash.valueB ? AI_Tools.bigDate(hash.valueB) : 'выбрать дату'}
					</span>
					<input type="hidden" id="{$hash.id}selectDate{$nameB}" name="{$nameB}" value="{$hash.valueB || ''}">
				</div>
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formAreaField = function() {/*
	<var (langs = hash.values || {'':hash.value})>
	<for (var lang in langs)>
		<div uiField="{$name+AI_Tools.name(lang)}" uiFieldRules="{$hash.rules || ''}" class="lang{$lang}{$hash.required ? ' formRequired' : ''}">
			<label for="{$id+lang}">{$title}</label>
			<dl>
				<dt>
					<textarea class="textarea" id="{$id+lang}" name="{$name+AI_Tools.name(lang)}" {$hash.disabled ? 'disabled' : ''} {$hash.length ? 'maxlength="' + hash.length + '"' : ''}>{$langs[lang] || ''}</textarea>
					<if (hash.configurable)>
						<div class="inputCheckbox {$!hash.disabled ? 'active' : ''}" onclick="Unit(this).trigger('active',!(Unit('{$id+lang}').disabled=!Unit('{$id+lang}').disabled))" title="Кликните для разблокировки поля"></div>
					<endif>
				</dt>
				<dd><comment>{$hash.description||''}</comment></dd>
			</dl>
		</div>
	<endfor>
*/};

AI_Templates.formSelectField = function() {/*
	<div uiField="{$name}" uiFieldRules="{$hash.rules||''}">
		<label for="{$id}">{$title}</label>
		<dl>
			<dt>
				<%= AI.template('formSelectItem', hash) %>
			</dt>
			<dd><comment>{$hash.description||''}</comment></dd>
		</dl>
	</div>
*/};

AI_Templates.formSelectItem = function() {/*
	<div class="{$hash.className || 'select'}{$hash.disabled ? ' disabled' : ''}" ui="Menu" {$hash.action?' uiAction="'+hash.action+'"':''}>
		<input type="hidden" uiMenu="value" id="{$id}" name="{$name}" value="{$hash.value || ''}" {$hash.attributes || ''} />
		<div uiMenu="label"></div>
		<ul uiMenu="dropdown" class="hidden">
			<if (hash.empty)>
				<li uiMenuOption="">{$hash.empty}</li>
			<endif>
			<for (var i in options)>
				<li uiMenuOption="{$i[0]===' '?i.slice(1):i}">{$options[i]}</li>
			<endfor>
		</ul>
	</div>
*/};

AI_Templates.formSelectColor = function() {/*
	<div class="{$hash.className || 'select selectColor'}{$hash.disabled ? ' disabled' : ''}" ui="Menu" {$hash.action?' uiAction="'+hash.action+'"':''}>
		<input type="hidden" uiMenu="value" id="{$id}" name="{$name}" value="{$hash.value || ''}" {$hash.attributes || ''} />
		<div uiMenu="label"></div>
		<ul uiMenu="dropdown" class="hidden">
			<if (hash.empty)>
				<li uiMenuOption=""><p>{$hash.empty}</p></li>
			<endif>
			<for (var i in options)>
				<if (options[i] && options[i].title)>
					<li uiMenuOption="{$i[0]===' '?i.slice(1):i}" title="{$options[i].title}"><span style="background-color: #{$options[i].extra};"></span><p>{$options[i].title}</p></li>
				<endif>
			<endfor>
		</ul>
	</div>
*/};

AI_Templates.formWysiwyg = function() {/*
	<var (langs = hash.values || {'':hash.value})>
	<for (var lang in langs)>
		<div class="nicWysy lang{$lang}">
			<textarea name="{$hash.name+AI_Tools.name(lang)}" ui="NicWysy" id="editor{$hash.name+AI_Tools.name(lang)}" style="width: 100%; height: 400px;">{$Unit.html(langs[lang] || '')}</textarea>
		</div>
	<endfor>
*/};

AI_Templates.formWysiwygOld = function() {/*
	<var (langs = hash.values || {'':hash.value})>
	<for (var lang in langs)>
		<div class="wysy lang{$lang}" ui="Wysiwyg">
			<div class="wysyMenu">
				<span uiWysy="bold"><b>B</b></span>
				<span uiWysy="underline"><u>U</u></span>
				<span uiWysy="italic"><i>I</i></span>
			</div>
			<div class="wysyBody" uiWysy="content">
				<textarea name="{$hash.name+AI_Tools.name(lang)}" uiWysy="value" class="hidden">{$Unit.html(langs[lang] || '')}</textarea>
			</div>
		</div>
	<endfor>
*/};

AI_Templates.listPaginator = function() {/*
	<if (hash.found)>
				
		<var (lmt = hash.limit)>
		<var (all = Math.ceil(hash.found / lmt))>
		<var (cur = Math.ceil(hash.index / lmt))>
		<var (min = 0)>
		<var (max = all)>
	
		<div class="paginator">
			<for (var i = 0; i < all; i++)>
				<span class="link" uiPagin="{$i*lmt}">{$i + 1}</span>
			<endfor>
		</div>
		
	<endif>
*/};

AI_Templates.simpleList = function() {/*
	<div class="head">
		<if (hash.collapse)>
			<span class="collapsable" ui="Trigger" uiTarget="{$hash.collapse}">{$hash.title}</span>
		<else>
			<span>{$hash.title}</span>
		<endif>
		<if (hash.actions && this.checkAccess(this.ACCESS.EDIT))>
			<var (actions = hash.actions instanceof Array ? hash.actions : [hash.actions])>
			<for (var i in actions)>
				<input type="button" onclick="{$actions[i].action}" value="{$actions[i].title}" class="miniButton" />
			<endif>
		<endif>
	</div>
	
	<div id="{$hash.collapse || ''}">
	
		<if (hash.filters)>
			<div class="body" ui="Table" uiOptions="{$Unit.stringify(hash.filters)}" uiAction="AI.go('{$this.name}','list',Unit.stringify(this.options))">
		<else>
			<div class="body">
		<endif>
			
			<if (hash.records && Unit.toArray(hash.records).length)>
				
				<if (hash.message)>
					<div class="message">{$hash.message}</div>
				<endif>
				
				<if (hash.todo && Unit.toArray(hash.todo).length)>
				
					<for (var i in hash.todo)>
						<div class="field30">{$hash.todo[i]}</div>
					<endfor>
				
				<endif>
			
				<table class="table">
				
					<if ( ! hash.hideHead)>
						<tr>
							<for (var c in hash.columns)>
								<var (column = hash.columns[c])>
								<th class="{$column.style || ''}">{$column.label || ''}</th>
							<endfor>
						</tr>
					<endif>
					
					<for (var i in hash.records)>
						<var (item = hash.records[i])>
						
						<tr id="{$hash.name||this.name}List{$i}" {$item.active === '0' ? ' class="inactive" title="Запись не активна"' : ''}>
							<for (var c in hash.columns)>
								<var (column = hash.columns[c])>
								<td class="{$column.style || ''}">
									<%=
										column.field instanceof Function?
											column.field.call(this, i, item):
											Unit.templater(column.field || '', {hash:hash,item:item}, this)
									%>
								</td>
							<endfor>
						</tr>
						
					<endfor>
					
				</table>
				
				<if (hash.paginator)>
					<%= AI.template('listPaginator', hash.paginator, this) %>
				<endif>
	
			<else>
				<div class="message">{$hash.messageEmpty || this.getMessage('EMPTY_LIST')}</div>
			<endif>
			
		</div>
		
		{$hash.footer || ''}
		
	</div>
*/};

AI_Templates.simpleForm = function() {/*
	<div class="head">
		<if (hash.collapse)>
			<span class="collapsable" ui="Trigger" uiTarget="{$hash.collapse}">{$hash.title}</span>
		<else>
			<span>{$hash.title}</span>
		<endif>
		
		<if (hash.multilang)>
			<ul ui="Menu" uiAction="Unit.setClassValue('{$hash.multilang}','selectedLang',this.selected)" class="langSelector" uiSelected="ru">
				<li uiMenuOption="ru">Русский</li>
				<li uiMenuOption="en">Английский</li>
			</ul>
		<endif>
	</div>
	
	<div id="{$hash.collapse || ''}">
	
		<div class="body">
		
			<if (hash.message)>
				<div uiForm="message" class="message">{$hash.message}</div>
			<endif>
	
			<for (var name in hash.fields)>
				<div class="{$name}">
				
					<for (var i in hash.fields[name])>
						{$hash.fields[name][i]}
					<endfor>
				
				</div>
			<endfor>
			
			<if (hash.required)>
				<p class="formRequiredLabel">{$this.messages.FORM_REQUIRED}</p>
			<endif>
		
		</div>
		
		{$hash.footer || ''}
		
	</div>
*/};

AI_Templates.simpleFilters = function() {/*
	<form class="form" ui="Form" uiControl="{$this.module}.controlFilter">
		
		<input type="hidden" name="index" value="{$hash.filters.index || 0}" >
		<input type="hidden" name="limit" value="{$hash.filters.limit || 100}" >
		<input type="hidden" name="order" value="{$hash.filters.order || ''}" >
		<input type="hidden" name="drect" value="{$hash.filters.drect || ''}" >
		
		<%= AI.template('simpleForm', {
			title:		'Фильтр записей',
			collapse:	'listFilters',
			fields:		hash.fields,
			footer: AI.template('simpleActions', [
				AI_Components.tableActionButton('ПРИМЕНИТЬ ФИЛЬТР', null, null, 'submit'),
				AI_Components.tableActionButton('СБРОСИТЬ ФИЛЬТР', "AI.go('" + this.name + "','list')",'gray')
			])
		}, this) %>
		
	</form>
*/};

AI_Templates.collapseForm = function() {/*
	<div class="head">
		<if (hash.collapse)>
			<span class="collapsable" ui="Trigger" uiTarget="{$hash.collapse}">{$hash.title}</span>
		<else>
			<span>{$hash.title}</span>
		<endif>
		<if (hash.action && this.checkAccess(this.ACCESS.EDIT))>
			<input type="button" onclick="{$hash.action.action}" value="{$hash.action.title}" class="miniButton" />
		<endif>
	</div>
	
	<div id="{$hash.collapse || ''}">
	
		<div class="body">
		
			<if (hash.message)>
				<div uiForm="message" class="message">{$hash.message}</div>
			<endif>
	
			{$hash.content || ''}
		
		</div>
		
		{$hash.footer || ''}
		
	</div>
*/};

AI_Templates.simpleActions = function() {/*
	<div class="actions">
		<for (var i in hash)>
			<var (item = hash[i])>
			<input type="{$item.type || 'button'}" value="{$item.title || ''}" onclick="{$item.action || ''}" class="bigButton {$item.style || ''}">
		<endfor>
	</div>
*/};

/*******************************************************************************

	CONFIGURATION MODULE ELEMENTS

*******************************************************************************/

AI_Templates.configList = function() {/*
	<div class="content">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
		
			<!--<div uiForm="message" class="message">{$this.getMessage('MESSAGE')}</div>-->
			
			<var (groups = this.getGroups())>
			<for (var i in groups)>
				<var (group = groups[i])>
				<%= AI.template('simpleList', {
					title:		group.title,
					records:	group.fields,
					collapse: 	'configEdit' + i,
					hideHead:	true,
					columns: [{
							label:	'Параметр',
							style:	'cell40',
							field:	function(i, item) {
								return typeof(item) == 'string' ? item : item.title;
							}
						},{
							label:	'Значение',
							style:	'cell50',
							field:	function(i, item) {
								if (typeof(item) == 'string') {
									return AI.template('formTextItem', {
										id:				this.module + 'Param' + i,
										name:			i,
										value:			(hash.config[i] || {}).value,
										disabled:		this.isLocked(i)
									});
								} else {
									return AI.template('formTextareaItem', {
										id:				this.module + 'Param' + i,
										name:			i,
										value:			(hash.config[i] || {}).value,
										disabled:		this.isLocked(i)
									});
								}
							}
						},{}
					]
				}, this) %>
			<endfor>
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit')
			]) %>
		
		</form>
	</div>
*/};

/*******************************************************************************

	DASHBOARD MODULE ELEMENTS

*******************************************************************************/

AI_Templates.dashboardList = function() {/*
	<div class="content">
		
		<if (hash.order)>
			<var (positions = Unit.group(hash.order.positions || {}, 'orderId'))>
			<var (products = hash.order.products || {})>
			<var (accounts = hash.order.accounts || {})>
			<var (attributes = hash.order.attributes || {})>
			
			<%= AI.template('simpleList', {
				title:		'Новые заказы',
				records:	hash.order.records,
				collapse: 	'dashboardOrder',
				messageEmpty:	'В данный момент нет новых заказов.',
				columns:	[{
						label:	'ID / Заказчик / Адрес',
						style:	'cell30',
						field:	function(i, item) {
							var user = accounts[item.accountId];
							var address = [];
							if (item.country) address.push(item.country);
							if (item.state) address.push(item.state);
							if (item.city) address.push(item.city);
							if (item.address) address.push(item.address);
							if (item.post) address.push(item.post);
							if (item.store) address.push(item.store);
							if (item.train) address.push(item.train);
							if (item.station) address.push(item.station);
							if (user) {
								var userdata = '<a href="' + AI.link('account','edit',item.accountId) + '">' + user.name + '&nbsp;' + user.lastname + '</a>' +
								' &lt;<a href="mailto:' + user.email + '">' + user.email + '</a>&gt;<br>';
							}
							else {
								var userdata = '<i>Пользователь был удалён.</i>';
							}
							return '<small class="gray">' + item.id + '.</small> ' +
								userdata+
								address.join(', ');
						}
					},
					{
						label:	'Позиции заказа',
						field:	function(i, item) {
							var itemPositions = positions[item.id];
							if ( ! itemPositions) return 'В заказе нет товаров.';
							var result = [];
							var total = 0;
							for (var j in itemPositions) {
								var pos = itemPositions[j] || {};
								var pro = products[ pos.productId ];
								if (pos && pro) {
									result.push(
										'<a href="' + AI_Product.getLink('edit',pro.id) + '">' + pro.title + ' / ' +
										(attributes[pos.sizeId] || '') + ' / ' + (attributes[pos.colorId] || '') + '</a> ' +
										pos.amount + ' x ' + pos.price + ' = ' + Unit.price(pos.amount * pos.price) + ' грн.'+
										(parseInt(pos.discount) > 0 ? ' <span class="gray">(со скидкой ' + Unit.price(AI_Tools.discount(pos.price,pos.discount)*pos.amount) + ' грн.)</span>' : '')
									);
									total += pos.amount * pos.price;
								}
							}
							result.push('<b>Общая сумма заказа: <big>' + Unit.price(total) + '</big> грн.</b>');
							if (item.priceDiscounted > 0 && item.priceDiscounted  != item.price) {
								result.push('<b>Сумма заказа со скидкой: <big>' + Unit.price(item.priceDiscounted) + '</big> грн.</b><br>'+
											'<b>Сумма скидки: <big>' + Unit.price(item.price - item.priceDiscounted) + ' грн.</big></b>');
							} 
							return result.join('<br>');
						}
					},
					AI_Components.tableOptionsColumn('Статус','cell10', 'status', AI_Order.getOrderStatuses()),
					{
						style: 'rowActions cell10',
						field: function(i, item) {
							return '<a href="' + AI_Order.getLink('edit',item.id) + '">просмотреть</a>';
						}
					}
				]
			}, this) %>
		<endif>
		
		<if (hash.prodComment)>
			<var (products = hash.prodComment.products || {})>
			
			<%= AI.template('simpleList', {
				title:		'Новые комментарии',
				records:	hash.prodComment.records,
				collapse: 	'dashboardComment',
				name:		'dashboardComment',
				messageEmpty:	'В данный момент нет новых комментариев.',
				columns:	[{
						label:	'ID / Автор Комментария / Товар',
						style:	'cell40',
						field:	function(i, item) {
							var product = products[item.productId] || {};
							return '<small class="gray">' + item.id + '.</small> ' +
								item.authorName + (item.authorMail ? ' &lt;<a href="mailto:' + item.authorMail + '">' + item.authorMail + '</a>&gt;' : '') +
								'<br><a href="' + AI.link('product','edit',item.productId) + '">' + product.title + '</a>';
						}
					},
					{
						label:	'Текст комментария',
						field:	function(i, item) {
							return item.message;
						}
					},
					{
						style: 'rowActions cell20',
						field: function(i, item) {
							return '<div ui="Menu">'+ 
								'<span uiMenuOption="1" class="link" onclick="' + AI_Product.module + '.action(\'confirmComment\',{id:' + item.id + ',active:1});Unit.trigger(\'dashboardCommentList'+i+'\');">подтвердить</span>'+
								' | '+
								'<span uiMenuOption="0" class="link" onclick="' + AI_Product.module + '.action(\'confirmComment\',{id:' + item.id + ',active:0});Unit.trigger(\'dashboardCommentList'+i+'\');">отклонить</span>'+
								'</div>';
						}
					}
				]
			}, this) %>
		<endif>
		
		<if (hash.siteComment)>
			<%= AI.template('simpleList', {
				title:		'Новые отзывы о сайте',
				records:	hash.siteComment.records,
				collapse: 	'dashboardSiteComment',
				name:		'dashboardSiteComment',
				messageEmpty:	'В данный момент нет новых отзывов.',
				columns:	[{
						label:	'ID / Автор отзыва',
						style:	'cell40',
						field:	function(i, item) {
							return '<small class="gray">' + item.id + '.</small> ' +
								item.name + (item.email ? ' &lt;<a href="mailto:' + item.email + '">' + item.email + '</a>&gt;' : '');
						}
					},
					{
						label:	'Текст отзыва',
						field:	function(i, item) {
							return item.message;
						}
					},
					{
						style: 'rowActions cell20',
						field: function(i, item) {
							return '<div ui="Menu">'+ 
								'<span uiMenuOption="1" class="link" onclick="' + AI_Comment.module + '.action(\'confirmComment\',{id:' + item.id + ',active:1});Unit.trigger(\'dashboardCommentList'+i+'\');">подтвердить</span>'+
								' | '+
								'<span uiMenuOption="0" class="link" onclick="' + AI_Comment.module + '.action(\'confirmComment\',{id:' + item.id + ',active:0});Unit.trigger(\'dashboardCommentList'+i+'\');">отклонить</span>'+
								'</div>';
						}
					}
				]
			}, this) %>
		<endif>
		
	</div>
*/};

/*******************************************************************************

	STATISTIC MODULE ELEMENTS

*******************************************************************************/

AI_Templates.statisticList = function() {/*
	<div class="content">
		
		<var (info = this.info || {})>
		
		<div class="head">
			Статистика просмотров
			
			<div class="form">
				<div class="headSelect">
					<%= AI.template('formSelectItem', {
						id:				this.module + 'Dropdown',
						name:			'domain',
						value:			info.domain || '',
						className:		'inlineSelect',
						action:			"AI.go('" + this.name + "','list','domain='+this.selected+'&period=" + (info.period || 1) + "')",
						options:		{'':'Все домены',1:'project.ru',2:'project.com.ua',3:'project.co.uk'}
					}) %>
				</div>
				
				<div class="headSelect">
					<%= AI.template('formSelectItem', {
						id:				this.module + 'Dropdown',
						name:			'period',
						value:			info.period || 1,
						className:		'inlineSelect',
						action:			"AI.go('" + this.name + "','list','domain=" + (info.domain || '') + "&period='+this.selected)",
						options:		{1:'За сутки',7:'За неделю',30:'За месяц',365:'За год'}
					}) %>
				</div>
			</div>
			
		</div>
		
		<div class="body">
			<div style="width:940px">
				<canvas id="statisticCanvas" height="440" width="940" ui="Statistic" uiData="{$Unit.stringify(hash.statAll)}" uiPeriod="{$info.period}"></canvas>
			</div>
		</div>
		
		<div class="barsInfo">
			<div><span style="background-color: rgba(151,187,205,1)"></span> - Уникальные посетители</div>
			<div><span style="background-color: rgba(220,220,220,1)"></span> - Просмотры страниц</div>
		</div>
		
	</div>
*/};

/*******************************************************************************

	ADMINISTRATION MODULE ELEMENTS

*******************************************************************************/

AI_Templates.adminNavigation = function() {/*
	<% if ( ! hash.id) return ''; %>
	
	<dl>
		<dt>
			<div>
				<a href="#dashboard" class="active">Информпанель</a>
			</div>
			
			<div>
				<a href="#order">Магазин</a>
				<ul>
					<li><a href="#order">Заказы</a></li>
					<li><a href="#product">Товары</a></li>
					<li><a href="#productType">Типы товаров</a></li>
					<li><a href="#productParam">Параметры товаров</a></li>
					<li><a href="#productCategory">Категории товаров</a></li>
					<li><a href="#productSupplier">Поставщики товаров</a></li>
					<li><a href="#productImport">Импортирование товаров</a></li>
					<li><a href="#productExport">Экспортирование фотографий товаров</a></li>
					<li><a href="#productExportXls">Экспортирование товаров для prom.ua</a></li>
				</ul>
			</div> 
			
			<div>
				<a href="#page">Содержимое</a>
				<ul>
					<li><a href="#page">Разделы</a></li>
					<li><a href="#menu">Меню</a></li>
					<li><a href="#comment">Отзывы о сайте</a></li>
					<li><a href="#banner">Рекламные баннеры</a></li>
				</ul>
			</div>
			
			<div>
				<a href="#account">Учетные записи</a>
				<ul>
					<li><a href="#admin">Администраторы</a></li>
					<li><a href="#account">Клиенты</a></li>
					<li><a href="#subscribe">Подписчики</a></li>
				</ul>
			</div>
			
			<div>
				<a href="#config">Настройки</a>
			</div>
			
			<div>
				<a href="#statistic">Статистика</a>
			</div>
			
			<!--<div>
				<a href="#help">Помощь</a>
			</div>-->
		</dt>
		<dd>
			Привет, <b>{$hash.login}</b>
			<input type="button" value="выйти" class="miniButton" onclick="AI_Admin.logout()">
		</dd>
	</dl>
*/};

AI_Templates.adminList = function() {/*
	<div class="content">
	
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Login',
						name:			'where[login like]',
						title:			'Логин',
						value:			filters['login like'],
						description:	'Поиск администраторов по имени учётной записи.'
					})
				],
				
				'center field30': [
					AI.template('formSelectField', {
						id:				this.module + 'Active',
						name:			'where[active]',
						title:			'Состояние',
						value:			filters.active,
						description:	'Поиск среди активных или неактивных администраторов.',
						options: {
							'': 'Любое состояние',
							' 0': 'Не активен',
							' 1': 'Активен'
						}
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата добавления',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск администраторов добавленных в диапазоне дат.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="login">Логин</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.login;
					}
				},{
					label:	'Права доступа',
					field:	function(i, item) {
						var access = [], p, m;
						for (var j in AI.modules) {
							 if ((p = item['permissions_' + j]) > 0 && (m = AI.module(j))) {
							 	access.push(AI.modules[j] + ': <b>' + m.access[p] + '</b>')
							 }
						}
						return access.length ? access.join('<br>') : '<i>Нет доступа</i>';
					}
				},
				AI_Components.tableDateColumn('Добавлен','cell10', 'added', true),
				AI_Components.tableActionsColumn()
			]
		}, this) %>
	</div>
*/};

AI_Templates.adminEdit = function() {/*
	<div class="content">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
			
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				fields: {
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Login',
							name:			'login',
							title:			'Логин',
							value:			form.login,
							length:			30,
							description:	'Имя учётной записи для авторизации в панели управления.'
						})
					],
					
					'center field30': [
						AI.template('formTextField', {
							id:				this.module + 'Password',
							name:			'password',
							type:			'password',
							title:			'Пароль',
							value:			form.password,
							length:			30,
							disabled:		form.id ? true : false,
							configurable:	form.id ? true : false,
							description:	'Пароль для авторизации в панели управления.'
						})
					],
					
					'right field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Active',
							name:			'active',
							title:			'Состояние',
							value:			form.active,
							disabled:		this.isLocked(form.id),
							description:	'Включение/отключение доступа к панели управления.',
							options: {
								0: 'Не активен',
								1: 'Активен'
							}
						})
					]
				}
			}, this) %>
			
			<%= AI.template('simpleList', {
				title:		'Управление правами доступа',
				records:	AI.modules,
				collapse: 	'adminPermissions',
				columns: [{
						label:	'Название модуля',
						style:	'cell30',
						field:	function(i, item) {
							return item;
						}
					},{
						label:	'Права доступа',
						style:	'cell40',
						field:	function(i, item) {
							return AI.template('formSelectItem', {
								id:				this.module + 'Permissions' + i,
								name:			'permissions_' + i,
								value:			form['permissions_' + i],
								disabled:		this.isLocked(form.id),
								options:		AI.module(i).access
							})
						}
					},{}
				]
			}, this) %>
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	ACCOUNTS MODULE

*******************************************************************************/
				
AI_Templates.accountList = function() {/*
	<div class="content">
		
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Email',
						name:			'where[email like]',
						title:			'E-mail',
						value:			filters['email like'],
						description:	'Поиск клиентов по адресу почтового ящика.'
					}),
					AI.template('formTextField', {
						id:				this.module + 'Name',
						name:			'where[name like]',
						title:			'Имя',
						value:			filters['name like'],
						description:	'Поиск клиентов по имени.'
					})
				],
				
				'center field30': [
					AI.template('formSelectField', {
						id:				this.module + 'Active',
						name:			'where[active]',
						title:			'Состояние',
						value:			filters.active,
						description:	'Поиск подтвержденных и неподтвержденных клиентов.',
						options: {
							'': 'Любое состояние',
							' 0': 'Не подтвержден',
							' 1': 'Подтвержден'
						}
					}),
					AI.template('formTextField', {
						id:				this.module + 'LastName',
						name:			'where[lastname like]',
						title:			'Фамилия',
						value:			filters['lastname like'],
						description:	'Поиск клиентов по фамилии.'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата регистрации',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск клиентов зарегистрированных в диапазоне дат.'
					}),
					AI.template('formTextField', {
						id:				this.module + 'Phone',
						name:			'where[phone like]',
						title:			'Телефон',
						value:			filters['phone like'],
						description:	'Поиск клиентов по номеру телефона.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / '+
							'<span class="link" uiOrder="name">Имя</span> / '+
							'<span class="link" uiOrder="lastname">Фамилия</span> / '+
							'<span class="link" uiOrder="email">E-mail</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.name + '&nbsp;' + item.lastname + ' &lt;<a href="mailto:' + item.email + '">' + item.email + '</a>&gt;';
					}
				},
				AI_Components.tableSimpleColumn('Телефон','cell10', 'phone', true),
				{
					label: '<span class="link" uiOrder="ordersCount" title="Количество заказов">Колич.</span> / <span class="link" uiOrder="ordersPrice">Сумма заказов</span>',
					style: 'cell20',
					field: function(i,item) {
						return item.ordersCount + ' / ' + Unit.price(item.ordersPrice) + ' грн.';
					}
				},
				AI_Components.tableDateColumn('Регистрация','cell10', 'added', true),
				AI_Components.tableActionsColumn()
			]
		}, this) %>
	</div>
*/};

AI_Templates.accountEdit = function() {/*
	<div class="content">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
			
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				fields: {
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Email',
							name:			'email',
							title:			'E-mail',
							value:			form.email,
							length:			100,
							description:	'Адрес электронной почты клиента.'
						}),
						AI.template('formTextField', {
							id:				this.module + 'Name',
							name:			'name',
							title:			'Имя',
							value:			form.name,
							length:			30,
							description:	'Имя клиента используется при оформлении заказа.'
						}),
						AI.template('formStaticField', {
							title:			'Дата регистрации',
							value:			AI_Tools.fullDate(form.added),
							description:	'Дата регистрации клиента на сайте.'
						})
					],
					
					'center field30': [
						AI.template('formTextField', {
							id:				this.module + 'Password',
							name:			'password',
							type:			'password',
							title:			'Пароль',
							value:			form.password,
							length:			30,
							disabled:		form.id ? true : false,
							configurable:	form.id ? true : false,
							description:	'Пароль для авторизации в личном кабинете.'
						}),
						AI.template('formTextField', {
							id:				this.module + 'LastName',
							name:			'lastname',
							title:			'Фамилия',
							value:			form.lastname,
							length:			30,
							description:	'Фамилия клиента используется при оформлении заказа.'
						}),
						AI.template('formStaticField', {
							title:			'Дата последней авторизации',
							value:			AI_Tools.fullDate(form.logged),
							description:	'Дата последнего входа клиента на сайт.'
						})
					],
					
					'right field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Active',
							name:			'active',
							title:			'Состояние',
							value:			form.active,
							disabled:		this.isLocked(form.id),
							description:	'Статус подтверждения учётной записи клиента.',
							options: {
								0: 'Не подтверждён',
								1: 'Подтверждён'
							}
						}),
						AI.template('formTextField', {
							id:				this.module + 'Phone',
							name:			'phone',
							title:			'Телефон',
							value:			form.phone,
							length:			20,
							description:	'Контактный номер телефона клиента.'
						}),
						AI.template('formStaticField', {
							title:			'Дата последнего заказа',
							value:			AI_Tools.fullDate(form.ordersDate),
							description:	'Дата совершения последнего заказа клиентом.'
						})
					]
				}
			}, this) %>
			
			<%= AI.template('simpleList', {
				title:		'Заказы клиента',
				records:	hash.orders,
				collapse:	'accountOrders',
				columns: [{
						label:	'ID / Адрес доставки',
						field:	function(i, item) {
							return '<small class="gray">' + item.id + '.</small> '+
								AI_Order.getCountries()[item.country] + ', ' + item.city + ', ' + item.address;
						}
					},
					AI_Components.tableDateColumn('Дата заказа','cell10', 'added'),
					AI_Components.tablePriceColumn('Сумма заказа','cell10', 'price'),
					AI_Components.tableOptionsColumn('Статус','cell10', 'status', AI_Order.getOrderStatuses()),
					{
						style: 'cell10 rowActions',
						field: function(i, item) {
							return '<a href="' + AI.link('order','edit',item.id) + '">просомтреть</a>'
						}
					}
				]
			}, this) %>
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	SUBSCRIBE MODULE

*******************************************************************************/

AI_Templates.subscribeList = function() {/*
	<div class="content">
		
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Email',
						name:			'where[email like]',
						title:			'E-mail',
						value:			filters['email like'],
						description:	'Поиск подписчиков по адресу почтового ящика.'
					})
				],
				
				'center field30': [
					AI.template('formTextField', {
						id:				this.module + 'Name',
						name:			'where[name like]',
						title:			'Имя',
						value:			filters['name like'],
						description:	'Поиск подписчиков по имени.'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата подписки',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск подписчиков добавленных в диапазоне дат.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / '+
							'<span class="link" uiOrder="name">Имя</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.name;
					}
				},
				AI_Components.tableSimpleColumn('E-mail','cell40', 'email', true),
				AI_Components.tableDateColumn('Добавлен','cell10', 'added', true),
				{
					label:	'',
					style:	'cell10 rowActions',
					field:	function(i, item) {
						return '<span class="link warning" onclick="' + this.module + '.action(\'drop\',\'' + item.id + '\')">удалить</a>';
					}
				}
			]
		}, this) %>
	</div>
*/};

/*******************************************************************************

	COMMENT MODULE

*******************************************************************************/
				
AI_Templates.commentList = function() {/*
	<div class="content">
		
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Email',
						name:			'where[email like]',
						title:			'E-mail автора',
						value:			filters['email like'],
						description:	'Поиск отзывов по почтовому адресу автора.'
					})
				],
				
				'center field30': [
					AI.template('formTextField', {
						id:				this.module + 'Name',
						name:			'where[name like]',
						title:			'Имя автора',
						value:			filters['name like'],
						description:	'Поиск отзывов по имени автора.'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата добавления',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск отзывов добавленных в диапазоне дат.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / '+
							'<span class="link" uiOrder="name">Имя</span> / ' + 
							'<span class="link" uiOrder="email">E-mail</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.name + ' &lt;' + item.email + '&gt;';
					}
				},
				AI_Components.tableSimpleColumn('Сообщение','cell40', 'message'),
				AI_Components.tableSimpleColumn('Оценка','cell10', 'rating', true),
				AI_Components.tableDateColumn('Добавлен','cell10', 'added', true),
				AI_Components.tableActionsColumn()
			]
		}, this) %>
	</div>
*/};

AI_Templates.commentEdit = function() {/*
	<div class="content selectedLang-ru" id="recordEdit">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
				
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				multilang:	'recordEdit',
				fields: {
		
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Name',
							name:			'name',
							title:			'Имя автора',
							value:			form.name,
							length:			50,
							required:		true,
							description:	'Имя автора кооментария.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Email',
							name:			'email',
							title:			'E-mail автора',
							value:			form.email,
							length:			50,
							required:		true,
							description:	'Электронный почтовый адрес автора сообщения.'
						}),
						
						AI.template('formDateField', {
							id:				this.module + 'Added',
							name:			'added',
							title:			'Дата добавления',
							value:			form.added,
							description:	'Дата добавления отзыва отображается на странице отзывов.'
						})
					],
					
					'center field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Rating',
							name:			'rating',
							title:			'Оценка',
							value:			form.rating,
							description:	'Оценка сайта от одной до пяти звезд.',
							options: 		{1:'1',2:'2',3:'3',4:'4',5:'5'}
						}),
						
						AI.template('formAreaField', {
							id:				this.module + 'Message',
							name:			'message',
							title:			'Отзыв',
							value:			form.message,
							length:			500,
							description:	'Текст отзыва, отображаемый на сайте.'
						})
					],
					
					'right field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Active',
							name:			'active',
							title:			'Доступность',
							value:			form.active,
							description:	'Включение отображения отзыва на сайте.',
							options: 		{0:'Отзыв не виден на сайте',1:'Отзыв виден на сайте',2:'Отзыв не подтверждён'}
						}),
						
						AI.template('formAreaField', {
							id:				this.module + 'Answer',
							name:			'answer',
							title:			'Ответ менеджера',
							value:			form.answer,
							length:			500,
							description:	'Текст ответа менеджера на отзыв.'
						})
					]
				}
			}, this) %>
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PAGES MODULE

*******************************************************************************/

AI_Templates.pageList = function() {/*
	<div class="content">
	
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Url',
						name:			'where[url like]',
						title:			'Адрес',
						value:			filters['url like'],
						description:	'Поиск разделов по url адресу.'
					})
				],
				
				'center field30': [
					AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'where[titleRu like]',
						title:			'Название',
						value:			filters['titleRu like'],
						description:	'Поиск разделов по названию.'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата добавления',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск разделов добавленных в диапазоне дат.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="titleRu">Название</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.titleRu;
					}
				},
				AI_Components.tableSimpleColumn('Адрес','cell30', 'url', true),
				AI_Components.tableDateColumn('Добавлен','cell10', 'added', true),
				AI_Components.tableActionsColumn()
			]
		}, this) %>
	</div>
*/};

AI_Templates.pageEdit = function() {/*
	<div class="content selectedLang-ru" id="recordEdit">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
				
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				multilang:	'recordEdit',
				fields: {
		
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Url',
							name:			'url',
							title:			'Адрес',
							value:			form.url,
							length:			200,
							required:		true,
							disabled:		this.isLocked(form.id),
							description:	'Адрес раздела в адресной строке браузера.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Title',
							name:			'title',
							title:			'Название',
							values:			{ru:form.titleRu, en:form.titleEn},
							length:			200,
							required:		true,
							description:	'Название раздела отображается в заголовке страницы.'
						})
					],
					
					'center field30': [
						AI.template('formAreaField', {
							id:				this.module + 'MetaKeywords',
							name:			'metaKeywords',
							title:			'Meta тэги',
							values:			{ru:form.metaKeywordsRu, en:form.metaKeywordsEn},
							length:			250,
							description:	'Meta тэги раздела для сео и поисковых систем.'
						})
					],
					
					'right field30': [
						AI.template('formAreaField', {
							id:				this.module + 'MetaDescription',
							name:			'metaDescription',
							title:			'Meta описание',
							values:			{ru:form.metaDescriptionRu, en:form.metaDescriptionEn},
							length:			250,
							description:	'Meta описание раздела для сео и поисковых систем.'
						})
					]
				}
			}, this) %>
			
			<if ( ! this.isLocked(form.id))>
			<%= AI.template('collapseForm', {
				title:		'Содержимое раздела',
				collapse:	'pageEditContent',
				content:	AI.template('formWysiwyg', {name:'content', values:{ru:form.contentRu,en:form.contentEn}})
			}, this) %>
			<endif>
			
			<input type="hidden" name="active" value="1" />
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	MENUS MODULE

*******************************************************************************/

AI_Templates.menuList = function() {/*
	<var (list = (hash.list || {}))>
	<var (records = this.getGroupedMenus(hash.list))>
	
	<div class="content">
	
		<for (var i in records[0])>
			<var (menu = records[0][i])>
			<%= AI.template('simpleList', {
				title:		menu.titleRu,
				records:	records[menu.id] || [],
				collapse: 	'menuParentList' + i,
				actions:	AI_Components.tableActionButton('добавить меню',this.module + ".action('edit',{parentId:'" + menu.id + "'})"),
				columns: [
					AI_Components.tableSimpleColumn('Название',null,'titleRu'),
					AI_Components.tableSimpleColumn('Адрес','cell40','url'),
					AI_Components.tableActionsColumn()
				]
			}, this) %>
		<endfor>
		
	</div>
*/};

AI_Templates.menuEdit = function() {/*
	<var (form = hash.form || {})>
	
	<if (form.id)>
		<var (form = this.getMenuInfo(form.id))>
	<endif>
	
	<div class="popup popupOneRow selectedLang-ru" id="recordEdit">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
		
			<%= AI.template('simpleForm', {
				title:		form.id ? 'Редактирование' : 'Добавление записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				multilang:	'recordEdit',
				fields: {
		
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Url',
							name:			'url',
							title:			'Адрес',
							value:			form.url,
							length:			250,
							required:		true,
							description:	'Адрес открываемый при нажатии на ссылку.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Title',
							name:			'title',
							title:			'Название',
							values:			{ru:form.titleRu, en:form.titleEn},
							length:			100,
							required:		true,
							description:	'Название ссылки отображаемое в меню.'
						})
					],
				}
			}, this) %>
					
			<div class="actions">
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				<if (form.parentId)>
					<input type="hidden" name="parentId" value="{$form.parentId}" />
				<endif>
				
				<input type="submit" value="{$form.id?'СОХРАНИТЬ':'ДОБАВИТЬ'}" class="bigButton">
				<input type="button" value="{$form.id?'ОТМЕНА':'ЗАКРЫТЬ'}" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

/*******************************************************************************

	BANNERS MODULE

*******************************************************************************/

AI_Templates.bannerList = function() {/*
	<var (list = (hash.list || {}))>
	<var (records = this.getGroupedBanners(hash.list.records))>
	<var (groups = {0:'Баннеры 980x422', 1:'Баннеры 317x198'})>
	
	<div class="content">
	
		<for (var i in groups)>
			<var (item = records[i])>
			<%= AI.template('simpleList', {
				title:		groups[i],
				records:	records[i] || [],
				collapse: 	'menuParentList' + i,
				actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit//" + i + "')"),
				columns: [
					AI_Components.tableSimpleColumn('Название',null,'title'),
					AI_Components.tableSimpleColumn('Ссылка','cell40','link'),
					AI_Components.tableActionsColumn()
				]
			}, this) %>
		<endfor>
		
	</div>
*/};

AI_Templates.bannerEdit = function() {/*
	<div class="content selectedLang-ru" id="recordEdit">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
				
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				fields: {
		
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Title',
							name:			'title',
							title:			'Название',
							value:			form.title,
							length:			100,
							required:		true,
							description:	'Название баннера отображается только в администрировании.'
						})
					],
					
					'center field30': [
						AI.template('formTextField', {
							id:				this.module + 'Link',
							name:			'link',
							title:			'Ссылка',
							value:			form.link,
							length:			100,
							required:		true,
							description:	'Ссылка баннера.'
						})
					],
					
					'right field30': [
						AI.template('formImageField', {
							id:				this.module + 'Image',
							name:			'image',
							title:			'Изображение',
							value:			form.image,
							length:			100,
							required:		true,
							description:	'Изображение баннера.'
						}, this)
					]
				}
			}, this) %>
			
			<if ( ! this.isLocked(form.id))>
			<%= AI.template('collapseForm', {
				title:		'Текст баннера',
				collapse:	'bannerEditText',
				content:	AI.template('formWysiwyg', {name:'text', value:form.text})
			}, this) %>
			<endif>
			
			<input type="hidden" name="active" value="1" />
			<input type="hidden" name="type" value="{$form.type || AI_Tools.hash().split('/')[3]}" />
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	ORDERS MODULE

*******************************************************************************/

AI_Templates.orderList = function() {/*
	<div class="content">
		
		<var (filters = (hash.list.filters || {}).where || {})>
		
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formSelectField', {
						id:				this.module + 'Country',
						name:			'where[country]',
						title:			'Страна',
						value:			filters['country'] || '',
						description:	'Поиск заказов по стране доставки.',
						options:		this.getCountries(),
						empty:			'Любая страна'
					})
				],
				
				'center field30': [
					AI.template('formSelectField', {
						id:				this.module + 'Status',
						name:			'where[status]',
						title:			'Статус',
						value:			filters.status || '',
						description:	'Поиск заказов по статусу.',
						options: 		this.getOrderStatuses(),
						empty:			'Любой статус'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата оформления',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск заказов по дате оформления.'
					})
				]
			}
		}, this) %>
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / Заказчик / Адрес',
					field:	function(i, item) {
						var user = (hash.list.accounts || {})[item.accountId];
						var address = [];
						if (item.country) address.push(item.country);
						if (item.state) address.push(item.state);
						if (item.city) address.push(item.city);
						if (item.address) address.push(item.address);
						if (item.post) address.push(item.post);
						if (item.store) address.push(item.store);
						if (item.train) address.push(item.train);
						if (item.station) address.push(item.station);
						if (user) {
							var userdata = '<a href="' + AI.link('account','edit',item.accountId) + '">' + user.name + '&nbsp;' + user.lastname +'</a>' +
								' &lt;<a href="mailto:' + user.email + '">' + user.email + '</a>&gt;<br>';
						}
						else {
							var userdata = '<i>Пользователь был удалён</i>';
						}
						
						return '<small class="gray">' + item.id + '.</small> ' +
							userdata+
							address.join(', ');
					}
				},
				AI_Components.tablePriceColumn('Сумма','cell10', 'price',true),
				AI_Components.tableDateColumn('Добавлен','cell10', 'added',true),
				AI_Components.tableDateColumn('Изменен','cell10', 'edited',true),
				AI_Components.tableOptionsColumn('Статус','cell10', 'status', this.getOrderStatuses()),
				AI_Components.tableActionsColumn()
			]
		}, this) %>
	</div>
*/};

AI_Templates.orderEdit = function() {/*
	<div class="content">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
			
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Информация о заказе',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				fields: {
					'left field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Status',
							name:			'status',
							title:			'Статус заказа',
							value:			form.status,
							description:	'Текущее состояние заказа.',
							options: 		this.getOrderStatuses()
						}),
						AI.template('formSelectField', {
							id:				this.module + 'CountryId',
							name:			'countryId',
							title:			'Страна доставки',
							value:			form.countryId,
							description:	'В какую страну необходимо отправить заказ.',
							options: 		this.getCountries()
						}),
						AI.template('formTextField', {
							id:				this.module + 'Store',
							name:			'store',
							title:			'Склад Новой Почты',
							value:			form.store,
							length:			50,
							description:	'Номер склада при доставке Новой Почтой.'
						}),
						AI.template('formSelectField', {
							id:				this.module + 'ExpressType',
							name:			'expressType',
							title:			'Тип Экспресс доставки',
							value:			form.expressType,
							description:	'Метод экспресс доставки.',
							options: 		this.getExpressTypes()
						})
					],
					
					'center field30': [
						AI.template('formSelectField', {
							id:				this.module + 'PaymentType',
							name:			'paymentType',
							title:			'Метод оплаты',
							value:			form.paymentType,
							description:	'Каким методом осуществляется оплата заказа.',
							options: 		this.getPaymentTypes()
						}),
						AI.template('formTextField', {
							id:				this.module + 'City',
							name:			'city',
							title:			'Город доставки',
							value:			form.city,
							length:			30,
							description:	'В какой город необходимо отправить заказ.'
						}),
						AI.template('formTextField', {
							id:				this.module + 'Train',
							name:			'train',
							title:			'Номер поезда',
							value:			form.train,
							length:			50,
							description:	'Номер поезда или направления при доставке поездом.'
						}),
						AI.template('formTextField', {
							id:				this.module + 'Passport',
							name:			'passport',
							title:			'Паспортные данные',
							value:			form.passport,
							length:			200,
							description:	'Паспортные данные для доставки ТК "Деловые Линии".'
						})
					],
					
					'right field30': [
						AI.template('formSelectField', {
							id:				this.module + 'DeliveryType',
							name:			'deliveryType',
							title:			'Метод доставки',
							value:			form.deliveryType,
							description:	'Каким методом осуществляется доставка заказа.',
							options: 		this.getDeliveryTypes()
						}),
						AI.template('formTextField', {
							id:				this.module + 'Address',
							name:			'address',
							title:			'Адрес доставки',
							value:			form.address,
							length:			200,
							description:	'По какому адресу необходимо отправить заказ.'
						}),
						AI.template('formTextField', {
							id:				this.module + 'Station',
							name:			'station',
							title:			'Название станции',
							value:			form.station,
							length:			50,
							description:	'Название станции для доставки поездом.'
						})
					]
				}
			}, this) %>
			
			<if (form.account)>
			
				<%= AI.template('simpleForm', {
					title:		'Информация о заказчике',
					collapse:	'orderAccountInfo',
					fields: {
						'left field30': [
							AI.template('formStaticField', {
								title:			'E-mail',
								value:			form.account.email,
								description:	'Адрес электронной почты клиента.'
							})
						],
						
						'center field30': [
							AI.template('formStaticField', {
								title:			'Имя и Фамилия',
								value:			form.account.name + ' ' + form.account.lastname,
								description:	'Имя и фамилия клиента указанные при регистрации.'
							})
						],
						
						'right field30': [
						AI.template('formStaticField', {
								title:			'Телефон',
								value:			form.account.phone,
								description:	'Контактный номер телефона клиента.'
							})
						]
					}
				}, this) %>
				
			<endif>
			
			<%= AI.template('simpleList', {
				title:		'Позиции заказа',
				records:	form.positions,
				collapse:	'orderPositionsList',
				columns: [{
						label:	'Товар',
						field:	function(i, item) {
							var prod = (form.products || {})[item.productId] || {};
							var attr = (form.attributes || {});
							return '<a href="' + AI.link('product','edit',item.productId) + '">' + prod.title + '</a>'+
								'<br>Размер: <u>' + (attr[item.sizeId] || '') + '</u>, цвет: <u>' + (attr[item.colorId] || '') + '</u>';
						}
					},
					AI_Components.tableInputColumn('Количество','cell10', 'amount', this.module + ".action('productAmount',{id:'%id',order:'%orderId',amount:this.value});Unit.inner('productAmount%id',Unit.price(AI_Tools.discount('%price','%discount')*this.value)+' грн.')"),
					AI_Components.tablePriceColumn('Закупка','cell10', 'priceBought'),
					AI_Components.tablePriceColumn('Продажа','cell10', 'price'),
					{
						label: 'Скидка',
						style: 'cell10',
						field: function(i, item) {
							return '<span id="productDiscount' + item.id + '">' + (item.discount.indexOf('%') > 0 ? item.discount : Unit.price(item.discount)+' грн.') + '</span>';
						}
					},
					{
						label: 'Сумма',
						style: 'cell10',
						field: function(i, item) {
							return '<span id="productAmount' + item.id + '">' + Unit.price(item.amount * AI_Tools.discount(item.price, item.discount)) + ' грн.</span>';
						}
					},
					AI_Components.tableDropdownColumn('Статус','cell10', 'status', this.getProductStatuses(), this.module + ".action('productStatus',{id:'%id',order:'%orderId',status:this.selected})"),
					{
						style: 'cell10 rowActions',
						field: function(i, item) {
							return '<span class="link warning" onclick="' + this.module + '.action(\'dropProduct\',{id:\'' + item.id + '\',order:\'' + item.orderId + '\'});Unit.remove(\'' + this.name + 'List' + i + '\');">удалить</span>';
						}
					}
				],
				footer: '<div id="orderOverall">' + this.getTemplate('EditOverall', {form:form}) + '</div>'
			}, this) %>
						
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

AI_Templates.orderEditOverall = function() {/*
	<div class="orderOverall" id="orderOverallContent">
		
		<if (form.price != form.priceDiscounted && form.priceDiscounted > 0)>
			<p>
				Сумма заказа со скидкой
				<span>{$Unit.price(form.priceDiscounted)} грн.</span>
			</p>
			<p>
				Сумма заказа без скидки
				<span><small>{$Unit.price(form.price)} грн.</small></span>
			</p>
			<p>
				Сумма скидки
				<span><small>{$Unit.price(form.price-form.priceDiscounted)} грн.</small></span>
			</p>
		<else>
			<p>
				Сумма заказа
				<span>{$Unit.price(form.price)} грн.</span>
			</p>
		<endif>
		
		<p>
			Сумма закупки
			<span><small>{$Unit.price(form.priceBought)} грн.</small></span>
		</p>
		
		<p>
			Доход с заказа
			<span><small>{$Unit.price((form.priceDiscounted || form.price) - form.priceBought)} грн.</small></span>
		</p>
	</div>
*/};

/*******************************************************************************

	PRODUCT SUPPLIER

*******************************************************************************/

AI_Templates.productSupplierList = function() {/*
	<div class="content">
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			paginator:	hash.list,
			actions:	AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="login">Название</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.title;
					}
				},
				AI_Components.tableActionsColumn()
			]
		}, this) %>
		
	</div>
*/};


AI_Templates.productSupplierEdit = function() {/*
	<div class="content">
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
			
			<if (form.id)>
				<input type="hidden" name="id" value="{$form.id}" />
			<endif>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				fields: {
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'TItle',
							name:			'title',
							title:			'Название',
							value:			form.title,
							length:			50,
							description:	'Название поставщика отображается в информации о товаре.'
						})
					],
					
					'center field30': [
					],
					
					'right field30': [
					]
				}
			}, this) %>
			
			<%= AI.template('simpleActions', [
				AI_Components.tableActionButton('СОХРАНИТЬ', null, null, 'submit'),
				AI_Components.tableActionButton('ВЕРНУТЬСЯ', "AI.go('" + this.name + "','list','" + this.lastRequestFilters + "')",'gray')
			]) %>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PRODUCT CATEGORIES

*******************************************************************************/

AI_Templates.productCategoryList = function() {/*
	<var (list = (hash.list || {}))>
	<var (records = this.getGroupedCategories(list.records))>
	
	<div class="content">
	
		<div class="head">
			{$this.getMessage('TITLE')}
			<if (this.checkAccess(this.ACCESS.EDIT))>
				<input type="button" onclick="AI.go('{$this.name}/edit')" value="добавить запись" class="miniButton" />
			<endif>
		</div>
	
		<div class="body">
			
			<if (records && records[0] && records[0].length)>
				
				<div class="message">{$this.getMessage('STATISTIC',hash.list)}</div>
			
				<table class="table" ui="Drag" uiAction="{$this.module}.action('setPosition',list);">
				
					<tr>
						<th>ID / Название</th>
						<th class="cell40">Адрес</th>
						<th class="cell10"></th>
						<th class="cell20"></th>
					</tr>
					
					<%= this.getTemplate('ListLevel', {records:records,category:0}) %>
					
				</table>
	
			<else>
			
				<div class="message">{$this.getMessage('EMPTY_LIST')}</div>
				
			<endif>
			
		</div>
	</div>
*/};

AI_Templates.productCategoryListLevel = function() {/*
	<for (var i in hash.records[hash.category])>
		<var (item = hash.records[hash.category][i])>
		
		<tr class="direct directDraggable" uiDrag="{$hash.category}" uiDragId="{$item.id}">
			<td colspan="4">
			
				<table>
					<tr {$item.visibility=='0'?'class="inactive"':''}>
						<td>
							<small class="gray">{$item.id}.</small>
							<if (hash.records[item.id])>
								<span class="link" ui="Trigger" uiTarget="{$this.name}ListRecord{$item.id}">{$item.title}</span>
							<else>
								{$item.titleRu}
							<endif>
						</td>
						<td class="cell40">/category/{$item.url}</td>
						<td class="cell10"></td>
						<td class="cell20 rowActions">
							<if (this.checkAccess(this.ACCESS.EDIT))>
								<a href="{$this.getLink('edit',item.id)}">редактировать</a>
								<!--<span class="link" onclick="{$this.module}.editRecord('{$item.id}')">редактировать</span>-->
							<endif>
							
							<if (this.checkAccess(this.ACCESS.DROP) && ! this.isLocked(item.id))>
								| <span class="link warning" onclick="{$this.module}.action('drop','{$item.id}')">удалить</a>
							<endif>
						</td>
					</tr>
		
					<if (hash.records[item.id])>
					<tr class="collapse hidden" id="{$this.name}ListRecord{$item.id}">
						<td colspan="4">
							<table>
								<%= this.getTemplate('ListLevel', {records:records,category:item.id}) %>
							</table>
						</td>
					</tr>
					<endif>
					
				</table>
			
			</td>
		</tr>
		
	<endfor>
*/};

AI_Templates.productCategoryEdit = function() {/*
	<var (form = hash.form || {})>
	<var (categoryOptions = this.getCategoriesOptions(hash.categories, form.id, {0:'Корневая категория'}, 0, '--- '))>
	
	<div class="content selectedLang-ru" id="recordEdit">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
		
			<%= AI.template('simpleForm', {
				title:		form.id ? 'Редактирование' : 'Добавление записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				multilang:	'recordEdit',
				fields: {
		
					'left field30': [
						AI.template('formSelectField', {
							id:				this.module + 'ParentId',
							name:			'parentId',
							title:			'Родительская категория',
							value:			form.parentId,
							length:			100,
							description:	'Название категории товаров отображаемое в каталоге.',
							options:		categoryOptions
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Title',
							name:			'title',
							title:			'Название',
							values:			{ru:form.titleRu,en:form.titleEn},
							length:			100,
							description:	'Название категории товаров отображаемое в каталоге.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Url',
							name:			'url',
							title:			'Адрес',
							value:			form.url,
							length:			50,
							description:	'Адрес категории отображаемый в адресной строке.'
						})
					],
					
					'center field30': [
						AI.template('formAreaField', {
							id:				this.module + 'MetaKeywords',
							name:			'metaKeywords',
							title:			'Meta тэги',
							value:			form.metaKeywords,
							length:			250,
							description:	'Meta тэги категории для сео и поисковых систем.'
						}),
						
						AI.template('formSelectField', {
							id:				this.module + 'Visibility',
							name:			'visibility',
							title:			'Отображение категории',
							value:			form.visibility,
							description:	'Отображение категории в каталоге товаров.',
							options:		{0:'Категория не отображается в каталоге',1:'Категория отображается в каталоге'}
						})
					],
					
					'right field30': [
					AI.template('formAreaField', {
						id:				this.module + 'MetaDescription',
						name:			'metaDescription',
						title:			'Meta описание',
						value:			form.metaDescription,
						length:			250,
						description:	'Meta описание категории для сео и поисковых систем.'
					})
					]
				}
			}, this) %>
					
			<div class="actions">
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="СОХРАНИТЬ" class="bigButton">
				<input type="button" value="ВЕРНУТЬСЯ" onclick="AI.go('{$this.name}','list','{$this.lastRequestFilters||''}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PRODUCT TYPE

*******************************************************************************/

AI_Templates.productTypeList = function() {/*
	<var (list = (hash.list || {}))>
	
	<div class="content">
	
		<div class="head">
			{$this.getMessage('TITLE')}
			<if (this.checkAccess(this.ACCESS.EDIT))>
				<input type="button" onclick="AI.go('{$this.name}/edit')" value="добавить запись" class="miniButton" />
			<endif>
		</div>
	
		<div class="body" ui="Table" uiOptions="{$Unit.stringify(list.filters)}" uiAction="AI.go('{$this.name}','list',Unit.stringify(this.options))">
			
			<if (list.records && list.records.length)>
				
				<div class="message">{$this.getMessage('STATISTIC',hash.list)}</div>
			
				<table class="table">
				
					<tr>
						<th>
							<span class="link" uiOrder="id">ID</span>
							/
							<span class="link" uiOrder="title">Название</span>
						</th>
						<th class="cell20"></th>
					</tr>
					
					<for (var i in list.records)>
						<var (item = list.records[i])>
						<tr>
							<td><small class="gray">{$item.id}.</small> {$item.title}</td>
							<td class="rowActions">
								<if (this.checkAccess(this.ACCESS.EDIT))>
									<a href="{$this.getLink('edit',item.id)}">редактировать</a>
								<endif>
								
								<if (this.checkAccess(this.ACCESS.DROP) && ! this.isLocked(item.id))>
									| <span class="link warning" onclick="{$this.module}.action('drop','{$item.id}')">удалить</a>
								<endif>
							</td>
						</tr>
					<endfor>
					
				</table>
				
				<%= AI.template('listPaginator', list, this) %>
	
			<else>
			
				<div class="message">{$this.getMessage('EMPTY_LIST')}</div>
				
			<endif>
			
		</div>
	</div>
*/};

AI_Templates.productTypeEdit = function() {/*
	<var (form = hash.form || {})>
	
	<div class="content">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
		
			<div class="head">Редактирование записи</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название',
						value:			form.title,
						length:			100,
						description:	'Название указываемое в типе товара в администрировании.'
					}) %>
				</div>
			
			</div>
			
			<div class="head">Параметры типа товаров</div>
			
			<div class="body" ui="Menu" uiMultiple="0">
			
				<input type="hidden" name="groupIds" value="{$form.groupIds || ''}" uiMenu="value">
				
				<table class="table">
			
					<tr>
						<th>Группа параметров</th>
					</tr>
					
					<for (var i in hash.groups)>
						<var (item = hash.groups[i])>
						<tr>
							<td>
								<div class="checkbox" uiMenuOption="{$item.id}">{$item.title}</div>
							</td>
						</tr>
					<endfor>
					
				</table>
				
			</div>
				
			<div class="actions">
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="СОХРАНИТЬ" class="bigButton">
				<input type="button" value="ВЕРНУТЬСЯ" onclick="AI.go('{$this.name}','list','{$this.lastRequestFilters||''}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PRODUCT PARAMS MODULE

*******************************************************************************/

AI_Templates.productParamList = function() {/*
	<var (groups = (hash.groups || {}))>
	<var (keys = this.getGroupedKeys(hash.keys || {}))>
	<var (values = this.getGroupedValues(hash.values || {}))>
	
	<div class="content">
	
		<div class="head">
			{$this.getMessage('TITLE')}
			<if (this.checkAccess(this.ACCESS.EDIT))>
				<input type="button" onclick="{$this.module}.action('editGroup',{})" value="добавить группу" class="miniButton" />
			<endif>
		</div>
	
		<div class="body">
			
			<if (groups && groups.length)>
				
				<table class="table">
				
					<tr>
						<th>
							ID
							/
							Название
						</th>
						<th class="cell40"></th>
					</tr>
					
					<for (var i in groups)>
						<var (group = groups[i])>
						<tr class="direct">
							<td>
								<small class="gray">{$group.id}.</small>
								<if (keys[group.id])>
									<span class="link" ui="Trigger" uiTarget="{$this.name}GroupRecord{$group.id}">{$group.title}</span>
								<else>
									{$group.title}
								<endif>
							</td>
							<td class="cell40 rowActions">
								<if (this.checkAccess(this.ACCESS.EDIT))>
									<span class="link" onclick="{$this.module}.action('editKey',{groupId:'{$group.id}'})">добавить параметр</span>
									|
									<span class="link" onclick="{$this.module}.action('editGroup',{groupId:'{$group.id}'})">редактировать</span>
								<endif>
								
								<if (this.checkAccess(this.ACCESS.DROP) && group.id != 1)>
									| <span class="link warning" onclick="{$this.module}.action('dropGroup','{$group.id}')">удалить</a>
								<endif>
							</td>
						</tr>
						
						<if (keys[group.id])>
						<tr class="collapse hidden" id="{$this.name}GroupRecord{$group.id}">
							<td colspan="2">
								
								<table>
				
									<for (var j in keys[group.id])>
										<var (key = keys[group.id][j])>
										<tr class="direct">
											<td>
												<small class="gray">{$key.id}.</small>
												<if (values[group.id] && values[group.id][key.id])>
													<span class="link" ui="Trigger" uiTarget="{$this.name}KeyRecord{$key.id}">{$key.title}</span>
												<else>
													{$key.title}
												<endif>
											</td>
											<td class="cell40 rowActions">
												<if (this.checkAccess(this.ACCESS.EDIT))>
													<span class="link" onclick="{$this.module}.action('editValue',{groupId:'{$group.id}',keyId:'{$key.id}'})">добавить значение</span>
													|
													<span class="link" onclick="{$this.module}.action('editKey',{groupId:'{$group.id}',keyId:'{$key.id}'})">редактировать</span>
												<endif>
												
												<if (this.checkAccess(this.ACCESS.DROP) && key.id != 3 && key.id != 4)>
													| <span class="link warning" onclick="{$this.module}.action('dropKey','{$key.id}')">удалить</a>
												<endif>
											</td>
										</tr>
										
										<if (values[group.id] && values[group.id][key.id])>
										<tr class="collapse hidden" id="{$this.name}KeyRecord{$key.id}">
											<td colspan="2">
												
												<table>
				
													<for (var k in values[group.id][key.id])>
														<var (value = values[group.id][key.id][k])>
														<tr class="direct">
															<td>
																<if (key.id == 1)>
																	<span class="colorPicked" style="background-color:#{$value.extra || ''}"></span>
																<endif>
																<small class="gray">{$value.id}.</small> {$value.title}
															</td>
															<td class="cell40 rowActions">
																<if (this.checkAccess(this.ACCESS.EDIT))>
																	<span class="link" onclick="{$this.module}.action('editValue',{groupId:'{$group.id}',keyId:'{$key.id}',valueId:'{$value.id}'})">редактировать</span>
																<endif>
																
																<if (this.checkAccess(this.ACCESS.DROP))>
																	| <span class="link warning" onclick="{$this.module}.action('dropValue','{$value.id}')">удалить</a>
																<endif>
															</td>
														</tr>
													<endfor>
													
												</table>
												
											</td>
										</tr>
										<endif>
										
									<endfor>
									
								</table>
								
							</td>
						</tr>
						<endif>
						
					<endfor>
					
				</table>
	
			<else>
			
				<div class="message">{$this.getMessage('EMPTY_LIST')}</div>
				
			<endif>
			
		</div>
	</div>
*/};

AI_Templates.productParamGroupEdit = function() {/*
	<var (form = hash.form || {})>
	
	<div class="popup popupOneRow">
	
		<form class="form" ui="Form" uiControl="{$this.module}.groupControl">
		
			<div class="head">{$form.id ? 'Редактирование записи' : 'Добавление записи'}</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название',
						value:			form.title,
						length:			50,
						description:	'Название блока сгруппированного параметров.'
					}) %>
				</div>
			
			</div>
					
			<div class="actions">
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="{$form.id?'СОХРАНИТЬ':'ДОБАВИТЬ'}" class="bigButton">
				<input type="button" value="{$form.id?'ОТМЕНА':'ЗАКРЫТЬ'}" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

AI_Templates.productParamKeyEdit = function() {/*
	<var (form = hash.form || {})>
	
	<div class="popup popupOneRow">
	
		<form class="form" ui="Form" uiControl="{$this.module}.keyControl">
		
			<div class="head">{$form.id ? 'Редактирование записи' : 'Добавление записи'}</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название',
						value:			form.title,
						length:			50,
						description:	'Название блока сгруппированного параметров.'
					}) %>
				</div>
			
			</div>
					
			<div class="actions">
				<if (form.groupId)>
					<input type="hidden" name="groupId" value="{$form.groupId}" />
				<endif>
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="{$form.id?'СОХРАНИТЬ':'ДОБАВИТЬ'}" class="bigButton">
				<input type="button" value="{$form.id?'ОТМЕНА':'ЗАКРЫТЬ'}" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

AI_Templates.productParamValueEdit = function() {/*
	<var (form = hash.form || {})>
	
	<div class="popup popupOneRow">
	
		<form class="form" ui="Form" uiControl="{$this.module}.valueControl">
		
			<div class="head">{$form.id ? 'Редактирование записи' : 'Добавление записи'}</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название',
						value:			form.title,
						length:			50,
						description:	'Название блока сгруппированного параметров.'
					}) %>
				</div>
				
				<if (form.keyId == 1)>
					<div class="field30">
						<%= AI.template('formColorField', {
							id:				this.module + 'Extra',
							name:			'extra',
							title:			'Цвет',
							value:			form.extra,
							description:	'Нажмите на поле для выбора цвета.'
						}) %>
					</div>
				<endif>
			
			</div>
					
			<div class="actions">
				<if (form.groupId)>
					<input type="hidden" name="groupId" value="{$form.groupId}" />
				<endif>
				<if (form.keyId)>
					<input type="hidden" name="keyId" value="{$form.keyId}" />
				<endif>
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="{$form.id?'СОХРАНИТЬ':'ДОБАВИТЬ'}" class="bigButton">
				<input type="button" value="{$form.id?'ОТМЕНА':'ЗАКРЫТЬ'}" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PRODUCTS IMPORT MODULE

*******************************************************************************/

AI_Templates.productImportList = function() {/*
	<div class="content form">
		
		<input type="file" class="hidden" id="productImportFile" onchange="AI_Uploader('{$hash.id}', '/api/productImport/upload/', this, {$this.module}.uploadedFile)">
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	[
				AI_Components.tableActionButton('Добавить все товары', "if(confirm('Добавление всех товаров может занять несколько часов, процесс нельзя прервать и он может замедлить работу сайта. Вы действительно хотите продолжить?'))" + this.module + ".action('acceptAll')"),
				AI_Components.tableActionButton('Импортировать файл', "Unit('productImportFile').click()")
			],
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="title">Название</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> ' + item.title;
					}
				},
				AI_Components.tableSimpleColumn('Артикул','cell10', 'realId', true),
				AI_Components.tableSimpleColumn('Категория','cell40', 'category', true),
				AI_Components.tableSimpleColumn('Цена','cell10', 'price', true),
				{
					label:	'',
					style:	'cell10 rowActions',
					field: function(i, item) {
						if (item.status == 2) return '';
						else return '<span class="link" onclick="' + this.module + '.action(\'accept\',\'' + item.id + '\');this.innerHTML=\'\';">добавить</span>';
					}
				}
			]
		}, this) %>
	</div>
*/};

/*******************************************************************************

	PRODUCTS EXPORT MODULE

*******************************************************************************/

AI_Templates.productExportList = function() {/*
	<div class="content form">
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	[
				AI_Components.tableActionButton('Экспортировать файл', this.module + ".action('selectDate')")
			],
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="url">Файл</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> <a href="/' + item.url + '">' + item.url + '</a>';
					}
				},
				{
					label:	'<span class="link" uiOrder="size">Размер</span>',
					style:	'cell10',
					field:	function(i, item) {
						return Unit.roundSize(item.size);
					}
				},
				AI_Components.tableDateColumn('Дата','cell10', 'added', true),
				{
					label:	'',
					style:	'cell10 rowActions',
					field: function(i, item) {
						if (item.status == 2) return '';
						else return '<span class="link" onclick="' + this.module + '.action(\'drop\',\'' + item.id + '\');">удалить</span>';
					}
				}
			]
		}, this) %>
	</div>
*/};

AI_Templates.productExportSelectDate = function() {/*
	<div class="popup popupOneRow">
		<form class="form" ui="Form" uiControl="{$this.module}.controlGenerate">
		
			<div class="head">Генерирование архива товаров</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
				
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название файла',
						value:			'',
						length:			50,
						description:	'Введите название для экспортируемого файла.'
					}) %>
					
					<%= AI.template('formDoubleDateField', {
						id:				this.module + 'Date',
						nameA:			'from',
						nameB:			'to',
						title:			'Период добавления товаров',
						valueA:			'',
						valueB:			'',
						description:	'За какой период необходимо создать архив изображений.'
					}) %>
				</div>
			
			</div>
					
			<div class="actions">
				<input type="submit" value="ЭКСПОРТИРОВАТЬ" class="bigButton">
				<input type="button" value="ОТМЕНА" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};
/*******************************************************************************

	PRODUCTS EXPORT XLS MODULE

*******************************************************************************/

AI_Templates.productExportXlsList = function() {/*
	<div class="content form">
		
		<%= AI.template('simpleList', {
			title:		this.getMessage('TITLE'),
			message:	this.getMessage('STATISTIC', hash.list),
			records:	hash.list.records,
			filters:	hash.list.filters,
			paginator:	hash.list,
			actions:	[
				AI_Components.tableActionButton('Экспортировать файл', this.module + ".action('selectDate')")
			],
			columns:	[{
					label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="url">Файл</span>',
					field:	function(i, item) {
						return '<small class="gray">' + item.id + '.</small> <a href="/' + item.url + '">' + item.url + '</a>';
					}
				},
				{
					label:	'<span class="link" uiOrder="size">Размер</span>',
					style:	'cell10',
					field:	function(i, item) {
						return Unit.roundSize(item.size);
					}
				},
				AI_Components.tableDateColumn('Дата','cell10', 'added', true),
				{
					label:	'',
					style:	'cell10 rowActions',
					field: function(i, item) {
						if (item.status == 2) return '';
						else return '<span class="link" onclick="' + this.module + '.action(\'drop\',\'' + item.id + '\');">удалить</span>';
					}
				}
			]
		}, this) %>
	</div>
*/};

AI_Templates.productExportXlsSelectDate = function() {/*
	<div class="popup popupOneRow">
		<form class="form" ui="Form" uiControl="{$this.module}.controlGenerate">
		
			<div class="head">Генерирование списка товаровдля prom.ua</div>
		
			<div class="body">
			
				<div uiForm="message" class="message">Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".</div>
				
				<div class="field30">
				
					<%= AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'title',
						title:			'Название файла',
						value:			'',
						length:			50,
						description:	'Введите название для экспортируемого файла.'
					}) %>
					
				</div>
			
			</div>
					
			<div class="actions">
				<input type="submit" value="ЭКСПОРТИРОВАТЬ" class="bigButton">
				<input type="button" value="ОТМЕНА" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

/*******************************************************************************

	PRODUCTS MODULE

*******************************************************************************/

AI_Templates.productList = function() {/*
	<div class="content form">
		
		<var (filters 			= (hash.list.filters || {}).where || {})>
		<var (categoryOptions	= AI_ProductCategory.getCategoriesOptions(hash.categories, null, {0:'Корневая категория'}, 0, '--- '))>
		<var (supplierOptions	= AI_ProductCategory.getCategoriesOptions(hash.supplier, null))>
		<var (typeOptions		= AI_ProductType.getTypesOptions(hash.types, null))>
	
		<%= AI.template('simpleFilters', {
			filters: hash.list.filters || {},
			fields: {
				'left field30': [
					AI.template('formTextField', {
						id:				this.module + 'Title',
						name:			'where[title like]',
						title:			'Название',
						value:			filters['title like'],
						description:	'Поиск товаров по названию.'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'CategoryId',
						name:			'where[categoryId]',
						title:			'Категория',
						value:			filters['categoryId'],
						description:	'Поиск товаров по категории.',
						options:		categoryOptions,
						empty:			'Любая категория'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'Recommended',
						name:			'where[recommended]',
						title:			'Популярные и новинки',
						value:			filters.recommended || '',
						description:	'Поиск популярных товаров и новинок.',
						options: 		{0:'Товар не виден в блоке популярных и новинок',1:'Товар виден в блоке популярных',2:'Товар виден в блоке новинок',3:'Товар виден в блоке популярных и новинок'},
						empty:			'Не указано'
					})
				],
				
				'center field30': [
					AI.template('formTextField', {
						id:				this.module + 'Article',
						name:			'where[article like]',
						title:			'Артикул',
						value:			filters['article like'],
						description:	'Поиск товаров по артикулу.'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'Type',
						name:			'where[typeId]',
						title:			'Тип товара',
						value:			filters.typeId || '',
						description:	'Поиск товаров по типу.',
						options: 		typeOptions,
						empty:			'Не указан'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'Availability',
						name:			'where[availability]',
						title:			'Наличие',
						value:			filters.availability || '',
						description:	'Поиск заказов по наличию.',
						options: 		this.getAvailabilityOptions(),
						empty:			'Любое наличие'
					})
				],
				
				'right field30': [
					AI.template('formDoubleDateField', {
						id:				this.module + 'Added',
						nameA:			'where[added >]',
						nameB:			'where[added <]',
						title:			'Дата добавления',
						valueA:			filters['added >'],
						valueB:			filters['added <'],
						description:	'Поиск товаров по дате добавления.'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'SupplierId',
						name:			'where[supplierId]',
						title:			'Поставщик',
						value:			filters['supplierId'],
						description:	'Поиск товаров поставщика.',
						options:		supplierOptions,
						empty:			'Любой поставщик'
					}),
					AI.template('formSelectField', {
						id:				this.module + 'Active',
						name:			'where[active]',
						title:			'Статус',
						value:			filters.active,
						description:	'Поиск активных и неактивных товаров.',
						options: 		{0:'Не доступен',1:'Доступен'},
						empty:			'Любой статус'
					})
				]
			}
		}, this) %>
		
		<div id="productList" ui="Selector">
			<%= AI.template('simpleList', {
				title:		this.getMessage('TITLE'),
				message:	this.getMessage('STATISTIC', hash.list),
				records:	hash.list.records,
				filters:	hash.list.filters,
				paginator:	hash.list,
				todo:		{
					category: AI.template('formSelectField', {
						id:				this.module + 'TodoCategory',
						name:			'todo[category]',
						title:			'Переместить в категорию',
						value:			'',
						description:	'Укажите категорию для перемещения выбранных товаров.',
						options: 		categoryOptions,
						action:			this.module + ".action('setCategory',{products:Unit('productList').getSelected(),category:this.selected});",
						empty:			'Укажите категорию'
					}),
					action: AI.template('formSelectField', {
						id:				this.module + 'TodoAction',
						name:			'todo[action]',
						title:			'Действие для выбранных товаров',
						value:			'',
						description:	'Укажите что необходимо сделать с выбранными товарами.',
						options: 		{'':'Выберите дейтсвие','toArchive':'Переместить в архив'},
						action:			this.module + ".action('todoAction',{products:Unit('productList').getSelected(),action:this.selected});"
					})
				},
				actions:	[
					AI_Components.tableActionButton('Добавить запись', "AI.go('" + this.name + "/edit')"),
					AI_Components.tableActionButton('Изменить скидки', this.module + ".action('recalculateDiscount')"),
					AI_Components.tableActionButton('Изменить цены', this.module + ".action('recalculatePrice')")
				],
				columns:	[
					AI_Components.checkboxColumn(),
					AI_Components.imageColumn(hash.list.images || {}),
					{
						label:	'<span class="link" uiOrder="id">ID</span> / <span class="link" uiOrder="article">Артикул</span> / <span class="link" uiOrder="titleRu">Название</span>',
						field:	function(i, item) {
							return '<small class="gray">' + item.id + '.</small> <b>' + item.article +'</b><br>' + item.titleRu;
						}
					},
					{
						label:	'<span class="link" uiOrder="price" title="Цена в каталоге">Цена</span> / <span class="link" uiOrder="priceBought" title="Цена закупки">Закупка</span> / <span class="link" uiOrder="priority" title="Позиция в каталоге: 0 - в начале, 255 - в конце">Приоритет</span>',
						style:	'cell25',
						field:	function(i, item) {
							var strPrice = item.price || '', intPrice = parseInt(strPrice);
							var strBought = item.priceBought || '', intBought = parseInt(strBought);
							return 
								'<div class="iTitle">Цена</div><div class="iContent"><input type="text" class="inlineInput" value="' + (strPrice > intPrice ? strPrice : intPrice) + '" onchange="' + this.module + '.action(\'productPrice\',{id:\'' + item.id + '\',price:this.value})"></div>'+
								'<div class="iTitle">Закупка</div><div class="iContent"><input type="text" class="inlineInput" value="' + (strBought > intBought ? strBought : intBought) + '" onchange="' + this.module + '.action(\'productPrice\',{id:\'' + item.id + '\',priceBought:this.value})"></div>'+
								'<div class="iTitle">Скидка</div><div class="iContent"><input type="text" class="inlineInput" value="' + (item.discount || '') + '" onchange="' + this.module + '.action(\'productPrice\',{id:\'' + item.id + '\',discount:this.value})" maxlength="5"></div>'+
								'<div class="iTitle">Приоритет</div><div class="iContent"><input type="text" class="inlineInput" value="' + parseInt(item.priority || '') + '" onchange="' + this.module + '.action(\'productPrice\',{id:\'' + item.id + '\',priority:this.value})"></div>';
						}
					},
					AI_Components.tableDateColumn('Добавлен','cell10', 'added', true),
					AI_Components.tableActionsColumn()
				]
			}, this) %>
		</div>
	</div>
*/};

AI_Templates.productEdit = function() {/*
	<var (form				= hash.form || {})>
	<var (attributes		= Unit.parseJson(form.attributes) || {})>
	<var (categoryOptions	= AI_ProductCategory.getCategoriesOptions(hash.categories, null, {0:'Корневая категория'}, 0, '--- '))>
	<var (supplierOptions	= AI_ProductSupplier.getTypesOptions(hash.supplier, {0:'Не указан'}))>
	<var (typeOptions		= AI_ProductType.getTypesOptions(hash.types, {0:'Не указан'}))>
	
	<var (groups			= (hash.groups || {}))>
	<var (keys				= AI_ProductParam.getGroupedKeys(hash.keys || {}))>
	<var (values			= AI_ProductParam.getGroupedValues(hash.values || {}))>
	
	<div class="content selectedLang-ru" id="recordEdit">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlEdit">
			
			<div class="head">
				<span class="collapsable" ui="Trigger" uiTarget="productEditImages">Изображения товара</span>
				<input type="file" id="{$this.name}Uploader" onchange="AI_Uploader('{$form.id}', '/api/productImage/upload/', this, {$this.module}.uploadedImage)" min="1" max="10" multiple="true" accept="image/*" class="hidden">
				<input type="button" onclick="Unit('{$this.name}Uploader').click()" value="добавить изображение" class="miniButton" />
			</div>
			<div class="body" id="productEditImages" style="z-index:2">
			
				<div id="{$this.name}Images">
					<%= this.getTemplate('Images', {images:hash.images}) %>
				</div>
				
			</div>
			
			<%= AI.template('simpleForm', {
				title:		'Редактирование записи',
				message:	'Заполните форму и нажмите "сохранить" для продолжения.<br>Для возвращения к списку запсией нажмите "вернуться".',
				multilang:	'recordEdit',
				required:	true,
				fields: {
		
					'left field30': [
						AI.template('formTextField', {
							id:				this.module + 'Title',
							name:			'title',
							title:			'Название',
							values:			{ru:form.titleRu, en:form.titleEn},
							length:			200,
							required:		true,
							description:	'Название товара отображается в каталоге.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Url',
							name:			'url',
							title:			'Адрес',
							value:			form.url,
							length:			200,
							required:		true,
							disabled:		true,
							configurable:	true,
							description:	'Адрес товара в адресной строке браузера.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Article',
							name:			'article',
							title:			'Артикул',
							value:			form.article,
							length:			100,
							required:		true,
							description:	'Артикул товара для быстрого поиска.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Priority',
							name:			'priority',
							title:			'Приоритет товара',
							value:			form.priority,
							length:			3,
							description:	'Число от 0 до 255, чем меньше число, тем выше в каталоге будет расположен товар при сортировке по "популярности".'
						}),
						
						AI.template('formAreaField', {
							id:				this.module + 'Description',
							name:			'description',
							title:			'Краткое описание товара',
							values:			{ru:form.descriptionRu, en:form.descriptionEn},
							length:			1000,
							required:		true,
							description:	'Краткое описание товара для быстрого просмотра.'
						})
					],
					
					'center field30': [
						AI.template('formSelectField', {
							id:				this.module + 'Availability',
							name:			'availability',
							title:			'Наличие',
							value:			form.availability,
							description:	'Индикатор наличия товара в каталоге.',
							options:		this.getAvailabilityOptions()
						}),
						
						AI.template('formSelectField', {
							id:				this.module + 'Active',
							name:			'active',
							title:			'Статус',
							value:			form.active === undefined ? 1 : form.active,
							description:	'Отображение товара в каталоге.',
							options:		{0:'Не доступен',1:'Доступен'}
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Price',
							name:			'price',
							title:			'Цена в каталоге',
							value:			form.price,
							length:			13,
							required:		true,
							description:	'Цена отображаемая в каталоге товаров.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'PriceBought',
							name:			'priceBought',
							title:			'Цена закупки',
							value:			form.priceBought,
							length:			13,
							required:		true,
							description:	'Цена закупки товара для калькуляции дохода.'
						}),
						
						AI.template('formTextField', {
							id:				this.module + 'Discount',
							name:			'discount',
							title:			'Скида',
							value:			form.discount,
							length:			5,
							description:	'Скидка на товар, например "19.99" или "10%".'
						}),
						
						AI.template('formAreaField', {
							id:				this.module + 'MetaKeywords',
							name:			'metaKeywords',
							title:			'Meta тэги',
							values:			{ru:form.metaKeywordsRu, en:form.metaKeywordsEn},
							length:			250,
							description:	'Meta тэги товара для сео и поисковых систем.'
						})
					],
					
					'right field30': [
						
						AI.template('formSelectField', {
							id:				this.module + 'CategoryId',
							name:			'categoryId',
							title:			'Категория',
							value:			form.categoryId,
							description:	'В какой категории отображать товар.',
							options:		categoryOptions
						}),
						
						AI.template('formSelectField', {
							id:				this.module + 'TypeId',
							name:			'typeId',
							title:			'Тип товара',
							value:			form.typeId,
							description:	'Тип товара определяет набор параметров товара.',
							options:		typeOptions
						}),
						
						AI.template('formSelectField', {
							id:				this.module + 'SupplierId',
							name:			'supplierId',
							title:			'Поставщик товара',
							value:			form.supplierId,
							description:	'Укажите поставщика данного товара.',
							options:		supplierOptions
						}),
						
						AI.template('formSelectField', {
							id:				this.module + 'Recommended',
							name:			'recommended',
							title:			'Популярный и новинка',
							value:			form.recommended,
							description:	'Отображение товара в блоке популярных товаров и новинок.',
							options:		{0:'Товар не виден в блоке популярных и новинок',1:'Товар виден в блоке популярных',2:'Товар виден в блоке новинок',3:'Товар виден в блоке популярных и новинок'}
						}),
						
						AI.template('formAreaField', {
							id:				this.module + 'MetaDescription',
							name:			'metaDescription',
							title:			'Meta описание',
							values:			{ru:form.metaDescriptionRu, en:form.metaDescriptionEn},
							length:			250,
							description:	'Meta описание товара для сео и поисковых систем.'
						})
					]
				}
			}, this) %>
			
			<%= AI.template('collapseForm', {
				title:		'Подробное описание товара',
				collapse:	'productEditDescription',
				content:	AI.template('formWysiwyg', {name:'fullDescription', values:{ru:form.fullDescriptionRu,en:form.fullDescriptionEn}})
			}, this) %>
			
			<div class="head"><span class="collapsable" ui="Trigger" uiTarget="productEditParams">Параметры товара</span></div>
			<div class="body" id="productEditParams">
			
				<table class="table">
					
					<for (var i in groups)>
						<var (group = groups[i])>
						<tr class="direct">
							<td>
								<span class="link" ui="Trigger" uiTarget="{$this.name}GroupRecord{$group.id}">{$group.title}</span>
							</td>
							<td class="cell100"></td>
						</tr>
						<tr class="collapse hidden" id="{$this.name}GroupRecord{$group.id}">
							<td colspan="2">
								
								<table>
									<for (var j in keys[group.id])>
										<var (key = keys[group.id][j])>
										<% if (key.id == 1) continue; %>
										<tr class="direct">
											<td>{$key.title}</td>
											<td class="cell100">
												
												<div ui="Menu" uiMultiple="0">
			
													<input type="hidden" name="attributes[{$key.id}]" value="{$attributes[key.id]}" uiMenu="value">
													
													<if (values[group.id])>
														<for (var i in values[group.id][key.id])>
															<var (item = values[group.id][key.id][i])>
															<div class="checkboxInline" uiMenuOption="{$item.id}">{$item.title}</div>
														<endfor>
													<endif>
													
												</div>
												
											</td>
										</tr>
									<endfor>
								</table>
								
							</td>
						</tr>
					<endfor>
					
				</table>
				
			</div>
			
			<if (form.id)>
			
				<div class="head"><span class="collapsable" ui="Trigger" uiTarget="productEditComments">Комментарии к товару</span></div>
				<div class="body" id="productEditComments">
				
					<if (hash.comments && hash.comments.length)>
						<table class="table">
					
							<tr>
								<th class="cell20">Автор комментария</th>
								<th>Сообщение</th>
								<th class="cell10">Дата</th>
								<th class="cell10"></th>
							</tr>
							
							<for (var i in hash.comments)>
								<var (item = hash.comments[i])>
								<tr id="{$this.name}ProductComment{$i}">
									<td>Не указан</td>
									<td>{$item.message}</td>
									<td>{$item.added > 0 ? AI_Tools.date(item.added) : 'Не указано'}</td>
									<td class="rowActions">
										<span class="link warning" onclick="{$this.module}.action('dropComment','{$item.id}');Unit.remove('{$this.name}ProductComment{$i}');">удалить</span>
									</td>
								</tr>
							<endfor>
							
						</table>
					<else>
						<div class="message">У данного товара нет комментариев.</div>
					<endif>
					
				</div>
				
			<endif>
			
			<div class="actions">
				<if (form.id)>
					<input type="hidden" name="id" value="{$form.id}" />
				<endif>
				
				<input type="submit" value="СОХРАНИТЬ" class="bigButton">
				<input type="button" value="ВЕРНУТЬСЯ" onclick="AI.go('{$this.name}','list','{$this.lastRequestFilters||''}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

AI_Templates.productImages = function() {/*
	<for (var i in hash.images)>
		<div class="imagePreview" id="{$this.name}ImageThumb{$i}">
			<span onclick="{$this.module}.action('dropImage','{$i}');Unit.remove('{$this.name}ImageThumb{$i}');Unit.remove('{$this.name}ImageColor{$i}');">удалить</span>
			<img src="/public/products/full/{$hash.images[i].url}">
			<input type="hidden" name="image[{$hash.images[i].id}]" value="{$hash.images[i].url}">
		</div>
		<div class="imagePreviewSelect" id="{$this.name}ImageColor{$i}">
		<%= AI.template('formSelectColor', {
			id:				this.module + 'ImageColor' + i,
			name:			'color[' + i + ']',
			value:			hash.images[i].colorId > 0 ? hash.images[i].colorId : '',
			className:		'select selectColor',
			action:			this.module + ".action('setImageColor',{id:'" + i + "',colorId:this.selected})",
			empty:			'<b>Выбрать цвет</b>',
			options:		this.getColors()
		}) %>
		</div>
	<endfor>
*/};

AI_Templates.productRecalculatePrice = function() {/*
	<var (form = hash.form || {})>
	
	<div class="popup popupOneRow">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlRecalculate">
		
			<div class="head">Пересчёт цен товаров</div>
		
			<div class="body">
			
				<div uiForm="message" class="message error">Будьте внимательны, данный метод пересчитает цены всех товаров в списке, соответствующих текущему фильтру.<br>Операцию невозможно будет отменить.</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'PriceBought',
						name:			'priceBought',
						title:			'Цена закупки',
						value:			'0.00',
						length:			10,
						description:	'Введите сумму на которую необходимо изменить стоимость всех товаров в текущем списке. Например "-50" или "10%".'
					}) %>
					<%= AI.template('formTextField', {
						id:				this.module + 'Price',
						name:			'price',
						title:			'Цена каталога',
						value:			'0.00',
						length:			10,
						description:	'Введите сумму на которую необходимо изменить стоимость всех товаров в текущем списке. Например "-50" или "10%".'
					}) %>
					<%= AI.template('formSelectField', {
						id:				this.module + 'Round',
						name:			'round',
						title:			'Округление цены',
						value:			'0',
						description:	'Округление цен при пересчете.',
						options:		{0:'Не округлять цены',1:'Округлять в меньшую сторону',2:'Округлять в большую сторону',3:'Округлять в ближайшую сторону'}
					}) %>
				</div>
			
			</div>
					
			<div class="actions">
				<input type="submit" value="ПЕРЕСЧИТАТЬ" class="bigButton">
				<input type="button" value="ЗАКРЫТЬ" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

AI_Templates.productRecalculateDiscount = function() {/*
	<var (form = hash.form || {})>
	
	<div class="popup popupOneRow">
	
		<form class="form" ui="Form" uiControl="{$this.module}.controlDiscount">
		
			<div class="head">Установка скидок на товары</div>
		
			<div class="body">
			
				<div uiForm="message" class="message error">Для установки скидки в гривнах введите число в формате "19.99" или "50".<br>Для установки скидки в процентах, введите число со знаком процент, например "10%".</div>
				
				<div class="field30">
					<%= AI.template('formTextField', {
						id:				this.module + 'Discount',
						name:			'discount',
						title:			'Сумма скидки',
						value:			'',
						length:			5,
						description:	'Введите сумму скидки для всех товаров в текущем списке. Например "19.99" или "10%".'
					}) %>
				</div>
			
			</div>
					
			<div class="actions">
				<input type="submit" value="СОХРАНИТЬ" class="bigButton">
				<input type="button" value="ОТМЕНА" onclick="AI.close('{$hash.id}')" class="bigButton gray">
			</div>
			
		</form>
	</div>
*/};

// 1813, 1696