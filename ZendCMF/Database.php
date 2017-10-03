<?php

namespace ZendCMF;

require_once('Helper.php');

// filter options
// where key = value
// where key in (values)
// where key like value
// where key > value
// where key <= value
// where key between a and b
// where key not ... value
// order by key 
// group by key
// sum, min, max, avg
// index, limit

/*$construct = array(
	'table' => 'product',
	'field' => 'id, avg(price) as medium, price, title',
	'where' => array(
		'available' => true,
		'price between' => array('1000', '5000'),
		'category in' => array('1','2','3'),
		'title like' => '%dress%',
		'color or' => array('red','blue'),
	),
	'index' => 0,
	'limit' => 100,
	'order' => 'price asc',
	'group' => 'color',
);*/

define('ZCMF_DB_ALL', 0);
define('ZCMF_DB_ROW', 1);
define('ZCMF_DB_COL', 2);
define('ZCMF_DB_VAR', 3);
define('ZCMF_DB_NUM', 4);
define('ZCMF_DB_LID', 5);

class Database
{
	private $connection = 'mysql:host=%s;dbname=%s';
	private static $pdo;
	private static $queries = array();
	
	protected function openDatabase($config)
	{
		self::$pdo = new \PDO(
			sprintf($this->connection, $config['dbHost'], $config['dbName']),
			$config['dbUser'],
			$config['dbPass']
		);
		$this->query("SET NAMES 'utf8'");
	}
	
	protected function closeDatabase()
	{
		//
	}
	
	protected function getLastError()
	{
		return self::$pdo->errorInfo();
	}
	
	protected function deleteQuery($construct)
	{
		$table = quote(get($construct, 'table'), '`');
		$where = $this->getWhereStatement(get($construct, 'where'));
		$query = array('DELETE', 'FROM', $table, 'WHERE', $where);
		
		if ( ! count($where))
		{
			return $this->setError('UNSAFE_OPERATION');
		}
		
		return $this->query(implode(' ', $query)) ? true: false;
	}
	
	protected function countQuery($construct)
	{
		$table = quote(get($construct, 'table'), '`');
		$where = $this->getWhereStatement(get($construct, 'where'));
		$query = array('SELECT', 'COUNT(*)', 'FROM', $table);
		
		if ($where)
		{
			array_push($query, 'WHERE', $where);
		}
		
		return $this->query(implode(' ', $query), ZCMF_DB_VAR);
	}
	
	protected function selectQuery($construct, $mode = ZCMF_DB_ALL)
	{
		$table = quote(get($construct, 'table'), '`');
		$field = $this->getFieldStatement(get($construct, 'field'));
		$where = $this->getWhereStatement(get($construct, 'where'));
		$found = get($construct, 'found') ? ' SQL_CALC_FOUND_ROWS' : '';
		$drect = get($construct, 'drect', 1) > 0 ? 'ASC' : 'DESC';
		$order = get($construct, 'order');
		$index = get($construct, 'index', 0, true);
		$limit = get($construct, 'limit', 100, true);
		$query = array('SELECT' . $found, $field, 'FROM', $table);
		
		// TODO: modify
		$order2 = get($construct, 'order2');
		$drect2 = get($construct, 'drect2', 2) > 0 ? 'ASC' : 'DESC';
		
		if ($where)	array_push($query, 'WHERE', $where);
		if ($order)
		{
			if (is_array($order)) array_push($query, 'ORDER BY FIELD (', implode(',', $order), ')', $drect);
			else array_push($query, 'ORDER BY', quote($order,'`'), $drect);
		}
		if ($order2)
		{
			array_push($query, ',', quote($order2,'`'), $drect2);
		}
		
		array_push($query, 'LIMIT', $index, ',', $limit);
		
		return $this->query(implode(' ', $query), $mode);
	}
	
	protected function insertQuery($construct)
	{
		$table = quote(get($construct, 'table'), '`');
		$store = $this->getSetStatement(get($construct, 'set'));
		$where = $this->getWhereStatement(get($construct, 'where'));
		$query = $where? 
			array('UPDATE', $table, 'SET', $store, 'WHERE', $where):
			array('INSERT INTO', $table, 'SET', $store);
		
		//$mode = $where ? ZCMF_DB_NUM : ZCMF_DB_LID;
		
		$result = $this->query(implode(' ', $query), ZCMF_DB_LID);
		
		if ($result === false) return $result;
		
		return get(get($construct, 'where'), 'id', $result);
	}

	protected function updateQuery($construct)
	{
		$table = quote(get($construct, 'table'), '`');
		$store = $this->getSetStatement(get($construct, 'set'));
		$where = $this->getWhereStatement(get($construct, 'where'));
		$query = $where? 
			array('UPDATE', $table, 'SET', $store, 'WHERE', $where):
			array('UPDATE', $table, 'SET', $store);
		
		$result = $this->query(implode(' ', $query), ZCMF_DB_LID);
		
		if ($result === false) return $result;
		
		return get(get($construct, 'where'), 'id', $result);
	}
	
	protected function query($query, $mode = ZCMF_DB_ALL)
	{
		try
		{
			//print_r($query);
			//die($query);
			$time = nanotime();
			if ($result = self::$pdo->query($query))
			{
				array_push(self::$queries, array(
					'query'		=> $query,
					'time'		=> round(nanotime() - $time, 8),
					'success'	=> true,
				));
				
				switch ($mode)
				{
					case ZCMF_DB_LID:
						return self::$pdo->lastInsertId();
						
					case ZCMF_DB_NUM:
						return $result->rowCount();
						
					case ZCMF_DB_VAR:
						return get($result->fetch(\PDO::FETCH_NUM), 0);
						
					case ZCMF_DB_ROW:
						return $result->fetch(\PDO::FETCH_ASSOC);
						
					default:
						return $result->fetchAll(\PDO::FETCH_ASSOC);
				}
			}
			
			else
			{
				array_push(self::$queries, array(
					'query'		=> $query,
					'time'		=> round(nanotime() - $time, 8),
					'success'	=> false,
				));
			}
		}
		
		catch (Exception $e) {}
		
		return false;
	}

	protected function getDbLogs()
	{
		return self::$queries;
	}
	
	protected function getFieldStatement($data)
	{
		if (is_string($data))	$data = explode(',', $data);
		if ( ! is_array($data))	$data = array();
		
		return count($data)?
			implode(',', quote($data, '`')):
			"*";
	}
	
	protected function getSetStatement($data)
	{
		if (is_string($data))	$data = explode(',', $data);
		if ( ! is_array($data))	$data = array();
		
		$fields = array();
		$actions = array('+=', '-=', '*=', '/=');
		
		foreach ($data as $key => $value)
		{
			$keys	= explode(' ', $key);
			$mode	= get($keys, 1, '=');
			
			if (in_array($mode, $actions))
			{
				$fields[] = quote(get($keys,0), '`') . '=' . quote(get($keys,0), '`') . $mode[0] . quote($value);
			}
			
			else
			{
				$fields[] = quote(get($keys,0), '`') . '=' . quote($value);
			}
		}
		
		return implode(', ', $fields);
	}
	
	protected function getWhereStatement($data)
	{
		if (is_string($data))			return $data;
		else if ( ! is_array($data))	return false;
		
		$join = array();
		
		foreach ($data as $key => $value)
		{
			$keys	= explode(' ', $key);
			$join[] = $this->getWhereItemStatement(
				get($keys, 1),
				get($keys, 0),
				$value
			);
		}
		
		return implode(' AND ', $join);
	}
	
	private function getWhereItemStatement($operator, $field, $value)
	{
		switch ($operator)
		{
			case 'in':
				if ( ! is_array($value)) $value = explode(',', $value);
				return quote($field, '`') . ' IN (' . implode(',', quote($value)) . ')';
			
			case 'protectedin':
				return quote($field, '`') . ' IN (' . $value . ')';
			
			case 'like':
				return quote($field, '`') . ' LIKE ' . quote('%' . $value . '%');
			
			case 'between':
				return quote($field, '`') . ' BETWEEN ' . get($value, 0, null, true) . ' AND ' . get($value, 1, null, true);
				
			case '>':
				return quote($field, '`') . '>' . quote($value);
				
			case '<':
				return quote($field, '`') . '<' . quote($value);
			
			case '>=':
				return quote($field, '`') . '>=' . quote($value);
				
			case '<=':
				return quote($field, '`') . '<=' . quote($value);
				
			case '!=':
				return quote($field, '`') . '!=' . quote($value);
				
			default:
				return quote($field, '`') . '=' . quote($value);
		}
	}
	
}
