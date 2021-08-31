<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{

		$data['title'] = ucfirst($page); // Capitalize the first letter
		
		echo view('templates/top', $data);
		echo view('templates/header', $data);
		echo view('index', $data);
		echo view('templates/footer', $data);
		echo view('templates/bottom', $data);

	}
}
