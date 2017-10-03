<?php

$parents		= $this->getParentCategories($this->categoryId);
$images			= $this->productImages;
$attributes		= $this->getAttributes();
$attributesExtra= $this->getAttrExtra();
$attributesKeys = $this->getAttributesKeys();
$productAttr	= json_decode(get($this->productInfo, 'attributes'), true);
$productSizes	= explode(',',get($productAttr, '2', ''));
$productColors	= explode(',',get($productAttr, '1', ''));

if (isset($images[$this->productColor]) && count($images[$this->productColor]))
{
	$defaultImg = get(array_values($images[$this->productColor]), 0);
}

else
{
	foreach ($images as $imgArray)
	{
		$defaultImg = get($imgArray, 0);
		break;
	}
}

$product		= $this->loadModel('product');
$productList	= $product->find(array('where' => array('active' => 1, 'availability != ' => 0),'limit' => 11, 'order' => 'priority', 'drect' => 1));
$productSame	= $product->find(array(
	'where' => array(
		'categoryId'		=> get($this->productInfo, 'categoryId'),
		'added <'			=> get($this->productInfo,'added'),
		'active'			=> 1,
		'availability != '	=> 0
	),
	'limit' => 5,
	'order' => 'priority',
	'drect' => 1,
	'order2' => 'added',
	'drect2' => 0
));

$account		= $this->loadModel('account');
$status			= $account->status();
$userId			= get($status, 'id');

if ($userId && $this->productId)
{
	$productView = $this->loadModel('productView');
	$productView->add($userId, $this->productId);
}

?>

<section class="content">
	<div class="block">
		
		<aside class="leftSide">
			<?= $this->loadView('project/elements/categories') ?>
			<?= $this->loadView('project/elements/social') ?>
		</aside>

		<aside class="rightSide" itemscope itemtype="http://schema.org/Product">
			
				<div class="breadcrumbs">
					<ul itemprop="breadcrumb">
						<li><a href="/"><?= $this->translate('Главная страница', 'Home page') ?></a></li>
							<li>|</li>
							<li><a href="/catalog/"><?= $this->translate('Каталог', 'Catalog') ?></a></li>
						<?php foreach ($parents as $category): ?>
							<li>|</li>
							<li><a href="/<?= get($category, 'url') ?>/"><?= $this->translate(get($category, 'titleRu'), get($category, 'titleEn')) ?></a></li>
						<?php endforeach; ?>
						<li>|</li>
						<li><?= $this->getArticleFull($this->productInfo) ?></li>
						<!--
						<li class="ru"><?= get($this->productInfo, 'titleRu') ?></li>
						<li class="en"><?= get($this->productInfo, 'titleEn') ?></li>
						-->
					</ul>
				</div>
				
			<form method="POST" ui="Form" uiControl="project.basketControl" id="productForm">
			
				<input type="hidden" name="id" value="<?= get($this->productInfo, 'id') ?>" />
				<input type="hidden" name="colorId" value="<?= $this->productColor?>" />
				<input type="hidden" name="price" value="<?= get($this->productInfo, 'price') ?>" />
				
				<!--<input type="hidden" name="price" value="<?= get($this->productInfo, 'price') ?>" />
				<input type="hidden" name="discount" value="<?= get($this->productInfo, 'discount') ?>" />
				<input type="hidden" name="url" value="<?= get($this->productInfo, 'url') ?>" />
				<input type="hidden" name="title" value="<?= get($this->productInfo, 'title') ?>" />
				<input type="hidden" name="article" value="<?= $this->getArticle($this->productInfo) ?>" />
				<input type="hidden" name="image" value="<?= get($defaultImg,'view') ?>" />-->
				
				<div class="prod-content" itemscope itemtype="http://schema.org/Product">
					<div class="left" ui="Preview">
						
						<?php foreach ($images as $colorId => $imgGroup): ?>
							<?php if ($colorId == $this->productColor): ?>
							<div ui="Menu" uiRequired="true">
							
								<?php foreach ($imgGroup as $imgId => $imgData): ?>
									<?php $imgUrl = get($imgData, 'view'); ?>
									<div class="img" id="image_<?= get($imgData,'id') ?>">
										<a href="/public/products/show/<?= $imgUrl ?>" uiPreview="/public/products/show/<?= $imgUrl ?>" class="zoom">
											<img src="/public/products/show/<?= $imgUrl ?>" alt="<?= $this->getImageAlt($imgData, $this->productInfo) ?>" title="<?= $this->getImageTitle($imgData, $this->productInfo, $imgId) ?>" <?= get($imgData,'width')/get($imgData,'height')>0.75 ? 'width="295"' : 'height="391"' ?> itemprop="image" />
										</a>
									</div>
								<?php endforeach; ?>
								
								<div class="imgs">
									<?php foreach ($imgGroup as $imgId => $imgData): ?>
										<?php $imgUrl = get($imgData, 'view'); ?>
										<img src="/public/products/mini/<?= $imgUrl ?>" alt="" uiMenuOption="#image_<?= get($imgData,'id') ?>" width="46" height="62" />
									<?php endforeach; ?>
								</div>
								
							</div>
							<?php else: ?>
								<?php foreach ($imgGroup as $imgId => $imgData): ?>
									<?php $imgUrl = get($imgData, 'view'); ?>
									<a href="/public/products/show/<?= $imgUrl ?>" uiPreview="/public/products/show/<?= $imgUrl ?>" class="hidden"></a>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
						
						<div class="links">
							<ul>
								<li>
									<a href="<?= $this->link('shipping') ?>">Быстрая доставка</a>
								</li>
								<li>
									<a href="<?= $this->link('shipping') ?>">Удобная оплата</a>
								</li>
								<li>
									<a href="<?= $this->link('shipping') ?>">Обмен и возврат товара</a>
								</li>
							</ul>
						</div>
	
						<div class="partner hidden">
							<div class="t1">Партнерская<br>программа</div>
							<div class="t2">№1</div>
							<div class="clear"></div>
							<a href="#">Узнать больше</a>
						</div>
					</div>
					<div class="right">
						
						<div class="head"><h1 itemprop="name"><?= $this->getArticleFull($this->productInfo) ?></h1></div>
						
						<!--
						<div class="head ru"><?= get($this->productInfo, 'titleRu') ?></div>
						<div class="head en"><?= get($this->productInfo, 'titleEn') ?></div>
						-->
	
						<div class="info">
							<div class="i1">
								<span class="i1title"><?= $this->translate('Артикул:', 'Article:') ?></span>
								<?= $this->getArticle($this->productInfo).'/'.$this->productColor ?>
								
								<?php
								
								if (isset($_SESSION['admin'])) print('<br><span class="i1title">Админ. артикул:</span> ' . get($this->productInfo, 'article'));
								
								?>
								<br>
								<span class="i1title"><?= $this->translate('Цвет:', 'Color:') ?></span>
								<?php if (isset($attributes[$this->productColor])): ?>
								<span><?= $attributes[$this->productColor] ?></span>
								<?php endif; ?>
							</div>
	
							<div class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<?= $this->price(get($this->productInfo, 'price'), get($this->productInfo, 'discount'), true) ?>
							</div>
						</div>
	
						<?php if (get($this->productInfo,'availability') > 0 ): ?>
                        <link itemprop="availability" href="http://schema.org/InStock" />
							<div class="row" ui="Menu" uiRequired="true" uiSelected="<?= $this->productColor ?>">
								
								<div class="head">
									Выберите цвет:
								</div>
								
								<?php foreach ($images as $colorId => $imgGroup): ?>
									<?php foreach ($imgGroup as $img): ?>
									<a href="<?= $this->link($this->productUrl, $colorId) ?>">
										<img src="/public/products/mini/<?= get($img, 'view') ?>" alt="" uiMenuOption="<?= $colorId ?>" width="38px" />
									</a>
									<?php break; endforeach; ?>
								<?php endforeach; ?>
							</div>
		
							<div class="sizes">
								<div class="head">
									<?= $this->translate('Выберите размер:', 'Select size:') ?>
								</div>
								<div class="data">
									<?php
									$productSizesTitles = array();
									foreach ($productSizes as $pSize)
									{
										if (isset($attributes[$pSize])) $productSizesTitles[$pSize] = $attributes[$pSize];
									}
									if ( ! count($productSizesTitles)) {
										$productSizesTitles[0] = 'UN';
									}
									foreach ($productSizesTitles as $iSize => $productSize):
									?>
									<div class="item" ui="Counter" uiAction="this.trigger('active', this.getValue() > 0);Unit.inner('productBasketMessage','');" id="productCounter_<?= $iSize ?>">
										<span onclick="Unit('productCounter_<?= $iSize ?>').setValue(true)"><?= $productSize ?></span>
		
										<div class="counter">
											<span class="up" uiCounter="add"></span>
											<input type="text" uiCounter="val" value="0" maxlength="3" name="sizeId[<?= $iSize ?>]" />
											<span class="down" uiCounter="del"></span>
										</div>
									</div>
									<?php endforeach; ?>
								</div>
								<input type="submit" class="btn" value="" />
								<p class="message" uiForm="message" id="productBasketMessage"></p>
								
								<script>
								if (project.basketGet('<?= get($this->productInfo, 'id') ?>','<?= $this->productColor ?>')) {
									Unit.inner('productBasketMessage', 'Товар добавлен в корзину.');
								}
								</script>
	
								<div class="clear"></div>
								<p onclick="Unit.trigger('productDetectSizes')" class="know"><?= $this->translate('Определить размер', 'Define size') ?></p>
								
								<div class="knowSizes hidden" id="productDetectSizes">
									<table border="1">
										<tr>
											<td>Международный</td>
											<td>Евро размер</td>
											<td>Российский</td>
											<td>Объем груди</td>
											<td>Объем бедер</td>
											<td>Объем талии</td>
										</tr>
										<tr>
											<td>S</td>
											<td>36</td>
											<td>42</td>
											<td>80-90</td>
											<td>80-90</td>
											<td>до 66</td>
										</tr>
										<tr>
											<td>M</td>
											<td>38</td>
											<td>44</td>
											<td>91-95</td>
											<td>91-96</td>
											<td>до 70</td>
										</tr>
										<tr>
											<td>L</td>
											<td>40</td>
											<td>46</td>
											<td>96-100</td>
											<td>97-104</td>
											<td>до 76</td>
										</tr>
										<tr>
											<td>XL</td>
											<td>—</td>
											<td>48</td>
											<td>102</td>
											<td>106</td>
											<td>78</td>
										</tr>
										<tr>
											<td>XXL</td>
											<td>—</td>
											<td>50</td>
											<td>104</td>
											<td>106-108</td>
											<td>82</td>
										</tr>
										<tr>
											<td>XXXL</td>
											<td>—</td>
											<td>52</td>
											<td>106</td>
											<td>109-112</td>
											<td>86</td>
										</tr>
									</table>
								</div>
							
							</div>
							
						<?php else: ?>	
							<p class="notAvailable">Снято с производства</p>
						<?php endif; ?>
	
						<div class="tabs">
							<div class="tabs-head" ui="Menu" uiRequired="true">
								<p uiMenuOption="#tab1"><?= $this->translate('О товаре', 'About product') ?></p>
								<p uiMenuOption="#tab2"><?= $this->translate('Оплата', 'Payment') ?></p>
								<p uiMenuOption="#tab3"><?= $this->translate('Доставка', 'Delivery') ?></p>
								<p uiMenuOption="#tab4"><?= $this->translate('Вопросы', 'Questions') ?></p>
							</div>
							<div class="tabs-content">
								<div class="tab" id="tab1">
									
									<p class="ru" itemprop="description"><?= get($this->productInfo, 'descriptionRu') ?></p>
									<p class="en" itemprop="description"><?= get($this->productInfo, 'descriptionEn') ?></p>
	
									<table class="table">
										<?php foreach ($productAttr as $pAttrId => $pAttrValues): ?>
											<?php if (strlen($pAttrValues)): ?>
												<?php $pAttrValues = explode(',', $pAttrValues); ?>
												<tr>
													<td><?= get($attributesKeys, $pAttrId) ?></td>
													<td>
														<?php
														$pAttrValuesComplete = array();
														foreach ($pAttrValues as $pAttrValue)
														{
															$pAttrValuesComplete[] = get($attributes, $pAttrValue);
														}
														echo implode(', ', $pAttrValuesComplete);
														?>
													</td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</table>
								</div>
								<div class="tab" id="tab2">
									<p><?= nl2br($this->getConfig('productShipping')) ?></p>
								</div>
								<div class="tab" id="tab3">
									<p><?= nl2br($this->getConfig('productPayment')) ?></p>
								</div>
								<div class="tab" id="tab4">
									<p><?= nl2br($this->getConfig('productQuestions')) ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
	
				<div class="buy-also" ui="Slider" uiSliderStep="5" uiSliderTime="7000">
					<div class="head"><?= $this->translate('Похожие товары:', 'Similar products:') ?></div>
					<div class="slidebar">
						<div class="slider" id="buy-same">
							<ul class="slides" uiSlider="body">
								
								<?php $index = 0; ?>
								<?php $productSameCount = 0; ?>
								<?php foreach ($productSame as $productItem): ?>
									<?php $productImage = array_shift($product->getImages($productItem['id'])); ?>
									<?php if ($productImage && $productItem['id'] != $this->productId && (++$productSameCount < 6)): ?>
										<li uiSliderItem="<?= $index++ ?>">
											<div class="item" itemscope itemprop="isSimilarTo"  itemtype="http://schema.org/Product">
												<div class="img" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product">
													<a href="<?= $this->link(get($productItem, 'url')) ?>">
														<img src="/public/products/mini/<?= get($productImage,'view') ?>" alt="" itemprop="image"/>
													</a>
												</div>
												<a itemprop="url" href="<?= $this->link(get($productItem, 'url')) ?>"><?= $this->getArticleFull($productItem) ?></a>
												<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price"><?= $this->price(get($productItem, 'price')) ?></div>
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
				
				<div class="buy-also" ui="Slider" uiSliderStep="5" uiSliderTime="7000">
					<div class="head"><?= $this->translate('С этим товаром также покупают:', 'With this product also bought:') ?></div>
					<div class="slidebar">
						<div class="slider" id="buy-also">
							<ul class="slides" uiSlider="body">
								
								<?php $index = 0; ?>
								<?php foreach ($productList as $productItem): ?>
									<?php $productImage = array_shift($product->getImages($productItem['id'])); ?>
									<?php if ($productImage && $productItem['id'] != $this->productId): ?>
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
						<div class="slideLeft" uiSlider="prev"></div>
						<div class="slideRight" uiSlider="next"></div>
					</div>
				</div>
			
			</form>

			<div class="comments">
				<div class="sub-title"><?= $this->translate('Отзывы о товаре:', 'Product comments:') ?></div>

				<?php foreach ($this->productComments as $comment): ?>
					<div class="item" itemprop="review" itemscope itemtype="http://schema.org/Review">
						<div class="name" itemprop="author">
							<?= get($comment, 'authorName') ?>
						</div>
						<div class="date" >
							30 марта 2014
							<?= strftime('%e $B %Y', get($comment, 'added')) ?>
						</div>
						<div class="text" itemprop="description">
							<?= htmlspecialchars(get($comment, 'message')) ?>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="clear"></div>
				
				<div id="productCommentAdded" class="t hidden">
					Спасибо за отзыв!<br>
					Ваш отзыв будет опубликован после проверки менеджером магазина.
				</div>
				
				<div id="productCommentForm" class="form hidden">
					
					<form ui="Form" uiControl="project.productCommentControl" class="tabs">
						
						<div class="form-box">
							<div class="tabs-content">
									
								<div id="commentError" uiForm="message" class="message"></div>
								
								<div class="item" uiField="authorName">
									<label>Имя<span>*</span></label>
									<input type="text" name="authorName" id="commentName" placeholder="" value="">
								</div>
								<div class="item" uiField="authorEmail">
									<label>E-mail<span>*</span></label>
									<input type="text" name="authorEmail" id="commentEmail" placeholder="" value="">
								</div>
								<div class="item" uiField="message">
									<label><span class="comment">Комментарий:</span></label>
									<textarea name="message" id="commentMessage" value=""></textarea>
								</div>
								
							</div>
						</div>
						
						<div>
							<input type="hidden" name="productId" value="<?= $this->productId ?>" />
							<input type="hidden" name="accountId" value="0" />
							<input type="hidden" name="active" value="2" />
							<input type="submit" class="btn" value="ОТПРАВИТЬ">
						</div>
						
					</form>
					
				</div>
				
				<div id="productCommentButton">
					<div class="t">
						Хотите поделиться<br>впечатлениями об этом товаре?
					</div>
					<input type="button" class="btn" value="Оставить отзыв" onclick="Unit.trigger('productCommentButton');Unit.trigger('productCommentForm');">
				</div>
			</div>

		</aside>
	</div>
</section>

<script>
project.currencyUsd = '<?= (float) $this->getConfig('currencyUsd') ?>';
project.currencyRub = '<?= (float) $this->getConfig('currencyRub') ?>';
</script>

<?= $this->loadView('project/elements/subscribe') ?>