<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field_Belongsto extends Fomg_Field {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		$meta = Jelly::meta($this->field->foreign['model']);

		// load options
		$options = Jelly::query($this->field->foreign['model'])
			->select()
			->as_array($meta->primary_key(), $meta->name_key());

		// load related model
		$value = $this->model->__get($name)->id();

		// add empty (null) option
		if($this->field->allow_null)
		{
			Arr::unshift($options, 0, '');
		}

		return Form::select($name, $options, $value, $attr);
	}
}
