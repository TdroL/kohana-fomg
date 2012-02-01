<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Fomg_Field_Primary extends Fomg_Field {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		unset($attr['class']);

		return Form::hidden($name, $this->model->__get($name), $attr);
	}
}
