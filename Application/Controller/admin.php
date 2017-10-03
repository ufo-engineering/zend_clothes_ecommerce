<?php

namespace Application\Controller;

class Admin extends \ZendCMF\Controller
{
	
	public function action()
	{
		return $this->returnHtml($this->loadView('admin/index'));
	}
	
}