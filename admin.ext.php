<?php



class EXT_Admin
{
	//--------------------------------------------------------------------------
	
	public $enable           = FALSE;
	public $enable_log       = FALSE;
	public $log_class        = 'ext.log';
	public $is_backend       = FALSE;
	public $is_admin_mode    = FALSE;
	public $admin_component  = 'admin';
	public $backend_menu     = array();
	public $backend_sub_menu = array();
	public $template = array(
		'admin_buttons_prefix' => '<span class="admin_buttons">',
		'admin_buttons_suffix' => '</span>',
		
		'admin_panel_prefix' => '<span class="admin_panel">',
		'admin_panel_suffix' => '</span>',
	);
	
	//--------------------------------------------------------------------------
	
	public $panel_actions = array();

	//--------------------------------------------------------------------------
	
	public function __construct()
	{
		$user = empty(sys::$ext->user) ? sys::$lib->user : sys::$ext->user;

		if ($user->group_id == 1 && $user->permission->check_url('admin'))
		{
			$this->enable = TRUE;
			
			sys::$lib->template->add_head_content('
				<link rel="stylesheet" type="text/css" href="/' . EXT_FOLDER . '/admin/css/admin_front.css" />
				<script type="text/javascript" src="/' . EXT_FOLDER . '/admin/js/admin_front.js"></script>
				<script type="text/javascript" src="/' . EXT_FOLDER . '/admin/ckeditor/ckeditor.js"></script>
			');
		}

		sys::set_config_items($this, 'ext_admin');
	}
	
	//--------------------------------------------------------------------------
	
	/**
	 * Логгирует действия администратора. Создание, удаление и изменение статуса
	 * @param  string  $call_str Строка вызова модели (model.news или ext.sub)
	 * @param  integer $pid      ID объекта
	 * @param  integer $status   Изменение статуса объекта. 0: откл; 1: вкл; -1: удален
	 * @param  integer $uid      ID пользователя (админа)
	 * @return void
	 */
	public function log($call_str, $pid, $status = 1, $uid = 0)
	{
		static $log = NULL;

		if ( ! $this->enable_log) return;

		if ($log === NULL)
		{
			$log =& sys::call($this->log_class);
		}

		$log->log($call_str, $pid, $status, $uid);
	}

	//--------------------------------------------------------------------------

	public function row_actions(&$model, &$row)
	{
		if ( ! $this->enable) return;
		
		$model_class = strtolower(get_class($model));
		$model_class = preg_replace("@^".EXTENSION_CLASS_PREFIX."@sui", 'ext.', $model_class);
		$model_class = preg_replace("@^".MODEL_CLASS_PREFIX."@sui", 'model.', $model_class);

		$row_url = 'model:' . $model_class . '/id:' . $row->id;
		
		$html = "<div id='a_row_{$row->id}'></div>";
		
		if (isset($row->status))
		{
			$act    = $row->status ? 'disable' : 'enable';
			$title  = $row->status ? 'Выключить' : 'Включить';
			$html .= "<a href='/{$this->admin_component}/act/{$act}/$row_url/' title='{$title}' class='a_act_button a_{$act}'><span>{$title}</span></a>";
		}
		
		$html .= "<a href='/{$this->admin_component}/act/edit/$row_url/' title='Редактировать' class='a_act_button a_edit'><span>Редактировать</span></a>";
		$html .= "<a href='/{$this->admin_component}/act/remove/$row_url/' onclick='return a_del_confirm()' title='Удалить' class='a_act_button a_remove'><span>Удалить</span></a>";
		
		return $this->_tpl('admin_buttons', $html);
	}
	
	//--------------------------------------------------------------------------
	
	public function set_actions(&$model)
	{
		if ( ! $this->enable) return;
		//if (empty($model->add_action) && empty($model->edit_action)) return;

		$model_class = strtolower(get_class($model));
		$model_class = preg_replace("@^".EXTENSION_CLASS_PREFIX."@sui", 'ext.', $model_class);
		$model_class = preg_replace("@^".MODEL_CLASS_PREFIX."@sui", 'model.', $model_class);

		$url = 'model:' . $model_class;

		/*
$this->panel_actions[get_class($model)] = array(
			'name'  => empty($model->name) ? $model_class_name : $model->name,
			'add_link'   => "/{$this->admin_component}/act/add/$url",
			'list_link'  => "/{$this->admin_component}/list/add/$url",
		);
*/
		
		if ( ! empty($model->name)) $this->panel_actions[get_class($model)]['name'] = $model->name;
		if ( ! empty($model->act_params)) $this->panel_actions[get_class($model)]['act_params'] = $model->act_params;
		
		if ( ! empty($model->add_action))
		{
			$this->panel_actions[get_class($model)][] = array(
				'title' => is_string($model->add_action) ? $model->add_action : 'Добавить',
				'link'  => "/{$this->admin_component}/act/add/$url",
				'type'  => 'add'
			);
		}
		
		if ( ! empty($model->edit_action))
		{
			$url .= '/id:' . $model->get_id();
			$this->panel_actions[get_class($model)][] = array(
				'title' => is_string($model->edit_action) ? $model->edit_action : 'Редактировать',
				'link'  => "/{$this->admin_component}/act/edit/$url",
				'type'  => 'edit'
			);
		}
	}
	
	//--------------------------------------------------------------------------
	
	function admin_panel()
	{
		if ( ! $this->enable) return;
		
		$result = '';
		// echo '<pre>';
		// print_r($this->panel_actions);exit;
		foreach ($this->panel_actions as $action)
		{
			$act_link = '';
			if ( ! empty($action['act_params']))
			{
				foreach ($action['act_params'] as $key => $val) $act_link .= "/{$key}:{$val}";
			}
			
			$result .= "<tr><div class='admin_panel_row'>";
			
			$result .= '<td>';
			if ( ! empty($action['name'])) $result .= "<span class='name'>{$action['name']}</span>";
			$result .= '</td>';
			
			foreach ($action as $i => $subact)
			{
				if ( ! is_numeric($i)) continue;
				$result .= "<td><a href='{$subact['link']}{$act_link}/' title='' class='a_act_button a_{$subact['type']}'><span>{$subact['title']}</span></a></td>";
			}
//			$result .= "<a href='{$action['link']}' title='{$action['title']}' class='a_act_button a_add'><span>{$action['title']}</span></a>";
//			$result .= "<a href='{$action['link']}' title='Все записи' class='a_act_button a_list'><span>Все записи</span></a>";
			$result .= '</div></tr>';
		}
		return $this->_tpl('admin_panel', "<table>{$result}</table>") . '<a href="/admin/" class="admin_to_backend"></a>';
	}
	
	//--------------------------------------------------------------------------
	
	public function add_main_menu_item($menu_item)
	{
		$priority = isset($menu_item['priority']) ? $menu_item['priority'] : NULL;
		
		$this->backend_menu[$menu_item['key']] = $menu_item;
		//$this->backend_menu_priority[$priority][$menu_item['key']] =& $this->backend_menu[$menu_item['key']];
	}
	
	//--------------------------------------------------------------------------
	
	private function _tpl($tpl, $content = '')
	{
		if ($content)
		{
			return $this->template[$tpl . '_prefix'] . $content . $this->template[$tpl . '_suffix'];
		}
	}
	
	//--------------------------------------------------------------------------
}