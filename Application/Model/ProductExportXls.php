<?php

namespace Application\Model;

class ProductExportXls extends \ZendCMF\Module
{
	protected static $table = 'product_export_xls';
	
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
	
	protected static $xls = array(
		
		array(
			'title'		=> 'Export Products Sheet',
			'fields'	=> array(
				'A' => 'Код_товара',
				'B' => 'Название_позиции',
				'C' => 'Ключевые_слова',
				'D' => 'Описание',
				'E' => 'Тип_товара',
				'F' => 'Цена',
				'G' => 'Валюта',
				'H' => 'Единица_измерения',
				'I' => 'Минимальный_объем_заказа',
				'J' => 'Оптовая_цена',
				'K' => 'Минимальный_заказ_опт',
				'L' => 'Ссылка_изображения',
				'M' => 'Наличие',
				'N' => 'Производитель',
				'O' => 'Страна_производитель',
				'P' => 'Номер_группы',
				'Q' => 'Адрес_подраздела',
				'R' => 'Возможность_поставки',
				'S' => 'Срок_поставки',
				'T' => 'Способ_упаковки',
				'U' => 'Идентификатор_товара',
				'V' => 'Уникальный_идентификатор',
				'W' => 'Идентификатор_подраздела',
				'X' => 'Идентификатор_группы',
				'Y' => 'Характеристики',
				'Z' => 'Пользовательские_Характеристики',
			),
		),
		
		array(
			'title'		=> 'Export Groups Sheet',
			'fields'	=> array(
				'A'	=> 'Номер_группы',
				'B'	=> 'Название_группы',
				'C'	=> 'Идентификатор_группы',
				'D'	=> 'Номер_родителя',
				'E'	=> 'Идентификатор_родителя',
			),
		),
		
	);
	
	
	public function generate($title = '', $output = false)
	{
		ignore_user_abort(true);
		set_time_limit(0);
		
		require_once 'Application/Classes/PHPExcel.php';	// Подключаем библиотеку PHPExcel
		
		$phpexcel = new \PHPExcel();						// Создаём объект PHPExcel
		
		$fieldsProduct = self::$xls[0]['fields'];
		$fieldsCategory = self::$xls[1]['fields'];
		
		$modelProduct = \ZendCMF\Controller::loadModel('product');
		$modelCategory = \ZendCMF\Controller::loadModel('productCategory');
		
		$recordCategory = $modelCategory->find();
		$recordProduct = $modelProduct->find(array(
			'where' => array('availability !=' => 0, 'active' => 1),
			'limit' => 10000,
			'order' => 'priority',
			'drect' => 1,
			'order2' => 'added',
			'drect2' => 0,
		));
		
		$indexProduct = 1;
		$indexCategory = 1;
		
		/* Каждый раз делаем активной 1-ю страницу и получаем её, потом записываем в неё данные */
		$pageA = $phpexcel->setActiveSheetIndex(0);			// Делаем активной первую страницу и получаем её
		$pageA->setTitle("Export Products Sheet");			// Ставим заголовок "Test" на странице
		
		foreach ($fieldsProduct as $cel => $celTitle)
		{
			$pageA->setCellValue($cel . $indexProduct, $celTitle);
		}
		
		foreach ($recordProduct as $record)
		{
			++$indexProduct;
			
			$recordNewImages = array();
			$recordImages = $modelProduct->getImages(get($record, 'id'));
			$recordAttr = json_decode(get($record, 'attributes'), true);
			
			foreach ($recordImages as $i => $img)
			{
				//$recordImages[$i] = 'http://' . get($_SERVER, 'HTTP_HOST') . '/public/products/show/'. $img;
				$url = get($img, 'view');
				//$url = 'http://project.ru/public/products/show/'. $url;
				if ($url) $recordNewImages[] = 'http://project.ru/public/products/show/'. $url;
				//$recordNewImages[] = 'http://project.ru/public/products/show/'. $img;
			}
		
			$pageA->setCellValue('A' . $indexProduct, str_pad(get($record, 'id'), 5, '0', STR_PAD_LEFT));
			$pageA->setCellValue('B' . $indexProduct, get($record,'title'));
			$pageA->setCellValue('C' . $indexProduct, get($record,'metaKeywordsRu'));
			$pageA->setCellValue('D' . $indexProduct, $modelProduct->getHtmlAttributes($recordAttr));
			$pageA->setCellValue('E' . $indexProduct, 'r');
			$pageA->setCellValue('F' . $indexProduct, get($record,'price'));
			$pageA->setCellValue('G' . $indexProduct, 'UAH');
			$pageA->setCellValue('H' . $indexProduct, 'ед.');
			$pageA->setCellValue('I' . $indexProduct, '');
			$pageA->setCellValue('J' . $indexProduct, '');
			$pageA->setCellValue('K' . $indexProduct, '');
			$pageA->setCellValue('L' . $indexProduct, implode(',', $recordNewImages));
			$pageA->setCellValue('M' . $indexProduct, '+');
			$pageA->setCellValue('N' . $indexProduct, '');
			$pageA->setCellValue('O' . $indexProduct, '');
			$pageA->setCellValue('P' . $indexProduct, get($record,'categoryId'));
			$pageA->setCellValue('Q' . $indexProduct, '');
			$pageA->setCellValue('R' . $indexProduct, '');
			$pageA->setCellValue('S' . $indexProduct, '');
			$pageA->setCellValue('T' . $indexProduct, '');
			$pageA->setCellValue('U' . $indexProduct, get($record, 'id'));
			$pageA->setCellValue('V' . $indexProduct, get($record, 'id'));
			$pageA->setCellValue('W' . $indexProduct, '');
			$pageA->setCellValue('X' . $indexProduct, get($record,'categoryId'));
			$pageA->setCellValue('Y' . $indexProduct, '');
			$pageA->setCellValue('Z' . $indexProduct, '');
		}
		
		$recordNewProduct = $modelProduct->find(array(
			'where' => array('availability !=' => 0, 'active' => 1, 'added >' => time() - (86400 * 10)),
			'limit' => 1000,
			'order' => 'added',
			'drect' => 0,
		));
		
		foreach ($recordNewProduct as $record)
		{
			++$indexProduct;
			
			$recordNewImages = array();
			$recordImages = $modelProduct->getImages(get($record, 'id'));
			$recordAttr = json_decode(get($record, 'attributes'), true);
			
			foreach ($recordImages as $i => $img)
			{
				//$recordImages[$i] = 'http://' . get($_SERVER, 'HTTP_HOST') . '/public/products/show/'. $img;
				$url = get($img, 'view');
				//$url = 'http://project.ru/public/products/show/'. $url;
				if ($url) $recordNewImages[] = 'http://project.ru/public/products/show/'. $url;
				//$recordNewImages[] = 'http://project.ru/public/products/show/'. $img;
			}
		
			$pageA->setCellValue('A' . $indexProduct, str_pad(get($record, 'id'), 5, '0', STR_PAD_LEFT));
			$pageA->setCellValue('B' . $indexProduct, get($record,'title'));
			$pageA->setCellValue('C' . $indexProduct, get($record,'metaKeywordsRu'));
			$pageA->setCellValue('D' . $indexProduct, $modelProduct->getHtmlAttributes($recordAttr));
			$pageA->setCellValue('E' . $indexProduct, 'r');
			$pageA->setCellValue('F' . $indexProduct, get($record,'price'));
			$pageA->setCellValue('G' . $indexProduct, 'UAH');
			$pageA->setCellValue('H' . $indexProduct, 'ед.');
			$pageA->setCellValue('I' . $indexProduct, '');
			$pageA->setCellValue('J' . $indexProduct, '');
			$pageA->setCellValue('K' . $indexProduct, '');
			$pageA->setCellValue('L' . $indexProduct, implode(',', $recordNewImages));
			$pageA->setCellValue('M' . $indexProduct, '+');
			$pageA->setCellValue('N' . $indexProduct, '');
			$pageA->setCellValue('O' . $indexProduct, '');
			$pageA->setCellValue('P' . $indexProduct, '5073575');
			$pageA->setCellValue('Q' . $indexProduct, '');
			$pageA->setCellValue('R' . $indexProduct, '');
			$pageA->setCellValue('S' . $indexProduct, '');
			$pageA->setCellValue('T' . $indexProduct, '');
			$pageA->setCellValue('U' . $indexProduct, '1000'.get($record, 'id'));
			$pageA->setCellValue('V' . $indexProduct, '1000'.get($record, 'id'));
			$pageA->setCellValue('W' . $indexProduct, '');
			$pageA->setCellValue('X' . $indexProduct, '');
			$pageA->setCellValue('Y' . $indexProduct, '');
			$pageA->setCellValue('Z' . $indexProduct, '');
		}

		if ( ! $output)
		{
			// Create a new worksheet called "My Data"
			$pageB = new \PHPExcel_Worksheet($phpexcel, "Export Groups Sheet");
			
			// Attach the "My Data" worksheet as the first worksheet in the PHPExcel object
			$phpexcel->addSheet($pageB, 1);
	
			//$pageB = $phpexcel->setActiveSheetIndex(1);			// Делаем активной первую страницу и получаем её
			//$pageB->setTitle("Export Groups Sheet");			// Ставим заголовок "Test" на странице
			
			foreach ($fieldsCategory as $cel => $celTitle)
			{
				$pageB->setCellValue($cel . $indexCategory, $celTitle);
			}
			
			foreach ($recordCategory as $record)
			{
				++$indexCategory;
			
				$pageB->setCellValue('A' . $indexCategory, get($record, 'id'));
				$pageB->setCellValue('B' . $indexCategory, get($record,'titleRu'));
				$pageB->setCellValue('C' . $indexCategory, get($record, 'id'));
				$pageB->setCellValue('D' . $indexCategory, get($record, 'parentId') > 0 ? get($record, 'parentId') : '');
				$pageB->setCellValue('E' . $indexCategory, get($record, 'parentId') > 0 ? get($record, 'parentId') : '');
			}
		}
		
		if ($output)
		{
			//http://project.localhost/api/productExportXls/autoupdate/
			header('Content-Disposition: attachment; filename=autoupdate.xlsx' );
			//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Type: application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			//header('Content-Length: ');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			
			$objWriter = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');//Excel2007
			$objWriter->save('php://output');
			die();
		}
		
		else
		{
			$objWriter = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
			
			$time	= time();
			$save	= 'public/uploads/' . date('Y.m.d_H.i.s') . '_' . $title . '.xlsx';
			
			$objWriter->save($save);
			
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
			);
		}
	}

	public function autoupdate()
	{
		$this->generate('autoupdate', true);
	}

}		