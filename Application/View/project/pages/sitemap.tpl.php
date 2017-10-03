<section class="content cart form">
	<div class="block simplePage">
		
		<div class="head"><h1><?= get($this->page, 'titleRu') ?></h1></div>

		<?php
		
		$menuMain	= $this->getMenu(1);
		
		$modelPage		= $this->loadModel('page');
		$modelProduct	= $this->loadModel('product');
		$modelCategory	= $this->loadModel('productCategory');
		
		$skipPage		= array(2,3,4,5,6,7,8,9,16,17,18);
		$skipCategory	= array();
		
		//$listPage		= $modelPage->find();
		//$listCategory	= $modelCategory->find();

		/*print('<ul class="sitemap">');
		foreach ($listPage as $iPage)
		{
			if (in_array(get($iPage,'id'), $skipPage)) continue;
			
			print('<li><a href="' . $this->link(get($iPage, 'url')) . '">' . get($iPage, 'titleRu') . '</a></li>');
		}
		print('</ul>');*/
		
		print('<ul class="sitemap">');
		print('<li><a href="/">Главная страница</a></li>');
		foreach ($menuMain as $iMenu)
		{
			//if (in_array(get($iMenu,'id'), $skipPage)) continue;
			
			print('<li><a href="' . $this->link(get($iMenu, 'url')) . '">' . $this->translate(get($iMenu, 'titleRu'), get($iMenu, 'titleEn')) . '</a></li>');
		}
		print('</ul>');

		print('<ul class="sitemap">');
		print('<li><ul>');
		print('<li><a href="/archive/">' . $this->translate('Архив товаров','Archive') . '</a></li>');
		
		$listCategory = $this->getCategoryGroup(0);
		
		foreach ($listCategory as $categoryId => $categoryItem)
		{
			$subCategory = $this->getCategoryGroup($categoryId);
			
			if (in_array(get($categoryId,'id'), $skipCategory)) continue;
			
			print('<li><a href="' . $this->link(get($categoryItem, 'url')) . '">' . $this->translate(get($categoryItem, 'titleRu'), get($categoryItem, 'titleEn')) . '</a></li>');
			
			if (count($subCategory))
			{
				print('<li><ul>');
				foreach ($subCategory as $subCategoryId => $subCategoryItem)
				{
					print('<li><a href="' . $this->link(get($subCategoryItem, 'url')) . '">' . $this->translate(get($subCategoryItem, 'titleRu'), get($subCategoryItem, 'titleEn')) . '</a></li>');
				}
				print('</ul></li>');
			}
		}
		
		print('</ul></li>');
		print('</ul>');
		
		?>
		
	</div>
</section>