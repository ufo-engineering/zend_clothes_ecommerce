<?php

namespace ZendCMF;

require_once('Helper.php');
require_once('Database.php');

define('ZCMF_MISSING_ROUTING_RULE', '1');
define('ZCMF_MISSING_ROUTING_CONTROLLER', '2');
define('ZCMF_MISSING_CONTROLLER_FILE', '3');
define('ZCMF_MISSING_CONTROLLER_CLASS', '4');
define('ZCMF_MISSING_CONTROLLER_METHOD', '5');
define('ZCMF_ERROR_LOADING_CONTROLLER', '6');
define('ZCMF_ERROR_EXECUTING_CONTROLLER', '7');

define('ZCMF_TYPE_FATAL', 'FATAL');
define('ZCMF_TYPE_ERROR', 'ERROR');
define('ZCMF_TYPE_MESSAGE', 'MESSAGE');

class Basic extends Database
{
	private static $config;
	private static $routing;
	private static $timer;
	private static $errors = array();
	private static $models = array();
	
	protected function getUrl($url = null)
	{
		if ($url == null) $url = get($_SERVER, 'REQUEST_URI');
		$url = preg_replace('/gclid=[a-zA-Z0-9\-\_]+\&?/', '', $url);
		$url = preg_replace('/utm_source=[a-zA-Z0-9\-\_]+\&?/', '', $url);
		$url = preg_replace('/utm_medium=[a-zA-Z0-9\-\_]+\&?/', '', $url);
		$url = preg_replace('/utm_campaign=[a-zA-Z0-9\-\_]+\&?/', '', $url);
		$url = preg_replace('/yclid=[a-zA-Z0-9\-\_]+\&?/', '', $url);
		$url = preg_replace('/\?$/', '', $url);
		return $url;
	}
	
	protected function loadModel($name)
	{
		if (isset(self::$models[$name]))
		{
			return self::$models[$name];
		}
		
		$file	= $this->getModelPath($name);
		$class	= $this->getNamespace('Model', $name);
		
		if (class_exists($class))
		{
			return new $class;
		}
		
		else if ( ! file_exists($file))
		{
			return false;
		}
		
		else if ( ! include($file))
		{
			return false;
		}
			
		else if ( ! class_exists($class))
		{
			return false;
		}
		
		return self::$models[$name] = new $class;
	}
	
	protected function loadView($name, $data = array())
	{
		$file	= $this->getViewPath($name);
		
		if ( ! file_exists($file))
		{
			return false;
		}
		
		ob_start();
		include($file);
		$content = ob_get_contents();
		ob_end_clean();
				
		return $content;
	}
	
	protected function isView($name)
	{
		return file_exists($this->getViewPath($name));
	}
	
	/**
	 * Compose Application namespace for target class
	 */
	protected function getNamespace()
	{
		$arguments = array(self::$config['namespace']);
		
		foreach (func_get_args() as $item)
		{
			$arguments[] = resolveName($item);
		}
		
		return implode('\\', $arguments);
	}
	
	/**
	 * Get Application Controller file path
	 */
	protected function getControllerPath($name)
	{
		return self::$config['cdController'] . $name . '.php';
	}
	
	/**
	 * Get Application Model file path
	 */
	protected function getModelPath($name)
	{
		return self::$config['cdModel'] . resolveName($name) . '.php';
	}
	
	/**
	 * Get Application View file path
	 */
	protected function getViewPath($name)
	{
		return self::$config['cdView'] . $name . '.tpl.php';
	}
	
	/**
	 * Get routing rules for passed url
	 */
	protected function getRouting($url)
	{
		if ( ! is_string($url))				$url = '/';
		if ($index = strpos($url, '?'))		$url = substr($url, 0, $index);
		
		foreach (self::$routing as $rule => $data)
		{
			if ($this->checkRoutingRule($url, $rule))
			{
				if (isset($data['realredirect']))
				{
					return $this->redirect($data['realredirect']);
				}
				
				return isset($data['redirect'])?
					$this->getRouting($data['redirect']):
					array($url, (object) $data);
			}
		}
		
		return false;
	}
	
	/**
	 * Class constructor
	 */
	protected function setConfig($config, $routing)
	{
		self::$config	= $config;
		self::$routing	= $routing;
		self::$timer	= nanotime();
		
		$this->openDatabase($config);
	}
	
	/**
	 * Add error to log
	 */
	protected function setError($message, $fields = null, $type = ZCMF_TYPE_ERROR)
	{
		array_push(self::$errors, array(
			'type'		=> $type,
			'message'	=> $message,
			'fields'	=> $fields,
		));
		return false;
	}
	
	/**
	 * Get last error
	 */
	protected function getError()
	{
		return array_pop(self::$errors);
	}
	
	/**
	 * Redirect browser to url
	 */
	public function redirect($url)
	{
		header('Location: ' . $url);
		
		$this->closeDatabase();
		exit();
	}
	
	/**
	 * Return HTML to the browser
	 */
	public function returnXML($xml)
	{
		header('Content-Type: application/xml');
		print($xml);
		
		$this->closeDatabase();
		exit();
	}
	
	/**
	 * Return HTML to the browser
	 */
	public function returnHtml($html)
	{
		print($html);
		print('<!-- ' . (nanotime() - self::$timer) . ' -->');
		
		if (isset($_GET['xdebug']) && $_GET['xdebug'] == 'true')
		{
			print('<!--');
			print_r($this->getDbLogs());
			print('-->');
		}
		
		$this->closeDatabase();
		exit();
	}
	
	/**
	 * Return JSON for AJAX requests
	 */	
	public function returnJson($result)//status code, status message, response
	{
		print json_encode(array(
			'error'		=> $this->getError(),
			'result'	=> $result,
		));
		
		$this->closeDatabase();
		exit();
	}
	
	/**
	 * Return error message via http code
	 */
	public function returnError($code, $message = '')
	{
		header('HTTP/1.0 ' . $code . ' ' . $message);
		
		$this->closeDatabase();
		exit();
	}
	
	/**
	 * Check single routing rule for url
	 */
	private function checkRoutingRule($url, $rule)
	{
		if ( ! is_string($url))		return false;
		if ( ! is_string($rule))	return false;
		
		for ($i = 0, $r = strlen($rule), $u = strlen($url); $i < $r; $i++)
		{
			// if == '?'
			if ($rule[$i] == '*')				return true;
			else if ( ! isset($url[$i]))		return false;
			else if ($rule[$i] !== $url[$i])	return false;
			else if ($i == $r - 1 && $u > $r)	return false;
		}
		
		return true;
	}
	
} 