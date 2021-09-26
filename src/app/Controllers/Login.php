<?php

namespace App\Controllers;

class Login extends BaseController
{
	public function index()
	{

		$data['title'] = ':: User Login ::';

		echo view('templates/header', $data);
		echo view('login', $data);
		echo view('templates/footer', $data);

	}

	public function action()
	{

		$request = \Config\Services::request();

		$method = $request->getMethod();

		if($method !== 'post') {
			return redirect()->to('/login');
		}

		$post = $request->getPost();

		$user_id = $post['id'];
		$user_password = $post['password'];

		$session = session();

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$builder->select('seq, id, password, status, username, email, role');
			$query = $builder->getWhere(['id'=> $user_id]);
			$row = $query->getRow();

			if (isset($row)) {

				if($row->password == $user_password){

					$session->set('login_user', array(
						'seq'=>$row->seq,
						'id'=>$row->id,
						'username'=>$row->username,
						'email'=>$row->email,
						'status'=>$row->status,
						'role'=>$row->role,
					));

					return redirect()->to('/papers');

				} else {
					// password except
					return redirect()->to('/login');
				}
			} else {
				// password except
				return redirect()->to('/login');
			}

		} catch(\Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
		
		$session->destroy();

		return redirect()->to('/login');

	}	

}
