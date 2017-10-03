<script src="/public/project/js/jquery-1.11.2.min.js"></script>
<script src="/public/project/js/jquery.maskedinput.js"></script>
<script src="/public/project/js/basket.js"></script>
<script id="templateBasketCart" type="text/html">
		
	<var (host = location.host.replace('www.',''))>		
	<var (basket = project.basketLoad())>
	<var (products = projectBasket.productInfo || {})>
	
	<if (host == 'project.com.ua')>
		<var (countries = [
			[0, 'Выберите страну'],
			[1, 'Украина'],
			[2, 'Россия'],
			[3, 'Белоруссия'],
			[4, 'Казахстан'],
			[5, 'Англия'],
			[99, 'Другая страна']
		])>
	<else>
		<var (countries = [
			[0, 'Выберите страну'],
			[2, 'Россия'],
			[1, 'Украина'],
			[3, 'Белоруссия'],
			[4, 'Казахстан'],
			[5, 'Англия'],
			[99, 'Другая страна']
		])>
	<endif>
	
	<var (selectedCountry = projectBasket.get('country'))>
		
	<form method="POST" ui="Form" uiAction="projectBasket.set('confirm',true)">
		<div class="block">
			<div class="head">Ваш заказ</div>
				
			<if (basket && Unit.toArray(basket).length && (Unit.toArray(basket)[0] || {}).id)>
				<table>
					<thead>
						<tr>
							<th class="number">№</th>
							<th class="photo">Фото</th>
							<th class="prod">Товар</th>
							<th class="size">Размер</th>
							<th class="count">Количество</th>
							<th class="price">Стоимость</th>
							<th class="delete">Удалить</th>
						</tr>
					</thead>
					<tbody>
						
						<var (amount = 0)>
						<var (counter = 1)>
						<var (price = 0)>
						<var (priceWithDiscount = 0)>
							
						<for (var i in basket)>
							<var (p = basket[i])>
							<var (pPrice = 0)>
							<var (product = products[p.id] || {})>
							<var (images = Unit.group(product.images, 'colorId'))>
							<var (image = Unit.toArray(images[p.colorId])[0] || {})>
							
							<tr>
								<td class="number">{$counter++}</td>
								<td class="photo"><img src="/public/products/mini/{$image.view}" alt="" width="75px" /></td>
								<td class="prod">
									<a href="<?= $this->link() ?>{$product.url}/{$p.colorId}/" class="link">{$product.title}/{$p.colorId}</a>
									<p><span>Артикул:</span> {$product.article}</p>
								</td>
								<td class="size">
									<for (var s in p.sizeTitle)>
										<div class="box">{$p.sizeTitle[s]}</div>
										<div class="clear"></div>
									<endfor>
								</td>
								
								<td class="count">
									<for (var s in p.sizeId)>
										<% iPrice = product.price; %>
										<% iAmount = parseInt(p.sizeId[s]) || 0; %>
										<% iDiscount = product.discount || ''; %>
										<% amount += parseInt(p.sizeId[s]) || 0; %>
										<%
										var discountedPrice = iPrice;
										if (iDiscount.indexOf('%') >= 0) {
											discountedPrice = iPrice - (iPrice / 100 * parseInt(iDiscount)); 
										} else {
											discountedPrice = iPrice - (parseInt(iDiscount) || 0);
										}
										price += iAmount * p.price;
										priceWithDiscount += iAmount * discountedPrice;
										%>
										<div class="box counter {$iAmount > 0 ? 'active' : ''}" ui="Counter" id="productCounter_{$i}_{$s}" uiAction="project.basketSet('{$p.id}','{$p.colorId || 0}','{$s}',this.getValue()); projectBasket.updateBasket();">
											<input type="text" uiCounter="val" value="{$iAmount}">
											<span class="up" uiCounter="add"></span>
											<span class="down" uiCounter="del"></span>
										</div>
										<div class="clear"></div>
									<endfor>
								</td>
								
								<td class="price">
									<for (var s in p.sizeId)>
										<% iAmount = parseInt(p.sizeId[s]) || 0; %>
										<div class="box">{$iAmount > 0 ? project.price(iAmount * iPrice, iDiscount) : '&nbsp;'}</div>
										<div class="clear"></div>
									<endfor>
								</td>
								
								<td class="delete">
									<span onclick="project.basketDel('{$i}');projectBasket.updateBasket();return false;" class="box"></span>
									<div class="clear"></div>
								</td>
							</tr>
							
						<endfor>
							
					</tbody>
				</table>
		
				<div class="price-box">
					<if (price != priceWithDiscount)>
					<u class="discount">Сумма заказа: {$project.price(price)}</u><br>
					Скидка: {$project.price(price - priceWithDiscount)}<br>
					Сумма со cкидкой: {$project.price(priceWithDiscount)}
					<else>
					Сумма заказа: {$project.price(price)}
					<endif>
				</div>
				<div class="info">
					<a href="<?= $this->link('catalog') ?>">Вернуться в каталог</a>
					
					<var (acceptAmount = (selectedCountry == 1 && amount >= 5) || (selectedCountry != 1 && amount >= 10))>
					<var (orderAvailable = projectBasket.set('orderAvailable', (price > 0 && selectedCountry > 0 && acceptAmount), false))>
					
					<if (orderAvailable)>
						<button type="submit" class="btn" onclick="btn_cart_click()"></button>
					<else>
						<if (selectedCountry && ! acceptAmount)>
						<p class="message">Добавьте минимум {$selectedCountry == 1 ? '5' : '10'} единиц товара.</p>
						<endif>
						<button type="submit" class="btn" onclick="btn_cart_click()"></button>
					<endif>
		
					<div class="select-box">
						<div class="selecter-box just" style="width: 100px;">
							<select class="select" id="country_select" onchange="projectBasket.set('country', this.value); set_mask()">
								<for (var i in countries)>
									<if ( ! (selectedCountry > 0 && countries[i][0] == 0))>
									<option value="{$countries[i][0]}" {$countries[i][0] == selectedCountry ? 'selected' : ''}>{$countries[i][1]}</option>
									<endif>
								<endfor>
							</select>
						</div>
					</div>
				</div>
			
			<else>
				
				<div class="body">В корзине нет товаров. Перейдите в каталог для добавления товаров в корзину.</div>
				<div class="info"><a href="<?= $this->link('catalog') ?>">Вернуться в каталог</a></div>
				
			<endif>
				
		</div>
	
	</form>
	<div style="display:none;">
		<form method="post" action="https://wl.walletone.com/checkout/checkout/Index" id="onlinePayForm" target="_blank">
			<input name="WMI_MERCHANT_ID"    value="154548456807"/>
			<input name="WMI_PAYMENT_AMOUNT" value="{$price}" id="wmiPrice" />
			<input name="WMI_CURRENCY_ID"    value="{$project.getCurrencyCodeOnlinePay()}"/>
			<input name="WMI_DESCRIPTION"    value="Оплата заказа в магазине project" id="wmiDescription" />
			<input name="WMI_SUCCESS_URL"    value="<?= $this->link('/');?>/payment/success" />
			<input name="WMI_FAIL_URL"       value="<?= $this->link('/');?>/payment/fail" />
			<input type="submit" />
		</form>
	</div>
	<div style="display:none;" id="liqcontainer">
		 <form method="POST" action="https://www.liqpay.com/api/checkout" accept-charset="utf-8" id="liqpayForm" target="_blank">
			<input type="hidden" name="data" id="liqData" value="" />
			<input type="hidden" name="signature" id="liqSign" value="" />
			<input type="submit" />
		</form>
	</div>
	<div style="display:none;">
		<input type="hidden" id="liqOrderId" value="" />
		<input type="hidden" id="liqAmount" value="" />
	</div>
</script>

<script type="text/html" id="templateBasketForm">
	
	<var (form = projectBasket.get('form') || {})>
	
	<var (selectedCountry = projectBasket.get('country'))>
	<var (selectedConfirm = projectBasket.get('confirm'))>
	<var (selectedDelivery = projectBasket.get('delivery'))>
	<var (selectedExpress = projectBasket.get('express'))>
	
	<var (deliveryTypes = {
		1: 'Новая Почта',
		2: 'Укр Почта',
		3: 'EMC',
		4: 'DIMEX',
		5: 'Helios Express',
		6: 'Доставка поездом',
		7: 'Экспресс доставка',
		8: 'Курьер Сервис Экспресс'
	})>
	
	<var (expressDelivery = {
		1: 'ПЭК',
		2: 'ЖелДорЭкспедиция',
		3: 'Деловые Линии',
		4: 'Байкал-Сервис',
		5: 'КурьерСервисЭкспресс',
		6: 'Почта России'
	})>
	
	<var (countries = {
		0: 'Выберите страну',
		1: 'Украина',
		2: 'Россия',
		3: 'Белоруссия',
		4: 'Казахстан',
		5: 'Англия',
		99: 'Другая страна'
	})>
	
	<var (fields = {
		store: {
			title:		'Номер склада',
			required:	true,
		},
		country: {
			title:		'Страна',
			required:	true,
		},
		state: {
			title:		'Область',
			required:	true,
		},
		city: {
			title:		'Город',
			required:	true,
		},
		address: {
			title:		'Адрес',
			required:	true,
		},
		post: {
			title:		'Индекс',
			required:	true,
		},
		train: {
			title:		'Номер поезда',
			required:	true,
		},
		station: {
			title:		'Название станции',
			required:	true,
		}
	})>
	
	<var (countriesDelivery = {
		1: [1,2],
		2: [7,3,4,6,2],
		3: [2,8,3,4],
		4: [2,8,3,4],
		5: [2,3,4,5],
		99: [2,3,4,5]
	})>
	
	<var (deliveryFields = {
		1: ['city','store'],
		2: ['country','state','city','address','post'],
		3: ['country','state','city','address','post'],
		4: ['country','state','city','address','post'],
		5: ['country','state','city','address','post'],
		6: ['train','station'],
		7: ['express','country','state','city','address','post'],
		8: ['country','state','city','address','post']
	})>
	
	<!-- ПЭК, ЖелДорЭкспедиция, Деловые Линии, Байкал-Сервис, Курьер Сервис Экспресс (Авиа), Почта России -->
	
	<% if ( ! selectedConfirm || ! projectBasket.get('orderAvailable')) return ''; %>
		
	<div class="block">
		<div class="tabs">
			<div class="tabs-head" ui="Menu" uiSelected="{$projectBasket.get('account') || 'new'}" uiAction="projectBasket.set('account', this.selected, false)">
				<span uiMenuOption="#tabArea:new">заказ с регистрацией</span>
                <span uiMenuOption="#tabArea:fast" onclick="set_mask(); user_set('fast');">быстрый заказ</span>
                <input type="hidden" value="formPayment" id="formToSubmit" />
			</div>
	
			<div class="tabs-content">
				<div class="tab tab1" id="tabArea:new">
					<div class="item" uiField="">
						   <label>Впервые у нас? </label>
						   <select name="" onchange="user_set(this.value)">
								<option value="new">Я новый пользователь</option>
								<option value="exist">У меня есть аккаунт</option>
						   </select>
					</div>
					<div style="clear:both; height:16px;"></div>
					<form class="form-box" ui="Form" uiControl="projectBasket.validateForm" id="formPayment">
						<input type="hidden" name="order_type" value="new" />
						<!-- uiAction="project.sendOrder(this.getValues());" -->
						<input type="hidden" name="countryId" value="{$selectedCountry}">
						<input type="hidden" name="paymentType" value="0" id="paymentType">
						
						<div id="basketError" uiForm="message" class="message"></div>
						
						<div class="item-group">
							<div class="item" uiField="name">
								<label>Имя<span>*</span></label>
								<input type="text" name="name" placeholder="" id="name_full" value="{$form.name || ''}">
							</div>
							<div class="item" uiField="lastname">
								<label>Фамилия<span>*</span></label>
								<input type="text" name="lastname" placeholder="" id="lastname_full" value="{$form.lastname || ''}">
							</div>
							<div class="item phone-item" uiField="email">
								<label>E-mail<span>*</span></label>
								<input type="text" name="email" placeholder="" id="email_full" value="{$form.email || ''}">
							</div>
							<div class="item phone-item" uiField="phone">
								<label>Телефон<span>*</span></label>
								<input type="text" name="phone" placeholder="" id="phone_full" value="{$form.phone || ''}">
							</div>
						</div>
	
						<div class="item-group">
							<div class="item" uiField="deliveryType">
								<label>Тип доставки<span>*</span></label>
								<select name="deliveryType" onchange="projectBasket.set('delivery',this.value)">
									<if ( ! selectedDelivery || countriesDelivery[selectedCountry].indexOf(selectedDelivery) == -1)>
										<option value="0">Выберите метод доставки</option>
									<endif>
									<for (var i in countriesDelivery[selectedCountry])>
										<var (iDelivery = countriesDelivery[selectedCountry][i])>
										<option value="{$iDelivery}" {$selectedDelivery == iDelivery ? 'selected' : ''}>{$deliveryTypes[iDelivery]}</option>
									<endfor>
								</select>
							</div>
							
							<if (selectedDelivery == 7)>
							<div class="item" uiField="expressType">
								<label>Транспорт. комп.<span>*</span></label>
								<select name="expressType" onchange="projectBasket.set('express',this.value)">
									<for (var i in expressDelivery)>
										<option value="{$i}" {$selectedExpress == i ? 'selected' : ''}>{$expressDelivery[i]}</option>
									<endfor>
								</select>
							</div>
							<endif>
														
							<!--<div class="item big">
								<label>Транспортная компания<span>*</span></label>
								<input type="text" name="deliveryCompany" placeholder="Нова Пошта">
							</div>-->
	
						<if (selectedDelivery > 0)>
							<div class="item-group">
								
								<if (selectedDelivery == 7 && selectedExpress == 3)>
									<div class="item" uiField="delivery[passport]">
										<label>Паспорт. данные<span>*</span></label>
										<input type="text" name="delivery[passport]" placeholder="" value="{$form['delivery[passport]'] || ''}">
										<br><small>Серия, номер, кем и когда выдан</small>
									</div>
								<endif>
								
								<for (var i in deliveryFields[selectedDelivery])>
									<var (iFieldKey = deliveryFields[selectedDelivery][i])>
									<var (iField = fields[iFieldKey])>
									<if (iField)>
										<div class="item" uiField="delivery[{$iFieldKey}]">
											<label>{$iField.title}{$iField.required ? '<span>*</span>' : ''}</label>
											<input type="text" name="delivery[{$iFieldKey}]" placeholder="" value="{$form['delivery['+iFieldKey+']'] || ''}">
										</div>
									<endif>
								<endfor>
								<!--<div class="item">
									<label>Страна<span>*</span></label>
									<input type="text" name="address[country]" placeholder="Украина">
								</div>
								<div class="item">
									<label>Область<span>*</span></label>
									<input type="text" name="address[state]" placeholder="Одесская">
								</div>
								<div class="item">
									<label>Город<span>*</span></label>
									<input type="text" name="address[city]" placeholder="Одесса">
								</div>
								<div class="item">
									<label>Улица, дом<span>*</span></label>
									<input type="text" name="address[street]" placeholder="ул. Прохоровская, 3а кв.7">
								</div>
								<div class="item">
									<label>Индекс<span>*</span></label>
									<input type="text" name="address[post]" placeholder="65000">
								</div>-->
							</div>
						<endif>
	
						<div class="item" uiField="comment">
							<label onclick="show_comment()"><span class="comment">Комментарий:</span></label>
							<textarea name="comment" id="comment" style="display:none">{$form.comment || ''}</textarea>
						</div>
						<div class="item">
							<button type="button" onclick="validate_full()" style="background: #000;color: #fff;" >Выбор оплаты</button>
						</div>
                        </div>
					</form>
					<form class="form-box" ui="Form" uiControl="projectBasket.validateForm" id="formPaymentUExist" style="display:none;">
						<input type="hidden" name="order_type" value="exist" />
						<input type="hidden" name="countryId" value="{$selectedCountry}">
						<input type="hidden" name="paymentType" value="0" id="paymentTypeUexist">
						<div class="item-group">
							<div class="item">
								<label>E-mail<span>*</span></label>
								<input type="text" name="email" id="email_f" placeholder="">
							</div>
							<div class="item">
								<label>пароль<span>*</span></label>
								<input type="password" name="password" id="password_f" placeholder="">
							</div>
							<div class="item">
							<button type="button" onclick="validate_f()" style="background: #000;color: #fff;" >Выбор оплаты</button>
						</div>
						</div>
					</form>
				</div>
				<div class="tab tab2" id="tabArea:fast">
					<form class="form-box" ui="Form" uiControl="projectBasket.validateForm" id="formPaymentFast">
						<input type="hidden" name="order_type" value="fast" />
						<input type="hidden" name="countryId" value="{$selectedCountry}">
						<input type="hidden" name="paymentType" value="0" id="paymentTypeFast">
						<div class="item-group">
                            <div class="item">
								<label>Имя<span>*</span></label>
								<input type="text" name="name" placeholder="">
							</div>
							<div class="item">
								<label>E-mail<span>*</span></label>
								<input type="text" name="email" id="email" placeholder="">
							</div>
							<div class="item">
								<label>Телефон<span>*</span></label>
								<input type="text" name="phone" id="phone" placeholder="">	
							</div>
                            <div class="item">
								<button type="button" onclick="validate_fast()" style="background: #000;color: #fff;" >Выбор оплаты</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
</script>

<script type="text/html" id="templateBasketPayments">
	
	<var (selectedCountry = projectBasket.get('country'))>
	<var (selectedConfirm = projectBasket.get('confirm'))>
	
	<% if ( ! selectedConfirm || ! projectBasket.get('orderAvailable')) return ''; %>
	
	<div class="block" id="payment_types" style="display:none">
		<div class="left">
		<if (selectedCountry == 1)>
			<div class="head">
				<p id="payment"> Оплатить Online</p>
			</div>
			<div class="payments-box">
				<!--span class="p13 active" onclick="Unit.inner(getPaymentId(),13);Unit(getForm()).onSubmit()" title="Wallet One payment system"></span-->
				<span class="p14 active" onclick="Unit.inner(getPaymentId(),14);Unit(getForm()).onSubmit()" title="LiqPay payment system"></span>
			</div>
			<div class="head">
				<p id="payment">Перевод на банковскую карту</p>
			</div>
		<else>
			<div class="head">
				<p id="payment"> Оплата на ФИО </p>
			</div>
		<endif>
			<div class="payments-box">
				<if (selectedCountry == 1)>
					<span class="p12 active" onclick="Unit.inner(getPaymentId(),6);Unit(getForm()).onSubmit()"></span>
				<else>
					<span class="p7 active" onclick="Unit.inner(getPaymentId(),1);Unit(getForm()).onSubmit()"></span>
					<span class="p8 active" onclick="Unit.inner(getPaymentId(),2);Unit(getForm()).onSubmit()"></span>
					<span class="p9 active" onclick="Unit.inner(getPaymentId(),3);Unit(getForm()).onSubmit()"></span>
					<span class="p10 active" onclick="Unit.inner(getPaymentId(),4);Unit(getForm()).onSubmit()"></span>
					<span class="p11 active" onclick="Unit.inner(getPaymentId(),5);Unit(getForm()).onSubmit()"></span>
				<endif>
				<input type="hidden" name="paymentType" value="1">
			</div>
		</div>
		<div class="right">
			<p class="h">
				100% гарантия безопасного платежа
			</p>

			<p>
				Безопасность платежей обеспечивается использованием SSL протокола для передачи конфиденциальной информации от клиента на сервер системы для дальнейшей обработки. Дальнейшая передача
				информации осуществляется по закрытым банковским сетям высшей степени защиты.
			</p>

			<p>
				Обработка полученных в зашифрованном виде конфиденциальных данных клиента (реквизиты карты, регистрационные данные и т. д.) производится в процессинговом центре. Таким образом, никто,
				даже продавец, не может получить персональные и банковские данные клиента, включая информацию о его покупках, сделанных в других магазинах.
			</p>
		</div>
	</div>
	
</script>

<section class="content cart hidden" id="orderSoccess">
	<div class="block simplePage">
		<div class="head">Ваш заказ</div>
		<div class="body">Спасибо, Ваш заказ в обработке!<br/><br/>Ожидайте письмо с точной суммой и реквизитами оплаты на e-mail, который Вы указали при оформлении заказа.</div>
	</div>
</section>

<section class="content cart hidden" id="orderFailed">
	<div class="block simplePage">
		<div class="head">Ваш заказ</div>
		<div class="body">Не удалось оформить заказ!<br><br>Попробуйте позже или свяжитесь с нашим менеджером.</div>
	</div>
</section>

<div id="orderPage">
	<section class="cart" id="basketCart"></section>
	<section class="form" id="basketForm"></section>
	<section class="section-hr gray"><div class="block"></div></section>
	<section class="payments" id="basketPayments"></section>
</div>

<script>
project.currencyUsd = '<?= (float) $this->getConfig('currencyUsd') ?>';
project.currencyRub = '<?= (float) $this->getConfig('currencyRub') ?>';
projectBasket.updateBasket();
</script>
