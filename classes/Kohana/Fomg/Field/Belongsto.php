<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field_Belongsto extends Fomg_Field {

	public function value()
	{
		return $this->model->__get($this->field->name);
	}

	public function plain()
	{
		$value = $this->value();

		if ($value->id())
		{
			$meta = Jelly::meta($this->field->foreign['model']);

			return $value->__get($meta->name_key()).' (id: '.$value->id().')';
		}

		return __('None');
	}

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		$meta = Jelly::meta($this->field->foreign['model']);

		// load related model
		$value = $this->value()->id();

		// load options
		$options = Jelly::query($this->field->foreign['model'])
			->select()
			->as_array($meta->primary_key(), $meta->name_key());

		// add empty (null) option
		if($this->field->allow_null)
		{
			Arr::unshift($options, 0, '');
		}

		return Form::select($name, $options, $value, $attr);
	}
}
