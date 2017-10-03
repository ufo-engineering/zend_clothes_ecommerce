<?php

namespace Application\Model;

class ProductData extends \ZendCMF\Module
{
	protected static $table = 'product_data';
	
	protected static $schema = array(
		'productId'			=> 'id',
		'paramKeyId'		=> 'id',
		'paramValueId'		=> 'id',
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
	
}