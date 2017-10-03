<?php

namespace Application\Controller;

class Api extends \ZendCMF\Controller
{
	
	public function defaultAction($controller, $module = '', $method = '')
	{
		if (strlen($module))
		{
			$result = $this->callApiMethod($module, $method, $_POST);
		}
		
		else
		{
			$result = array();
			
			foreach ($_POST as $var => $info)
			{
				$module		= get($info, 'module');
				$method		= get($info, 'method');
				$arguments	= get($info, 'arguments');
				
				if ($module && $method)
				{
					$result[$var] = $this->callApiMethod($module, $method, $arguments);
				}
			}
		}
		
		return $this->returnJson($result);
	}
	
	private function callApiMethod($api, $method, $arguments)
	{
		$model = $this->loadModel($api);
		
		if ( ! is_object($model))
		{
			return $this->returnError(404, 'UNDEFINED_MODULE');
		}
		
		else if ( ! method_exists($model, $method))
		{
			return $this->returnError(404, 'UNDEFINED_METHOD');	
		}
		
		$passed = array();
		$object	= new \ReflectionMethod($model, $method);
		$params	= $object->getParameters();
		
		if ( ! $object->isPublic())
		{
			return $this->returnError(404, 'PROTECTED_METHOD');	
		}
		
		foreach ($params as $param)
		{
			$name	= $param->getName();
			$value	= null;
			
			if ($param->isDefaultValueAvailable())
			{
				$value = $param->getDefaultValue();
			}
			
			$passed[$name] = get($arguments, $name, $value);
		}

		return call_user_func_array(array($model, $method), $passed);
	}
	
}
