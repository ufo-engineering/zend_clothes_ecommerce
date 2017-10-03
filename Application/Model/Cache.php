<?php

namespace Application\Model;

class Cache extends \ZendCMF\Module
{
	protected static $table = 'cache';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'id'		=> 'id',
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function clear()
	{
		return $this->query('TRUNCATE `cache`');
	}
	
}