<?php

namespace Application\Model;

class Account extends \ZendCMF\Module
{
	protected static $table = 'account';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'email'		=> 'required string email unique max:30',
		'phone'		=> 'required string max:30',
		'name'		=> 'required string max:30',
		'password'	=> 'required string max:30',
	);
	
	protected static $private = array(
		'password'
	);
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function restore($email)
	{
		if ( ! $result = $this->getSecure($email,'email'))
		{
			return $this->setError('UNDEFINED_EMAIL', array('email'=>'undefined'));
		}
		
		return true;
	}
	
	public function login($email, $password)
	{
		$fields = array(
			'email'		=> $email,
			'password'	=> $password,
		);
		
		if (count($validation = $this->validate($fields,1)))
		{
			return $this->setError('EMPTY_FIELDS', $validation);
		}
		
		else if ( ! $result = $this->getSecure($email,'email'))
		{
			return $this->setError('UNDEFINED_EMAIL', array('email'=>'undefined'));
		}
		
		else if ($result['password'] !== $password)
		{
			return $this->setError('UNDEFINED_PASSWORD', array('password'=>'undefined'));
		}
		
		else if ( ! $result['active'])
		{
			return $this->setError('DISABLED_ACCESS', array('email'=>'locked'));	
		}
		
		else
		{
			return $_SESSION['user'] = $this->safePrivateRow($result);
		}
	}
	
	public function logout()
	{
		unset($_SESSION['user']);
		return null;
	}
	
	public function status()
	{
		return get($_SESSION, 'user');
	}
	
	public function updateCache($id)
	{
		$model = $this->loadModel('order');
		$found = $model->find(array(
			'field'	=> 'id,added,price',
			'where'	=> array('acountId' => $id),
		));
		
		$count = 0;
		$price = 0;
		$added = 0;
		
		foreach ($found as $order)
		{
			$added = max($order['added'], $added);
			$price += $order['price'];
			$count++;
		}
		
		$data = array(
			'id'			=> $id,
			'ordersCount'	=> $count,
			'ordersPrice'	=> $price,
			'ordersDate'	=> $added,
		);
		
		$this->save($data);
		
		return $data;
	}
	
}
