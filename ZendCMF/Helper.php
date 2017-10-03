<?php

function get($array, $key, $value = null, $slashes = false)
{
	$result = isset($array[$key]) ? $array[$key] : $value;
	return $slashes ? addslashes($result) : $result;
}

function assoc($target, $key = false, $value = false)
{
	$result = array();
	
	foreach ($target as $i => $item)
	{
		$result[$key === false ? $i : get($item, $key)] = $value === false ? $item : get($item, $value);
	}
	
	return $result;
}

function group($array, $key)
{
	$result = array();
	
	if ( ! is_array($array)) return $result;
	
	foreach ($array as $x => $item)
	{
		$k = get($item, $key);
		
		if ( ! isset($result[$k]))
		{
			$result[$k] = array();
		}
		
		$result[$k][$x] = $item;
	}
	
	return $result;
}

function quote($target, $quote = "'")
{
	if (is_array($target))
	{
		foreach ($target as $key => $value)
		{
			$target[$key] = quote($value, $quote);
		}
		
		return $target;
	}
	else
	{
		return $quote . addslashes($target) . $quote;
	}
}

function resolveName($string)
{
	if ( ! is_string($string) || ! strlen($string)) return '';
	return strtoupper($string[0]) . substr($string, 1);
}

function nanotime()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float)$usec + (float)$sec;
}

function price($price)
{
	$price = (float) $price;
	if ( ! is_float($price)) $price = 0;
	return round($price);
}

function discount($price, $discount)
{
	if (($discount = trim($discount)) > 0)
	{
		
		if (substr($discount, -1) == '%')
		{
			return price($price - ($price / 100 * substr($discount, 0, -1)));
		}
		else
		{
			return price($price - $discount);
		}
	}
	
	return price($price);
}

function email($mail, $subject, $content)
{
	$headers  = "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: project <info@project.ru>\r\n";
	//$headers .= "Bcc: birthday-archive@example.com\r\n";
	
	$html = '<html><head>' . $subject . '</head><body>' . $content . '</body></html>';
	
	return mail($mail, $subject, $content, $headers); 
}

function translit($string) {
	$converter = array(
		'а' => 'a',   'б' => 'b',   'в' => 'v',
		'г' => 'g',   'д' => 'd',   'е' => 'e',
		'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',
		'и' => 'i',   'й' => 'y',   'к' => 'k',
		'л' => 'l',   'м' => 'm',   'н' => 'n',
		'о' => 'o',   'п' => 'p',   'р' => 'r',
		'с' => 's',   'т' => 't',   'у' => 'u',
		'ф' => 'f',   'х' => 'h',   'ц' => 'c',
		'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
		'ь' => 'j',   'ы' => 'y',   'ъ' => '',
		'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
		
		'А' => 'A',   'Б' => 'B',   'В' => 'V',
		'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
		'Ё' => 'Yo',  'Ж' => 'Zh',  'З' => 'Z',
		'И' => 'I',   'Й' => 'Y',   'К' => 'K',
		'Л' => 'L',   'М' => 'M',   'Н' => 'N',
		'О' => 'O',   'П' => 'P',   'Р' => 'R',
		'С' => 'S',   'Т' => 'T',   'У' => 'U',
		'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
		'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
		'Ь' => 'J',	  'Ы' => 'Y',   'Ъ' => '',
		'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
	);
	return strtr($string, $converter);
}

function strToUrl($str) {
	// переводим в транслит
	$str = translit($str);
	// в нижний регистр
	$str = strtolower($str);
	// заменям все ненужное нам на "-"
	$str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
	$str = preg_replace('/[\-]+/', '-', $str);
	// удаляем начальные и конечные '-'
	$str = trim($str, '-');
	return $str;
}