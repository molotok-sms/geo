<?php

// Подключение верхнего колонтитула
require('header.php');

?><div class="search simple_page">
	<h1 class="header">Поиск городов</h1>
	<div class="content">
		<p>На нашем сайте доступен поиск городов:</p>
		<ul>
			<li><a data-page="geo/search/coords" href="<?=WWW?>/geo/search/coords/">/geo/search/coords</a> - по координатам</li>
			<li><a data-page="geo/search/name" href="<?=WWW?>/geo/search/name/">/geo/search/name</a> - по имени</li>
		</ul>
		<p>Всего городов: <?=$_data['data']?></p>
	</div>
</div>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
