<?php

$options	= $this->options;
$filters	= $this->filters;
$products	= $this->products;
$parents	= $this->parents;
$paginator	= $this->paginator;
$attributes	= $this->getAttributes();
$attrExtra	= $this->getAttrExtra();

?>

<?= $this->loadView('project/elements/quickview') ?>
<h1 style="display: none;"><?=$this->h1?></h1>
<section class="content">
	<form method="GET" id="catalogForm">
		
		<?php if ($this->search): ?>
		<input type="hidden" name="q" value="<?= $this->search ?>" />
		<?php endif; ?>
		
		<div class="block">
			
			<aside class="leftSide">
				<?= $this->loadView('project/elements/categories') ?>
				<?= $this->loadView('project/elements/filters') ?>
				<?= $this->loadView('project/elements/social') ?>
			</aside>
	
			<aside class="rightSide">
            <script type="text/javascript" src="/public/js/share42.js"></script>
            <div class="share42init" style="float: right;" data-top1="170" data-top2="20" data-margin="18"></div>
				<div class="breadcrumbs">
					<ul itemprop="breadcrumb">
						<li><a href="/"><?= $this->translate('Главная страница', 'Home page') ?></a></li>
						<li>|</li>
						<li><a href="/catalog/"><?php
						$pageTitle = '';
						if ($this->isNew) $pageTitle = $this->translate('Новинки', 'New');
						else if ($this->isArchive) $pageTitle = $this->translate('Архив', 'Archive');
						else $pageTitle = $this->translate('Каталог', 'Catalog');
						print($pageTitle);
						?></a></li>
						
						<?php foreach ($parents as $category): ?>
							<li>|</li>
							<?php if (get($category, 'id') == $this->categoryId): ?>
							<li><?= $this->translate(get($category, 'titleRu'), get($category, 'titleEn')) ?></li>
							<?php else: ?>
							<li><a href="/<?= get($category, 'url') ?>/"><?= $this->translate(get($category, 'titleRu'), get($category, 'titleEn')) ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
	
				<?php if ($this->search): ?>
					<div class="title"><span><?= $this->translate('Поиск', 'Search') ?>: <?= htmlspecialchars($this->search) ?></span>
				<?php else: ?>
					<div class="title"><span><?= $this->categoryInfo ? get($this->categoryInfo, 'titleRu') : $pageTitle ?><?= isset($this->metaFilters) ? ' ' . implode($this->metaFilters, ', ') : '' ?></span>
				<?php endif; ?>
				
				<div id="seoHideSelectorContent"></div>
				
				<script type="text/html" id="seoHideSelector">
					<div class="box">
						<label><?= $this->translate('Товаров на странице:', 'Products on page:') ?></label>
		
						<div class="selecter-box" style="width: 40px;">
							<select class="select" name="limit" onchange="Unit('catalogForm').submit()">
								<option value="24" <?= $options->limit == 24 ? 'selected="selected"' : '' ?> >24</option>
								<option value="48" <?= $options->limit == 48 ? 'selected="selected"' : '' ?> >48</option>
								<option value="96" <?= $options->limit == 96 ? 'selected="selected"' : '' ?> >96</option>
							</select>
						</div>
					</div>
					<div class="box">
						<label><?= $this->translate('Сортировать по', 'Order by') ?></label>
		
						<div class="selecter-box" style="width: 100px;">
							<select class="select" name="order" onchange="Unit('catalogForm').submit()">
								<?php if (false): ?>
								<option value="relevant" <?= $options->order == 'relevant' ? 'selected="selected"' : '' ?> >релевантности</option>
								<?php endif; ?>
								<option value="rating" <?= $options->order == 'rating' ? 'selected="selected"' : '' ?> >популярности</option>
								<option value="priceAsc" <?= $options->order == 'priceAsc' ? 'selected="selected"' : '' ?> >цена по возр.</option>
								<option value="priceDesc" <?= $options->order == 'priceDesc' ? 'selected="selected"' : '' ?> >цена по убыв.</option>
								<option value="new" <?= $options->order == 'new' ? 'selected="selected"' : '' ?> >новинки</option>
							</select>
						</div>
					</div></div>
					<hr>
				</script>
				
				<script>
				Unit.inner('seoHideSelectorContent', Unit('seoHideSelector').innerHTML);
				</script>
	
				<div class="items">
					
					<?php if ($paginator->found == 0 || count($products) == 0): ?>
						
						<p class="messageEmpty">Товары не найдены, попробуйте изменить параметры поиска.</p>
						
					<?php endif; ?>
					
					<?php foreach ($products as $productItem): ?>
						<?php $productId = $productItem['id']; ?>
						<?php $productImages = $this->getImages($productItem['id']); ?>
						<?php $productAttributes = json_decode(get($productItem, 'attributes'), true); ?>
						<?php $productSizes = explode(',',get($productAttributes, '2', '')); ?>
						<?php $productColors = explode(',',get($productAttributes, '1', '')); ?>
						
						<?php
						$jsonSizes = array();
						foreach ($productSizes as $pSize)
						{
							if (isset($attributes[$pSize])) $jsonSizes[$pSize] = $attributes[$pSize];
						}
						$jsonColors = array();
						foreach ($productImages as $imageId => $productImage)
						{
							$pColor = get($productImage, 'colorId');
							if (isset($attributes[$pColor])) $jsonColors[$pColor] = $attributes[$pColor];
						}
						$productJson = array(
							'availability' => $productItem['availability'],
							'id'		=> $productItem['id'],
							'url'		=> $productItem['url'],
							'article'	=> $this->getArticle($productItem),
							'titleRu'	=> $this->getArticleFull($productItem),
							'titleEn'	=> $this->getArticleFull($productItem),
							'price'		=> $this->price($productItem['price'], $productItem['discount']),
							'priceFloat'=> $productItem['price'],
							'discount'	=> $productItem['discount'],
							'images'	=> $productImages,
							'colors'	=> $jsonColors,
							'sizes'		=> $jsonSizes,
						);
						?>
						
						<div class="item" itemscope itemtype="http://schema.org/Product">
							<div class="img">
								<a itemprop="url" href="<?= $this->link(get($productItem, 'url')) ?>">
									<?php $productImagesLimit = 0; $productImagesGroup = array(); ?>
									<?php foreach ($productImages as $imageId => $productImage): ?>
									<?php $productImageUrl = get($productImage,'view'); ?>
									<?php
									$imageGroup = get($productImage, 'colorId');
									if (isset($productImagesGroup[$imageGroup])) continue;
									$productImagesGroup[$imageGroup] = $imageId;
									?>
									<img itemprop="image" src="/public/products/mini/<?= $productImageUrl ?>" id="productImage:img<?= $imageId ?>" alt="<?= $this->getImageAlt($productImage, $productItem) ?>" title="<?= $this->getImageTitle($productImage, $productItem) ?>" />
									<?php if (++$productImagesLimit >=4) break; ?>
									<?php endforeach; ?>
									<?php if(get($productItem, 'discount') > 0): ?>
									<p class="discount">Скидка <?= strpos(get($productItem, 'discount'), '%') > 0 ? get($productItem, 'discount') : $this->price(get($productItem, 'discount')) ?></p>
									<?php endif; ?>
								</a>
								<a class="quick popups" href="<?= $this->link(get($productItem, 'url')) ?>" onclick='project.showProductPopup(<?= json_encode($productJson, JSON_HEX_QUOT | JSON_HEX_APOS) ?>);return false;'></a>
							</div>
							
							<a class="name" itemprop="name" href="<?= $this->link(get($productItem, 'url')) ?>"><?= $this->getArticleFull($productItem) ?></a>
		
							<div class="colors">
								<?php
								$colorLimit = 0;
								foreach ($productColors as $pColor)
								{
									if (isset($attrExtra[$pColor])) echo '<span style="background-color:#' . $attrExtra[$pColor] . ';"></span>';
									if (++$colorLimit >= 9) break;
								}
								?>
							</div>
		
							<div class="price"><?= $this->price(get($productItem, 'price'), get($productItem, 'discount')) ?></div>
		
							<div class="box">
								<div class="imgs itemSlider">
									<div class="left" ui="Menu" uiRequired="true" id="imageMenu_<?= $productId ?>">
										<?php $productImagesLimit = 0; ?>
										<?php foreach ($productImages as $imageId => $productImage): ?>
										<?php $productImageUrl = get($productImage,'view'); ?>
										<?php
										if ( ! in_array($imageId, $productImagesGroup)) continue;
										?>
										<img uiMenuOption="#productImage:img<?= $imageId ?>" onmouseover="Unit('imageMenu_<?= $productId ?>').selectMenu('img<?= $imageId ?>');" src="/public/products/mini/<?= $productImageUrl ?>" data-img="/public/products/show/<?= $productImageUrl ?>" /><br>
										<?php if (++$productImagesLimit >=4) break; ?>
										<?php endforeach; ?>
									</div>
								</div>
								
								<a class="name" href="<?= $this->link(get($productItem, 'url')) ?>"><?= $this->getArticleFull($productItem) ?></a>
		
								<div class="colors">
									<?php
									foreach ($productColors as $pColor)
									{
										if (isset($attrExtra[$pColor])) echo '<span style="background-color:#' . $attrExtra[$pColor] . ';"></span>';
									}
									?>
								</div>
		
								<div class="price"><?= $this->price(get($productItem, 'price'), get($productItem, 'discount')) ?></div>
								<div class="sizes">
									<?php
									$productSizesTitles = array();
									foreach ($productSizes as $pSize)
									{
										if (isset($attributes[$pSize])) $productSizesTitles[] = $attributes[$pSize];
									}
									if (count($productSizesTitles))
									{
										print('Размеры в наличии: ' . implode(', ',$productSizesTitles));
									}
									?>
								</div>
							</div>
						</div>
					
					<?php endforeach; ?>
					
				</div>
	
				<div class="bottom-line">
					
					<?php if ($paginator->found > 0): ?>
					<div class="pagination">
						<ul>
							<?php $min = ($paginator->cur-1) * $paginator->limit; ?>
							<?php $max = ($paginator->cur+1) * $paginator->limit; ?>
							<?php $end = ($paginator->max-1) * $paginator->limit; ?>
							
							<li><a href="<?= $this->getPaginLink($min >= 0 ? $min : 0) ?>" class="prev"></a></li>
							<?php for ($i = $paginator->min; $i < $paginator->max; $i++): ?>
								<?php if ($i == $paginator->cur): ?>
								<li><span class="active"><?= $i + 1 ?></span></li>
								<?php else: ?>
								<li><a href="<?= $this->getPaginLink($i * $paginator->limit) ?>" class="<?= $paginator->cur == $i ? 'active' : '' ?>"><?= $i + 1 ?></a></li>
								<?php endif; ?>
							<?php endfor; ?>
							<li><a href="<?= $this->getPaginLink($max >= $end ? $end : $max) ?>" class="next"></a></li>
						</ul>
					</div>
					<?php endif; ?>
	
					<div class="box">
						<label><?= $this->translate('Товаров на странице:', 'Products on page:') ?></label>
	
						<div class="selecter-box" style="width: 40px;">
							<select class="select" onchange="Unit('catalogForm')['limit'].value=this.value; Unit('catalogForm').submit()">
								<option value="24" <?= $options->limit == 24 ? 'selected="selected"' : '' ?> >24</option>
								<option value="48" <?= $options->limit == 48 ? 'selected="selected"' : '' ?> >48</option>
								<option value="96" <?= $options->limit == 96 ? 'selected="selected"' : '' ?> >96</option>
							</select>
						</div>
					</div>
				</div>
			
			<?php
			
			if ($options->index == 0)
			{
				$links = array();
				$categories = $this->getCategoryGroup($this->categoryId);
				
				if ( ! count($categories))
				{
					$categories = $this->getCategoryGroup(get($this->categoryInfo, 'parentId'));
				}
				
				if ( ! count($categories))
				{
					$categories = $this->getCategoryGroup(0);
				}
				
				foreach ($categories as $category)
				{
					$links[] = '<a href="/' . get($category, 'url') . '/">' . $this->translate(get($category, 'titleRu'),get($category, 'titleEn')) . '</a>';
				}
				
				if (count($links))
				{
					print('<div class="categoryLinks">' . implode(', ', $links)) . '.</div>';
				}
			}
			
			?>
				
			</aside>
			
		</div>
	</form>
</section>