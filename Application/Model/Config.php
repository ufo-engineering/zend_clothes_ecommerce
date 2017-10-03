<?php

namespace Application\Model;

class Config extends \ZendCMF\Basic
{
	protected static $table		= 'config';
	protected static $config	= null;
	
	public function get($key)
	{
		$all = $this->getAll();
		return get(get($all, $key), 'value');
	}
	
	public function getAll()
	{
		if (self::$config == null)
		{
			self::$config = assoc($this->selectQuery(array(
				'table' => self::$table,
			)), 'key');
		}
		
		return self::$config;
	}
	
	public function set($key, $value)
	{
		return $this->insertQuery(array(
			'table'	=> self::$table,
			'set'	=> array('value' => $value),
			'where'	=> array('key' => $key),
		));
	}
	
	public function setAll($form)
	{
		$result = array();
		
		foreach ($form as $key => $value)
		{
			$old = $this->get($key);
			
			if ($old !== $value)
			{
				$result[] = $this->set($key, $value);
			}
		}
		
		return $result;
	}
	
}