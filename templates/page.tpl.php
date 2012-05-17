<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title><?=$this->title ?></title>
	
	<!-- Meta -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<!-- Favicon -->
	<!--link rel="shortcut icon" href="/res/favicon.ico" /-->

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="/extensions/admin/css/admin_backend.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="/res/css/datepicker.css" media="screen, projection" />
	
	<!-- JS -->
	<script type="text/javascript" src="/res/js/jquery.js"></script>
	<script type="text/javascript" src="/res/js/datepicker.js"></script>
</head>
<body>

<table id="container">
<tr>
<td id="h_menu_column">
	<a href="/"><img src="/extensions/admin/img/btn_back_to_site.png" alt=""></a>
</td>
<td id="h_content_column">
	<?=$this->user->email ?>
</td>
</tr>
<tr>
<td id="menu_column">
	<ul>
		<? foreach ($this->admin->backend_menu_priority as $key=>$item): ?>
			<li<?=FF_DEVMODE ? " title='{$item['priority']}'" : '' ?><?=$this->a_component == $key?' class="selected"':NULL ?>><a href="/admin/<?=$key ?>"><img src="<?=$item['icon'] ?>" alt="" /><?=$item['title'] ?></a></li>
		<? endforeach ?>
	</ul>
</td>
<td id="content_column">
	<? if ( count($this->admin->backend_sub_menu)): ?>
	<div class="sub_tabs">
		<? foreach ($this->admin->backend_sub_menu as $link => $title): ?>
			<a href="<?=$link ?>"<?=strpos('/' . $this->uri->uri_string . '/', $link) === FALSE?'':' class="selected"'?>><?=$title ?></a>
		<? endforeach ?>
	</div>
	<br>
	<? endif ?>
	<?=$content ?>
</td>
</tr>
</table>

</body>
</html>
