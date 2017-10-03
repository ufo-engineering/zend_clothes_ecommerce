<?php

namespace ZendCMF;

require_once('Basic.php');
require_once('Module.php');
require_once('Controller.php');

/**
 * How it works
 * 
 * Application class receive site configuration and routing rules.
 * Due to routing rules determinate which controller should be called.
 * Calling required controller with passed arguments (url path).
 * Returning 404 if request can't be routed or controller doesn't exist.
 */
class Application extends Basic
{
	
	/**
	 * Class constructor
	 */
	public function __construct($config, $routing)
	{
		session_start();
		
		$this->setConfig($config, $routing);
	}
	
	/**
	 * Execute request
	 */
	public function run($url)
	{
		list($url, $routed) = $this->getRouting($url);
		
		if ( ! is_object($routed))
		{
			return $this->returnError(404, ZCMF_MISSING_ROUTING_RULE);
		}
		
		else if ( ! is_string($routed->controller))
		{
			return $this->returnError(404, ZCMF_MISSING_ROUTING_CONTROLLER);
		}
		
		$this->callController($routed->controller, $url);
	}
	
	/**
	 * Call required controller with arguments
	 */
	public function callController($controller, $url)
	{
		$arguments	= array_slice(explode('/', $url), 1);
		$action		= get($arguments, 1, '');
		$method		= 'action' . resolveName($action);
		
		$file		= $this->getControllerPath($controller);
		$class		= $this->getNamespace('Controller', $controller);
		
		if (get($arguments, count($arguments) - 1) === '')
		{
			$arguments = array_slice($arguments, 0, -1);
		}
		
		if ( ! file_exists($file))
		{
			return $this->returnError(404, ZCMF_MISSING_CONTROLLER_FILE);
		}
		
		else if ( ! include($file))
		{
			return $this->returnError(500, ZCMF_ERROR_LOADING_CONTROLLER);
		}
			
		else if ( ! class_exists($class))
		{
			return $this->returnError(404, ZCMF_MISSING_CONTROLLER_CLASS);
		}
		
		else if ( ! method_exists($class, $method)
				&& ! method_exists($class, $method = 'defaultAction'))
		{
			return $this->returnError(404, ZCMF_MISSING_CONTROLLER_METHOD);
		}
		
		try
		{
			$this->returnHtml(call_user_func_array(
				array(new $class, $method),
				$arguments
			));
		}
		
		catch(ErrorException $e)
		{
			return $this->returnError(500, ZCMF_ERROR_EXECUTING_CONTROLLER);
		}
	}
	
} 