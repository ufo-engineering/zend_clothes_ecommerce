<?php

namespace Application\Model;

use Controller;

class Statistic extends \ZendCMF\Module
{
	protected static $table = 'statistic';
	
	protected static $schema = array(
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
	
	public function findAll($domain = null, $period = 1)
	{
		switch ($period)
		{
			case 1:		$time = 3600; break;		// 1 day = 24 / 6 = 4h
			case 7:		$time = 86400; break;		// 7 days = 7 * 24 / 7 = 24h
			case 30:	$time = 259200; break;		// 30 days = 30 * 24 / 6 = 120h
			case 365:	$time = 2628000; break;		// 365 days = 365 * 24 / 12 = 730h
			default:	$time = 3600;
		}

		$from	= ((int) $period) * 86400;
		$group	= 'FLOOR(time/' . $time . ')';//86400
		$where	= $group .  ' >= ' . (floor((time() - $from) / $time));
		
		if ($domain)
		{
			return $this->query("SELECT COUNT(id) as 'hits', COUNT(DISTINCT ip) as 'host', COUNT(DISTINCT sessionId) as 'sess', " . $group . " as 'date' FROM statistic WHERE " . $where . " AND domain = '" . addslashes($domain) . "' GROUP BY date");
		}
		else
		{
			//print("SELECT COUNT(id) as 'hits', COUNT(DISTINCT sessionId) as 'host', year, month, day, " . $group . " as 'date' FROM statistic WHERE " . $where . " GROUP BY date");
			return $this->query("SELECT COUNT(id) as 'hits', COUNT(DISTINCT ip) as 'host', COUNT(DISTINCT sessionId) as 'sess', " . $group . " as 'date' FROM statistic WHERE " . $where . " GROUP BY date");
		}
	}

	public function add()
	{
		$url		= $this->getUrlId($this->getUrl());//get($_SERVER, 'REQUEST_URI')
		$session	= $this->getSession(session_id());
		$host		= $this->getHost(get($_SERVER, 'HTTP_HOST'));
		
		return $this->save(array(
			'urlId'		=> $url,
			'sessionId'	=> $session,
			'domain'	=> $host,
			'year'		=> date('Y') - 2000,
			'month'		=> date('m'),
			'day'		=> date('d'),
			'time'		=> time(),
			'ip'		=> ip2long(get($_SERVER, 'REMOTE_ADDR'))
		));
	}
	
	public function getHost($host)
	{
		$host = str_replace('www.', '', strtolower($host));
		
		switch ($host)
		{
			case 'project.ru':		return 1;
			case 'project.com.ua':	return 2;
			case 'project.co.uk':		return 3;
			default:				return 0;
		}
	}
	
	public function getUrlId($url)
	{
		$model = \ZendCMF\Controller::loadModel('statisticUrl');
		$found = $model->get($url, 'url');
		
		if ($found)
		{
			return get($found, 'id');
		}
		
		return $model->save(array(
			'url' => $url,
		));
	}
	
	public function getSession($session)
	{
		if (isset($_SESSION['id']))
		{
			return $_SESSION['id'];
		}
		
		$model = \ZendCMF\Controller::loadModel('statisticSession');
		$found = $model->get($session, 'session');
		
		if ($found)
		{
			return $_SESSION['id'] = get($found, 'id');
		}
		
		return $_SESSION['id'] = $model->save(array(
			'session'	=> $session,
			'referer'	=> get($_SERVER, 'HTTP_REFERER'),
			'browser'	=> get($_SERVER, 'HTTP_USER_AGENT'),
			'ip'		=> get($_SERVER, 'REMOTE_ADDR'),
			'time'		=> time(),
		));
		
		// HTTP_HOST - domain (statistic)
		// HTTP_REFERER - referer (session)
		// HTTP_USER_AGENT - browser (session)
		// REMOTE_ADDR - ip (session)
		// REQUEST_URI - url (ulr)
	}

}