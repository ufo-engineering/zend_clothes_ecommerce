<?php

namespace Application\Model;

use Controller;

class Product extends \ZendCMF\Module
{
	protected static $table = 'product';
	
	protected static $logging = true;
	
	protected static $schema = array(
		'categoryId'		=> 'id',
		'typeId'			=> 'id',
		'url'				=> 'required unique url max:200',
		'titleRu'			=> 'required string max:200',
		'titleEn'			=> 'required string max:200',
		'descriptionRu'		=> 'string',
		'descriptionEn'		=> 'string',
		'metaKeywordsRu'	=> 'string max:250',
		'metaKeywordsEn'	=> 'string max:250',
		'metaDescriptionRu'	=> 'string max:250',
		'metaDescriptionEn'	=> 'string max:250',
		'price'				=> 'required price',
		'priceBought'		=> 'required price',
		'priority'			=> 'int'
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
	
	public function search($search)
	{
		// Разобрать искомую строку $search на отдельные слова
		//preg_match_all('/[[:alnum:]]{3,}/is', stripslashes($search), $matches);
		preg_match_all('/[а-яА-ЯA-Za-z0-9]{3,}/is', stripslashes($search), $matches);
		$words = array_unique($matches[0]);

		$true_words = Array();
		if (count($words))
		{
			foreach ($words as $word)
			{
				// Обрабатывать только слова длиннее 3 символов
				if (strlen($word) > 3)
				{
					// От слов длиннее 7 символов отрезать 2 последних буквы
					if (strlen($word) > 7)
					{
						$word = substr($word, 0, (strlen($word) - 2));
					}
					// От слов длиннее 5 символов отрезать последнюю букву
					elseif (strlen($word) > 5)
					{
						$word = substr($word, 0, (strlen($word) - 1));
					}
					$true_words[] = addcslashes(addslashes($word), '%_');
				}
			}
		}
		// Список уникальных поисковых слов
		$true_words = array_unique($true_words);
		
		//print_r($true_words);

		if ( ! count($true_words)) return array();

		// Вес отдельных слов в заголовке и тексте
		$coeff_title = round((20 / count($true_words)), 2);
		$coeff_text = round((10 / count($true_words)), 2);
	
		// Формируем запрос к базе
		$query = "SELECT id, ";
		// Условия для полного совпадения фразы в заголовке и тексте
		$query .= "( IF (`title` LIKE '%" . addslashes($search) . "%', 60, 0)";
		$query .= "+ IF (`search` LIKE '%" . addslashes($search) . "%', 10, 0)";
	
		// Условия для каждого из слов
		foreach ($true_words as $word)
		{
			$query .= "+ IF (`title` LIKE '%" . addslashes($word) . "%', " . addslashes($coeff_title) . ", 0)";
			$query .= "+ IF (`search` LIKE '%" . addslashes($word) . "%', " . addslashes($coeff_text) . ", 0)";
		}
		
		$query .= ") AS `relevant` FROM `" . addslashes($this::$table) . "`";
	
		// Условие выборки - вхождение фразы или отдельных слов в заголовок или текст
		$query .= " WHERE (";
		$query .= " `title` LIKE '%" . addslashes($search) . "%' OR `search` LIKE '%" . addslashes($search) . "%'";
		
		// Условия для каждого из слов
		foreach ($true_words as $word)
		{
			$query .= " OR `title` LIKE '%" . addslashes($word) . "%'";
			$query .= " OR `search` LIKE '%" . addslashes($word) . "%'";
		}
		
		$query .= ") ORDER BY `relevant` DESC";

		return $this->query($query);
	}

	public function recalculate($filters, $form)
	{
		$set		= array();
		$price		= get($form, 'price');
		$bought		= get($form, 'priceBought');
		$discount	= get($form, 'discount');
		$round		= get($form, 'round');
		$where		= get($filters, 'where');
		
		if ($price)
		{
			$perc	= substr($price, -1) == '%';
			$diff	= substr($price, 0, 1) == '-';
			$price	= abs($this->fixPrice($price));
			
			if ($price)
			{			
				if ($perc)	$set['price *='] = $diff ? 1 - $price / 100 : 1 + $price / 100;
				else		$set['price ' . ($diff ? '-=' : '+=')] = $price;
			}
		}
		
		if ($bought)
		{
			$perc	= substr($bought, -1) == '%';
			$diff	= substr($bought, 0, 1) == '-';
			$bought	= abs($this->fixPrice($bought));
			
			if ($bought)
			{			
				if ($perc)	$set['priceBought *='] = $diff ? 1 - $bought / 100 : 1 + $bought / 100;
				else		$set['priceBought ' . ($diff ? '-=' : '+=')] = $bought;
			}
		}
		
		if (isset($form['discount']))
		{
			$set['discount'] = str_replace('-', '', $discount);
		}
		
		if ( ! count($set))
		{
			if ($round > 0 && $round < 4)
			{
				$operations	= array(1 => 'FLOOR', 'CEILING', 'ROUND');
				$operation	= get($operations, $round, 'ROUND');
				$rounded = $this->query('UPDATE ' . $this::$table . ' SET price = ' . $operation . '(price),  priceBought = ' . $operation . '(priceBought) WHERE ' . $this->getWhereStatement($where), ZCMF_DB_NUM);
				//print($operation);
				//print($rounded);
			}
			
			return false;
		}
		
		$result = $this->updateQuery(array(
			'table'	=> $this::$table,
			'where' => $where,
			'set'	=> $set,
		));
		
		if ($round > 0 && $round < 4)
		{
			$operations	= array(1 => 'FLOOR', 'CEILING', 'ROUND');
			$operation	= get($operations, $round, 'ROUND');
			$rounded = $this->query('UPDATE ' . $this::$table . ' SET price = ' . $operation . '(price),  priceBought = ' . $operation . '(priceBought) WHERE ' . $this->getWhereStatement($where), ZCMF_DB_NUM);
			//print($operation);
			//print($rounded);
		}
		
		return $result;
	}
	
	public function findAll($filters)
	{
		$result		= \ZendCMF\Module::findAll($filters);
		$records	= get($result, 'records');
		$ids		= assoc($records, 'id', 'id');
		
		if (count($ids))
		{
			$result['images'] = $this->getAllImages(array_keys($ids));
		}
		
		return $result;
	}
	
	public function save($form)
	{
		if (isset($form['color']))
		{
			$form['attributes']['1'] = implode(',', array_unique(array_values($form['color'])));
			unset($form['color']);
		}
		
		$attributes = null;
		
		if (isset($form['attributes']))
		{
			$attributes = $form['attributes'];
			$form['attributes'] = json_encode($attributes);
		}
		
		$images = get($form, 'image');
		unset($form['image']);
		
		if ( ! isset($form['url']) && ! get($form,'id'))
		{
			$form['url'] = $form['title'] = str_pad($this->getNextId(), 5, '0', STR_PAD_LEFT);
		}
		
		if (isset($form['discount']))
		{
			$form['discount'] = str_replace('-', '', $form['discount']);
		}
		
		$id = \ZendCMF\Module::save($form);
		
		if (is_array($images) && count($images) && $id && ! get($form,'id')) {
			//print_r($images);
			
			$img = \ZendCMF\Controller::loadModel('productImage');
			$img->move(array_keys($images), $id);
		}
		
		if ($attributes)
		{
			$model	= \ZendCMF\Controller::loadModel('productData');
			$found	= array();
			$delete	= array();
			
			if ($id > 0)
			{
				$found = assoc($model->find(array(
					'where' => array('productId' => $id),
				)), 'paramValueId');
			}
			
			if ($id)
			{
				foreach ($attributes as $keyId => $field)
				{
					if ($field != '')
					{
						foreach (explode(',', $field) as $valueId)
						{
							if (isset($found[$valueId]))
							{
								unset($found[$valueId]);
							}
							else
							{
								$model->save(array(
									'productId'		=> $id,
									'paramKeyId'	=> $keyId,
									'paramValueId'	=> $valueId,
								));
							}
						}
					}
				}
			
				if (count($delete = array_keys(assoc($found,'id'))))
				{
					$model->drop($delete, 'id in');
				}
			}
		}
		
		$this->updateRecordFields($id);
		$this->updateRecordImages($id);
		//$this->updateRecordAttributes();
		//$this->updateRecordImages($id);
		
		return $id;
	}

	public function updateAll($index = 0, $limit = 1000)
	{
		ignore_user_abort(true);
		set_time_limit(0);
		
		$ids = assoc($this->find(array(
			'field' => 'id',
			'limit' => $limit,
			'index' => $index,
		)), 'id', 'id');
		
		foreach ($ids as $id)
		{
			$this->updateRecordFields($id);
			$this->updateRecordImages($id);
		}
		
		return $ids;
	}
	
	public function getChipestProduct($filters = array())
	{
		return $this->selectQuery(array(
			'table' => $this::$table,
			'where' => $filters,
			'field' => 'price',
			'limit' => 1,
			'order' => 'price',
		), ZCMF_DB_VAR);
	}

	protected function updateRecordFields($id)
	{
		$record = $this->get($id);
		
		if ( ! $record) return false;
		
		$id = get($record, 'id');
		$title = trim(get($record, 'titleRu'));
		$split = explode(' ', $title);
		$search1 = get($record, 'descriptionRu');
		$search2 = get($record, 'fullDescriptionRu');
		$search3 = $this->getNamedAttributes(json_decode(get($record, 'attributes'), true));
		
		$name = $split[0];
		$part = get($split, 1);
		$name .= ' ' . str_pad($id, 5, '0', STR_PAD_LEFT);
		
		$article = strToUrl($name);
		$search = $search1 . ' ' . $search2 . ' ' . $search3;
		
		$search = $this->strtolower_utf8(strip_tags($search));
		$search = preg_replace('/[\s\t\n\r]+/',' ', $search);
			
		return $this->updateQuery(array(
			'table'	=> $this::$table,
			'where' => array('id' => $id),
			'set'	=> array(
				'url'		=> strToUrl($name),
				'title'		=> $name,
				'search'	=> $search,
			),
		));
	}
	
	protected function updateRecordImages($id)
	{
		$model	= \ZendCMF\Controller::loadModel('productImage');
		$record = $this->get($id);
		
		if ( ! $record) return false;
		
		$id = get($record, 'id');
		$url = get($record, 'url');
		$art = str_pad($id, 5, '0', STR_PAD_LEFT);
		 
		$model->updateImages($id, $art, $url);
		
		//
	}
	
	public function setCategory($products, $category)
	{
		//print_r($products);
		//print_r($category);
		
		//return false;
		
		if ( ! is_array($products) || ! count($products)) return false;
		
		return $this->updateQuery(array(
			'table'	=> $this::$table,
			'where' => array('id in' => $products),
			'set'	=> array('categoryId' => $category),
		));
	}

	public function toArchive($products)
	{
		if ( ! is_array($products) || ! count($products)) return false;
		
		return $this->updateQuery(array(
			'table'	=> $this::$table,
			'where' => array('id in' => $products),
			'set'	=> array('availability' => '0'),
		));
	} 
	
	public function getComments($id, $active = null)
	{
		$model = \ZendCMF\Controller::loadModel('productComment');
		$find = array(
			'where' => array(
				'productId' => $id,
			)
		);
		
		if ($active) $find['where']['active'] = 1;
		
		return $model->find($find);
	}
	
	public function getImages($id)
	{
		$id = (int) $id;
		if ( ! $id) return array();
		$model = \ZendCMF\Controller::loadModel('productImage');
		$found = $model->find(array(
			'where' => array(
				'productId' => $id,
			),
			'order' => 'priority',
		));
		
		return assoc($found, 'id');
	}
	
	public function getAllImages($ids)
	{
		$model = \ZendCMF\Controller::loadModel('productImage');
		return group($model->find(array(
			'where' => array('productId in' => $ids),
			'field' => 'id,productId,colorId,url,view',
			'limit' => '10000'
		)), 'productId');
	}
	
	public function setImageColor($id, $colorId)
	{
		$model = \ZendCMF\Controller::loadModel('productImage');
		return $model->save(array(
			'id'		=> $id,
			'colorId'	=> $colorId,
		));
	}
	
	public function dropComment($id)
	{
		$model = \ZendCMF\Controller::loadModel('productComment');
		
		return $model->drop($id);
	}
	
	public function dropImage($id)
	{
		$model = \ZendCMF\Controller::loadModel('productImage');
		$found = $model->get($id);
		
		if ($found)
		{
			$file = $found['url'];
			$view = $found['view'];
			if (is_file('public/products/full/' . $file)) unlink('public/products/full/' . $file);
			if (is_file('public/products/prev/' . $file)) unlink('public/products/prev/' . $file);
			if (is_file('public/products/crop/' . $file)) unlink('public/products/crop/' . $file);
			if (is_file('public/products/show/' . $view)) unlink('public/products/show/' . $view);
			if (is_file('public/products/mini/' . $view)) unlink('public/products/mini/' . $view);
		}
		
		return $model->drop($id);
	}
	
	private function fixPrice($price)
	{
		return round((float) str_replace(',', '.', $price), 2);
	}

	public function getNamedAttributes($attributes)
	{
		$result = array();
		
		if ( ! is_array($attributes)) return $result;
		
		if ( ! isset($this->attributesKeys))
		{
			$attributesModule		= \ZendCMF\Controller::loadModel('productParam');
			$this->attributesKeys	= assoc($attributesModule->getKeys(), 'id', 'title');
			$this->attributesValues	= assoc($attributesModule->getValues(), 'id', 'title');
		}
		
		foreach ($attributes as $key => $values)
		{
			$iFound	= array();
			$iAll	= explode(',', $values);
			$iKey	= get($this->attributesKeys, $key);
			
			foreach ($iAll as $value)
			{
				$iFound[] = $iKey . ' ' .get($this->attributesValues, $value);
			}
			
			$result[] = implode(', ', $iFound);
		}
		
		return implode('; ', $result);
	}

	public function getHtmlAttributes($attributes)
	{
		$result = array();
		
		if ( ! is_array($attributes)) return '';
		
		if ( ! isset($this->attributesKeys))
		{
			$attributesModule		= \ZendCMF\Controller::loadModel('productParam');
			$this->attributesKeys	= assoc($attributesModule->getKeys(), 'id', 'title');
			$this->attributesValues	= assoc($attributesModule->getValues(), 'id', 'title');
		}
		
		foreach ($attributes as $key => $values)
		{
			$iFound	= array();
			$iAll	= explode(',', $values);
			$iKey	= get($this->attributesKeys, $key);
			
			foreach ($iAll as $value)
			{
				$val = get($this->attributesValues, $value);
				if ($val && trim($val)) $iFound[] = $val;
			}
			
			if ($iKey && count($iFound)) $result[] = $iKey . ': ' .implode(', ', $iFound);
		}
		
		return implode("<br>", $result);
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