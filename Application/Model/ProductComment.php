<?php

namespace Application\Model;

class ProductComment extends \ZendCMF\Module
{
	protected static $table = 'product_comment';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'authorName'			=> 'required string max:100',
		'authorEmail'			=> 'required string max:100',
		'message'				=> 'required string max:1000',
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function findAll($filters)
	{
		$result		= parent::findAll($filters);
		$records	= get($result, 'records');
		$products	= assoc($records, 'id', 'productId');
		
		if (count($products))
		{
			$result['products'] = $this->getAllProducts($products);
		}
		
		return $result;
	}
	
	public function confirm($id, $active = 2)
	{
		return $this->save(array(
			'id'		=> $id,
			'active'	=> $active,
		));
	}
	
	protected function getAllProducts($ids)
	{
		$model = $this->loadModel('product');
		$found = $model->find(array(
			'field'		=> 'id,title,url',
			'where'		=> array('id in' => array_unique($ids))
		));
		
		return assoc($found, 'id');
	}
}