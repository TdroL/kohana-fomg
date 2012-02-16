<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Fomg extends Controller {

	public function action_index()
	{
		$data = array(
			'model' => new Model_Fomg_Model(),
			'orm' => new Model_Fomg_Orm()
		);

		$fomg = new Fomg($data['model']);

		$view = new View_Fomg();
		$view->form = $fomg;

		$this->response->body($view);
	}

	public function action_assets()
	{
		$ext = pathinfo($this->request->param('file'), PATHINFO_EXTENSION);
		$filename = pathinfo($this->request->param('file'), PATHINFO_FILENAME);

		$file = Kohana::find_file('assets', 'fomg/'.$filename, $ext);

		if (empty($file))
		{
			return;
		}

		$content = file_get_contents($file);
		$this->response->headers('Content-Type', File::mime_by_ext($ext));
		$this->response->body($content);
	}

}
