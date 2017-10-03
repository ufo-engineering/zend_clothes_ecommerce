<?php

namespace Application\Model;

class ProductExport extends \ZendCMF\Module
{
	protected static $table = 'product_export';
	
	protected static $schema = array(
		'url'	=> 'required string',
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
	
	public function generate($from, $to, $title = '')
	{
		ignore_user_abort(true);
		set_time_limit(0);
		
		if ( ! ($from > 0 && $to > $from))
		{
			return $this->setError('FORM_ERROR', array('from' => 'required', 'to' => 'required'));
		}
		
		$model = $this->loadModel('product');
		$found = assoc($model->find(array(
			'where' => array('added >' => $from, 'added <' => ($to+86400), 'active' => 1, 'availability !=' => 0),
			'field'	=> 'id',
			'limit'	=> '5000',
		)), 'id', 'id');
		
		if ( ! count($found))
		{
			return $this->setError('NO_RECORDS');
		}
		
		$images	= $model->getAllImages($found);
		
		$time	= time();
		$zip	= new \ZipArchive();
		$save	= 'public/uploads/' . date('Y.m.d_H.i.s') . '_' . $title . '.zip';
		
		if (($file = $zip->open($save, \ZIPARCHIVE::CREATE)) !== true)
		{
			return false;
		}
		
		foreach ($images as $product)
		{
			foreach ($product as $image)
			{
				$file = get($image, 'view');
				$path = 'public/products/show/' . $file;
				$name = preg_replace('/^([^\/]+\/)*/', '', $file);
				
				if (is_file($path)) {
					$zip->addFile($path, $name);
				}
				//print($path . ' - ' . $name . '<br>');
			}
		}
		
		if ($zip->numFiles == 0)
		{
			return $this->setError('NO_FILES');
		}
		
		$count = $zip->numFiles;
		$status = $zip->status;
		
		$zip->close();
		
		if (is_file($save)) $size = filesize($save);
		else $size = 0;
		
		$result = $this->save(array(
			'url'	=> $save,
			'title'	=> $title,
			'added'	=> time(),
			'size'	=> $size,
			'time'	=> time() - $time,
		));
		
		return array(
			'time' => time() - $time,
			'size' => $size,
			'save' => $save,
			'title' => $title,
			'files'	=> $count,
			'status' => $status,
		);
	}

}		