<?php

return array(
	
	'/' => array(
		'redirect' => '/index',
	),
	
	'/index.html' => array(
		'realredirect' => '/',
	),
	
	'/index.php' => array(
		'realredirect' => '/',
	),
	
	'/api/*' => array(
		'controller' => 'api',
	),
	
	'/admin*' => array(
		'controller' => 'admin',
	),
	
	'/catalog' => array(
		'controller' => 'catalog',
	),
	
	'/catalog/*' => array(
		'controller' => 'catalog',
	),
	
	'/new' => array(
		'controller' => 'catalog',
	),
	
	'/new/*' => array(
		'controller' => 'catalog',
	),
	
	'/archive' => array(
		'controller' => 'catalog',
	),
	
	'/archive/*' => array(
		'controller' => 'catalog',
	),
	
	'/search' => array(
		'controller' => 'catalog',
	),
	
	'/search/*' => array(
		'controller' => 'catalog',
	),
	
	'/product/*' => array(
		'controller' => 'product',
	),
	
	'/sitemap.xml' => array(
		'controller' => 'sitemap'
	),
	
	'/Sitemap.xml' => array(
		'controller' => 'sitemap'
	),
	
	'/*' => array(
		'controller' => 'page'
	),
	
);