var AI_Components = {};

AI_Components.tableActionButton = function(label, action, style, type) {
	return {
		title:	label,
		action:	action,
		style:	style,
		type:	type
	};
};

AI_Components.checkboxColumn = function(label, style) {
	return {
		label: '<input type="checkbox" uiSelector="all">',
		style: '',
		field: function(i, item) {
			return '<input type="checkbox" uiSelector="' + item.id + '" />';
		}
	};
};

AI_Components.imageColumn = function(images) {
	return {
		label: '',
		style: '',
		field: function(i, item) {
			var imgs = images[item.id];
			for (var i in imgs) return '<img src="/public/products/mini/' + imgs[i].view + '" width="60px" />';
			return '';
		}
	};
};

AI_Components.tableActionsColumn = function(label, style) {
	return {
		label:	label || '',
		style:	style || 'cell20 rowActions',
		field:	function(i, item) {
			var field = [];
			
			if (this.checkAccess(this.ACCESS.EDIT))
				field.push(this.actions.edit.popup?
					'<span class="link" onclick="' + this.module + '.action(\'edit\',{id:\'' + item.id + '\'})" title="Редактировать запись">редактировать</span>':
					'<a href="' + this.getLink('edit',item.id) + '" title="Редактировать запись">редактировать</a>');
			
			if (this.checkAccess(this.ACCESS.DROP) && ! this.isLocked(item.id))
				field.push('<span class="link warning" onclick="' + this.module + '.action(\'drop\',\'' + item.id + '\')" title="Удалить запись">удалить</a>');
				
			return field.join(' | ');
		}
	};
};

AI_Components.tableSimpleColumn = function(label, style, field, order) {
	return {
		label:	order ? '<span class="link" uiOrder="' + field + '">' + label + '</span>' : label,
		style:	style,
		field:	function(i, item) {
			return item[field];
		}
	};
};

AI_Components.tablePriceColumn = function(label, style, field, order) {
	return {
		label:	order ? '<span class="link" uiOrder="' + field + '">' + label + '</span>' : label,
		style:	style,
		field:	function(i, item) {
			return Unit.price(item[field]) + ' грн.';
		}
	};
};

AI_Components.tableDateColumn = function(label, style, field, order) {
	return {
		label:	order ? '<span class="link" uiOrder="' + field + '">' + label + '</span>' : label,
		style:	style,
		field:	function(i, item) {
			return item[field] > 0 ? AI_Tools.date(item[field]) : 'Не указано';
		}
	};
};

AI_Components.tableOptionsColumn = function(label, style, field, options) {
	return {
		label:	label,
		style:	style,
		field:	function(i, item) {
			return options[item[field]];
		}
	};
};

AI_Components.tableInputColumn = function(label, style, field, action, order) {
	return {
		label:	order ? '<span class="link" uiOrder="' + field + '">' + label + '</span>' : label,
		style:	style,
		field:	function(i, item) {
			var strPrice = item[field] || '', intPrice = parseInt(strPrice);
			return '<input type="text" class="inlineInput" value="' + (strPrice > intPrice ? strPrice : intPrice)+ '" onchange="' + Unit.replace(action||'', item, '%') + '">';
		}
	};
};

AI_Components.tableDropdownColumn = function(label, style, field, options, action) {
	return {
		label:	label,
		style:	style,
		field: function(i, item) {
			return AI.template('formSelectItem', {
				id:				this.module + 'Dropdown' + item.id,
				name:			'product[' + field + '][' + item.id + ']',
				value:			item[field],
				className:		'inlineSelect',
				action:			Unit.replace(action||'', item, '%'),
				options:		options
			});
		}
	};
};