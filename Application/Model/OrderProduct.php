<?php

namespace Application\Model;

class OrderProduct extends \ZendCMF\Module
{
	protected static $table = 'product_order_product';
	
	protected static $schema = array(
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function dropOrder($order, $account)
	{
		return $this->deleteQuery(array(
			'table' => $this::$table,
			'where' => array(
				'orderId'	=> $order,
				'accountId'	=> $account,
			),
		));
	}
	
}