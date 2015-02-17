<?php

// Функция реализации контроллера
function controller_geo ($params)
{
	global $_header;
	global $_header_title;
	
	
	// Инициализация параметров
	if (!isset($params[0])) $params[0] = '';
	if (!isset($params[1])) $params[1] = '';
	
	
	// Получение входных параметров
	$fajax = isset($_REQUEST['ajax']) && $_REQUEST['ajax'] ? true : false;
	
	
	// Инициализация адреса вызываемой страницы
	// (будет использоваться и в views/header.php)
	$_page = '';
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array(), 'pages' => array(), 'page' => 1);
	
	
	// Если запрошен поиск по координатам
	if (($params[0] == 'search') && ($params[1] == 'coords'))
	{
		// Если координаты переданы через форму
		if (isset($_REQUEST['longitude']) && isset($_REQUEST['latitude']))
		{
			// Сохранение координат
			$longitude = $_REQUEST['longitude'];
			$latitude = $_REQUEST['latitude'];
			
			// Получение радиуса
			$radius = isset($_REQUEST['radius']) ? $_REQUEST['radius'] : 0.3;
			
			// Исправление разделителя дробной части
			$longitude = str_replace(',', '.', $longitude);
			$latitude = str_replace(',', '.', $latitude);
			$radius = str_replace(',', '.', $radius);
			
		}
		// Иначе, если координаты переданы в URL-адресе
		elseif (isset($params[2]))
		{
			// Сохранение координат
			list($longitude, $latitude, $radius) = explode(',', $params[2] . ',,');
			
			// Если какие-то значения не переданы, заполнение значениями по умолчанию
			if (!$longitude) $longitude = false;
			if (!$latitude) $latitude = false;
			if (!$radius) $radius = 0.3;
			
			// Удаление параметра
			array_shift($params);
			
		}
		else
		{
			// Инициализация значением по умолчанию
			$longitude = false;
			$latitude = false;
			
		}
		
		
		// Получение номера страницы
		$page = isset($params[2]) ? $params[2] : 1;
		
		
		// Запоминание вызываемой страницы
		$_page = 'geo/search/coords';
		// Настройка заголовка страницы
		$_header_title = 'Поиск по координатам';
		
		
		// Если переданы координаты
		if (($longitude !== false) && ($latitude !== false))
		{
			// Приведение к числу
			$longitude = (double) $longitude;
			$latitude = (double) $latitude;
			$radius = (double) $radius;
			
			
			// Получение количества городов
			$cities_count = cities_search_by_coords_count($longitude, $latitude, $radius);
			$cities_count = $cities_count['result'];
			// Получение количества страниц
			$page_count = ceil($cities_count / SEARCH_RESULTS_ON_PAGE);
			
			// Проверка на граничные значения
			if ($page < 1) $page = 1;
			if ($page > $page_count) $page = 1;
			
			
			// Инициализация списка страниц
			$lst_pages = array(1);
			
			// Перебор страниц ближайших к текущей (от -2 до +2)
			for ($i = $page - 2; $i <= $page + 2; $i++)
			{
				// Если такая страница существует (без первой и последней)
				if (($i > 1) && ($i < $page_count))
				{
					// Добавление страницы в список
					$lst_pages[] = $i;
					
				}
				
			}
			
			// Если доступно больше одной страницы, добавление последней страницы
			if ($page_count > 1) $lst_pages[] = $page_count;
			
			
			// Поиск городов по координатам
			$result = cities_search_by_coords($longitude, $latitude, $radius, SEARCH_RESULTS_ON_PAGE * ($page - 1), SEARCH_RESULTS_ON_PAGE);
			
			// Если выполнение запроса успешно
			if (is_array($result['result']))
			{
				// Сохранение результата
				$_data['status'] = true;
				$_data['data'] = array
				(
					'longitude' => str_replace(',', '.', $longitude),
					'latitude' => str_replace(',', '.', $latitude),
					'radius' => str_replace(',', '.', $radius),
					'list' => $result['result']
				);
				$_data['pages'] = $lst_pages;
				$_data['page'] = $page;
				
			}
			
			
			// Если это AJAX-запрос, отключение вывода колонтитулов
			if ($fajax) $_header = false;
			
			// Подключение представления
			require(APP . '/views/cities.search.coords.php');
			
			// Завершение выполнения
			exit;
			
		}
		else
		{
			// Если это AJAX-запрос, отключение вывода колонтитулов
			if ($fajax) $_header = false;
			
			// Подключение представления
			require(APP . '/views/cities.search.coords.php');
			
			// Завершение выполнения
			exit;
			
		}
		
	}
	// Иначе, если запрошен поиск по имени
	elseif (($params[0] == 'search') && ($params[1] == 'name'))
	{
		// Если имя передано через форму
		if (isset($_REQUEST['name']))
		{
			// Сохранение имени
			$name = $_REQUEST['name'];
			
		}
		// Иначе, если имя передано в URL-адресе
		elseif (isset($params[2]))
		{
			// Сохранение имени
			$name = $params[2];
			
			// Удаление параметра
			array_shift($params);
			
		}
		// Иначе, инициализация значением по умолчанию
		else $name = false;
		
		
		// Получение номера страницы
		$page = isset($params[2]) ? $params[2] : 1;
		
		
		// Запоминание вызываемой страницы
		$_page = 'geo/search/name';
		// Настройка заголовка страницы
		$_header_title = 'Поиск по имени';
		
		
		// Если передано имя
		if ($name !== false)
		{
			// Получение количества городов
			$cities_count = cities_search_by_name_count($name);
			$cities_count = $cities_count['result'];
			// Получение количества страниц
			$page_count = ceil($cities_count / SEARCH_RESULTS_ON_PAGE);
			
			// Проверка на граничные значения
			if ($page < 1) $page = 1;
			if ($page > $page_count) $page = 1;
			
			
			// Инициализация списка страниц
			$lst_pages = array(1);
			
			// Перебор страниц ближайших к текущей (от -2 до +2)
			for ($i = $page - 2; $i <= $page + 2; $i++)
			{
				// Если такая страница существует (без первой и последней)
				if (($i > 1) && ($i < $page_count))
				{
					// Добавление страницы в список
					$lst_pages[] = $i;
					
				}
				
			}
			
			// Если доступно больше одной страницы, добавление последней страницы
			if ($page_count > 1) $lst_pages[] = $page_count;
			
			
			// Поиск городов по имени
			$result = cities_search_by_name($name, SEARCH_RESULTS_ON_PAGE * ($page - 1), SEARCH_RESULTS_ON_PAGE);
			
			// Если выполнение запроса успешно
			if (is_array($result['result']))
			{
				// Сохранение результата
				$_data['status'] = true;
				$_data['data'] = array
				(
					'name' => $name,
					'list' => $result['result']
				);
				$_data['pages'] = $lst_pages;
				$_data['page'] = $page;
				
			}
			
			
			// Если это AJAX-запрос, отключение вывода колонтитулов
			if ($fajax) $_header = false;
			
			// Подключение представления
			require(APP . '/views/cities.search.name.php');
			
			// Завершение выполнения
			exit;
			
		}
		else
		{
			// Если это AJAX-запрос, отключение вывода колонтитулов
			if ($fajax) $_header = false;
			
			// Подключение представления
			require(APP . '/views/cities.search.name.php');
			
			// Завершение выполнения
			exit;
			
		}
		
	}
	
	
	// Получение количества городов
	$result = cities_get_count();
	
	// Если выполнение запроса успешно
	if ($result['result'])
	{
		// Сохранение результата
		$_data['status'] = true;
		$_data['data'] = $result['result'];
		
	}
	
	
	// Если это AJAX-запрос, отключение вывода колонтитулов
	if ($fajax) $_header = false;
	
	// Подключение представления
	require(APP . '/views/cities.search.php');
	
}


?>
