<?php

namespace App\Controllers;

class Editor extends BaseController
{
	public function index()
	{

		$data['title'] = 'Editor';

		echo view('templates/editor/top', $data);
		echo view('editor/index', $data);
		echo view('templates/editor/bottom', $data);

	}

	public function save()
	{

		print_r($request);
		//echo $request->getIPAddress();

	}

	public function list()
	{

		$data['title'] = 'Editor List';

		echo view('templates/top', $data);
		echo view('templates/header', $data);
		echo view('editor/list', $data);
		echo view('templates/footer', $data);
		echo view('templates/bottom', $data);
	}
}
