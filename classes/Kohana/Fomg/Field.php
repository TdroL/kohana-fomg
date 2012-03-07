<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field {

	public $model;
	public $field;
	public $fomg;

	public static function factory($type, $model, $field, $fomg)
	{
		$class_name = strtolower('fomg_field_'.$type);
		$class_name = str_replace('_', ' ', $class_name);
		$class_name = ucwords($class_name);
		$class_name = str_replace(' ', '_', $class_name);

		if (class_exists($class_name))
		{
			$object = new $class_name;
		}
		else
		{
			$object = new Fomg_Field;
		}

		$object->model = $model;
		$object->field = $field;
		$object->fomg  = $fomg;

		return $object;
	}

	public function value()
	{
		return $this->model->__get($this->field->name);
	}

	public function plain()
	{
		return $this->value();
	}

	public function render(array $attr = array())
	{
		return '[Unknown field type]';
	}

	public function attr(array $attr = array())
	{
		$rules = $this->field->rules;

		foreach ($rules as $rule)
		{
			switch ($rule[0])
			{
				case 'not_empty':
					$attr['required'] = 'required';
				break;
			}
		}

		return $attr;
	}
}
