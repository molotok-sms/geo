<?php

// Функция получения информации о городе или списка всех городов
function cities_get ($cid=false)
{
	// Если запрошена информация о конкретном городе
	if ($cid !== false)
	{
		// Проверка входных данных
		if (!is_numeric($cid) || !($cid > 0)) return array('result' => false, 'error' => 'Не указан город', 'error_arg' => 'cid');
		
		// Формирование запроса на выборку
		$query = '
SELECT
	`name`,
	`longitude`,
	`latitude`
	
FROM `cities`
WHERE `cid` = "' . $cid . '";
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result) && count($result))
		{
			// Получение первой записи
			$result = current($result);
			
			// Возврат информации о городе
			return array('result' => $result, 'error' => '', 'error_arg' => '');
			
		}
		
		
		// По умолчанию, возврат ошибки
		return array('result' => false, 'error' => 'Ошибка получения информации о городе', 'error_arg' => '');
		
	}
	// Иначе, если запрошен список всех автомобилей
	else
	{
		// Формирование запроса на выборку
		$query = '
SELECT
	`name`,
	`longitude`,
	`latitude`
	
FROM `cities`
ORDER BY `name`
;
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result))
		{
			// Возврат списка
			return array('result' => $result, 'error' => '', 'error_arg' => '');
			
		}
		
		
		// По умолчанию, возврат ошибки
		return array('result' => false, 'error' => 'Ошибка получения списка городов', 'error_arg' => '');
		
	}
	
}


// Функция получения количества всех городов
function cities_get_count ()
{
	// Формирование запроса на выборку
	$query = '
SELECT
	COUNT(*) AS "count"
	
FROM `cities`
ORDER BY `name`
;
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи из результата выполнения запроса
		$result = array_shift($result);
		
		// Получение количества записей
		$result = $result['count'];
		
		// Возврат списка
		return array('result' => $result, 'error' => '', 'error_arg' => '');
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка получения списка городов', 'error_arg' => '');
	
}


// Функция получения поиска городов по координатам
function cities_search_by_coords ($longitude, $latitude, $radius, $limit_offset=0, $limit_count=SEARCH_RESULTS_ON_PAGE)
{
	// Проверка входных параметров
	if (!is_numeric($longitude)) return array('result' => false, 'error' => 'Не указана долгота', 'error_arg' => 'longitude');
	if (!is_numeric($latitude)) return array('result' => false, 'error' => 'Не указана широта', 'error_arg' => 'latitude');
	if (!is_numeric($radius)) return array('result' => false, 'error' => 'Не указан радиус', 'error_arg' => 'radius');
	
	
	// Приведение к типу
	$limit_offset = (int) $limit_offset;
	$limit_count = (int) $limit_count;
	
	// Проверка граничных значений
	if ($limit_offset < 0) $limit_offset = 0;
	if ($limit_count < 0) $limit_count = 0;
	
	
	// Корень из 2/2
	$sqrt = 1.414213562373;
	
	// Радиус умноженный на корень из 2
	$r_sqrt = $radius * $sqrt;
	// Радиус в квадрате
	$r_r = $radius * $radius;
	
	
	//
	// Поиск городов укладывающихся в квадрат и ромб, описывающие окружность с радиусом R
	// (чтобы не возводить все координаты в квадрат)
	//
	// А также, в саму окружность радиусом R
	//
	
	// Формирование запроса на выборку
	$query = '
SELECT
	`name`,
	`longitude`,
	`latitude`
	
FROM `cities`
WHERE
	`longitude` BETWEEN ' . str_replace(',', '.', $longitude - $radius) . ' AND ' . str_replace(',', '.', $longitude + $radius) . '
	AND `latitude` BETWEEN ' . str_replace(',', '.', $latitude - $radius) . ' AND ' . str_replace(',', '.', $latitude + $radius) . '
	
	AND `latitude` <= -`longitude` + ' . str_replace(',', '.', $longitude + $latitude + $r_sqrt) . '
	AND `latitude` >= -`longitude` + ' . str_replace(',', '.', $longitude + $latitude - $r_sqrt) . '
	
	AND `latitude` <= `longitude` + ' . str_replace(',', '.', -$longitude + $latitude + $r_sqrt) . '
	AND `latitude` >= `longitude` + ' . str_replace(',', '.', -$longitude + $latitude - $r_sqrt) . '
	
	AND (
		(`longitude` - ' . str_replace(',', '.', $longitude) . ') * (`longitude` - ' . str_replace(',', '.', $longitude) . ')
		+ (`latitude` - ' . str_replace(',', '.', $latitude) . ') * (`latitude` - ' . str_replace(',', '.', $latitude) . ')
		<= ' . str_replace(',', '.', $r_r) . '
	)
	
ORDER BY `name`
LIMIT ' . $limit_offset . ', ' . $limit_count . ';
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result))
	{
		// Возврат списка
		return array('result' => $result, 'error' => '', 'error_arg' => '');
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка поиска городов по координатам и радиусу', 'error_arg' => '');
	
}


// Функция получения количества городов удовлетворяющих результатам поиска по координатам
function cities_search_by_coords_count ($longitude, $latitude, $radius)
{
	// Проверка входных параметров
	if (!is_numeric($longitude)) return array('result' => false, 'error' => 'Не указана долгота', 'error_arg' => 'longitude');
	if (!is_numeric($latitude)) return array('result' => false, 'error' => 'Не указана широта', 'error_arg' => 'latitude');
	if (!is_numeric($radius)) return array('result' => false, 'error' => 'Не указан радиус', 'error_arg' => 'radius');
	
	
	// Корень из 2/2
	$sqrt = 1.414213562373;
	
	// Радиус умноженный на корень из 2
	$r_sqrt = $radius * $sqrt;
	// Радиус в квадрате
	$r_r = $radius * $radius;
	
	
	//
	// Поиск городов укладывающихся в квадрат и ромб, описывающие окружность с радиусом R
	// (чтобы не возводить все координаты в квадрат)
	//
	// А также, в саму окружность радиусом R
	//
	
	// Формирование запроса на выборку
	$query = '
SELECT
	COUNT(*) AS "count"
	
FROM `cities`
WHERE
	`longitude` BETWEEN ' . str_replace(',', '.', $longitude - $radius) . ' AND ' . str_replace(',', '.', $longitude + $radius) . '
	AND `latitude` BETWEEN ' . str_replace(',', '.', $latitude - $radius) . ' AND ' . str_replace(',', '.', $latitude + $radius) . '
	
	AND `latitude` <= -`longitude` + ' . str_replace(',', '.', $longitude + $latitude + $r_sqrt) . '
	AND `latitude` >= -`longitude` + ' . str_replace(',', '.', $longitude + $latitude - $r_sqrt) . '
	AND `latitude` <= `longitude` + ' . str_replace(',', '.', -$longitude + $latitude + $r_sqrt) . '
	AND `latitude` >= `longitude` + ' . str_replace(',', '.', -$longitude + $latitude - $r_sqrt) . '
	
	AND (
		(`longitude` - ' . str_replace(',', '.', $longitude) . ') * (`longitude` - ' . str_replace(',', '.', $longitude) . ')
		+ (`latitude` - ' . str_replace(',', '.', $latitude) . ') * (`latitude` - ' . str_replace(',', '.', $latitude) . ')
		<= ' . str_replace(',', '.', $r_r) . '
	)
	
;
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи из результата выполнения запроса
		$result = array_shift($result);
		
		// Получение количества записей
		$result = $result['count'];
		
		// Возврат списка
		return array('result' => $result, 'error' => '', 'error_arg' => '');
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка поиска городов по координатам и радиусу', 'error_arg' => '');
	
}


// Функция получения поиска городов по имени
function cities_search_by_name ($name, $limit_offset=0, $limit_count=SEARCH_RESULTS_ON_PAGE)
{
	// Приведение к типу
	$limit_offset = (int) $limit_offset;
	$limit_count = (int) $limit_count;
	
	// Проверка граничных значений
	if ($limit_offset < 0) $limit_offset = 0;
	if ($limit_count < 0) $limit_count = 0;
	
	
	// Формирование запроса на выборку
	$query = '
SELECT
	`name`,
	`longitude`,
	`latitude`
	
FROM `cities`
WHERE `name` LIKE "%' . $name . '%"
ORDER BY `name`
LIMIT ' . $limit_offset . ', ' . $limit_count . ';
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result))
	{
		// Возврат списка
		return array('result' => $result, 'error' => '', 'error_arg' => '');
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка поиска городов по имени', 'error_arg' => '');
	
}


// Функция получения количества городов удовлетворяющих результатам поиска по имени
function cities_search_by_name_count ($name)
{
	// Формирование запроса на выборку
	$query = '
SELECT
	COUNT(*) AS "count"
	
FROM `cities`
WHERE `name` LIKE "%' . $name . '%"
;
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи из результата выполнения запроса
		$result = array_shift($result);
		
		// Получение количества записей
		$result = $result['count'];
		
		// Возврат списка
		return array('result' => $result, 'error' => '', 'error_arg' => '');
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка поиска городов по имени', 'error_arg' => '');
	
}


?>
