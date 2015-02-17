<?php if (isset($_header) && !$_header) return; ?>
<!DOCTYPE html>
<html>
<head>
<link href="<?=WWW?>/public/images/favicon.ico" rel="icon" type="image/x-icon" />
<link href="<?=WWW?>/public/css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="content-type" content="text/html;charset=<?=CODEPAGE?>">
<script language="javascript" src="<?=WWW?>/public/js/jquery.min.js"></script>
<script language="javascript" src="<?=WWW?>/public/js/common.js"></script>
<title><?=((isset($_header_title) && ($_header_title != '')) ? $_header_title . ' :: ' : '')?>База городов</title>
</head>
<body>
	<div class="header_frame">
		<div class="header">
			<div class="title">
				<a class="go_main_page" href="<?=WWW?>/" title="Перейти на главную страницу">База городов</a>
			</div>
			<div class="menu">
				<ul>
					<li class="<?=(($_page == '') ? 'selected' : '')?>"><a data-page="" href="<?=WWW?>/">Главная</a></li>
					<li class="<?=(($_page == 'geo/search/coords') ? 'selected' : '')?>"><a data-page="geo/search/coords" href="<?=WWW?>/geo/search/coords">Поиск по координатам</a></li>
					<li class="<?=(($_page == 'geo/search/name') ? 'selected' : '')?>"><a data-page="geo/search/name" href="<?=WWW?>/geo/search/name">Поиск по имени</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="header_spacer">&nbsp;</div>
	<div class="main_frame">
