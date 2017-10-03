<?php

namespace Application\Controller;

require_once('page.php');

class Catalog extends Page
{
	
	protected $orderOptions = array(
		'new'		=> array('added', 0),
		'rating'	=> array('priority', 1, 'added', 0),
		'priceAsc'	=> array('priceBought', 1),
		'priceDesc'	=> array('priceBought', 0),
	);
	
	/*public function actionUpdateImages($action, $method, $index = 0, $limit = 1000)
	{
		$product	= $this->loadModel('product');
		$image		= $this->loadModel('productImage');
		$found		= assoc($product->find(array('field' => 'id,title', 'index' => $index, 'limit' => $limit)), 'id', 'title');
		$result		= array();
		
		foreach ($found as $product => $article)
		{
			$result[] = $image->updateImages($product, $article);
		}
		
		return $result;
	}*/
	public $h1;
    public $url_filter;
	public function actionUpdateProducts($action, $method, $index = 0, $limit = 1000)
	{
		$product	= $this->loadModel('product');
		$result		= $product->updateAll($index, $limit);
		
		return $this->returnJson($result);
	}
	
	public function defaultAction($action)
	{
		$this->isNew		= $action == 'new';
		$this->isArchive	= $action == 'archive';
		$this->search		= $action == 'search' ? trim(get($_GET, 'q', '')) : null;
		$this->categoryLink	= implode(array_slice(func_get_args(), 1), '/');
		$this->categoryUrl	= $this->getFilteredLink($this->categoryLink);
        if(strpos($this->categoryUrl,'filter') !== false){
            $this->categoryUrl = substr($this->categoryUrl,0,strpos($this->categoryUrl,'/filter'));
        }
		$this->categoryInfo	= $this->getCategory(urldecode($this->categoryUrl), 'url');
		$this->categoryId	= get($this->categoryInfo, 'id');
		$this->cnnc			= $this->categoryUrl;
        $this->categoryFilter = get($this->categoryInfo, 'filters');
        $this->categoryFilterParams = get($this->categoryInfo, 'filter_params');
        
        $url_str = implode('/',func_get_args());
        if(strpos($url_str,'/filter') != false){
            $new_filter = explode('/filter/',$url_str);
            $this->url_filter = $new_filter[1];
            //echo $new_filter[1];
        }
		
		if ($this->categoryUrl && ! $this->categoryInfo)
		{
			$this->args = array('404');
			return $this->returnHtml($this->loadView('project/index'));
		}
		
		$this->options		= $this->getOptions();
		$this->filters		= $this->getFilters($this->options);
		$this->products		= $this->getProducts($this->filters);
		$this->parents		= $this->getParentCategories($this->categoryId);
		$this->paginator	= $this->getPaginator($this->options);
		
		if ($this->options && $this->paginator && isset($this->paginator->cur))
		{
			
			$min = ($this->paginator->cur-1) * $this->paginator->limit;
			$max = ($this->paginator->cur+1) * $this->paginator->limit;
			$end = ($this->paginator->max-1) * $this->paginator->limit;
		
			if ($this->paginator->cur > 0)						$this->prev = $this->getPaginLink($min);
			if ($this->paginator->cur < $this->paginator->max)	$this->next = $this->getPaginLink($max);
		}
		
		if ($this->categoryInfo)
		{
			if ( ! isset($this->product))
			{
				$this->product = $this->loadModel('product');
			}
			
			$mTitle = get($this->categoryInfo, 'titleRu');
			$mPrice = $this->product->getChipestProduct(array('categoryId' => $this->categoryId));
			
			$fOptions	= $this->getFilteringOptions();
            $filtered = $this->parse_filter($this->url_filter,$fOptions);
            //$filtered	= get($_GET, 'f', array());
            //print_r($_GET['f']);
            
			if (count($filtered))
			{
				$mFilters = array();
				$this->metaFilters = array();
				
				foreach ($fOptions as $optionId => $key)
				{
					$mKeyFilters = array();
					foreach (get($key, 'options', array()) as $values)
					{
						$filterId = get($values, 'id');
						$filterKeyId = get($values, 'keyId');
						if (isset($filtered[$filterKeyId]) && in_array($filterId, $filtered[$filterKeyId]))
						{
							 $mKeyFilters[] = get($values, 'title');
						}
					}
					
					if (count($mKeyFilters))
					{
						$mFilters[] = get($key, 'title') . ' ' . implode($mKeyFilters, ' ').', ';
						$this->metaFilters[] = get($key, 'title') . ' ' . implode($mKeyFilters, ', ');
					}
				}
				
				$mFilters			= implode($mFilters, ' ');
                if (isset($this->categoryInfo['parentId']) && ($mParent = $this->getCategory($this->categoryInfo['parentId']))){
                    $sex ='';
    			 if($this->categoryInfo['parentId'] == 99) $sex = 'Женские ';
    				$mDescription		= 'Купить ' . $sex. $mTitle . ' ' . $mFilters . ' оптом от производителя по цене от ' . $mPrice . ' гривен в интернет-магазине project. Доставка по Киеву и Украине.';
    				$mKeywords			= 'купить ' .  $sex.$mTitle . ' ' . $mFilters . ', розница, опт';
                    $this->metaTitle 	= $sex.$mTitle . ' ' . $mFilters . ' от производителя интернет-магазина project';
                    $this->h1           = $sex.$mTitle. ' ' .$mFilters ;
				}else{
				    $mDescription		= 'Купить ' . $mTitle . ' ' . $mFilters . ' оптом от производителя по цене от ' . $mPrice . ' гривен в интернет-магазине project. Доставка по Киеву и Украине.';
    				$mKeywords			= 'купить ' . $mTitle . ' ' . $mFilters . ', розница, опт';
				    $this->metaTitle 	= $mTitle . ' ' . $mFilters . ' от производителя интернет-магазина project';
                    $this->h1           = $sex.$mTitle. ' ' .$mFilters;
				}
			}
			
			else if (isset($this->categoryInfo['parentId']) && ($mParent = $this->getCategory($this->categoryInfo['parentId'])))
			{
			 $sex ='';
			 if($this->categoryInfo['parentId'] == 99) $sex = 'Женские ';
				$mDescription		= 'Купить ' . $sex.$mTitle . ' оптом от производителя по цене от ' . $mPrice . ' гривен в интернет-магазине project. Доставка по Киеву и Украине.';
				$mKeywords			= 'купить ' . $sex.$mTitle . ', розница, опт';
				$this->h1           =  $sex.$mTitle;
				$this->metaTitle	=  $sex.$mTitle.' оптом и в розницу от производителя - интернет-магазин project';
			}
			
			else
			{
				$mDescription		= 'Супер цена на ' . $this->word_cases($mTitle,'3') . ' оптом от производителя в интернет-магазине project. Доставка по Киеву и Украине. Модные новинки и бестселлеры. +38 (044) 338-97-80.';
				$mKeywords			= 'интернет-магазин, розница, опт, '.$mTitle;
				$this->h1           = 'project - '.$mTitle.' оптом и в розницу от производителя';
				$this->metaTitle	= 'Интернет-магазин '.$this->word_cases($mTitle,'2').' оптом и в розницу от производителя в Киеве и Украине - интернет-магазин project';
			}
			
			$this->metaKeywords		= get($this->categoryInfo, 'metaKeywordsRu', $mKeywords);
			$this->metaDescription	= get($this->categoryInfo, 'metaDescriptionRu', $mDescription);
            
		}
		
		/*print('<br>');
		print(urldecode($this->categoryUrl));
		print('<br>');
		print($this->categoryId);
		print('<br>');
		print_r($this->categoryInfo);*/
		
		return Page::defaultAction('catalog');
	}
	
	protected function getOptions()
	{
		if (isset($this->options))
		{
			return $this->options;
		}
		
		$page = preg_match('/page([0-9]+)\/?/', $this->categoryLink, $pageMatches) ? get($pageMatches, 1) : 1;
		
		$default = $this->isNew ? 'new' : 'rating';
		$limit	= (int) get($_GET, 'limit', 48);
		$index	= (int) get($_GET, 'index', ($page - 1) * $limit);
		$order	= get($_GET, 'order', $default);
		
		if ( $index % 24 > 0)						$index = 0;
		if ( ! in_array($limit, array(24,48,96)))	$limit = 48;
		if ( ! isset($this->orderOptions[$order]))	$order = 'new';
			
		return $this->options = (object) array(
			'index'	=> $index,
			'limit'	=> $limit,
			'order'	=> $order,
		);
	}
	
	protected function getFilters($options)
	{
		if (isset($this->filters))
		{
			return $this->filters;
		}
		
		$this->filters = array(
			'where' => array('active' => 1, 'availability !=' => 0),
			'limit'	=> $options->limit,
			'index'	=> $options->index,
			'order'	=> $this->orderOptions[$options->order][0],
			'drect'	=> $this->orderOptions[$options->order][1],
		);
		
		if (isset($this->orderOptions[$options->order][2]))
		{
			$this->filters['order2'] = $this->orderOptions[$options->order][2];
		}
		
		if (isset($this->orderOptions[$options->order][3]))
		{
			$this->filters['drect2'] = $this->orderOptions[$options->order][3];
		}
		
		if ($this->search)
		{
			if ( ! isset($this->product))
			{
				$this->product = $this->loadModel('product');
			}
			
			unset($this->filters['where']['availability !=']);
			
			$this->searchResult = assoc($this->product->search($this->search), 'id', 'relevant');
			$this->searchIds = array_keys($this->searchResult);
			//print_r($this->searchIds);
			
			$this->filters['where']['id in'] = $this->searchIds;
			
			if ( ! isset($_GET['order']))
			{
				$options->order = 'relevant';
				$this->filters['order'] = array_merge(array('id'), $this->searchIds);
			}
		}
		
		if ($this->isNew)
		{
			$this->filters['where']['added >'] = time() - (86400 * 10);
		}
		
		if ($this->isArchive)
		{
			unset($this->filters['where']['availability !=']);
			$this->filters['where']['availability'] = 0;
		}
		
		if (isset($_GET['priceFrom']))
		{
			$this->filters['where']['price >='] = $_GET['priceFrom'];
		}
		
		if (isset($_GET['priceTo']))
		{
			$this->filters['where']['price <='] = $_GET['priceTo'];
		}
		$fOptions	= $this->getFilteringOptions();
        $filtered = $this->parse_filter($this->url_filter,$fOptions);
		if ($filtered !== false)
		{
			$where		= array();
			
			foreach ($filtered as $keyId => $value)
			{
				$where[] = "paramKeyId='" . addslashes($keyId) . "' AND paramValueId IN (" . implode(',', quote(array_values($value))) . ")";
			}
			
			//$this->filters['where']['id protectedin'] = 'SELECT productId FROM product_data WHERE (' . implode(') OR (', $where) . ') GROUP BY productId';
			$this->foundIds = $this->query('SELECT productId as \'id\' FROM product_data WHERE (' . implode(') OR (', $where) . ') GROUP BY productId');
			
			foreach ($this->foundIds as $i => $key)
			{
				$this->foundIds[$i] = $key['id'];
			}
			
			$this->filters['where']['id in'] = $this->foundIds;
			
			//print_r($this->foundIds);
			
			//print_r($_GET['f']);
			//print($this->filters['where']['id protectedin']);
		}
		
		if ($this->categoryId)
		{
			$ids = $this->getChildCategories($this->categoryId);
			$this->filters['where']['categoryId in'] = $ids;
		}
		
		return $this->filters;
	}
	
	protected function getPaginator($options)
	{
		$object = new \stdClass();
		$object->found	= get($this->productsAll, 'found');
		$object->total	= get($this->productsAll, 'total');
		$object->order	= get($this->productsAll, 'order');
		$object->limit	= $options->limit;
		$object->index	= $options->index;
		$object->min	= 0;
		$object->max	= ceil($object->found / $object->limit);
		$object->cur	= ceil($object->index / $object->limit);
		
		if ($object->cur > 7) $object->min = $object->cur - 7;
		if ($object->cur < $object->max - 7) $object->max = $object->cur + 7;
		
		return $object;
	}
	
	protected function getProducts($filters)
	{
		if ( ! isset($this->product))
		{
			$this->product = $this->loadModel('product');
		}
		
		$this->productsAll	= $this->product->findAll($filters);
		
		return get($this->productsAll, 'records');
	}
	
	protected function getImages($id)
	{
		if ( ! isset($this->product))
		{
			$this->product = $this->loadModel('product');
		}
		
		return $this->product->getImages($id);
	}
	
	protected function getAttributes()
	{
		$model = $this->loadModel('productParam');
		return assoc($model->getValues(), 'id', 'title');
	}
	
	protected function getAttrExtra()
	{
		$model = $this->loadModel('productParam');
		return assoc($model->getValues(), 'id', 'extra');
	}
	
	protected function getAttributesKeys()
	{
		$model = $this->loadModel('productParam');
		return assoc($model->getKeys(), 'id', 'title');
		
	}
	
	protected function getCategory($value, $key = 'id')
	{
		if ( ! isset($this->categories))
		{
			$module = $this->loadModel('productCategory');
			$this->categories = $module->getGrouped();
		}
		
		foreach ($this->categories as $group)
		{
			foreach ($group as $category)
			{
				if (get($category, $key) == $value) return $category;
			}
		}
		
		return null;
	}
	
	protected function getCategoryGroup($id)
	{
		if ( ! isset($this->categories))
		{
			$module = $this->loadModel('productCategory');
			$this->categories = $module->getGrouped();
		}
		
		return get($this->categories, $id);
	}
	
	protected function getParentCategories($id)
	{
		$result = array();
		
		while ($id && ($parent = $this->getCategory($id)))
		{
			array_unshift($result, $parent);
			$id = get($parent, 'parentId');
		}
		
		return $result;
	}
	
	protected function getChildCategories($parent)
	{
		$result	= array($parent);
		$group	= $this->getCategoryGroup($parent);
		
		if (is_array($group) && count($group))
		{
			foreach ($group as $id => $category)
			{
				$childs = $this->getChildCategories($id);
				$result = array_merge($result, $childs);
			}
		}
		
		return $result;
	}
	
	protected function getFilteringOptions()
	{
		$result	= array();
		$model	= $this->loadModel('productParam');
		$groups	= $model->getGroups();
        if(empty($this->categoryFilter)){
            $keys	= $model->getKeys();
        }else{
            $filterCat = explode(',',$this->categoryFilter);
            $keys	= $model->getKeys('',$filterCat);
        }
		
		$values	= group($model->getValues(), 'keyId');
        if(empty($this->categoryFilterParams)){
            foreach ($keys as $key)
    		{
    			$key['options'] = get($values, $key['id'], array());
    			$result[] = $key;
    		}
        }else{
            $key['options'] = array();
            $filterParams = explode(',',$this->categoryFilterParams);
            foreach ($keys as $key)
    		{
                $options = get($values, $key['id'], array());
                foreach($options as $option){
                    if(in_array($option['id'],$filterParams )){
                        $key['options'][] = $option; 
                    } 
                }
    			$result[] = $key;
    		}
            
        }
		return $result;
	}
	
	protected function getPaginLink($index)
	{
		$link = '/';
		$data = count($_GET) ? $_GET : array();
		
		if ($this->categoryUrl) $link .= $this->categoryUrl . '/';
		else $link .= 'catalog/';
		
		if (isset($data['index'])) $data['index'] = $index;
		else $data['index'] = $index;
		
		if ($data['index'] > 0) $link .= 'page' . ($data['index'] / get($data, 'limit', $this->paginator->limit) + 1) . '/';
		
		unset($data['index']);
		
		$get = $this->stringufyUrlArguments($data);
		
		return $link . (strlen($get) ? '?' . $get : '');
	}
	
	protected function stringufyUrlArguments($data, $sep = '&', $ass = '=', $prn = null)
	{
		$dump = array();
		foreach ($data as $k => $v)
		{
			$str = $prn ? $prn . '[' . $k . ']' : $k;
			$dump[] = is_array($v) ?
				$this->stringufyUrlArguments($v, $sep, $ass, $str):
				$str . $ass . urlencode($v);
		}
		return implode($sep, $dump);
	}
    
    protected function word_cases($str, $case)
    {
        $words = explode(' ',$str);
        foreach($words as $word){
            switch($word){
                case 'ОДЕЖДА':
                    switch ($case){
                        case '1':
                            break;
                        case '2':
                            $word = substr($word,0,strlen($word)-2);
                            $word .='Ы';
                            break;
                        case '3':
                            $word = substr($word,0,strlen($word)-2);
                            $word .='У';
                            break;
                    }
                    break;
                case 'ЖЕНСКАЯ':
                case 'ДЕТСКАЯ':
                case 'МУЖСКАЯ':
                    switch ($case){
                        case '1':
                            break;
                        case '2':
                            $word = substr($word,0,strlen($word)-4);
                            $word .='ОЙ';
                            break;
                        case '3':
                            $word = substr($word,0,strlen($word)-4);
                            $word .='УЮ';
                            break;
                        }
                        break;
                case 'ОБУВЬ':
                    switch ($case){
                        case '1':
                            break;
                        case '2':
                            $word = substr($word,0,strlen($word)-2);
                            $word .='И';
                            break;
                        case '3':
                            break;
                    }
                    break;
                case 'ВЕРХНЯЯ':
                    switch ($case){
                        case '1':
                            break;
                        case '2':
                            $word = substr($word,0,strlen($word)-4);
                            $word .='ЕЙ';
                            break;
                        case '3':
                            $word = substr($word,0,strlen($word)-4);
                            $word .='ЮЮ';
                            break;
                    }
                    break;
            }
            $word_arr[] = $word;
        }
        $result = implode(' ',$word_arr);
        return $result;
    }
    
    /**
     * @параметр-строка $text - текст для транслитерации.
     * @параметр-строка $rule - правила транслитерации (google или iso-9, или gost).
     * @параметр-строка $word_spliter - спецсимвол для разделения слов.
     * @возвращаемая строка - транслитерированный текст.
     */
    public function _tr($text, $rule = 'google', $word_spliter = '_') {
        $text = mb_strtolower($text, 'UTF-8');
        /* FIX looping */
        if (preg_match('#^([a-z0-9]+)$#i', $word_spliter) > 0) {
            $word_spliter = '-';
        }
        /* правила транслитерации */
        $tr_array = array();
        switch ($rule) {

            case 'iso-9':
                $tr_array = array(
                    "а" => "a", "ци" => "ci", "цe" => "ce",
                    "б" => "b", "в" => "v", "г" => "g", "д" => "d",
                    "е" => "e", "ё" => "yo", "ж" => "zh", "з" => "z",
                    "и" => "i", "й" => "j", "к" => "k", "л" => "l",
                    "м" => "m", "н" => "n", "о" => "o", "п" => "p",
                    "р" => "r", "с" => "s", "т" => "t", "у" => "u",
                    "ф" => "f", "х" => "kh", "ц" => "cz", "ч" => "ch",
                    "ш" => "sh", "щ" => "shh", "ь" => "", "ы" => "y",
                    "ъ" => "", "э" => "e", "ю" => "yu", "я" => "ya",
                    "йо" => "yo", "ї" => "yi", "і" => "i", "є" => "ye",
                    "ґ" => "g"
                );
                break;
            case 'gost':
                $tr_array = array(
                    "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
                    "е" => "e", "ё" => "jo", "ж" => "zh",
                    "з" => "z", "и" => "i", "й" => "jj", "к" => "k", "л" => "l",
                    "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
                    "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh",
                    "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
                    "ы" => "y", "ь" => "", "э" => "eh", "ю" => "yu", "я" => "ya"
                );
                break;

            default:
            case 'google':
                $tr_array = array(
                    "а" => "a", "ый" => "iy", "ые" => "ie",
                    "б" => "b", "в" => "v", "г" => "g", "д" => "d",
                    "е" => "e", "ё" => "yo", "ж" => "zh", "з" => "z",
                    "и" => "i", "й" => "y", "к" => "k", "л" => "l",
                    "м" => "m", "н" => "n", "о" => "o", "п" => "p",
                    "р" => "r", "с" => "s", "т" => "t", "у" => "u",
                    "ф" => "f", "х" => "kh", "ц" => "ts", "ч" => "ch",
                    "ш" => "sh", "щ" => "shch", "ь" => "", "ы" => "y",
                    "ъ" => "", "э" => "e", "ю" => "yu", "я" => "ya",
                    "йо" => "yo", "ї" => "yi", "і" => "i", "є" => "ye",
                    "ґ" => "g"
                );
                break;
        }
        /* замена кириллических символов */
        $out = str_ireplace(array_keys($tr_array), array_values($tr_array), $text);

        /* нормализация теккста для url - в нижний регистр */
        $out = mb_strtolower($out, 'UTF-8');

        $out = preg_replace('#([^a-z0-9]+)#i', $word_spliter, $out);
        $double_spliter = $word_spliter . $word_spliter;
        $count = 1;
        while ($count > 0) {
            $out = str_replace($double_spliter, $word_spliter, $out, $count);
        }

        $out = trim($out, $word_spliter);
        return $out;
    }
    
    public function parse_filter($filter,$foptions){
        if(empty($filter)) return false;
        $fil_cat = explode('/',$filter);
        foreach($fil_cat as $cat){
            $f = explode('-',$cat);
            foreach($foptions as $option){
                if($option['title_en'] == $f[0]){
                    foreach($f as $val){
                        if($f[0] == $val) continue;
                        foreach($option['options'] as $keys){
                            if($keys['title_en'] == $val){
                                $result[$option['id']][] = $keys['id'];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
}