<?php

$menuMain	= $this->getMenu(1);
$menuHelp	= $this->getMenu(2);
$menuAbout	= $this->getMenu(3);

$aGoogle	= file_exists('seo/google.txt') ? file_get_contents('seo/google.txt') : '';
$aYandex	= file_exists('seo/yandex.txt') ? file_get_contents('seo/yandex.txt') : '';
$aRemark	= file_exists('seo/remark.txt') ? file_get_contents('seo/remark.txt') : '';
$aJvSite	= file_exists('seo/jvsite.txt') ? file_get_contents('seo/jvsite.txt') : '';
//$aReport	= file_exists('seo/report.txt') ? file_get_contents('seo/report.txt') : '';

//$this->getConfig('analyticGoogle')
//$this->getConfig('analyticYandex')

?>

<!DOCTYPE html>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv=Content-Type>
	<meta name="w1-verification" content="154548456807" />
	<meta name="robots" content="noindex,nofollow">
	
<?php if (isset($this->metaTitle)): ?>
	<title><?= $this->metaTitle ?></title>
<?php else: ?>
	<title>Интернет-магазин одежды, обуви и сумок оптом и в розницу от производителя в Киеве и Украине - интернет-магазин project</title>
<?php endif; ?>

	<meta name="keywords" content="<?= isset($this->metaKeywords) ? $this->metaKeywords . ', ' : '' ?>интернет-магазин <?= $this->getHost() ?>">
<?php if (isset($this->metaDescription)): ?>
	<meta name="description" content="<?= $this->metaDescription ?>">
<?php endif; ?>
	
	<link rel="alternate" href="http://project.com.ua<?= get($_SERVER, 'REQUEST_URI','/') ?>" hreflang="ru-UA" />
	<link rel="alternate" href="http://project.ru<?= get($_SERVER, 'REQUEST_URI','/') ?>" hreflang="ru-RU" />
	<link rel="alternate" href="http://project.co.uk<?= get($_SERVER, 'REQUEST_URI','/') ?>" hreflang="en-GB" />
	<link rel="alternate" href="http://project.com.ua<?= get($_SERVER, 'REQUEST_URI','/') ?>" hreflang="x-default" />
	<?php if (isset($this->cnnc) && $this->cnnc): ?><link rel="canonical" href="http://<?= $this->getHost() ?>/<?= $this->cnnc ?>/" />
	<?php elseif (count($_GET)): ?><link rel="canonical" href="http://<?= $this->getHost() . preg_replace('/\?.*/','',get($_SERVER, 'REQUEST_URI')) ?>" /><?php endif; ?>
	<?php if (isset($this->prev) && $this->prev): ?><link rel="prev" href="http://<?= $this->getHost() ?><?= $this->prev ?>" /><?php endif; ?>
	<?php if (isset($this->next) && $this->next): ?><link rel="next" href="http://<?= $this->getHost() ?><?= $this->next ?>" /><?php endif; ?>

	<link href="/public/project/css/index.css" rel="stylesheet" type="text/css">
	<link href="https://plus.google.com/110224429186497736953" rel="publisher">

	<script src="/public/js/Unit.min.js"></script>
	<script src="/public/js/UnitUI.min.js"></script>
	<script src="/public/project/js/index.js"></script>
    <script src="/public/project/js/jquery-1.11.2.min.js"></script>
	<!--
	<script src="/public/project/js/jquery.js"></script>
	<script src="/public/project/js/jquery.flexslider-min.js"></script>
	<script src="/public/project/js/jquery.nouislider.js"></script>
	<script src="/public/project/js/jquery.liblink.js"></script>
	<script src="/public/project/js/jquery.selecter.js"></script>
	<script src="/public/project/js/jquery.popups.js"></script>
	<script src="/public/project/js/lightbox.min.js"></script>
	<script src="/public/project/js/init.js"></script>
	-->
	
	<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->
</head>
<body class="selectedLang-ru selectedCurrency-usd" id="root" itemscope itemtype="http://schema.org/WebPage">

<?php

/*switch ($this->getHost())
{
	case 'project.ru':
		print(str_replace('$CODE','UA-54035450-2',$aGoogle));
	break;
	case 'project.com.ua':
		print(str_replace('$CODE','UA-54035450-3',$aGoogle));
	break;
	case 'project.co.uk':
		print(str_replace('$CODE','UA-54035450-4',$aGoogle));
	break;
}

*/ ?>

<?= $aGoogle ?>

<script type="text/html" id="loginPopupTemplate">
	<div class="loginPopup">
		<form ui="Form" uiControl="project.login" id="popupLoginForm">
			<div class="lpClose" onclick="Unit.remove('loginPopup')"></div>
			<div class="lpTitle">Войти на сайт</div>
			<div class="lpMessage" uiForm="message"></div>
			<div class="lpField" uiField="email"><input type="text" name="email" placeholder="Логин"></div>
			<div class="lpField" uiField="password"><input type="password" name="password" placeholder="Пароль"></div>
			<div class="lpPassword"><span onclick="Unit.trigger('popupLoginForm');Unit.trigger('popupRestoreForm');">Забыли пароль?</span></div>
			<div class="lpSubmit"><input type="submit" value="ВОЙТИ"></div>
		</form>
		<form ui="Form" uiControl="project.restore" id="popupRestoreForm" class="hidden">
			<div class="lpClose" onclick="Unit.remove('loginPopup')"></div>
			<div class="lpTitle">Восстановление пароля</div>
			<div class="lpRestore">Для восстановления пароля введиде<br>E-mail указанный при регистрации.</div>
			<div class="lpMessage" uiForm="message"></div>
			<div class="lpField" uiField="email"><input type="text" name="email" placeholder="Введите Ваш E-mail"></div>
			<div class="lpPassword">&nbsp;</div>
			<div class="lpSubmit"><input type="submit" value="ОТПРАВИТЬ"></div>
		</form>
		<form id="popupRestoreComplete" class="hidden">
			<div class="lpClose" onclick="Unit.remove('loginPopup')"></div>
			<div class="lpTitle">Восстановление пароля</div>
			<div class="lpRestore">На Ваш E-mail отправлено письмо<br>с новым паролем.</div>
			<div class="lpMessage" uiForm="message"></div>
			<div class="lpField"></div>
			<div class="lpPassword">&nbsp;</div>
			<div class="lpSubmit"><input type="button" value="ВОЙТИ" onclick="Unit.remove('loginPopup');project.showLoginPopup();"></div>
		</form>
	</div>
</script>

<section class="top-line">
	<div class="block">
		<ul class="login-ul" id="accountArea">
			<li class="out"><span onclick="project.showLoginPopup()" class="login"><?= $this->translate('Вход', 'Log in') ?></span></li>
			<li class="out">|</li>
			<li class="out"><span onclick="document.location = '/registration/'"><?= $this->translate('Регистрация', 'Registration') ?></span></li>
			<li class="in"><span onclick="document.location='/account/'" id="accountName"></span></li>
			<li class="in">|</li>
			<li class="in"><span class="login" onclick="project.logout()"><?= $this->translate('Выйти', 'Log out') ?></span></li>
		</ul>

		<ul ui="Menu" uiAction="Unit('root').setClassValue('selectedCurrency',this.selected);Unit.setCookie('currency',this.selected);" id="selectCurrency">
			<li><span uiMenuOption="usd">USD</span></li>
			<li>|</li>
			<li><span uiMenuOption="uah">UAH</span></li>
			<li>|</li>
			<li><span uiMenuOption="rub">RUR</span></li>
		</ul>

		<ul ui="Menu" uiAction="Unit('root').setClassValue('selectedLang',this.selected);Unit.setCookie('language',this.selected);" id="selectLanguage">
			<li><span uiMenuOption="ru">Ru</span></li>
			<li>|</li>
			<li><span uiMenuOption="en">En</span></li>
		</ul>
		
		<ul>
			<li><a href="/comments/" class="comments hidden" id="commentsCounter">Отзывы</a></li>
		</ul>
		
		<script>
		var host = location.host.replace('www.','');
		var defaultCurrency = 'uah';
		var defaultLanguage = 'ru';
		if (host == 'project.ru') defaultCurrency = 'rub';
		if (host == 'project.co.uk') defaultCurrency = 'usd', defaultLanguage = 'en';
		var currency = Unit.getCookie('currency') || defaultCurrency;
		var language = Unit.getCookie('language') || defaultLanguage;
		Unit('selectCurrency').setAttribute('uiSelected', currency);
		Unit('selectLanguage').setAttribute('uiSelected', language);
		Unit('root').setClassValue('selectedCurrency', currency);
		Unit('root').setClassValue('selectedLang', language);
		</script>
	</div>
</section>

<header>
	<div class="block">
		
		<div class="logo-box">
			<a href="/" class="logo"></a>
		</div>
	
		<div class="search-box">
			<form method="GET" action="/search/">
				<div class="search">
					<span class="clean" onclick="Unit.inner('searchString','')"></span>
					<input type="text" name="q" value="" id="searchString" placeholder="<?= $this->getConfig('indexSearchLabel') ?>">
					<button type="submit"></button>
				</div>
			</form>
		</div>
		
		<div class="phones-box">
			<div class="phones">
				<?php
				
				switch ($this->getHost())
				{
					case 'project.ru':
						print($this->getConfig('indexRuPhoneFirst') . '<br>' . $this->getConfig('indexRuPhoneSecond'));
					break;
					case 'project.co.uk':
						print($this->getConfig('indexWwPhoneFirst') . '<br>' . $this->getConfig('indexWwPhoneSecond'));
					break;
					default:
						print($this->getConfig('indexPhoneFirst') . '<br>' . $this->getConfig('indexPhoneSecond'));
					break;
				}
				
				?>
				<!--0 800 123 &bull; 45 &bull; 67<br>+38 063 400 &bull; 30 &bull; 20--></br>
			</div>
		</div>
	
		<div class="cart-box">
			<div class="cart">
				<a href="<?= $this->link('basket') ?>"><?= $this->translate('Моя Корзина', 'My basket') ?></a><br>
				<p><span id="basketAmount">0</span> <?= $this->translate('товаров', 'products') ?> | <span id="basketPrice"><?= $this->price(0) ?></span></p>
			</div>
		</div>
	
	</div>
</header>

<section class="section-hr">
	<div class="block"></div>
</section>

<section class="top-menu">
	<div class="block">
		<ul class="ru"><?php foreach ($menuMain as $menuItem): ?><li><a href="<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a></li><?php endforeach; ?></ul>
		<ul class="en"><?php foreach ($menuMain as $menuItem): ?><li><a href="<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleEn') ?></a></li><?php endforeach; ?></ul>
	</div>
</section>

<section class="section-hr">
	<div class="block"></div>
</section>

<?php

if ($this->isView('project/pages/' . get($this->args, 0)))
{
	print $this->loadView('project/pages/' . get($this->args, 0));
}

else if ($this->page)
{        
	print '<section class="content cart">';
	print '<div class="block simplePage">';?>
    <div class="breadcrumbs">
					<ul itemprop="breadcrumb">
						<li><a href="/"><?= $this->translate('Главная страница', 'Home page') ?></a></li>
						<li>|</li>
						<li><a href="<?=$this->url?>"><?=get($this->page, 'titleRu')?></a></li>
					</ul>
				</div><?
	print '<div class="head"><h1>' . get($this->page, 'titleRu') . '</h1></div>';
	print get($this->page, 'contentRu');
	print '</div>';
	print '</section>';
}

?>

<section class="links-box">
	<div class="block">

		<div class="links">
			<div class="head"><?= $this->translate('Сервис и помощь', 'Support') ?></div>
			<div class="clear"></div>
			<ul class="ru"><?php foreach ($menuHelp as $menuItem): ?><li><span class="link" onclick="document.location='<?= get($menuItem, 'url') ?>'"><?= get($menuItem, 'titleRu') ?></span></li><?php endforeach; ?></ul>
			<ul class="en"><?php foreach ($menuHelp as $menuItem): ?><li><span class="link" onclick="document.location='<?= get($menuItem, 'url') ?>'"><?= get($menuItem, 'titleEn') ?></span></li><?php endforeach; ?></ul>
		</div>

		<div class="links">
			<div class="head"><?= $this->translate('О компании', 'About Company') ?></div>
			<div class="clear"></div>
			<ul class="ru"><?php foreach ($menuAbout as $menuItem): ?><li><span class="link" onclick="document.location='<?= get($menuItem, 'url') ?>'"><?= get($menuItem, 'titleRu') ?></span></li><?php endforeach; ?></ul>
			<ul class="en"><?php foreach ($menuAbout as $menuItem): ?><li><span class="link" onclick="document.location='<?= get($menuItem, 'url') ?>'"><?= get($menuItem, 'titleEn') ?></span></li><?php endforeach; ?></ul>
		</div>

		<div class="contacts">
			<div class="head"><?= $this->translate('Контакты', 'Contacts') ?></div>
			<div class="clear"></div>
			<div class="phones">
				<?php
				
				switch ($this->getHost())
				{
					case 'project.ru':
						print($this->getConfig('indexRuPhoneFirst') . '<br>' . $this->getConfig('indexRuPhoneSecond'));
					break;
					case 'project.co.uk':
						print($this->getConfig('indexWwPhoneFirst') . '<br>' . $this->getConfig('indexWwPhoneSecond'));
					break;
					default:
						print($this->getConfig('indexPhoneFirst') . '<br>' . $this->getConfig('indexPhoneSecond'));
					break;
				}
				
				?>
			</div>
			<div class="clear"></div>
			<div class="email"><a href="mailto:<?= $this->getConfig('indexMail') ?>"><?= $this->getConfig('indexMail') ?></a></div>
			<div class="clear"></div>
			<a href="<?= $this->link('contacts') ?>" class="all"><?= $this->translate('Все контакты', 'All contacts') ?></a>
		</div>

		<div class="group hidden">
			<div class="head"><?= $this->translate('Присоединяйтесь в соцсетях:', 'Follow us:') ?></div>
			<div class="vk"><img src="/public/project/img/join.png" alt=""/></div>
		</div>

	</div>
</section>

<section class="section-hr">
	<div class="block"></div>
</section>

<section class="partners hidden">
	<div class="head"><?= $this->translate('мы принимаем К оплате:', 'We accept:') ?></div>
	<div class="items"><img src="/public/project/img/partners.png" alt=""/></div>
</section>

<section class="section-hr gray">
	<div class="block"></div>
</section>

<footer>
	<div class="block"><?= $this->getConfig('indexCopy') ?></div>
</footer>

<?php

/*switch ($this->getHost())
{
	case 'project.ru':
		print(str_replace('$CODE','27024543',$aYandex));
	break;
	case 'project.com.ua':
		print(str_replace('$CODE','27024801',$aYandex));
	break;
	case 'project.co.uk':
		//print(str_replace('$CODE','',$this->getConfig('analyticYandex')));
	break;
}*/
switch ($this->getHost()){
	case 'project.ru':
		$src_code= '<script type="text/javascript">(window.Image ? (new Image()) : document.createElement(\'img\')).src = location.protocol + \'//vk.com/rtrg?r=JyG9MQDL0/N3Q/uGyz0*P6zUv4vppjLeUlXuh38HB9KMG2SbA8ISdNq21j4p8bABDMytca2NPOUaP*mZQ7teVX6JjcJndKu0fRxCVdCpyjD/u5thMTIzFgXRQLguea669Uanw5tvI35IV6ky3L9IkTOiiKtxKbmSqzcMJgjhXmQ-\';</script>';
	break;
	case 'project.com.ua':
		$src_code= '<script type="text/javascript">(window.Image ? (new Image()) : document.createElement(\'img\')).src = location.protocol + \'//vk.com/rtrg?r=VsM8ohOSrzhekdIg2LL4LT6PxRo*C4bb*S6w*H1*tF8UVcG9fBDxZHkGxkHALUuDpH1zX/IkjJgbYWqklk9TCr08NPBH5Ro2KgP/UDPhJ5Lj8KopbYvh6y*gSUY8G5MPD33fYcFiCn36RfXUT3r5ks73w4KwzP/qv9my*jDNxdY-\';</script>';
	break;
	default:
		$src_code= '';
	break;
}
echo $src_code;
?>

<?= $aYandex ?>

<?= $aRemark ?>

<?= $aJvSite ?>

<script type="text/javascript">
var _cp = {trackerId: 10512};
(function(d){
var cp=d.createElement('script');cp.type='text/javascript';cp.async = true;
cp.src='//tracker.cartprotector.com/cartprotector.js';
var s=d.getElementsByTagName('script')[0]; s.parentNode.insertBefore(cp, s);
})(documen
</body>
</html>
