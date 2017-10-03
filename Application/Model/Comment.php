<?php

namespace Application\Model;

class Comment extends \ZendCMF\Module
{
	protected static $table = 'comment';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'id'		=> 'id',
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
	
	public function confirm($id, $active = 2)
	{
		return $this->save(array(
			'id'		=> $id,
			'active'	=> $active,
		));
	}
	
}