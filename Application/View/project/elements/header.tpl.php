<!DOCTYPE html>
<html>
  <head>
	<meta content="text/html; charset=utf-8" http-equiv=Content-Type>
	<title>project</title>

    <link href="/public/project/css/style.css" rel="stylesheet" type="text/css">
	<link href="https://plus.google.com/110224429186497736953" rel="publisher">

	<script src="/public/js/Unit.js"></script>
	<script src="/public/js/UnitUI.js"></script>
	<script src="/public/project/js/index.js"></script>
    
    <!--[if lt IE 9]>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
  </head>
  <body class="selectedLang-ru selectedCurrency-usd" id="root" itemscope itemtype="http://schema.org/WebPage">

    <section class="top-line">
      <div class="block">
        <ul class="login-ul">
          <li><a href="#" class="login">Вход</a></li>
          <li>|</li>
          <li><a href="#">Регистрация</a></li>
        </ul>

        <ul ui="Menu" uiAction="Unit('root').setClassValue('selectedCurrency',this.selected)" uiSelected="usd">
          <li><a href="#" uiMenuOption="usd">USD</a></li>
          <li>|</li>
          <li><a href="#" uiMenuOption="uah">UAH</a></li>
          <li>|</li>
          <li><a href="#" uiMenuOption="rub">RUR</a></li>
        </ul>

        <ul ui="Menu" uiAction="Unit('root').setClassValue('selectedLang',this.selected)" uiSelected="ru">
          <li><a href="#" uiMenuOption="ru">Ru</a></li>
          <li>|</li>
          <li><a href="#" uiMenuOption="en">En</a></li>
        </ul>
      </div>
    </section>

    <header>
      <div class="block">
        <div class="logo-box">
          <a href="#" class="logo"></a>
        </div>

        <div class="search-box">
          <div class="search">
            <a href="#" class="clean"></a>
            <input type="text" value="<?= $config->get('indexSearchLabel') ?>">
            <button></button>
          </div>
        </div>

        <div class="phones-box">
          <div class="phones">
          	<?= $config->get('indexPhoneFirst') ?><br><?= $config->get('indexPhoneSecond') ?>
          	<!--0 800 123 &bull; 45 &bull; 67<br>+38 063 400 &bull; 30 &bull; 20--></br>
          	</div>
        </div>

        <div class="cart-box">
          <div class="cart">
            <a href="#">Моя Корзина</a><br>

            <p>150 товаров | 500 USD</p>
          </div>
        </div>
      </div>
    </header>

    <section class="section-hr">
      <div class="block"></div>
    </section>

    <section class="top-menu">
      <div class="block">
        <ul>
        	<?php foreach ($menuMain as $menuItem): ?><li><a href="<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a></li><?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section class="section-hr">
      <div class="block"></div>
    </section>
