<?php

$product		= $this->loadModel('product');
$productListRec	= $product->find(array('where' => array('active' => 1, 'recommended in' => array(1,3)),'limit' => 30, 'order' => 'added', 'drect' => 0));
$productListNew	= $product->find(array('where' => array('active' => 1, 'recommended in' => array(1,2)),'limit' => 30, 'order' => 'added', 'drect' => 0));

$banner			= $this->loadModel('banner');
$bannerGrouped	= $banner->getGrouped();
$bannerBig		= get($bannerGrouped, 0);
$bannerSmall	= get($bannerGrouped, 1);

?>
<h1 style="display: none;">project - Интернет-магазин одежды оптом и в розницу от производителя</h1>
<section class="top-slider" ui="Slider" uiSliderTime="7000">
	<div class="block">
		<div class="slider">
			<ul class="slides" uiSlider="body">
				
				<?php $bannerIndex = 0; ?>
				<?php foreach ($bannerBig as $bannerId => $bannerInfo): ?>
				<li uiSliderItem="<?= $bannerIndex++ ?>">
					<div class="item">
						<div class="t" style="font: 100 45pt Roboto; line-height: 45pt; text-transform: uppercase;">
							<?= get($bannerInfo, 'text') ?>
						</div>
						<a href="<?= get($bannerInfo, 'link') ?>" class="btn">Узнать больше</a>
						<img src="<?= get($bannerInfo, 'image') ?>" alt=""/>
					</div>
				</li>
				<?php endforeach; ?>
				
				<!--
				<li uiSliderItem="0">
					<div class="item">
						<div class="t" style="top: 60px; font: 100 45pt Roboto; line-height: 45pt; text-transform: uppercase; text-align: right;">
							Новый<br>ассортимент<br>платьев
						</div>
						<div class="t" style="top: 250px; font: 700 22.5pt Roboto; line-height: 54pt; text-transform: uppercase; text-align: right;">
							от 150 грн
						</div>
						<a href="<?= $this->link('catalog') ?>" class="btn">Узнать больше</a>
						<img src="/public/project/img/top-slider/i1.png" alt=""/>
					</div>
				</li>
				<li uiSliderItem="1">
					<div class="item">
						<div class="t" style="top: 60px; font: 100 45pt Roboto; line-height: 45pt; text-transform: uppercase; text-align: right;">
							Новый<br>ассортимент<br>платьев
						</div>
						<div class="t" style="top: 250px; font: 700 22.5pt Roboto; line-height: 54pt; text-transform: uppercase; text-align: right;">
							от 150 грн
						</div>
						<a href="<?= $this->link('catalog') ?>" class="btn">Узнать больше</a>
						<img src="/public/project/img/top-slider/i2.png" alt=""/>
					</div>
				</li>
				<li uiSliderItem="2">
					<div class="item">
						<div class="t" style="top: 60px; font: 100 45pt Roboto; line-height: 45pt; text-transform: uppercase; text-align: right;">
							Новый<br>ассортимент<br>платьев
						</div>
						<div class="t" style="top: 250px; font: 700 22.5pt Roboto; line-height: 54pt; text-transform: uppercase; text-align: right;">
							от 150 грн
						</div>
						<a href="<?= $this->link('catalog') ?>" class="btn">Узнать больше</a>
						<img src="/public/project/img/top-slider/i3.png" alt=""/>
					</div>
				</li>
				-->
			</ul>
		</div>
		<ul class="navigation">
			<?php $bannerIndex = 0; ?>
			<?php foreach ($bannerBig as $bannerId => $bannerInfo): ?>
			<li uiSliderLink="<?= $bannerIndex++ ?>"></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="info-items">
	<div class="block">
		
		<?php $bannerIndex = 1; ?>
		<?php foreach ($bannerSmall as $bannerId => $bannerInfo): ?>
			<?php if ($bannerIndex <= 3): ?>
				<div class="item item<?= $bannerIndex++ ?>">
					<div class="background" style="background:url('<?= get($bannerInfo, 'image') ?>') no-repeat 0 0;"></div>
					<div class="text">
						<?= get($bannerInfo, 'text') ?>
					</div>
					<div class="more">
						<a href="<?= get($bannerInfo, 'link') ?>">Узнать больше</a>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		
		<!--
		<div class="item item1">
			<div class="t1">
				Для минимального заказа
			</div>
			<div class="t2">
				По оптовым ценам
			</div>
			<div class="t3">
				купите любые <span><?= $this->getHost() == 'project.com.ua' ? '5' : '10' ?></span> единиц товара
			</div>

			<div class="more">
				<a href="<?= $this->link('shipping') ?>">Узнать больше</a>
			</div>
		</div>
		
		<?php if ($this->getHost() == 'project.com.ua'): ?>
		<div class="item item2">
			<div class="t1">
				Доставка
			</div>
			<div class="t2">
				По Украине
			</div>
			<div class="t3" style="padding-top:12px; padding-bottom:10px;">
				<span>1-2</span> дня
			</div>

			<div class="more">
				<a href="<?= $this->link('shipping') ?>">Узнать больше</a>
			</div>
		</div>
		<?php else: ?>
		<div class="item item2">
			<div class="t1">
				Доставка
			</div>
			<div class="t2">
				По России, Казахстану
				<br>
				и Беларуси
			</div>
			<div class="t3">
				<span>5-7</span> дней
			</div>

			<div class="more">
				<a href="<?= $this->link('shipping') ?>">Узнать больше</a>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="item item3">
			<div class="t1">
				Присоединяйтесь
			</div>
			<div class="t2">
				к нашим группам
			</div>
			<div class="soc">
				<a href="#" class="vk"></a>
				<a href="#" class="fb"></a>
			</div>

			<div class="more">
				<a href="<?= $this->link('contacts') ?>">Узнать больше</a>
			</div>
		</div>
		-->
		
	</div>
</section>

<?php if (count($productListNew)): ?>
<section class="new-items" ui="Slider" uiSliderStep="5" uiSliderTime="7000">
	<div class="block">
		<div class="head"><h2><?= $this->translate('Новые поступления', 'New products')
         ?></h2></div>
		<div class="slidebar">
			<div class="slider" id="new-slider">
				<ul class="slides" uiSlider="body">
	
					<?php $index = 0; ?>
					<?php foreach ($productListNew as $productItem): ?>
						<?php $productImage = array_shift($product->getImages($productItem['id'])); ?>
						<?php if ($productImage): ?>
							<li uiSliderItem="<?= $index++ ?>">
								<div class="item">
									<div class="img">
										<a href="<?= $this->link(get($productItem, 'url')) ?>">
											<img src="/public/products/mini/<?= get($productImage,'view') ?>" alt="<?= $this->getImageAlt($productImage, $productItem) ?>" title="<?= $this->getImageTitle($productImage, $productItem) ?>" />
										</a>
									</div>
									<a href="<?= $this->link(get($productItem, 'url')) ?>"><?= get($productItem, 'title') ?></a>
									<div class="price"><?= $this->price(get($productItem, 'price')) ?></div>
								</div>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				
				</ul>
			</div>
			<div class="slideLeft" uiSlider="prev"></div>
			<div class="slideRight" uiSlider="next"></div>
		</div>
	</div>
</section>
<?php endif; ?>

<?= $this->loadView('project/elements/subscribe') ?>

<?php if (count($productListRec)): ?>
<section class="popular-items" ui="Slider" uiSliderStep="5" uiSliderTime="7000">
	<div class="block">
		<div class="head"><h2><?= $this->translate('ПОПУЛЯРНЫЕ ТОВАРЫ', 'Popular products') ?></h2></div>
		<div class="slidebar">
			<div class="slider" id="popular-slider">
				<ul class="slides" uiSlider="body">
					
					<?php $index = 0; ?>
					<?php foreach ($productListRec as $productItem): ?>
						<?php $productImage = array_shift($product->getImages($productItem['id'])); ?>
						<?php if ($productImage): ?>
							<li uiSliderItem="<?= $index++ ?>">
								<div class="item">
									<div class="img">
										<a href="<?= $this->link(get($productItem, 'url')) ?>">
											<img src="/public/products/mini/<?= get($productImage,'view') ?>" alt="<?= $this->getImageAlt($productImage, $productItem) ?>" title="<?= $this->getImageTitle($productImage, $productItem) ?>" />
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
			<div class="slideLeft" uiSlider="prev"></div>
			<div class="slideRight" uiSlider="next"></div>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="bottom-action hidden">
	<div class="block">
		<div class="t1">
			ПАРТНЕРСКАЯ ПРОГРАММА №1
		</div>
		<div class="t2">
			ПОЛУЧИ ГОТОВЫЙ БИЗНЕС ЗА <span>5000</span> РУБЛЕЙ В ГОД
		</div>
		<div class="t3">
			Зарабатывайте с нами!
		</div>
		<a href="#" class="btn">Узнать больше</a>
	</div>
</section>