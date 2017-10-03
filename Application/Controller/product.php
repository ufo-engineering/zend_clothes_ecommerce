<?php

namespace Application\Controller;

require_once('catalog.php');

class Product extends Catalog
{
	
	public function defaultAction($action, $url, $color = '')
	{
		$this->model			= $this->loadModel('product');
		$this->productUrl		= $url;
		$this->productColor		= $color;
		$this->productInfo		= $this->model->get($this->productUrl, 'url');
		$this->productId		= get($this->productInfo, 'id');
		$this->categoryId		= get($this->productInfo, 'categoryId');
		$this->productImages	= group($this->getImages($this->productId), 'colorId');
		$this->productComments	= $this->model->getComments($this->productId, true);
		$this->cnnc				= $this->productUrl;
		
		if ( ! $this->productColor && count($this->productImages))
		{
			$this->productColor	= get(array_keys($this->productImages), 0);
		}
		
		if ($this->productInfo)
		{
			$this->metaTitle		= get($this->productInfo, 'title');
			$this->metaKeywords		= get($this->productInfo, 'metaKeywordsRu');
			$this->metaDescription	= get($this->productInfo, 'metaDescriptionRu');
		}
		
		return Page::defaultAction('product');
	}
	
}