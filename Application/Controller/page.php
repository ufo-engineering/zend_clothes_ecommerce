<?php

namespace Application\Controller;

class Page extends \ZendCMF\Controller
{
	protected $config;
	protected $menu;
	
	private $cache = false;
	
	private $hosts = array(
		'project.com.ua'	=> 1,
		'project.ru'		=> 2,
		'project.co.uk'	=> 3,
	);
	
	protected function statistic()
	{
		$statistic = $this->loadModel('statistic');
		$statistic->add();
	}
	
	protected function getCache($url)
	{
		if ( ! $this->cache || (isset($_GET['xdebug']) && $_GET['xdebug'] == 'true') || substr($url,0,9) == '/account/')
		{
			return false;
		}
		
		$cache	= $this->loadModel('cache');
		$domain	= get($this->hosts, $this->getHost(), 0);
		$data	= get($cache->find(array(
			'where' => array('url' => $url, 'domain' => $domain)
		)), 0);
		
		$this->cached = $data;
		
		return get($data, 'added') > time() - 86400 ? $data : null;
	}
	
	protected function setCache($url, $html, $control = 0)
	{
		if ( ! $this->cache || isset($_GET['xdebug']) && $_GET['xdebug'] == 'true')
		{
			return false;
		}
		
		$cache	= $this->loadModel('cache');
		$domain	= get($this->hosts, $this->getHost(), 0);
		$id		= isset($this->cached) ? get($this->cached, 'id') : null;
		$save	= array(
			'added'		=> time(),
			'etag'		=> md5($url . time()),
			'url'		=> $url,
			'html'		=> $html,
			'control'	=> $control,
			'domain'	=> $domain,
		);
		
		if ($id) $save['id'] = $id;
		$id = $cache->save($save);
		
		return $cache->get($id);
	}
	
	public function defaultAction()
	{
		global $app;
		
		$url = $this->getUrl();
		
		if (($cache = $this->getCache($url)))
		{
			$this->statistic();
		
			$mod = get($_SERVER, 'HTTP_IF_MODIFIED_SINCE');
			$add = get($cache, 'added');
			$dat = gmdate("D, d M Y H:i:s", $add) . " GMT";
			
			
			header("Cache-Control: public, must-revalidate");
			header("Expires: " . gmdate("D, d M Y H:i:s", get($cache, 'added', time()) + 86400) . " GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", get($cache, 'added', time())) . " GMT");
			//header("Expires: " . gmdate("D, d M Y H:i:s", $add + 86400) . " GMT"); // Date in the past
			//header("Cache-Control: public, must-revalidate"); // HTTP/1.1
			//header("Pragma: cache"); // HTTP/1.0
			
			if ($mod == $dat)
			{
				header('HTTP/1.0 304 Not Modified');
				exit();
			}
			
			return $this->returnHtml(get($cache,'html'));
		}
		
		$this->args = func_get_args();
		$this->path = join('/', $this->args);
		$this->link = $this->getFilteredLink($this->path);
		$this->page = $this->getPageInfo($this->link);
		
		if ( ! $this->page)
		{
			$category = $this->loadModel('productCategory');
            if(strpos($this->link,'/filter') !== false){
                $this->link = $this->args[0];
            }
			if ($category->get($this->link,'url'))
			{
                
				return $app->run('/catalog/' . $this->path);
			}
			
			$product = $this->loadModel('product');
			
			if ($product->get($this->args[0],'url'))
			{
				return $app->run('/product/' . $this->path);
			}
			
			$this->args = array('404');
			return $this->returnHtml($this->loadView('project/index'));
		}
		
		if ( ! isset($this->metaTitle))
		{
			$this->metaTitle		= get($this->page, 'titleRu');
			$this->metaKeywords		= get($this->page, 'metaKeywordsRu');
			$this->metaDescription	= get($this->page, 'metaDescriptionRu');
		}
		
		$this->statistic();
		
		$this->time = nanotime();
		$html = $this->loadView('project/index');
		$ccid = $this->setCache($url, $html);
		
		if ($ccid)
		{
			header("Cache-Control: public, must-revalidate");
			header("Expires: " . gmdate("D, d M Y H:i:s", get($ccid, 'added', time()) + 86400) . " GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", get($ccid, 'added', time())) . " GMT");
			//header("Etag: \"" . get($ccid, 'etag') . "\"");
		}
		
		//header("Vary: Content-ID");
		//header("Content-ID: " . $ccid);
		
		return $this->returnHtml($html);
	}
	
	protected function getFilteredLink($url)
	{
		return preg_replace('/\/?page[0-9]+\/?$/', '', $url);
	}
	
	protected function getHost()
	{
		return str_replace('www.', '', get($_SERVER, 'HTTP_HOST'));
	}
	
	protected function getPageInfo($url)
	{
		$model = $this->loadModel('page');
		return $model->get($url, 'url');
	}
	
	protected function getConfig($key)
	{
		if ( ! $this->config)
		{	
			$this->config = $this->loadModel('config');
		}
		
		return $this->config->get($key);
	}
	
	protected function getMenu($key)
	{
		if ( ! $this->menu)
		{
			$menu		= $this->loadModel('menu');
			$this->menu	= $menu->getGrouped();
		}
		
		return get($this->menu, $key, array());
	}
	
	protected function getArticle($product)
	{
		return str_pad(get($product, 'id'), 5, '0', STR_PAD_LEFT);
	}
	
	protected function getArticleFull($product)
	{
		$id = get($product, 'id');
		$title = trim(get($product, 'titleRu'));
		$split = explode(' ', $title);
		$name = $split[0];
		$part = get($split, 1);
		//if ($part)
		$name .= ' ' . str_pad($id, 5, '0', STR_PAD_LEFT);
		return $name;
	}
	
	protected function getImageAlt($image, $product)
	{
		if ( ! isset($this->altCategory))
		{
			$category			= $this->loadModel('productCategory');
			$this->altCategory	= assoc($category->find(array('field' => 'id,title')), 'id', 'title');
		}
		
		if ( ! isset($this->altParam))
		{
			$param			= $this->loadModel('productParam');
			$this->altParam	= assoc($param->getValues(), 'id', 'title');
		}
		
		$alt = array();
		$alt[] = get($this->altCategory, get($product, 'categoryId'));
		$alt[] = get($this->altParam, get($image, 'colorId'));
		
		return implode(', ', $alt);
	}
	
	protected function getImageTitle($image, $product, $index = 1)
	{
		$alt = $this->getImageAlt($image, $product);
		
		return $alt .= ', ' . $index;
	}
	
	protected function getCategoryGroup($id)
	{
		if ( ! isset($this->categories))
		{
			$module = $this->loadModel('productCategory');
			$this->categories = $module->getGrouped();
		}
		
		return get($this->categories, $id);
	}
	
	protected function price($price, $discount = '', $schema = false)
	{
		$usd	= (float) $this->getConfig('currencyUsd');
		$rub	= (float) $this->getConfig('currencyRub');
		
		if ($schema)
		{
			$html = '<span class="uah" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" content="' . price($price) . '">' . price($price) . '</span>&nbsp;<span itemprop="priceCurrency" content="UAH">грн</span></span>'.
				'<span class="usd" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" content="' . price($price / $usd) . '">' . price($price / $usd) . '</span>&nbsp;<span itemprop="priceCurrency" content="USD">$</span></span>'.
				'<span class="rub" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" content="' . price($price / $rub) . '">' . price($price / $rub) . '</span>&nbsp;<span itemprop="priceCurrency" content="RUB">р.</span></span>';
		}
		
		else
		{
			$html = '<span class="uah">' . price($price) . '&nbsp;грн</span>'.
				'<span class="usd">' . price($price / $usd) . '&nbsp;$</span>'.
				'<span class="rub">' . price($price / $rub) . '&nbsp;р.</span>';
		}
		
		if (($discount = trim($discount)) && $discount > 0)
		{
			
			if (substr($discount, -1) == '%')
			{
				$discounted = $price - ($price / 100 * substr($discount, 0, -1));
			}
			else
			{
				$discounted = $price - $discount;
			}
			
			$html = '<u class="discount">' . $html . '</u> '.
				'<span class="uah">' . price($discounted) . '&nbsp;грн</span>'.
				'<span class="usd">' . price($discounted / $usd) . '&nbsp;$</span>'.
				'<span class="rub">' . price($discounted / $rub) . '&nbsp;р.</span>';
		}
		
		return $html;
	}
	
	protected function translate($russian, $english)
	{
		return '<span class="ru">' . $russian . '</span>'.
			'<span class="en">' . $english . '</span>';
	}
	
	protected function link()
	{
		$link = trim(implode('/', func_get_args()), '/');
		return 'http://' . $this->getHost() . '/' . (strlen($link) ? $link . '/' : '');
	}
	
}