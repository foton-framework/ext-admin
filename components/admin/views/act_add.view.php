<? $this->template->title = 'Добавление объекта' ?>

<div class="admin_form" id="admin_form">
	<a href="<?=$this->form->value('back_link') ?>">Назад</a>
	<hr>
	<?=h_form::open_multipart('/' . $this->uri->uri_string()) ?>
		
		<? if ( ! empty($message)): ?>
			<div class="message"><?=$message?></div>
		<? endif ?>
		
		<?=$this->form->render() ?>
		
		<hr>
		<?=h_form::submit('Добавить') ?>
		
	<?=h_form::close() ?>
</div>