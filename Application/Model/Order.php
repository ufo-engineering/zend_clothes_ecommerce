<?php

namespace Application\Model;
require_once $_SERVER['DOCUMENT_ROOT'].'Application/Classes/LiqPay.php';

class Order extends \ZendCMF\Module
{
	protected static $table = 'product_order';
	
	protected static $logging = true;
		
	protected static $schema = array(
	);
	
	protected static $private = array();
	
	protected static $locked = array();
	
	protected static $access = array(
		'DENY'	=> 0,	// access denied
		'VIEW'	=> 1,	// can view any records
		'EDIT'	=> 2,	// can add or edit only own records
		'DROP'	=> 3,	// can edit any records
	);
	
	public function send($form, $basket, $currency='UAH')
	{
		$productModule	= \ZendCMF\Controller::loadModel('product');
		$account	= \ZendCMF\Controller::loadModel('account');
		$positions	= \ZendCMF\Controller::loadModel('orderProduct');
		$attributes	= $this->getAttributes();
		$delivery	= get($form, 'delivery');
		
		$accountInfo = $account->get(get($form, 'email'), 'email');
		if ($accountInfo){
			// проверить статус пользователя
			$accountId = get($accountInfo, 'id');
			$form['name']= get($accountInfo, 'name');
			$form['phone']= get($accountInfo, 'phone');
		}
		else{
			switch ($form['order_type']){
				case 'new':
					$user= array(
						'name'		=> get($form, 'name'),
						'lastname'	=> get($form, 'lastname'),
						'phone'		=> get($form, 'phone'),
						'email'		=> get($form, 'email'),
						'password'	=> substr(md5(get($form, 'email')), 0, 7),
						'active'	=> 1,
					);
				break;
				case 'fast':
					$user= array(
						'name'		=> get($form, 'name'),
						'lastname'	=> '',
						'phone'		=> get($form, 'phone'),
						'email'		=> get($form, 'email'),
						'password'	=> substr(md5(get($form, 'email')), 0, 7),
						'active'	=> 1,
					);
				break;
			}
			//сохраняем пользователя
			$accountId	= $account->save($user);
		}

		if ( ! $accountId){
			return false;
		}
		
		$orderId = $this->save(array(
			'accountId'		=> $accountId,
			'countryId'		=> get($form, 'countryId'),
			'paymentType'	=> get($form, 'paymentType'),
			'deliveryType'	=> get($form, 'deliveryType'),
			'expressType'	=> get($form, 'expressType'),
			'passport'		=> get($delivery, 'passport'),
			'country'		=> get($delivery, 'country'),
			'state'			=> get($delivery, 'state'),
			'city'			=> get($delivery, 'city'),
			'address'		=> get($delivery, 'address'),
			'store'			=> get($delivery, 'store'),
			'train'			=> get($delivery, 'train'),
			'station'		=> get($delivery, 'station'),
			'comment'		=> get($form, 'comment'),
			'status'		=> 0,
		));
		if ( ! $orderId){
			return false;
		}
		
		$index = 0;
		$positionResult = array();
		$positionsMail = array();
		$priceWithDiscount = 0;
		$items = array();
		$totalamount = 0;
        
		foreach ($basket as $product)
		{
			foreach ($product['sizeId'] as $sizeId => $amount)
			{
				// получить данные товара
					
				$productInfo = $productModule->get(get($product, 'id'));
				
				if ($amount > 0)
				{
					$positionResult[] = $positions->save(array(
						'orderId'		=> $orderId,
						'accountId'		=> $accountId,
						'productId'		=> get($product, 'id'),
						'colorId'		=> get($product, 'colorId'),
						'sizeId'		=> $sizeId,
						'price'			=> get($productInfo, 'price'),
						'priceBought'	=> get($productInfo, 'priceBought'),
						'discount'		=> get($productInfo, 'discount'),
						'amount'		=> $amount,
					));
					
					$colorId			= get($product, 'colorId');
					$productPrice		= get($productInfo, 'price', 0);
					$productDiscount	= get($productInfo, 'discount', '');
					$productDiscounted	= $productPrice;
					
					if ($productDiscount)
					{
						if (substr($productDiscount, -1) == '%')
						{
							$productDiscounted = $productPrice - ($productPrice / 100 * substr($productDiscount, 0, -1));
						}
						else
						{
							$productDiscounted = $productPrice - $productDiscount;
						}
					}
					
					$items[] = array(
						'id'		=> get($productInfo, 'id') . '/' . $colorId . '/' . $sizeId,
						'article'	=> get($productInfo, 'article') . ' ' . get($attributes, $sizeId, 'UN') . ' ' . get($attributes, $colorId),
						'title'		=> get($productInfo, 'title') . ' ' . get($attributes, $sizeId, 'UN') . ' ' . get($attributes, $colorId),
						'variant'	=> get($attributes, $sizeId, 'UN') . ' ' . get($attributes, $colorId),
						'price'		=> $productDiscounted,
						'amount'	=> $amount,
					);
					
					$positionsMail[] = (++$index) . '. '. get($productInfo, 'title').
									' (' . get($productInfo, 'article') . ')'.
									'. ' . get($attributes, $sizeId, 'UN') .
									', ' . get($attributes, get($product, 'colorId')) .
									', <b>' . $amount . ' X ' . $productDiscounted . ' = ' . ($amount * $productDiscounted) . ' грн/ '. ($amount * $productDiscounted)/20 .' $/ '. number_format(($amount * $productDiscounted)/0.385,2) .' rub.</b>';
					$totalamount += $amount;
					$priceWithDiscount += $amount * $productDiscounted;
				}
			}
		}
		
		$listCountries = $this->getCountries();
		$listPayments = $this->ggetPaymentTypes();
		$listDelivery = $this->getDeliveryTypes();
		$listExpress = $this->getExpressTypes();
		
		$order = $this->calculateOrder($orderId);
		
        if($form['order_type'] == 'fast'){
            $mail = '<big> Внимание! Этот заказ оформлен на сайте как быстрый! </big><br>
                     <big>Заказ №' . $orderId . '</big><br>';
        }else{
            $mail = '<big>Заказ №' . $orderId . '</big><br>';
		}
		$mail .= '<br><b>Заказчик</b><br>';
		$mail .= '<p>Имя, Фамилия: ' . $form['name'] . ', ' . get($form,'lastname') . '</p><br>';
		$mail .= '<p>Телефон: ' . $form['phone'] . '</p><br>';
		$mail .= '<p>E-mail: ' . $form['email'] . '</p><br>';
		
		$mail .= '<br><b>Доставка</b><br>';
		$mail .= 'Страна доставки: ' . get($listCountries, get($form, 'countryId')) . '<br>';
		$mail .= 'Тип доставки: ' . get($listDelivery, get($form, 'deliveryType')) . '<br>';
		
		if (isset($delivery['express']))	$mail .= 'Транспорт. комп.: ' . get($listExpress, get($form, 'express')) . '<br>';
		if (isset($delivery['passport']))	$mail .= 'Паспорт. данные: ' . get($delivery, 'passport') . '<br>';
		if (isset($delivery['country']))	$mail .= 'Страна: ' . get($delivery, 'country') . '<br>';
		if (isset($delivery['state']))		$mail .= 'Область: ' . get($delivery, 'state') . '<br>';
		if (isset($delivery['city']))		$mail .= 'Город: ' . get($delivery, 'city') . '<br>';
		if (isset($delivery['address']))	$mail .= 'Адрес: ' . get($delivery, 'address') . '<br>';
		if (isset($delivery['post']))		$mail .= 'Почтовый индекс: ' . get($delivery, 'post') . '<br>';
		if (isset($delivery['store']))		$mail .= 'Склад: ' . get($delivery, 'store') . '<br>';
		if (isset($delivery['train']))		$mail .= 'Номер поезда: ' . get($delivery, 'train') . '<br>';
		if (isset($delivery['station']))	$mail .= 'Станция: ' . get($delivery, 'station') . '<br>';
		
		$mail .= '<br><b>Комментарий:</b><br>' . get($form, 'comment') . '<br>';
		
		$mail .= '<br><b>Оплата</b><br>';
		$mail .= 'Тип оплаты: ' . get($listPayments, get($form, 'paymentType')) . '<br>';
		
		$mail .= '<br><b>Позиции заказа:</b><br>';
		$mail .= implode('<br>', $positionsMail);
		
		$mail .= '<br><br><b>Сумма заказа: <big>' . get($order,'price') . ' грн. '.$totalamount.' ед.</big></b><br>';
		
		if (get($order,'price') !== $priceWithDiscount)
		{
			$mail .= '<b>Сумма скидки: <big>' . (get($order,'price') - $priceWithDiscount) . ' грн.</big></b><br>';
			$mail .= '<b>Сумма со скидкой: <big>' . $priceWithDiscount . ' грн.</big></b><br>';
		}

		$this->sendOrderEmail('Заказ №' . $orderId, $mail);
		
		$liqpay= new \LiqPay(KEY, SECRET_KEY);
		return array(
			'id'		=> $orderId,
			'data'		=> \ZendCMF\Module::get($orderId),
			'form'		=> $form,
			'price'		=> get($order,'price'),
			'items'		=> $items,
			'liqpay'   => $liqpay->cnb_only_fields(array(
			  'version'        => '3',
			  'amount'         => get($order,'price'),
			  'currency'       => $currency,
			  'description'    => 'Оплата заказа #'.$orderId.' в магазине project.',
			  'order_id'       => $orderId
			 ))
		);
	}

	protected function sendOrderEmail($title, $content)
	{
		//print($content);
		//var_dump(email('project.store@gmail.com', $title, $content));
		//var_dump(email('crackjack@inbox.ru', $title, $content));
		
		email('project.store@gmail.com, misterspelik@gmail.com', $title, $content);
	}
	
	public function findAll($filters)
	{
		$result		= \ZendCMF\Module::findAll($filters);
		$records	= get($result, 'records');
		$accounts	= assoc($records, 'id', 'accountId');
		$orders		= array_keys($accounts);
		
		if (count($accounts))
		{
			$result['accounts']		= $this->getAllAccounts($accounts);
			$result['positions']	= $this->getAllPositions($orders);
			$result['attributes']	= $this->getAttributes();
			$result['products']		= $this->getAllProducts(
				assoc($result['positions'], 'id', 'productId')
			);
		}
		
		return $result;
	}
	
	public function save($form)
	{
		if (isset($form['product']))
		{
			if (isset($form['product']['amount']))
			{
				//
			}
			
			if (isset($form['product']['status']))
			{
				//
			}
			
			unset($form['product']);
		}
		
		$result = \ZendCMF\Module::save($form);
		
		if ($result)
		{
			$order = $this->get($result);
			$this->updateAccountCache(get($order, 'accountId'));
		}
		
		return $result;
	}
	
	public function get($value, $key = 'id')
	{
		$result = \ZendCMF\Module::get($value, $key);
		
		if ($result && isset($result['id']))
		{
			$result['account']		= $this->getAccount($result['accountId']);
			$result['positions']	= $this->getPositions($result['id']);
			$result['attributes']	= $this->getAttributes();
			$result['products']		= $this->getAllProducts(
				assoc($result['positions'], 'id', 'productId')
			);
		}
		
		return $result;
	}
	
	public function drop($value, $field = 'id')
	{
		$order = $this->get($value, $field);
		
		if ($order)
		{
			$model = \ZendCMF\Controller::loadModel('orderProduct');
			$model->dropOrder(get($order,'id'), get($order,'accountId'));
		}
		
		return \ZendCMF\Module::drop($value, $field);
	}
	
	public function setProductStatus($order, $id, $status)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$result = $model->save(array(
			'id'		=> $id,
			'status'	=> $status,
		));
		
		return $this->calculateOrder($order);
	}
	
	public function setProductAmount($order, $id, $amount)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$result = $model->save(array(
			'id'		=> $id,
			'amount'	=> $amount,
		));
		
		return $this->calculateOrder($order);
	}
	
	/*public function addProduct($orderId, $productId, $colorId=0, $amount=1)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$result = $model->save(array(
			'accountId'	=> get($_SESSION, 'account'),
			'orderId'	=> $orderId,
			'productId'	=> $productId,
			'colorId'	=> $colorId,
		));
		
		return $this->calculateOrder($order);
	}*/
	
	public function dropProduct($order, $id)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$result = $model->drop($id); 
		
		return $this->calculateOrder($order);
	}
	
	protected function calculateOrder($id)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$found = $model->find(array(
			'where' => array('orderId' => $id)
		));
		
		if ($found)
		{
			$price	= 0;
			$bought	= 0;
			$disco	= 0;
			
			foreach ($found as $position)
			{
				$amount	= get($position, 'amount', 0);
				$price	+= $amount * get($position, 'price', 0);
				$bought	+= $amount * get($position, 'priceBought', 0);
				$disco	+= $amount * discount(get($position, 'price', 0), get($position, 'discount'));
			}
			
			$data = array(
				'id'				=> $id,
				'price'				=> $price,
				'priceBought'		=> $bought,
				'priceDiscounted'	=> $disco,
			);
			
			$this->save($data);
			
			return $data;
		}
		
		else
		{
			return false;
		}
	}
	
	protected function getAttributes()
	{
		$model = \ZendCMF\Controller::loadModel('productParam');
		return assoc($model->getValues(), 'id', 'title');
	}
	
	public function getPositions($id)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$found = $model->find(array(
			'where' => array('orderId' => $id),
		));
		
		return $found;
	}
	
	protected function getAccount($id)
	{
		$model = \ZendCMF\Controller::loadModel('account');
		return $model->get($id);
	}
	
	protected function getAllAccounts($ids)
	{
		$model = \ZendCMF\Controller::loadModel('account');
		$found = $model->find(array(
			'field'		=> 'id,name,lastname,email',
			'where'		=> array('id in' => array_unique($ids))
		));
		
		return assoc($found, 'id');
	}
	
	protected function getAllPositions($ids)
	{
		$model = \ZendCMF\Controller::loadModel('orderProduct');
		$found = $model->find(array(
			'where'		=> array('orderId in' => array_unique($ids))
		));
		
		return assoc($found, 'id');
	}
	
	public function getAllInfo($ids)
	{
		$result = array();
		$model = \ZendCMF\Controller::loadModel('product');
		
		$attributesModule	= \ZendCMF\Controller::loadModel('productParam');
		//$attributesKeys		= assoc($attributesModule->getKeys(), 'id', 'title');
		$attributesValues	= assoc($attributesModule->getValues(), 'id', 'title');
			
		$images		= $model->getAllImages($ids);
		$products	= $model->find(array(
			'field'	=> 'id,url,title,price,discount,availability,attributes',
			'where' => array('id in' => $ids)
		));
		
		foreach ($products as $i => $product)
		{
			$pid = get($product, 'id');
			
			$attributes			= json_decode(get($product, 'attributes'), true);
			$attributesColors	= explode(',', get($attributes, '1'));
			$attributesSizes	= explode(',', get($attributes, '2'));
			
			$colors = array();
			$sizes = array();
			
			foreach ($attributesColors as $key)
			{
				if ($key != '' && isset($attributesValues[$key]))
				{
					$colors[$key] = get($attributesValues,$key);
				}
			}
			
			foreach ($attributesSizes as $key)
			{
				if ($key != '' && isset($attributesValues[$key]))
				{
					$sizes[$key] = get($attributesValues,$key);
				}
			}
			
			$product['article'] = str_pad($pid, 5, '0', STR_PAD_LEFT);
			$product['images'] = get($images, $pid);
			$product['colors'] = $colors;
			$product['sizes'] = $sizes;
			$result[$pid] = $product;
		}		
		
		return $result;
	}
	
	protected function getAllProducts($ids)
	{
		$model = \ZendCMF\Controller::loadModel('product');
		$found = $model->find(array(
			'field'		=> 'id,title,url',
			'where'		=> array('id in' => array_unique($ids))
		));
		
		return assoc($found, 'id');
	}
	
	protected function updateAccountCache($accountId)
	{
		if ($accountId)
		{
			$model = \ZendCMF\Controller::loadModel('account');
			return $model->updateCache($accountId);
		}
	}
	
	protected function getCountries()
	{
		return array(
			0	=> 'Не указано',
			1	=> 'Украина',
			2	=> 'Россия',
			3	=> 'Белоруссия',
			4	=> 'Казахстан',
			5	=> 'Англия',
			99	=> 'Другая страна'
		);
		
	}
	
	protected function ggetPaymentTypes()
	{
		return array(
			0	=> 'Не указано',
			1	=> 'MoneyGram',
			2	=> 'Contact',
			3	=> 'UniStream',
			4	=> 'Колибри',
			5	=> 'Золотая Корона',
			6	=> 'Приват24'
		);
	}
	
	protected function getDeliveryTypes()
	{
		return array(
			0	=> 'Не указано',
			1	=> 'Новая Почта',
			2	=> 'Укр Почта',
			3	=> 'EMC',
			4	=> 'DIMEX',
			5	=> 'Helios Express',
			6	=> 'Доставка поездом',
			7	=> 'Экспресс доставка',
			8	=> 'Курьер Сервис Экспресс'
		);
	}
	
	protected function getExpressTypes()
	{
		return array(
			0	=> 'Не указано',
			1	=> 'ПЭК',
			2	=> 'ЖелДорЭкспедиция',
			3	=> 'Деловые Линии',
			4	=> 'Байкал-Сервис',
			5	=> 'КурьерСервисЭкспресс',
			6	=> 'Почта России',
		);
	}
	
	public function getOrderStatuses()
	{
		return array(
			0 => 'Ожидает подтверждения',
			1 => 'Обрабатывается',
			2 => 'Комплектуется',
			3 => 'Доставляется',
			4 => 'Доставлен',
			5 => 'Отменен',
			6 => 'Отклонен'
		);
	}
    
    public function log_error($err_msg, $basket){
        foreach($basket as $product){
            $prod_ids[] = $product['id'];
        }
        if(!$err_msg) $err_msg = '';
        $filename = $_SERVER['DOCUMENT_ROOT'].'Application/order_errors';
        $fp = fopen($filename, 'a');
        $msg = date("Y-m-d H:i:s")."Error: ".$err_msg.", ".$_SERVER["HTTP_USER_AGENT"].", ".$_SERVER["HTTP_HOST"].", product_id: ".implode(', ',$prod_ids);
        fwrite($fp, $msg."\r\n");
        fclose($fp);
        return 'ok';
    }

}
