<?php

namespace Application\Model;

use Controller;

class StatisticUrl extends \ZendCMF\Module
{
	protected static $table = 'statistic_url';
	
	protected static $schema = array(
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