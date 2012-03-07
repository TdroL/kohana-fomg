<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field_Url extends Fomg_Field {

	public function plain()
	{
		$value = $this->value();
		return Html::anchor($value, $value);
	}

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		$attr['type'] = 'url';

		return Form::input($name, $this->value(), $attr);
	}
}
