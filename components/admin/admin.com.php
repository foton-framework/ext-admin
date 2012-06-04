<?php



class EXT_COM_Admin extends SYS_Component
{
	//--------------------------------------------------------------------------
	
	function init()
	{
		if ($this->user->group_id != 1 || !$this->user->permission->check_url('admin'))
		{
			sys::error_404();
		}
		$this->admin->is_admin_mode = TRUE;
		$this->template->column = $this->template->columns = NULL;
		$this->load->library('Form');
		$this->form->template = array(
			'error_prefix'       => '<div class="error">',
			'error_suffix'       => "</div>",
			'error_divider'      => "<hr>",
			'error_label_prefix' => '<b>"',
			'error_label_suffix' => '"</b>',
			'form_prefix'        => '<table class="ff_form">',
			'form_suffix'        => '</table>',
			'row_prefix'         => '<tr><td>',
			'row_suffix'         => '</td></tr>',
			'label_prefix'       => '',
			'label_suffix'       => ':<br>',
			'field_prefix'       => '',
			'field_suffix'       => '<hr>',
		);
	}
	
	//--------------------------------------------------------------------------
	
	function router($a_component = NULL)
	{
		$this->view = FALSE;
		
		$this->template->enable(TRUE);
		$this->template->set_template_path(EXT_PATH);
		$this->template->set_template_folder('admin/templates');
		$this->template->set_template_default('page');
		$this->template->a_component = $a_component;
		
		$this->admin->is_backend = TRUE;

		$this->_include_admin_configs(EXT_PATH);
		$this->_include_admin_configs(COM_PATH);
		
		$sort = array();
		foreach ($this->admin->backend_menu as &$row) $sort[$row['key']] = $row['priority'];
		asort($sort, SORT_NUMERIC);
		foreach ($sort as $key => $val) $this->admin->backend_menu_priority[$key] =& $this->admin->backend_menu[$key];
/*
		echo '<pre>';
		print_r($this->admin->backend_menu_priority);
		exit;
*/
		if ($a_component)
		{
			$arguments = func_get_args();
			array_shift($arguments);
			$com = $this->admin->backend_menu[$a_component]['com'];
			echo $this->load->component($com . ($arguments?'/':'') . implode('/', $arguments));
		}
		
		if ($a_component == NULL)
		{
			$first_elem = current($this->admin->backend_menu_priority);
			ob_get_level() && ob_clean();
			header("Location: /admin/{$first_elem['key']}/");
		}
	}
	
	//--------------------------------------------------------------------------
	
	function _include_admin_configs($dir)
	{
		$dh = opendir($dir);
		while ($file = readdir($dh))
		{
			if ($file{0} == '.' || $file{0} == '_') continue;
			$admin_config = $dir . $file . '/admin/config.admin' . EXT;
			if ( ! file_exists($admin_config)) continue;
			include_once $admin_config;
		}
		closedir($dh);
	}
	
	//--------------------------------------------------------------------------
	//   FrontEnd Actions
	//--------------------------------------------------------------------------
	
	function act_act($action)
	{
		$this->view = FALSE;
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
		$opts    = array_splice(func_get_args(), 1);
		
		$request = new stdClass();
		
		foreach ($opts as $opt)
		{
			list($key, $val) = explode(':', $opt);
			$request->$key = $val;
		}
		
		$model = new $request->model;
		
		// Устанавливаем параметры запроса в модели
		foreach ($request as $key => $value)
		{
			$model->act_params[$key] = $value;
		}

		switch ($action)
		{
			case 'enable':
			case 'disable':
				$this->db->where($model->table() . '.id = ?', $request->id);
				$model->update($model->table(), array('status' => ($action == 'enable') ));
				$redirect = $referer . '#a_row_' . $request->id;
				break;
			
			case 'remove':
				$model->delete($model->table(), array('id=?' => $request->id));
				$redirect = $referer;
				break;
			
			case 'edit':
				$this->db->where($model->table() . '.id = ?', $request->id);
				$this->view = 'act_edit';
				$row_data = $model->get_row(NULL, FALSE);
				
				$model->row_data =& $row_data;

				$model->init_form();
				//stripslashes
				if ($row_data) foreach ($row_data as $field => $val) $this->form->set_value($field, ($val));

				$this->form->set_field('back_link', 'hidden');
				if (empty($_POST['back_link'])) $this->form->set_value('back_link', $referer);
				
				if ($this->form->validation())
				{
					$this->db->where($model->table() . '.id = ?', $request->id);
					$model->update();
					
					$this->data['message'] = 'Данные изменены';
					
					if ( ! empty($_POST['save_back']))
					{											
						ob_get_level() && ob_clean();
						header('Location: ' . $this->form->value('back_link'));
					}
				}
				
				break;
			
			case 'add':
				$this->view = 'act_add';
				
				$model->init_form();
				
				$this->form->set_field('back_link', 'hidden');
				if (empty($_POST['back_link'])) $this->form->set_value('back_link', $referer);
				
				if ($this->form->validation())
				{
					foreach ($request as $key => $val) if ( ! isset($_POST[$key])) $_POST[$key] = $val;
					//if ( ! empty($request->sub_id)) $_POST['sub_id'] = $request->sub_id;
					
					$model->insert();

					if ($model->db->affected_rows()) header('Location: ' . $this->form->value('back_link'));
				}
				break;
				
			default:
				sys::error_404();
		}
		
		if ( ! empty($redirect))
		{
			ob_get_level() && ob_clean();
			header('Location: ' . $redirect);
		}
		
		if (method_exists(&$model, 'admin_custom_form'))
		{
			$form = $model->admin_custom_form($action);
			if ($form)
			{
				$this->view = FALSE;
				echo $form;
			}
		}
	}
	
	//--------------------------------------------------------------------------
	//   Upload file from CKEditor
	//--------------------------------------------------------------------------
	
	function act_uploader()
	{
		$this->load->library('upload');

		$this->upload->set_allowed_types('jpg|gif|png');
		$this->upload->set_max_size(3);
		$this->upload->set_upload_path('files/images');
		//$this->upload->set_file_name();
		
		if ($result = $this->upload->run('upload'))
		{
			echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(" . $_GET['CKEditorFuncNum'] . ", '/" . $result->file_path . "' );</script>";
		}
	}
	
	//--------------------------------------------------------------------------

}