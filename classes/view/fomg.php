<?php defined('SYSPATH') or die('No direct script access.');

class View_Fomg extends Kostache {

	public $form;

	public function css()
	{
		return array(
			Url::site('fomg/assets/style.css')
		);
	}

	public function js()
	{
		return array(
			Url::site('fomg/assets/fomg.js')
		);
	}

	public function jquery()
	{
		return array(
			'cdn' => '//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js',
			'local' => Url::site('fomg/assets/jquery.js')
		);
	}

}


