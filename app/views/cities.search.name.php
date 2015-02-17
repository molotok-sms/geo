<?php

// Подключение верхнего колонтитула
require('header.php');


// Вывод формы поиска
?><div class="cities search name">
	<h1 class="header">Поиск городов по имени</h1>
	<form action="/geo/search/name/" class="content" method="post">
		<div class="label">Имя:</div>
		<div class="field"><input name="name" value="<?=(isset($_data['data']['name']) ? $_data['data']['name'] : '')?>"></div>
		<div class="action"><input class="button" type="submit" value="Найти"></div>
	</form>
</div>
<?php


// Если произошла ошибка
if ($_data['status'] && isset($_data['data']['list']) && count($_data['data']['list']))
{
	// Вывод панели навигации по страницам
?><div class="pager" <?=((count($_data['pages']) <= 1) ? 'style="display: none;"' : '')?>>
<?php
	
	// Перебор доступных номеров страниц
	for ($i = 0; $i < count($_data['pages']); $i++)
	{
		// Если текущий номер сильно больше предыдущего
		if (($i > 0) && ($_data['pages'][$i] - $_data['pages'][$i - 1] > 1))
		{
			// Вывод пробельных символов
?>	<div class="spacer">...</div>
<?php
			
		}
		
		// Если это текущая страница
		if ($_data['pages'][$i] == $_data['page'])
		{
			// Вывод номера текущей страницы
?>	<div class="page current"><a data-page="<?=$_page?>" data-title="" href="#"><?=$_data['pages'][$i]?></a></div>
<?php
			
		}
		else
		{
			// Вывод ссылки на страницу
?>	<div class="page"><a data-page="<?=$_page?>" data-title="" href="<?=WWW?>/geo/search/name/<?=$_data['data']['name']?>/<?=$_data['pages'][$i]?>" title="Перейти на страницу"><?=$_data['pages'][$i]?></a></div>
<?php
			
		}
		
	}
	
	
?></div>
<?php
	
	
	// Вывод элементов
?><div class="cities">
<?php
	
	// Перебор элементов
	foreach ($_data['data']['list'] as $item)
	{
		// Вывод текущего элемента
?>	<div class="item">
		<div class="name"><?=$item['name']?></div>
		<div class="longitude"><?=$item['longitude']?></div>
		<div class="latitude"><?=$item['latitude']?></div>
	</div>
<?php
		
	}
	
?></div>
<?php
	
	
	// Вывод панели навигации по страницам
?><div class="pager" <?=((count($_data['pages']) <= 1) ? 'style="display: none;"' : '')?>>
<?php
	
	// Перебор доступных номеров страниц
	for ($i = 0; $i < count($_data['pages']); $i++)
	{
		// Если текущий номер сильно больше предыдущего
		if (($i > 0) && ($_data['pages'][$i] - $_data['pages'][$i - 1] > 1))
		{
			// Вывод пробельных символов
?>	<div class="spacer">...</div>
<?php
			
		}
		
		// Если это текущая страница
		if ($_data['pages'][$i] == $_data['page'])
		{
			// Вывод номера текущей страницы
?>	<div class="page current"><a data-page="<?=$_page?>" data-title="" href="#"><?=$_data['pages'][$i]?></a></div>
<?php
			
		}
		else
		{
			// Вывод ссылки на страницу
?>	<div class="page"><a data-page="<?=$_page?>" data-title="" href="<?=WWW?>/geo/search/name/<?=$_data['data']['name']?>/<?=$_data['pages'][$i]?>" title="Перейти на страницу"><?=$_data['pages'][$i]?></a></div>
<?php
			
		}
		
	}
	
	
?></div>
<?php
	
}


// Подключение нижнего колонтитула
require('footer.php');

?>
