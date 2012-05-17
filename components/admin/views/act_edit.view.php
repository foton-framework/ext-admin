<? $this->template->title = 'Редактирование объекта' ?>

<div class="admin_form" id="admin_form">
	<a href="<?=$this->form->value('back_link') ?>">Назад</a>
	<hr>
	<?=h_form::open_multipart('/' . $this->uri->uri_string()) ?>
		
		<? if ( ! empty($message)): ?>
			<div class="message"><?=$message?></div>
		<? endif ?>
		
		<?=$this->form->render() ?>
		
		<?=h_form::submit('Сохранить') ?>
		<?=h_form::hidden('save_back', 0) ?>
		<button onclick="$('input[name=save_back]').val(1); return true">Сохранить и закрыть</button>
		
	<?=h_form::close() ?>
</div>