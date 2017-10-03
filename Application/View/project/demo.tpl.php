<?php

$config			= $this->loadModel('config');

$menu			= $this->loadModel('menu');
$menuList		= $menu->getGrouped();
$menuMain		= get($menuList, 1, array());
$menuHelp		= get($menuList, 2, array());
$menuAbout		= get($menuList, 3, array());

$product		= $this->loadModel('product');
$productList	= $product->find(array('where'=>array('active'=>1)));

$usd	= (float) $config->get('currencyUsd');
$rub	= (float) $config->get('currencyRub');

?>

<!doctype html>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv=Content-Type>
	<title>project</title>
	<link rel="stylesheet" type="text/css" href="/public/project/index.css">
	<script src="/public/js/Unit.js"></script>
	<script src="/public/js/UnitUI.js"></script>
</head>
<body class="selectedLang-ru selectedCurrency-usd" id="root">


<header>

	<section>
		
		<menu ui="Menu" uiSelected="ru" uiAction="Unit('root').setClassValue('selectedLang',this.selected)">
			<span uiMenuOption="ru">Ru</span>
			|
			<span uiMenuOption="en">En</span>
		</menu>
		
		<menu ui="Menu" uiSelected="usd" uiAction="Unit('root').setClassValue('selectedCurrency',this.selected)">
			<span uiMenuOption="usd">USD</span>
			|
			<span uiMenuOption="uah">UAH</span>
			|
			<span uiMenuOption="rub">RUR</span>
		</menu>
		
		<menu class="ru">
			<span class="a">Вход</span> | <a href="/register">Регистрация</a>
		</menu>
		
		<menu class="en">
			<span class="a">Login</span> | <a href="/register">Registration</a>
		</menu>
		
	</section>
	
	<section>
		
		<a href="/" class="logo">project<br><small>оптовый интернет-магазин</small></a>
		
		<form action="/search" method="GET">
			<input type="reset" value="">
			<input type="text" name="query" placeholder="<?= $config->get('indexSearchLabel') ?>">
			<input type="submit" value="">
		</form>
		
		<address>
			<?= $config->get('indexPhoneFirst') ?>
			<br>
			<?= $config->get('indexPhoneSecond') ?>
		</address>
		
		<fieldset class="ru">
			<button>МОЯ КОРЗИНА</button>
			<p class="uah">150 товаров | <?= price(1500) ?> грн.</p>
			<p class="usd">150 товаров | <?= price(1500 / $usd) ?> $</p>
			<p class="rub">150 товаров | <?= price(1500 / $rub) ?> р.</p>
		</fieldset>
		
		<fieldset class="en">
			<button>MY BASKET</button>
			<p class="uah">150 products | <?= price(1500) ?> грн.</p>
			<p class="usd">150 products | <?= price(1500 / $usd) ?> $</p>
			<p class="rub">150 products | <?= price(1500 / $rub) ?> р.</p>
		</fieldset>
		
		<nav class="ru">
			<? foreach ($menuMain as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a><? endforeach; ?>
		</nav>
		
		<nav class="en">
			<? foreach ($menuMain as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleEn') ?></a><? endforeach; ?>
		</nav>
		
	</section>
	
</header>



<section class="bigboard">
	
	<ul ui="Menu" uiRequired="true">
		<li uiMenuOption="#bigBanner1" class="hidden"></li>
		<li uiMenuOption="#bigBanner2" class="hidden"></li>
		<li uiMenuOption="#bigBanner3" class="hidden"></li>
	</ul>
	
	<img src="/public/images/banners/big1.jpg" id="bigBanner1">
	<img src="/public/images/banners/big2.jpg" id="bigBanner2">
	<img src="/public/images/banners/big3.jpg" id="bigBanner3">
	
</section>



<section class="information">
	
	<a href="/" class="infDiscount"></a>
	<a href="/" class="infDelivery"></a>
	<a href="/" class="infSocial"></a>
	
	<? /*<hgroup>
		<h3>Для минимального заказа</h3>
		<h2>ПО ОПТОВЫМ ЦЕНАМ</h2>
		<h4>купите любые <big>3</big> единицы товара</h4>
		<h5><a href="/">Узнать больше</a></h5>
	</hgroup>
	
	<hgroup>
		<h3>Для минимального заказа</h3>
		<h2>ПО ОПТОВЫМ ЦЕНАМ</h2>
		<h4>купите любые <big>3</big> единицы товара</h4>
		<h5><a href="/">Узнать больше</a></h5>
	</hgroup>
	
	<hgroup>
		<h3>Для минимального заказа</h3>
		<h2>ПО ОПТОВЫМ ЦЕНАМ</h2>
		<h4>купите любые <big>3</big> единицы товара</h4>
		<h5><a href="/">Узнать больше</a></h5>
	</hgroup> */ ?>
	
</section>



<section class="slider">
	
	<h1 class="ru">НОВЫЕ ПОСТУПЛЕНИЯ</h1>
	<h1 class="en">NEW PRODUCTS</h1>
		
	<input type="button" class="prev" value="">
	<input type="button" class="next" value="">
	
	<div>
		<div>
			
			<? foreach ($productList as $productItem): ?>
			<? $productImage = array_shift($product->getImages($productItem['id'])); ?>
				<? if ($productImage): ?>
				<article>
					<a href="/product/<?= get($productItem,'url') ?>">
						<img src="/public/products/prev/<?= $productImage ?>">
						<span class="ru"><?= get($productItem,'titleRu') ?></span>
						<span class="en"><?= get($productItem,'titleEn') ?></span>
						<p class="uah"><?= price(get($productItem,'price')) ?> грн.</p>
						<p class="usd"><?= price(get($productItem,'price') / $usd) ?> $</p>
						<p class="rub"><?= price(get($productItem,'price') / $rub) ?> р.</p>
					</a>
				</article>
				<? endif; ?>
			<? endforeach; ?>
			
		</div>
	</div>
	
</section>



<section class="subscribe">
	
	<h2>НЕ ПРОПУСТИТЕ АКЦИИ, НОВИНКИ И СПЕЦПРЕДЛОЖЕНИЯ!</h2>
	
	<form action="/subscribe">
		<label>Подпишитесь, чтобы первыми узнавать о новинках и акциях.</label>
		<input type="text" placeholder="Имя">
		<input type="text" placeholder="E-mail">
		<input type="submit" value="Подписаться">
	</form>
	
</section>



<section class="slider">
	
	<h1 class="ru">ПОПУЛЯРНЫЕ ТОВАРЫ</h1>
	<h1 class="en">POPULAR PRODUCTS</h1>
	
	<input type="button" class="prev" value="">
	<input type="button" class="next" value="">
	
	<div>
		<div>
			
			<? foreach ($productList as $productItem): ?>
			<? $productImage = array_shift($product->getImages($productItem['id'])); ?>
				<? if ($productImage): ?>
				<article>
					<a href="/product/<?= get($productItem,'url') ?>">
						<img src="/public/products/prev/<?= $productImage ?>">
						<span class="ru"><?= get($productItem,'titleRu') ?></span>
						<span class="en"><?= get($productItem,'titleEn') ?></span>
						<p class="uah"><?= price(get($productItem,'price')) ?> грн.</p>
						<p class="usd"><?= price(get($productItem,'price') / $usd) ?> $</p>
						<p class="rub"><?= price(get($productItem,'price') / $rub) ?> р.</p>
					</a>
				</article>
				<? endif; ?>
			<? endforeach; ?>
			
		</div>
	</div>
	
</section>



<section class="partners">
	
	<h2><b>ПАРТНЕРСКАЯ ПРОГРАММА №<big>1</big></b></h2>
	
	<h1>ПОЛУЧИ ГОТОВЫЙ БИЗНЕС ЗА <big>5000</big> РУБЛЕЙ В ГОД</h1>
		
	<h3>ЗАРАБОТАЙТЕ С НАМИ! <input type="button" class="bigbutton" value="УЗНАТЬ БОЛЬШЕ"></h3>
	
</section>



<footer>
	
	<figure class="ru">
		<figcaption>СЕРВИС И ПОМОЩЬ</figcaption>
		<? foreach ($menuHelp as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a><br><? endforeach; ?>
	</figure>
	
	<figure class="en">
		<figcaption>HELP AND SERVICES</figcaption>
		<? foreach ($menuHelp as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleEn') ?></a><br><? endforeach; ?>
	</figure>
	
	<figure class="ru">
		<figcaption>О КОМПАНИИ</figcaption>
		<? foreach ($menuAbout as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a><br><? endforeach; ?>
	</figure>
	
	<figure class="en">
		<figcaption>ABOUT COMPANY</figcaption>
		<? foreach ($menuAbout as $menuItem): ?><a href="/<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleEn') ?></a><br><? endforeach; ?>
	</figure>
	
	<figure class="ru">
		<figcaption>КОНТАКТЫ</figcaption>
		<p class="icoPhone">
			<?= $config->get('indexPhoneFirst') ?>
			<br>
			<?= $config->get('indexPhoneSecond') ?>
		</p>
		<p class="icoMail"><?= $config->get('indexMail') ?></p>
		<a href="/contacts">Все контакты</a>
	</figure>
	
	<figure class="en">
		<figcaption>CONTACTS</figcaption>
		<p class="icoPhone">
			<?= $config->get('indexPhoneFirst') ?>
			<br>
			<?= $config->get('indexPhoneSecond') ?>
		</p>
		<p class="icoMail"><?= $config->get('indexMail') ?></p>
		<a href="/contacts">All contacts</a>
	</figure>
	
	<figure class="ru social">
		<figcaption>ПРИСОЕДИНЯЙТЕСЬ В СОЦСЕТЯХ:</figcaption>
		<img src="/public/images/banners/social.jpg">
	</figure>
	
	<figure class="en social">
		<figcaption>WE IN SOCIAL NETWORKS:</figcaption>
		<img src="/public/images/banners/social.jpg">
	</figure>
	
	<hr>
	
	<figure class="ru payments">
		<figcaption>МЫ ПРИНИМАЕМ К ОПЛАТЕ:</figcaption>
		<img src="/public/images/banners/payments.jpg">
	</figure>
	
	<figure class="en payments">
		<figcaption>WE ACCEPT:</figcaption>
		<img src="/public/images/banners/payments.jpg">
	</figure>
	
	<hr>
	
	<center><?= $config->get('indexCopy') ?></center>
	
</footer>



</body>
</html>