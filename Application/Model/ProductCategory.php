<?php

namespace Application\Model;

class ProductCategory extends \ZendCMF\Module
{
	protected static $table = 'product_category';
	
	protected static $schema = array(
		'parentId'			=> 'id',
		'level'				=> 'int',
		'url'				=> 'required unique url max:50',
		'titleRu'			=> 'required string max:100',
		'titleEn'			=> 'required string max:100',
		'metaKeywords'		=> 'string max:250',
		'metaDescription'	=> 'string max:250',
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
	
	public function setPosition($form)
	{
		if ( ! is_array($form) || ! count($form)) return false;
		
		$result = array();
		
		foreach ($form as $id => $position)
		{
			$result[] = $this->updateQuery(array(
				'table' => $this::$table,
				'where' => array('id' => $id),
				'set'	=> array('position' => $position),
			));
		}
		
		return $result;
	}
	
	public function drop($value)
	{
		$groups = $this->getGrouped();
		$values = $this->getIdsRecursive($groups, $value);
		
		$values[] = $value;
		
		return $this->deleteQuery(array(
			'table' => $this::$table,
			'where' => array(
				'id in' => $values,
			),
		));
	}
	
	public function getGrouped()
	{
		$found	= $this->find(array(
			'field' => 'id,parentId,url,titleRu,titleEn,visibility,filters,filter_params',
			'order' => 'position',
		));
		
		return group(assoc($found, 'id'), 'parentId');
	}
	
	private function getIdsRecursive($groups, $id)	
	{
		$result = array();
		
		if (isset($groups[$id]))
		{
			$ids = array_keys($groups[$id]);
			$result = array_merge($result, $ids);
			
			for ($i = 0; $i < count($ids); $i++)
			{
				$new = $this->getIdsRecursive($groups, $ids[$i]);
				$result = array_merge($result, $new);
			}
		}
		
		return $result;
	}
}