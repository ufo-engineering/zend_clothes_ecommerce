<?php

namespace Application\Model;

class Subscribe extends \ZendCMF\Module
{
	protected static $table = 'subscribe';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'id'		=> 'id',
		'accountId'	=> 'unique id',
		'email'		=> 'required string max:100',
		'name'		=> 'required string max:50',
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
}