<?php

namespace Application\Model;

class ProductParam extends \ZendCMF\Controller
{

	public function getProductParams($id)
	{
		return array();
	}
	
	public function getGroups()
	{
		if (isset($this->groups))
		{
			return $this->groups;
		}
		
		$model = $this->loadModel('productParamGroup');
		return $this->groups = $model->find();
	}
	
	public function saveGroup($title, $id = null)
	{
		$model = $this->loadModel('productParamGroup');
		
		return $model->save(array(
			'title'		=> $title,
			'id'		=> $id,
		));
	}
	
	public function dropGroup($id)
	{
		$model	= $this->loadModel('productParamGroup');
		$key	= $this->loadModel('productParamKey');
		$value	= $this->loadModel('productParamValue');
		
		$key->drop($id, 'groupId');
		$value->drop($id, 'groupId');
		
		return $model->drop($id);
	}
	
	public function getKeys($groupId = null, $filterscat = null)
	{
		if ( ! $groupId && isset($this->keys) && !$filterscat)
		{
			return $this->keys;
		}
		
		$where = array();
		$model = $this->loadModel('productParamKey');
		
		if ($groupId)
		{
			$where['groupId'] = $groupId;
		}
        
        if ($filterscat)
		{
            $keys = array();
            foreach($filterscat as $filter){
                $where['id'] = $filter;
                $key = $model->find(array(
        			'where'		=> $where,
  		        ));
                $keys = array_merge($keys,$key);
            }
		}else{
		  $keys = $model->find(array(
			'where'		=> $where,
		  ));
          
		}
		
		if ( ! $groupId)
		{
			$this->keys = $keys;
		}
		
		return $keys;
	}
	
	public function saveKey($title, $groupId, $id = null)
	{
		$model = $this->loadModel('productParamKey');
		
		return $model->save(array(
			'title'		=> $title,
			'groupId'	=> $groupId,
			'id'		=> $id,
		));
	}
	
	public function dropKey($id)
	{
		$model = $this->loadModel('productParamKey');
		$value = $this->loadModel('productParamValue');
		
		$value->drop($id, 'keyId');
		
		return $model->drop($id);
	}
	
	public function getValues($groupId = null, $keyId = null)
	{
		if ($groupId == null && $keyId == null && isset($this->values))
		{
			return $this->values;
		}
		
		$where = array();
		$model = $this->loadModel('productParamValue');
		
		if ($groupId)
		{
			$where['groupId'] = $groupId;
		}
		
		if ($keyId)
		{
			$where['keyId'] = $keyId;
		}
		
		$values = $model->find(array(
			'where'		=> $where,
			'limit'		=> 1000,
		));
		
		if ($groupId == null && $keyId == null)
		{
			$this->values = $values;
		}
		
		return $values;
	}
	
	public function saveValue($title, $extra, $groupId, $keyId, $id = null)
	{
		$model = $this->loadModel('productParamValue');
		
		return $model->save(array(
			'title'		=> $title,
			'extra'		=> $extra,
			'groupId'	=> $groupId,
			'keyId'		=> $keyId,
			'id'		=> $id,
		));
	}
	
	public function dropValue($id)
	{
		$model = $this->loadModel('productParamValue');
		
		return $model->drop($id);
	}

}