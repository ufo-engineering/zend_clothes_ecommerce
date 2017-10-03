<?php

namespace Application\Model;

use Controller;

class ProductView extends \ZendCMF\Module
{
	protected static $table = 'product_view';
	
	protected static $schema = array(
		'productId'			=> 'id',
		'accountId'			=> 'id',
	);
	
	protected static $private = array(
	);
	
	protected static $locked = array(
	);
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function get($account)
	{
		return $this->find(array(
			'where' => array('accountId' => $account),
			'order' => 'date',
			'drect' => '1',
			'limit' => '24',
		));
	}
	
	public function add($account, $product)
	{
		$is = $this->is($account, $product);
		$save = array(
			'accountId' => $account,
			'productId' => $product,
			'date'		=> time(),
		);
		
		if (is_array($is) && count($is))
		{
			$save['id'] = get($is, 'id');
		}
		
		return $this->save($save);
	}
	
	public function is($account, $product)
	{
		return get($this->find(array(
			'where' => array('accountId' => $account, 'productId' => $product),
			'limit' => '1',
		)), 0);
	}
	
}