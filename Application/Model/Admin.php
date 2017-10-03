<?php

namespace Application\Model;

class Admin extends \ZendCMF\Module
{
	protected static $table = 'admin';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'login'		=> 'required string unique max:30',
		'password'	=> 'required string max:30',
	);
	
	protected static $private = array(
		'password'
	);
	
	protected static $locked = array(
		1
	);
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function login($login, $password)
	{
		$fields = array(
			'login'		=> $login,
			'password'	=> $password,
		);
		
		if (count($validation = $this->validate($fields)))
		{
			return $this->setError('EMPTY_FIELDS', $validation);
		}
		
		else if ( ! $result = $this->getSecure($login,'login'))
		{
			return $this->setError('UNDEFINED_LOGIN', array('login'=>'undefined'));
		}
		
		else if ($result['password'] !== $password)
		{
			return $this->setError('UNDEFINED_PASSWORD', array('password'=>'undefined'));
		}
		
		else if ( ! $result['active'])
		{
			return $this->setError('DISABLED_ACCESS', array('login'=>'locked'));	
		}
		
		else
		{
			return $_SESSION['admin'] = $this->safePrivateRow($result);
		}
	}
	
	public function logout()
	{
		unset($_SESSION['admin']);
		return null;
	}
	
	public function status()
	{
		return get($_SESSION, 'admin');
	}
}