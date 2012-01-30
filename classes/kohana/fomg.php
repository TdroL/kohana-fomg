<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg {

	protected $config;
	protected $model;
	protected $allow = '*';
	protected $class = array();
	protected $attr = array();

	protected $labels = array();
	protected $fields = array();
	protected $errors = array();

	public $id;
	public $url = array(
		'action' => NULL,
		'cancel' => NULL,
	);

	public function __construct($model)
	{
		$this->config = Kohana::$config->load('fomg');

		$this->id = 'form-'.str_replace(array('model_', '_'), array('', '-'), strtolower(get_class($model)));

		$this->model = $model;

		$this->url['action'] = Request::current()->uri();

		$this->_process();
	}

	public function set($key, $value, $append = FALSE)
	{
		if (strpos($key, '.') !== FALSE)
		{
			list($key, $path) = explode('.', $key, 2);

			if ( ! Arr::is_array($this->$key))
			{
				$this->$key = array();
			}

			if ($path == '*')
			{
				foreach ($this->$key as & $elem)
				{
					$elem = $value;
				}
			}
			else
			{
				Arr::set_path($this->$key, $path, $value);
			}

			return $this;
		}

		$this->$key = $value;
		return $this;
	}

	public function open()
	{
		return Form::open($this->url['action'], array(
			'id' => $this->id,
			'enctype' => 'multipart/form-data'
		));
	}

	public function label()
	{
		$labels = array();

		foreach ($this->labels as $name => $label)
		{
			$name = Arr::path($this->attr, $name.'.id', $name);
			$labels[$name] = Form::label($this->id.'-'.$name, $label);
		}

		return $labels;
	}

	public function field()
	{
		$fields = array();

		foreach ($this->fields as $name => $field)
		{
			$attr = Arr::get($this->attr, $name, array());
			$attr += array(
				'id' => $this->id.'-'.$name,
			);

			if ($class = Arr::get($this->class, $name))
			{
				$attr += array(
					'class' => $class,
				);
			}

			$attr += $field->attr();

			$fields[$name] = $field->render($attr);
		}

		return $fields;
	}

	public function fields()
	{
		$label = $this->label();
		$field = $this->field();
		$fields = array();

		if ( ! empty($this->allowed) AND $this->allowed != '*')
		{
			foreach ($this->allowed as $name)
			{
				$fields[] = array(
					'label' => Arr::get($label, $name),
					'input' => Arr::get($field, $name),
					'error' => Arr::get($this->errors, $name)
				);
			}
		}
		else
		{
			foreach ($field as $name => $value)
			{
				$fields[] = array(
					'label' => Arr::get($label, $name),
					'input' => Arr::get($field, $name),
					'error' => Arr::get($this->errors, $name)
				);
			}
		}

		return $fields;
	}

	public function close()
	{
		return Form::hidden('csfr', Security::token()).
			Form::close();
	}

	protected function _process()
	{
		$fields = $this->model->meta()->fields();

		$override = array();
		if (is_callable(array($this->model, 'fields')))
		{
			$override = $this->model->fields();
		}

		foreach ($fields as $key => $field)
		{
			$this->labels[$key] = __($field->label);
			$this->attr[$key] = array();
			$this->class[$key] = '';

			if (isset($override[$key]))
			{
				$fields[$key] = $override[$key];
			}
			else
			{
				$fields[$key] = get_class($field);
				$fields[$key] = preg_replace('/^.+_Field_(.+)$/i', '$1', $fields[$key]);
			}

			$fields[$key] = strtolower($fields[$key]);

			$this->fields[$key] = Fomg_Field::factory($fields[$key], $this->model, $field, $this);
		}
	}

}
