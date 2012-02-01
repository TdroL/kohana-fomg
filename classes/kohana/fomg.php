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

			Arr::set_path($this->$key, $path, $value);

			return $this;
		}

		$this->$key = $value;
		return $this;
	}

	public function open()
	{
		$attr = Arr::get($this->attr, 'form', array());

		$attr['id'] = Arr::get($attr, 'id', $this->id);
		$attr['enctype'] = Arr::get($attr, 'enctype', 'multipart/form-data');
		if ($class = Arr::get($this->class, 'form'))
		{
			$attr['class'] = $class;
		}

		return Form::open($this->url['action'], $attr);
	}

	public function label()
	{
		$labels = array();

		$default_attr = Arr::get($this->attr, 'label:all', array());
		$default_class = Arr::get($this->class, 'label:all');

		$inputs_attr = Arr::get($this->attr, 'input', array());
		$labels_attr = Arr::get($this->attr, 'label', array());
		$labels_class = Arr::get($this->class, 'label', array());

		foreach ($this->labels as $name => $label)
		{
			$name = Arr::path($inputs_attr, $name.'.id', $name);

			$attr = Arr::get($labels_attr, $name, $default_attr);

			if ($class = Arr::get($labels_class, $name, $default_class))
			{
				$attr += array(
					'class' => $class,
				);
			}

			$labels[$name] = Form::label($this->id.'-'.$name, $label, $attr);
		}

		return $labels;
	}

	public function field()
	{
		$fields = array();

		$default_attr = Arr::get($this->attr, 'input:all', array());
		$default_class = Arr::get($this->class, 'input:all');

		$inputs_attr = Arr::get($this->attr, 'input', array());
		$inputs_class = Arr::get($this->class, 'input', array());

		foreach ($this->fields as $name => $field)
		{
			$attr = Arr::get($inputs_attr, $name, $default_attr);
			$attr += array(
				'id' => $this->id.'-'.$name,
			);

			if ($class = Arr::get($inputs_class, $name, $default_class))
			{
				$attr += array(
					'class' => $class,
				);
			}

			$attr = $field->attr($attr);

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
