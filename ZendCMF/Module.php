<?php

namespace ZendCMF;

class Module extends Basic
{
	public function findAll($filters = array())
	{
		if ( ! is_array($filters))
		{
			$filters = array();
		}
		
		$timer = nanotime();
		$count = array('table' => $this::$table);
		$found = array_merge($filters, array('found' => true));
		
		return array(
			'filters'	=> $filters,
			'records'	=> $this->find($found),
			'locked'	=> $this::$locked,
			'found'		=> $this->query('SELECT FOUND_ROWS()', ZCMF_DB_VAR),
			'total'		=> $this->countQuery($count),
			'index'		=> get($filters, 'index', 0),
			'limit'		=> get($filters, 'limit', 100),
			'time'		=> nanotime() - $timer,
		);
	}
	
	public function find($filters = array())
	{
		if ( ! is_array($filters))
		{
			$filters = array();
		}
		
		$where				= get($filters, 'where', array());
		$filters['table']	= $this::$table;
		
		if (count($this->safePrivateRow($where)) != count($where))
		{
			return $this->setError('PROTECTED_FIELD');
		}
		
		return $this->safePrivateArray($this->selectQuery($filters));
	}
	
	public function get($value, $field = 'id')
	{
		if (in_array($field,$this::$private))
		{
			return $this->setError('PROTECTED_FIELD');
		}
		
		return $this->safePrivateRow($this->getSecure($value, $field));
	}
	
	protected function getSecure($value, $field = 'id')
	{
		return $this->selectQuery(array(
			'table' => $this::$table,
			'limit' => 1,
			'where' => array(
				$field => $value,
			),
		), ZCMF_DB_ROW);
	}
	
	public function save($form)
	{
		$id		= get($form, 'id');
		$where	= array();
		
		if ( ! is_array($form) || ! count($form))
		{
			return false;
		}
		
		if (count($validation = $this->validate($form, $id)))
		{
			return $this->setError('FORM_ERROR', $validation);
		}
		
		else if (isset($form['id']))
		{
			if (isset($this::$logging) && $this::$logging)
			{
				$form['edited'] = time();
			}
			
			$where['id'] = $id;
			unset($form['id']);
		}
		
		else if (isset($this::$logging) && $this::$logging)
		{
			$form['added'] = time();
		}
		
		$result = $this->insertQuery(array(
			'table'	=> $this::$table,
			'where' => $where,
			'set'	=> $form,
		));
		
		if ($result === false)
		{
			$this->setError($this->getLastError());
		}
		
		return $result;
	}
	
	public function drop($value, $field = 'id')
	{
		// check access
		return $this->deleteQuery(array(
			'table' => $this::$table,
			'where' => array(
				$field => $value,
			),
		));
	}
	
	public function getCount($form)
	{
		return $this->countQuery(array(
			'table' => $this::$table,
			'where' => get($form, 'where', array()),
		));
	}
	
	protected function getNextId()
	{
		$status = $this->query("SHOW TABLE STATUS LIKE '" . addslashes($this::$table) . "'", ZCMF_DB_ROW);
		return get($status, 'Auto_increment');
	}
	
	public function validate($fields, $id = null)
	{
		$result = array();
		
		foreach ($this::$schema as $field => $rules)
		{
			if ($error = $this->validateField(get($fields,$field), $rules, $id))
			{
				$result[$field] = $error;
			}
		}
		
		return $result;
	}
	
	protected function validateField($value, $rules, $id = null)
	{
		foreach (explode(' ', $rules) as $rule)
		{
			switch ($rule)
			{
				case 'required':
					if (strlen(trim($value)) == 0 && ! ($id && $value == null))
					{
						return 'required';
					}
				break;
				
				case 'url':
					if ($value != null && ! preg_match('/^[a-zA-Z0-9\-\_\+]*$/', $value))
					{
						return 'incorrect';
					}
				break;
			}
		}
		
		return false;
	}
	
	protected function safePrivateArray($array)
	{
		if ( ! is_array($array)) return array();
		
		foreach ($array as $id => $row)
		{
			$array[$id] = $this->safePrivateRow($row);
		}
		
		return $array;
	}
	
	protected function safePrivateRow($row)
	{
		if ( ! is_array($row)) return array();
		
		$safe = array();

		foreach ($row as $key => $value)
		{
			if (isset($this::$private) && ! in_array(trim($key), $this::$private))
			{
				$safe[$key] = $value;
			}
		}
		
		return $safe;
	}
	
}
