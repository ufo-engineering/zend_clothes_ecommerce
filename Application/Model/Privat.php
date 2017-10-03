<?php
namespace Application\Model;
require_once $_SERVER['DOCUMENT_ROOT'].'Application/Classes/LiqPay.php';

class Privat extends \ZendCMF\Module{
	public $liqpay;
	private $public_key= 'i31094649814';
	private $private_key= 'LLPPsY47YoQCZ97ViIrT7dJ7H7RmdWmsXbN9JMQQ';
	
	public function __construct(){
		$this->liqpay= new \LiqPay($this->public_key, $this->private_key);
	}
	
	public function form($order_id=1, $amount=22, $currency='UAH'){
		$html = $this->liqpay->cnb_form(array(
		  'version'        => '3',
		  'amount'         => $amount,
		  'currency'       => $currency,
		  'description'    => 'Оплата заказа #'.$order_id.' в магазине project.',
		  'order_id'       => $order_id
		 ));
		
		return array(
			'form' => $html
		);
	}
	
	public function fields($order_id=1, $amount=22, $currency='UAH'){
		return $this->liqpay->cnb_only_fields(array(
		  'version'        => '3',
		  'amount'         => $amount,
		  'currency'       => $currency,
		  'description'    => 'Оплата заказа #'.$order_id.' в магазине project.',
		  'order_id'       => $order_id
		 ));
	}
}
