<?php

namespace Application\Model;

class Menu extends \ZendCMF\Module
{
	protected static $table = 'menu';
	
	protected static $schema = array(
		'parentId'	=> 'id',
		'url'		=> 'required unique string max:100',
		'titleRu'	=> 'required string max:100',
		'titleEn'	=> 'required string max:100',
	);
	
	protected static $private = array();
	
	protected static $locked = array(1,2,3);
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function getGrouped()
	{
		$found	= $this->find(array(
			'field' => 'id,parentId,url,titleRu,titleEn',
		));
		
		return group(assoc($found, 'id'), 'parentId');
	}
	
}