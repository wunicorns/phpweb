<?php

namespace App\Controllers;

class Join extends BaseController
{
	public function index()
	{

		$data['title'] = 'Editor List';

		echo view('templates/header', $data);
		echo view('join', $data);
		echo view('templates/footer', $data);

	}

	public function join()
	{


	}

}
