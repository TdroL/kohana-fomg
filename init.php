<?php defined('SYSPATH') or die('No direct script access.');

Route::set('fomg-assets', 'fomg/assets/<file>', array(
		'file' => '.+'
	))
	->defaults(array(
		'controller' => 'fomg',
		'action'     => 'assets',
	));


Route::set('fomg-examples', 'fomg(/<action>(/<params>))', array(
		'params' => '.+'
	))
	->defaults(array(
		'controller' => 'fomg',
		'action'     => 'index',
	));
