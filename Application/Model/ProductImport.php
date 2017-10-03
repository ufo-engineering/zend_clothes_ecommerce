<?php

/*
"Код_товара":""
"Название_позиции":"Свитер Локти. В расцветках"
"Ключевые_слова":"свитер"
"Описание":"<p>Размеры: S, M, L</p><br><p>Цвета: коралл, горчица, молочный, серый, черный.</p>"
"Тип_товара":"w"
"Цена":"150.00"
"Валюта":"UAH"
"Единица_измерения":"шт."
"Минимальный_объем_заказа":"1.000"
"Оптовая_цена":"",
"Минимальный_заказ_опт":"",
"Ссылка_изображения":"http://images.ua.prom.st/19954353_w640_h640_d832751pid1070291536856ca2.jpg"
"Наличие":"+"
"Номер_группы":"1908841"
"Адрес_подраздела":"http://prom.ua/Zhenskie-kofty-i-kardigany"
"Возможность_поставки":""
"Срок_поставки":""
"Способ_упаковки":""
"Уникальный_идентификатор","10702915"
"Идентификатор_товара":""
"Идентификатор_подраздела":"30504"
"Идентификатор_группы":""
"Производитель":""
"Страна_производитель":""
"Скидка":""
"Характеристики":""
"Пользовательские_Характеристики":""
*/


namespace Application\Model;

class ProductImport extends \ZendCMF\Module
{
	protected static $table = 'product_import';
	
	protected static $schema = array(
		'code'				=> 'string',
		'title'				=> 'string',
		'keywords'			=> 'string',
		'description'		=> 'string',
		'type'				=> 'string',
		'price'				=> 'price',
		'currency'			=> 'string',
		'countType'			=> 'string',
		'minCount'			=> 'string',
		'optPrice'			=> 'price',
		'minOptPrice'		=> 'price',
		'images'			=> 'string',
		'available'			=> 'string',
		'group'				=> 'string',
		'category'			=> 'string',
		'delivery'			=> 'string',
		'deliveryTime'		=> 'string',
		'packageType'		=> 'string',
		'realId'			=> 'int',
		'productId'			=> 'int',
		'categoryId'		=> 'int',
		'groupId'			=> 'int',
		'manufacturer'		=> 'string',
		'country'			=> 'string',
		'discount'			=> 'string',
		'info'				=> 'string',
		'additionalInfo'	=> 'string',
	);
	
	protected static $fields = array(
		"Код_товара"						=> "code",
		"Название_позиции"					=> "title",
		"Ключевые_слова"					=> "keywords",
		"Описание"							=> "description",
		"Тип_товара"						=> "type",
		"Цена"								=> "price",
		"Валюта"							=> "currency",
		"Единица_измерения"					=> "countType",
		"Минимальный_объем_заказа"			=> "minCount",
		"Оптовая_цена"						=> "optPrice",
		"Минимальный_заказ_опт"				=> "minOptPrice",
		"Ссылка_изображения"				=> "images",
		"Наличие"							=> "available",
		"Номер_группы"						=> "group",
		"Адрес_подраздела"					=> "category",
		"Возможность_поставки"				=> "delivery",
		"Срок_поставки"						=> "deliveryTime",
		"Способ_упаковки"					=> "packageType",
		"Уникальный_идентификатор"			=> "realId",
		"Идентификатор_товара"				=> "productId",
		"Идентификатор_подраздела"			=> "categoryId",
		"Идентификатор_группы"				=> "groupId",
		"Производитель"						=> "manufacturer",
		"Страна_производитель"				=> "country",
		"Скидка"							=> "discount",
		"Характеристики"					=> "info",
		"Пользовательские_Характеристики"	=> "additionalInfo",
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
	
	public function upload()
	{
		ignore_user_abort(true);
		set_time_limit(0);
		
		$ids = $this->getIds();
		$result	= array();
		
		foreach ($_FILES as $name => $info)
		{
			$path = $_FILES[$name]['tmp_name'];
			$file = fopen($path, "r");
			$keys = array();
			$line = 0;
			
			if ($file !== false)
			{
				while (($data = fgetcsv($file)) !== FALSE)
				{
					if ($line == 0)
					{
						foreach ($data as $col)
						{
							$keys[] = $this::$fields[$col];
						}
					}
					
					else if (count($data) == count($keys))
					{
						$row = array();
						foreach ($data as $key => $col)
						{
							$row[ $keys[$key] ] = $col;
						}
						
						if (isset($row['realId']) && ! isset($ids[$row['realId']]))
						{
							$result[] = $this->save($row);
						}
					}
					
					$line++;
				}
				
				fclose($file);
			}
		}

		return $result;
	}
	
	public function acceptAll($limit = 1000)
	{
		$index = 0;
		$result = array();
		$limit = min(max($limit, 0), 5000);
		
		$content = $this->getParsingStatus();
		
		if ($content)
		{
			return false;
		}
		
		while (++$index <= $limit && ($record = $this->getNextAcceptable()))
		{
			$this->setParsingStatus();
			$result[] = $this->accept(get($record, 'id'));
			usleep(100000);//0.1s
			if ( ! file_exists('public/uploads/parsing.txt')) break;
		}
		
		$this->abort();
		
		return $result;
	}
	
	public function abort()
	{
		$file = 'public/uploads/parsing.txt';
		if (file_exists($file)) unlink($file);
	}
	
	public function accept($id)
	{
		ignore_user_abort(true);
		set_time_limit(0);
		
		$time = nanotime();
		
		//header('Content-Type: text/html; charset=UTF-8');
		
		$model	= \ZendCMF\Controller::loadModel('product');
		$record	= $this->get($id);
		
		if ( ! $id || ! $record) return false;
		
		$price			= get($record, 'price', 0);
		$priceCurrency	= get($record, 'currency');
		
		if (strtolower($priceCurrency) == 'usd')
		{
			$currency = $this->getCurrency();
			if ($currency > 0) $price *= $currency;
		}
		
		$attr	= $this->getAttributes(get($record, 'description'));
		$save	= array(
			'url'			=> get($record, 'realId'),
			'titleRu'		=> get($record, 'title'),
			'titleEn'		=> get($record, 'title'),
			'descriptionRu'	=> strip_tags(get($record, 'description')),
			'descriptionEn'	=> strip_tags(get($record, 'description')),
			'article'		=> get($record, 'realId'),
			'price'			=> $price ? $price : 0,
			'priceBought'	=> $price ? $price : 0,
			'availability'	=> get($record, 'available') == '+' ? 3 : 0,
			'categoryId'	=> $this->getCategory(get($record, 'category')),
			'attributes'	=> $attr,
			'active'		=> 1,
			'priority'		=> 255,
		);
		
		$result = $model->save($save);
		$images = explode(',', get($record, 'images'));
		$priority = 255;
		
		if ($result)
		{
			$imageModel = \ZendCMF\Controller::loadModel('productImage');
			$imageCount = 0;
			
			foreach ($images as $img)
			{
				if (strlen(trim($img)))
				{
					$imgResult		= $imageModel->load($result, trim($img));
					$imgPriority	= get($imgResult, 'priority', 255);
					if ($imgPriority < $priority) $priority = $imgPriority;
					if ($imgResult) $imageCount++;
				}
			}

			if ($priority < 255)
			{
				$r = $model->save(array(
					'id'		=> $result,
					'priority'	=> $priority,
				));
			}
		
			$this->save(array(
				'id'				=> get($record, 'id'),
				'status'			=> '2',
				'importedImages'	=> $imageCount,
				'importedTime'		=> nanotime()*1000 - $time*1000,
				'importedId'		=> $result,
			));
		}
		
		else
		{
			$this->save(array(
				'id'		=> get($record, 'id'),
				'status'	=> '1',
			));
		}
		
		return $result;
	}

	public function getParsingStatus()
	{
		$file = 'public/uploads/parsing.txt';
		if ( ! file_exists($file)) return false;
		$content = file_get_contents($file);
		return strlen($content) && $content > (time() + 15);
	}
	
	protected function setParsingStatus()
	{
		$file = 'public/uploads/parsing.txt';
		file_put_contents($file, time());
	}

	protected function getNextAcceptable()
	{
		return get($this->find(array(
			'where' => array('status' => '0'),
			'field' => 'id',
			'limit' => 1,
		)), 0);
	}
	
	/*public function analizeDescription()
	{
		$found = $this->find(array(
			'field'	=> 'id,description',
			'limit'	=> '100',
		));
		
		//header('Content-Type: text/html; charset=UTF-8');
		
		foreach ($found as $row)
		{
			$data  = $this->getAttributes($row['description']);
			
			print_r($data);
		}
		
		return array();
	}*/

	protected function getAttributes($text)
	{
		$data	= $this->findAttributes($text);
		$sizes	= $this->getSizes($data['sizes']);
		$colors	= $this->getColors($data['colors']);
		
		return array(
			1	=> implode(',', $colors),
			2	=> implode(',', $sizes),
		);
	}
	
	protected function getCategory($name)
	{
		if ( ! isset($this->categories))
		{
			$this->categoryModel = \ZendCMF\Controller::loadModel('productCategory');
			$this->categories = assoc($this->categoryModel->find(array(
				'field' => 'id,url',
				'limit' => '1000',
			)), 'url', 'id');
		}
		
		$name = str_replace('http://prom.ua/', '', $name);
		
		if (isset($this->categories[$name]))
		{
			return $this->categories[$name];
		}
		
		else
		{
			$id = $this->categoryModel->save(array(
				'url'	=> $name,
				'title'	=> $name,
			));
			return $this->categories[$name] = $id;
		}
	}
	
	protected function getSizes($sizes)
	{
		$result = array();
		
		if ( ! isset($this->sizes))
		{
			$this->sizeModel = \ZendCMF\Controller::loadModel('productParam');
			$found = $this->sizeModel->getValues(1, 2);
			
			$this->sizes = array();
		
			foreach ($found as $row)
			{
				$id		= get($row, 'id');
				$title	= $this->strtolower_utf8(get($row, 'title'));
				$this->sizes[$title] = $id;
			}
		}
		
		foreach ($sizes as $size)
		{
			if (isset($this->sizes[$size]))
			{
				$result[] = $this->sizes[$size];
			}
			else
			{
				$valueId			= $this->sizeModel->saveValue($size, '', 1, 2);
				$result[]			= $valueId;
				$this->sizes[$size]	= $valueId;
			} 
		}
		
		return $result;
	}
	
	protected function getColors($colors)
	{
		$result = array();
		
		if ( ! isset($this->colors))
		{
			$this->colorModel = \ZendCMF\Controller::loadModel('productParam');
			$found = $this->colorModel->getValues(1, 1);
			
			$this->colors = array();
		
			foreach ($found as $row)
			{
				$id		= get($row, 'id');
				$title	= $this->strtolower_utf8(get($row, 'title'));
				$this->colors[$title] = $id;
			}
		}
		
		foreach ($colors as $color)
		{
			if (isset($this->colors[$color]))
			{
				$result[] = $this->colors[$color];
			}
			else
			{
				$valueId				= $this->colorModel->saveValue($color, '', 1, 1);
				$result[]				= $valueId;
				$this->colors[$color]	= $valueId;
			} 
		}
		
		return $result;
	}
	
	protected function getCurrency()
	{
		if ( ! isset($this->currency))
		{
			$config = \ZendCMF\Controller::loadModel('config');
			$this->currency = $config->get('currencyUsd');
		}
		
		return $this->currency;
	}

	protected function findAttributes($text)
	{
		$text = $this->strtolower_utf8($text);
		$text = strip_tags($text);
		$text = str_replace('&nbsp;', ' ', $text);
		$text = str_replace('&quot;', ' ', $text);
		$preg = preg_replace('/[\,\.\:\;\s\t\r\n]+/', ' ', $text);
		$part = explode(' ', $preg);
		
		$sizes = array(
			'42' => 's',
			'44' => 'm',
			'46' => 'l',
			'48' => 'xl',
			'с' => 's',
			'м' => 'm',
			'л' => 'l',
			's' => 's',
			'm' => 'm',
			'l' => 'l',
			'xl' => 'xl',
			'xxl' => 'xxl',
			'xxxl' => 'xxxl',
			'42-44' => 's',
			'44-46' => 'm',
			'46-48' => 'l',
			'50-52' => 'xl',
			'42-46' => 'm',
			's-xl' => 's',
		);
		
		$foundSizes = array();
		$foundColors = array();
		
		// detect sizes
		foreach ($part as $i => $item)
		{
			if (isset($sizes[$item]))
			{
				$foundSizes[] = $sizes[$item];
			}
		}
		
		// detect colors
		if (($indexA = strpos($text, 'цвет')))
		{
			$indexB = strpos($text, ' ', $indexA);
			$indexC = strpos($text, ':', $indexA);
			$indexBC = strpos($text, '-', $indexA);
			
			$indexD = strlen($text);
			
			if ($indexB > 0) $indexD = $indexB;
			if ($indexC > 0 && $indexD > $indexC) $indexD = $indexC;
			if ($indexBC > 0 && $indexD > $indexBC) $indexD = $indexBC;
			
			$colors = substr($text, $indexD + 1);
			$indexE = strpos($colors, '.');
			$indexF = strpos($colors, "\n");
			
			$indexG = strlen($colors);
			
			if ($indexE > 0) $indexG = $indexE;
			if ($indexF > 0 && $indexG > $indexF) $indexG = $indexF;
			
			$colors = substr($colors, 0, $indexG);
			
			if (($indexCC = strpos($colors, 'размер')) > 1)
			{
				$colors = substr($colors, 0, $indexCC);
			}
			
			if (($indexCC = strpos($colors, 'ткань')) > 1)
			{
				$colors = substr($colors, 0, $indexCC);
			}
			
			$colors = str_replace(':', ';', $colors);
			
			$separator = ',';
			$indexH1 = strpos($colors, ';'); 
			$indexH2 = strpos($colors, ',');
			
			if ($indexH1 > 0 && $indexH2 < 1) $separator = ';';
			
			$tempColors = explode($separator, $colors);
			$exclude = array('с','м','л','с м л','-');
			
			foreach ($tempColors as $i => $color)
			{
				//$color = @iconv("UTF-8","windows-1251", $color);
				//$color = preg_replace('/[^а-яa-z0-9\(\)\s\-]+/', ' ', $color);
				//$color = str_replace("\r\n", " ", $color);
				$color = trim($color);
				if (strlen($color) > 0 && ! in_array($color, $exclude))
				{
					$foundColors[$i] = $color;
				}
			}
			
			//print($text);
			//print_r($foundColors);
		}
		
		return array(
			'sizes'		=> array_unique($foundSizes),
			'colors'	=> array_unique($foundColors),
		);
	}
	
	public function test()
	{
		header('Content-Type: text/html; charset=UTF-8');
		
		$index = 0;
		$limit = 10;
		
		$result = $this->find(array(
			'field' => 'id,description',
			'limit' => 100,
			'index' => 200,
		));
		
		print('<pre>');
		
		foreach ($result as $i => $row)
		{
			print("\r\n" . $i . " ---------------------------------\r\n");
			
			$text = get($row, 'description');
			$resp = $this->findAttributes($text);
			
			print($text);
			print("--\r\n");
			print_r($resp);
		}

		return $result;
	}
	
	protected function getIds()
	{
		return assoc($this->find(array(
			'field'		=> 'id,realId',
			'limit'		=> '100000',
		)), 'realId', 'id' );
	}

	protected function strtolower_utf8($string){
		$convert_to = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
			"v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
			"ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
			"з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
			"ь", "э", "ю", "я"
		);
		$convert_from = array(
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
			"V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
			"Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
			"З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы",
			"Ь", "Э", "Ю", "Я"
		);

		return str_replace($convert_from, $convert_to, $string);
	}

}
		