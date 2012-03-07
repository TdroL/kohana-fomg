<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field_Text extends Fomg_Field {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		return Form::textarea($name, $this->value(), $attr);
	}
}
