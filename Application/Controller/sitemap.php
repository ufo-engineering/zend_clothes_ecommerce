<?php

namespace Application\Controller;

require_once('page.php');

class Sitemap extends Page
{
	
	private $xml = array(
		'<?xml version="1.0" encoding="UTF-8"?>',
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
	);
	
	public function defaultAction()
	{
		$modelPage		= $this->loadModel('page');
		$modelProduct	= $this->loadModel('product');
		$modelCategory	= $this->loadModel('productCategory');
		
		$skipPage		= array(2,3,4,5,6,7,8,9,16,17,18);
		$skipCategory	= array();
		
		$listPage		= $modelPage->find();
		$listCategory	= $modelCategory->find();

		foreach ($listPage as $iPage)
		{
			if (in_array(get($iPage,'id'), $skipPage)) continue;
			
			$this->push(
				get($iPage,'url'),
				max(get($iPage,'added'), get($iPage,'edited'))
			);
		}

		foreach ($listCategory as $iCategory)
		{
			if (in_array(get($iCategory,'id'), $skipCategory)) continue;
			
			$date = $modelProduct->find(array(
				'where' => array('categoryId' => get($iCategory,'id')),
				'field'	=> 'id,added',
				'order'	=> 'added',
				'drect'	=> 0,
				'limit' => 1,
			));
			
			$this->push(
				get($iCategory,'url'),
				get(get($date, 0), 'added')
			);
		}
		
		$this->xml[] = '</urlset>';
		
		return $this->returnXML($this->flash());
	}

	private function push($url, $date = null, $priority = null)
	{
		$host = 'http://' . $this->getHost() . '/';
		$freq = array('always','hourly','daily','weekly','monthly','yearly','never');
		$change = 0;
		
		if ($date >= 0)
		{
			$period = round((time() - $date) / 3600);
			if ($period < 1) $change = 1;
			else if ($period < 24) $change = 2;
			else if ($period < (24*7)) $change = 3;
			else if ($period < (24*30)) $change = 4;
			else if ($period < (24*365)) $change = 5;
		}
		
		$this->xml[] = '<url>'.
				'<loc>' . $host . $url . '/</loc>'.
				($date > 0 ? '<lastmod>' . date('Y-m-d', $date) . '</lastmod>' : '').
				($change > 0 ? '<changefreq>' . get($freq, $change) . '</changefreq>' : '').
				($priority ? '<priority>' . $priority . '</priority>' : '').
				'</url>';
	}
	
	private function flash()
	{
		return implode("\r\n", $this->xml);
	}
	
}