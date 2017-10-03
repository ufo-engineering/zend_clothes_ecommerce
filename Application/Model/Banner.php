<?php

namespace Application\Model;

class Banner extends \ZendCMF\Module
{
	protected static $table = 'banner';
	
	protected static $schema = array(
		'type'		=> 'required',
		'link'		=> 'required string max:100',
		'title'		=> 'required string max:100',
		'link'		=> 'required string max:100',
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function getGrouped()
	{
		$found	= $this->find();
		
		return group(assoc($found, 'id'), 'type');
	}
	
	/**
	 * load POST file
	 */
	public function upload($id)
	{
		$result	= array();
		
		foreach ($_FILES as $i => $info)
		{
			$name = $this->getTempFileName();
			$move = move_uploaded_file($info['tmp_name'], $name);
			
			if ($move)
			{
				$result[$i] = '/' . $name;
			}
		}
		
		return $result;
	}
	
	private function getTempFileName()
	{
		return 'public/uploads/banners/' . date('Ymd_His') . '.jpg';
	}
	
}