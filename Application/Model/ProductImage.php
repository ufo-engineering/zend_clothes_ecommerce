<?php

namespace Application\Model;

class ProductImage extends \ZendCMF\Module
{
	protected static $table = 'product_image';
	
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
	
	public function move($images, $product)
	{
		return $this->updateQuery(array(
			'table'	=> $this::$table,
			'where' => array('id in' => $images),
			'set'	=> array('productId' => $product),
		));
	}
	
	/**
	 * load POST file
	 */
	public function upload($id)
	{
		$result	= array();
		
		foreach ($_FILES as $i => $info)
		{
			$name = $this->getTempFileName();
			$move = move_uploaded_file($_FILES[$i]['tmp_name'], $name);
			
			if ($move)
			{
				if (($r = $this->insert($id, $name)))
				{
					$result[get($r,'id')] = $r;
				}
				
				unlink($name);
			}
		}
		
		return $result;
	}
	
	/**
	 * load file by url
	 */
	public function load($id, $url)
	{
		$move = false;
		$name = $this->getTempFileName();
		$file = file_get_contents($url);
		
		if ($file)
		{
			if (file_put_contents($name, $file))
			{
				$move = $this->insert($id, $name);
			}
			
			unlink($name);
		}
		
		return $move;
	}
	
	public function updateImages($productId, $productArticle = '', $productUrl = '')
	{
		$index = 0;
		$found = $this->find(array('where' => array('productId' => $productId)));
		
		foreach ($found as $image)
		{
			$dir = floor($productId / 100);
			$img = $productUrl . '_' . (++$index) . '.jpg';
			$new = $dir . '/'. $image['id'] . '.jpg';
			
			if ( ! is_dir('public/products/full/' . $dir)) mkdir('public/products/full/' . $dir);
			if ( ! is_dir('public/products/show/' . $dir)) mkdir('public/products/show/' . $dir);
			if ( ! is_dir('public/products/mini/' . $dir)) mkdir('public/products/mini/' . $dir);
			
			if ($image['url'] != $new)
			{
				rename('public/products/full/' . $image['url'], 'public/products/full/' . $new);
				$image['url'] = $new;
			}
			
			$src = 'public/products/full/' . $image['url'];
			$dst = 'public/products/show/' . $dir . '/'. $img;
			$min = 'public/products/mini/' . $dir . '/'. $img;
			$wtm = 'public/images/wm.png';
			
			resize_img($src, $dst, 750, 1000, $wtm, $productArticle);
			resize_img($src, $min, 180, 240, $wtm);
			
			$result[] = $img;
			
			$this->save(array(
				'id'	=> $image['id'],
				'url'	=> $new,
				'view'	=> $dir . '/'. $img,
			));
			
			//print('<hr><img src="/' . $src . '" /><img src="/' . $dst . '" /><img src="/' . $min . '" />');
		}

		return $result;
	}
	
	protected function insert($id, $dst)
	{
		$dir = floor($id);
		$img = $dir . '/' . $this->getNextId() . '.jpg';
		$src = 'public/products/full/' . $img;
		
		if ( ! is_dir('public/products/full/' . $dir))		mkdir('public/products/full/' . $dir);
		if ( ! ($res = resize_img($dst, $src, 750, 1000)))	return false;
		
		$imS = getimagesize($src);
		$imW = get($imS, 0, 0);
		$imH = get($imS, 1, 0);
		$imP = $this->getPriority($imW, $imH);
		$inf = array(
			'productId'	=> $id,
			'url'		=> $img,
			'width'		=> $imW,
			'height'	=> $imH,
			'priority'	=> $imP,
		);
		
		if (($res = $this->save($inf)))
		{
			$inf['id'] = $res;
			return $inf;
		}
		
		return false;
	}
	
	protected function getPriority($width, $height)
	{
		$resolution = round($width / $height * 1000);//725
		if ($resolution < 725) $resolution = (725 - $resolution);
    	else $resolution -= 725;
		return round(255 / 2000 * min(max($resolution, 0), 2000));
	}
	
	private function getTempFileName()
	{
		return 'public/uploads/' . rand(1000,9999) . '.jpg';
	}

}

function resize_img($src, $dst, $toW = null, $toH = null, $wtm = null, $art = null, $qat = 80)
{
	if ( ! file_exists($src))			return false;
	if ( ! ($imS = getimagesize($src)))	return false;
	if ( ! ($toW > 0 && $toH > 0))		return false;
	
	$imR = $imS[0] / $imS[1];
	$toR = $imR > $toW / $toH;
	$niW = min($imS[0], round($toR ? $toW : $toH * $imR));
	$niH = min($imS[1], round($toR ? $toW / $imR : $toH));
	$imT = array_pop(explode('/', $imS['mime']));
	$imF = 'imagecreatefrom' . $imT;
	
	if ( ! function_exists($imF))		return false;
	
	$img = $imF($src);
	$new = imagecreatetruecolor($niW, $niH);
	
	imagecopyresampled($new, $img, 0, 0, 0, 0, $niW, $niH, $imS[0], $imS[1]);
	
	if ($wtm && file_exists($wtm) && ($wtS = getimagesize($wtm)))
	{
		$wtW = round($niW * 0.7);
		$wtH = round($wtW / ($wtS[0] / $wtS[1]));
		$wtX = round(($niW - $wtW) / 2);
		$wtY = round(($niH - $wtH) / 2);
		$wtF = imagecreatefrompng($wtm);
		imagecopyresampled($new, $wtF, $wtX, $wtY, 0, 0, $wtW, $wtH, $wtS[0], $wtS[1]);
	}
	
	if ($art)
	{
		$imC = imagecolorallocate($new, 255, 255, 255);
		$imB = imagecolorallocate($new, 0, 0, 0);
		$imX = round(10);
		$imY = round($niH - 10);
		$imF = 'public/images/arial.ttf';
		imagettftext($new, 14, 0, $imX + 1, $imY + 1, $imB, $imF, $art);
		imagettftext($new, 14, 0, $imX, $imY, $imC, $imF, $art);
	}
	
	$result = imagejpeg($new, $dst, $qat);
	
	imagedestroy($img);
	imagedestroy($new);
	
	return $result;
}