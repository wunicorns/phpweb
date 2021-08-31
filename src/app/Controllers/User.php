<?php

namespace App\Controllers;

class User extends BaseController
{
	public function index()
	{


	}

	public function login()
	{

		$data['title'] = 'Editor List';

		echo view('templates/user/header', $data);
		echo view('user/login', $data);
		echo view('templates/user/footer', $data);
		
	}

	public function logout()
	{

		$data['title'] = 'Editor List';

		
	}

	public function join()
	{

		$data['title'] = 'Editor List';

		echo view('templates/user/header', $data);
		echo view('user/join', $data);
		echo view('templates/user/footer', $data);

	}
}
