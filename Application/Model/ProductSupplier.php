<?php

namespace Application\Model;

class ProductSupplier extends \ZendCMF\Module
{
	protected static $table = 'product_supplier';
	
	protected static $schema = array(
		'title'				=> 'required string max:50',
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