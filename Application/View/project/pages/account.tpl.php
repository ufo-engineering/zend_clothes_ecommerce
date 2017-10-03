<?php

$account	= $this->loadModel('account');
$status		= $account->status();
$id			= get($status, 'id');

?>

<section class="content cart form registration">
	
	<div class="block simplePage">
		
		<?php if ($status && $id): ?>
		
		<?php
		
		$orders			= $this->loadModel('order');
		$ordersInfo		= $orders->findAll(array('where' => array('accountId' => $id)));
		$orderStatuses	= $orders->getOrderStatuses();
		
		$ordersList		= get($ordersInfo, 'records');
		$productsList	= get($ordersInfo, 'products');
		$positionsList	= get($ordersInfo, 'positions');
		$attributesList	= get($ordersInfo, 'attributes');
		$positionsGroup	= group($positionsList, 'orderId');
		
		/*
		print('<pre>');
		print_r($ordersList);
		print('</pre>');
		*/
		
		?>
		
		<aside class="leftSide">
				
				<ul class="items">
					<li class="active">
						<a href="/account/">Личный кабинет</a>
						<div class="clear"></div>
						<ul ui="Menu" uiRequired="true" id="accountMenu">
							<li><span uiMenuOption="#account:orders">Мои заказы</span></li>
							<li><span uiMenuOption="#account:billing">Мой счет</span></li>
							<li><span uiMenuOption="#account:information">Мои данные</span></li>
							<li><span uiMenuOption="#account:viewed">Просмотренные товары</span></li>
							<li class="hidden"><span uiMenuOption="#account:alerts">Оповещания</span></li>
							<li class="hidden"><span uiMenuOption="#account:edit">Редактирование</span></li>
							<li class="hidden"><span uiMenuOption="#account:password">Изменение пароля</span></li>
						</ul>
					</li>
				</ul>
				
			</aside>
			
			<aside class="rightSide">
				
				<div id="account:orders" class="hidden">
					<h1 class="title"><span>Мои заказы</span></h1>
					
					<?php if (count($ordersList)): ?>
						<table class="accountTable">
							<tr>
								<th>Номер заказа</th>
								<th>Сумма заказа</th>
								<th>Дата заказа</th>
								<th>Статус заказа</th>
								<th></th>
							</tr>
							<?php foreach ($ordersList as $i => $orderItem): ?>
							<tr>
								<td>#<?= get($orderItem, 'id') ?></td>
								<td><?= $this->price(get($orderItem, 'price')) ?></td>
								<td><?= date('d.m.Y', get($orderItem, 'added')) ?></td>
								<td><?= get($orderStatuses, get($orderItem, 'status')) ?></td>
								<td><span class="link" onclick="Unit.trigger('orderInfo_<?= $i ?>')">просмотреть заказ</span></td>
							</tr>
							<tr id="orderInfo_<?= $i ?>" class="hidden">
								<td colspan="5">
									
									<table>
										<tr>
											<th>Товар</th>
											<th>Размер</th>
											<th>Цвет</th>
											<th>Количество</th>
											<th>Цена</th>
											<th>Сумма</th>
										</tr>
										<?php foreach (get($positionsGroup,get($orderItem, 'id')) as $orderPosition): ?>
											<?php $product = get($productsList, get($orderPosition, 'productId')); ?>
											<tr>
												<td>
													<a href="<?= $this->link(get($product, 'url')) ?><?= get($orderPosition, 'colorId') ?>/">
														<?= $this->getArticleFull($product) ?>
													</a>
												</td>
												<td><?= get($attributesList, get($orderPosition, 'sizeId')) ?></td>
												<td><?= get($attributesList, get($orderPosition, 'colorId')) ?></td>
												<td><?= get($orderPosition, 'amount') ?></td>
												<td><?= $this->price(get($orderPosition, 'price')) ?></td>
												<td><?= $this->price(get($orderPosition, 'price') * get($orderPosition, 'amount')) ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
									
								</td>
							</tr>
							<?php endforeach; ?>
						</table>
					<?php else: ?>
						<p>В данный момент у вас нет заказов.<br>Для оформления заказа добавьте товары в корзину.<br><a href="<?= $this->link('catalog') ?>">Перейти в каталог</a></p>
					<?php endif; ?>
				</div>
				
				<div id="account:billing" class="hidden">
					<h1 class="title"><span>Мой счет</span></h1>
					<p>На Вашем счету нед средств.</p>
				</div>
				
				<div id="account:information" class="hidden">
					<h1 class="title"><span>Мои данные</span></h1>
					<table>
						<tr>
							<td>Имя</td>
							<td><?= get($status, 'name') ?></td>
						</tr>
						<tr>
							<td>Фамилия</td>
							<td><?= get($status, 'lastname') ?></td>
						</tr>
						<tr>
							<td>E-mail</td>
							<td><?= get($status, 'email') ?></td>
						</tr>
						<tr>
							<td>Телефон</td>
							<td><?= get($status, 'phone') ?></td>
						</tr>
					</table>
					<button type="button" class="button" onclick="Unit('accountMenu').selectMenu('edit')">РЕДАКТИРОВАТЬ</button>
					&nbsp;
					&nbsp;
					<span class="link" onclick="Unit('accountMenu').selectMenu('password')">ИЗМЕНИТЬ ПАРОЛЬ</span>
				</div>
				
				<div id="account:edit" class="tabs hidden">
					<form class="form-box" ui="Form" uiControl="project.registrationControl" id="formAccount">
						<div><h1 class="title"><span>Редактирование</span></h1></div>
						<div id="basketError" uiForm="message" class="message"></div>
				
						<div class="item-group">
							<div class="item" uiField="name">
								<label>Имя<span>*</span></label>
								<input type="text" name="name" placeholder="" value="<?= get($status, 'name') ?>">
							</div>
							<div class="item" uiField="lastname">
								<label>Фамилия<span>*</span></label>
								<input type="text" name="lastname" placeholder="" value="<?= get($status, 'lastname') ?>">
							</div>
							<div class="item phone-item" uiField="email">
								<label>E-mail<span>*</span></label>
								<input type="text" name="email" placeholder="" value="<?= get($status, 'email') ?>">
							</div>
							<div class="item phone-item" uiField="phone">
								<label>Телефон<span>*</span></label>
								<input type="text" name="phone" placeholder="" value="<?= get($status, 'phone') ?>">
							</div>
						</div>
						
						<input type="hidden" name="id" value="<?= $id ?>">
						<button type="submit" class="button">СОХРАНИТЬ</button>
						&nbsp;
						&nbsp;
						<span class="link" onclick="Unit('accountMenu').selectMenu('information')">ОТМЕНА</span>
					</form>
				</div>
				
				<div id="account:password" class="tabs hidden">
					<form class="form-box" ui="Form" uiControl="project.registrationControl" id="formPassword">
						<div><h1 class="title"><span>Изменение пароля</span></h1></div>
						<div id="basketError" uiForm="message" class="message"></div>
				
						<div class="item phone-item" uiField="password">
							<label>Пароль<span>*</span></label>
							<input type="password" name="password" placeholder="" value="">
						</div>
						<div class="item phone-item" uiField="confirm">
							<label>Подтверждение пароля<span>*</span></label>
							<input type="password" name="confirm" placeholder="" value="">
						</div>
						
						<input type="hidden" name="id" value="<?= $id ?>">
						<button type="submit" class="button">СОХРАНИТЬ</button>
						&nbsp;
						&nbsp;
						<span class="link" onclick="Unit('accountMenu').selectMenu('information')">ОТМЕНА</span>
					</form>
				</div>
				
				<div id="account:viewed" class="hidden">
					<h1 class="title"><span>Просмотренные товары</span></h1>
					
					<?php
					
					$productView	= $this->loadModel('productView');
					$productViewed	= assoc($productView->get($id), 'id', 'productId');
					
					$product		= $this->loadModel('product');
					$productList	= $product->find(array('where' => array('id in' => array_values($productViewed)), 'limit' => 24));
					
					?>
					
					<?php if (count($productList)): ?>
					<div class="viewed-list">
						<div class="slidebar">
							<div class="slider">
								<ul class="slides">
									
									<?php $index = 0; ?>
									<?php foreach ($productList as $productItem): ?>
										<?php $productImage = array_shift($product->getImages($productItem['id'])); ?>
										<?php if ($productImage): ?>
											<li uiSliderItem="<?= $index++ ?>">
												<div class="item">
													<div class="img">
														<a href="<?= $this->link(get($productItem, 'url')) ?>">
															<img src="/public/products/mini/<?= get($productImage,'view') ?>" alt=""/>
														</a>
													</div>
													<a href="<?= $this->link(get($productItem, 'url')) ?>"><?= $this->getArticleFull($productItem) ?></a>
													<div class="price"><?= $this->price(get($productItem, 'price')) ?></div>
												</div>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
					<?php endif; ?>
					
				</div>
				
				<div id="account:alerts" class="hidden">
					<h1 class="title"><span>Оповещания</span></h1>
				</div>
				
			</aside>
		
		<?php else: ?>
			
			<div>
				Для доступа в личный кабинет необходимо авторизироваться:<br>
				<a href="/login/">Войти в личный кабинет</a>
			</div>
			
		<?php endif; ?>
	
	</div>
	
</section>