<?php

$categories	= $this->getCategoryGroup(0);
$rootCategory = isset($this->categoryId) ? $this->categoryId : 0;

?>

<ul class="items">
	
	<?php foreach ($categories as $categoryId => $categoryItem): ?>
		<?php $subCategory = $this->getCategoryGroup($categoryId); ?>
		
		<?php if ( ! get($categoryItem,'visibility')) continue; ?>
		
		<li<?= $rootCategory == $categoryId || isset($subCategory[$rootCategory]) || $categoryId == '99' ? ' class="active"' : '' ?>>
	
			<a href="<?= $this->link(get($categoryItem, 'url')) ?>"><?= $this->translate(get($categoryItem, 'titleRu'), get($categoryItem, 'titleEn')) ?></a>
			<div class="clear"></div>
			
			<?php if ($subCategory): ?>
				<ul>
				<?php foreach ($subCategory as $subCategoryId => $subCategoryItem): ?>
					
					<?php if ( ! get($subCategoryItem,'visibility')) continue; ?>
					
					<li<?= $rootCategory == $subCategoryId ? ' class="active"' : '' ?>><a href="<?= $this->link(get($subCategoryItem, 'url')) ?>"><?= $this->translate(get($subCategoryItem, 'titleRu'), get($subCategoryItem, 'titleEn')) ?></a></li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			
		</li>
	
	<?php endforeach; ?>
</ul>