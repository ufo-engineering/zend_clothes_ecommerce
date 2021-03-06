<?php

namespace Application\Model;

class Page extends \ZendCMF\Module
{
	protected static $table = 'page';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'url'		=> 'required unique string url max:100',
		'titleRu'	=> 'required string max:100',
		'titleEn'	=> 'required string max:100',
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