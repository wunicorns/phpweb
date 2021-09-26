<?php

namespace App\Controllers;

class User extends BaseController
{
	public function index()
	{

	}

	public function detail($seq)
	{

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$builder->select('seq, id, password, status, username, email, role');
			$query = $builder->getWhere(['seq'=> $seq]);
			$row = $query->getRow();

			$return = array('error' => 0);
			header('Content-Type: application/json; charset=utf-8');

			if (isset($row)) {

				$return['result'] = array(
					'seq'=>$row->seq,
					'id'=>$row->id,
					'username'=>$row->username,
					'email'=>$row->email,
					'status'=>$row->status,
					'role'=>$row->role,
				);

				echo json_encode($return);

			} else {
				// password except
				$return['error'] = 1;
				echo json_encode($return);
			}

		} catch(\Exception $e) {
			// echo 'Message: ' . $e->getMessage();
			$return['error'] = 2;
			$return['message'] = $e->getMessage();
			echo json_encode($return);
		}

	}

	public function logout()
	{

		$data['title'] = ':: 로그아웃 ::';

		$session = session();

		$session->destroy();

		return redirect()->to('/login');
		
	}

	public function save(){
		
		$request = \Config\Services::request();

		$method = $request->getMethod();

		if($method !== 'post') {
			return redirect()->to('/user/list');
		}

		$post = $request->getPost();

		$user_id = $post['id'];
		$user_password = $post['password'];

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$session = session();

			$loginUser = $session->get('login_user');

			$today = date("Y-m-d H:i:s");

			$data = [
				'username'=> $post['username'],
				'email'=> $post['email'], 
				'role'=> 'user', 
				'status'=> $post['status'],
			];
			
			if(isset($post['seq']) && $post['seq'] != '') {
				$data['update_dt'] = $today;
				$data['update_user_seq'] = $loginUser['seq'];

				if(isset($post['password']) && $post['password'] != ''){
					if(isset($post['password_confirm']) && $post['password_confirm'] != ''){
						if($post['password_confirm'] == $post['password']){
							$data['password'] = $post['password'];	
						} else {
							throw new \Exception("password_diff");
						}
					} else {
						throw new \Exception("password_confirm");
					}
				}
				
				$builder->set($data);
				$builder->where('seq', $post['seq']);
				$builder->update();
			} else {
				$data['id'] = $post['id'];
				
				if(isset($post['password']) && $post['password'] != ''){
					if(isset($post['password_confirm']) && $post['password_confirm'] != ''){
						if($post['password_confirm'] == $post['password']){
							$data['password'] = $post['password'];	
						} else {
							throw new \Exception("password_diff");
						}
					} else {
						throw new \Exception("password_confirm");
					}
				} else {
					throw new \Exception("password_required");
				}

				$data['create_dt'] = $today;
				$data['create_user_seq'] = $loginUser['seq'];
				
				$builder->set($data);
				$builder->insert();
			}
			
			echo json_encode($return);

		} catch(\Exception $e){
			$return['error'] = 1;
			$return['message'] = $e->getMessage();
			echo json_encode($return);
		}

	}

	public function delete(){
		
		$request = \Config\Services::request();

		$method = $request->getMethod();

		if($method !== 'post') {
			return redirect()->to('/user/list');
		}

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		$post = $request->getPost();

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$session = session();

			$loginUser = $session->get('login_user');

			$builder->where('seq', $post['seq']);
			$builder->delete();

			echo json_encode($return);

		} catch(\Exception $e){
			$return['error'] = 1;
			$return['message'] = $e->getMessage();
			echo json_encode($return);
		}
	}

	public function condition($builder, $param){
		$stype = $param["type"];
		$svalue = $param["value"];

		$builder->where(['role !='=>'root']);

		if($svalue && $svalue != ''){
			if($stype == 'id'){
				$builder->like('id', $svalue);
			} else if($stype == 'email'){
				$builder->like('email', $svalue);	
			} else {
				$builder->like('username', $svalue);
			}			
		}

		return $builder;
	}

	public function list()
	{

		$data['title'] = ':: 사용자 관리 ::';

		
		$request = \Config\Services::request();

		$method = $request->getMethod();

		$stype = $request->getVar("stype");
		$svalue = $request->getVar("svalue");

		$page = $request->getVar("page") ?? 1;

		$listSize = 10;
		$navSize = 10;

		$data['stype'] = $stype;
		$data['svalue'] = $svalue;
		$data['page'] = $page;
		
		$data['list_size'] = $listSize;
		$data['nav_size'] = $navSize;

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$builder->select('seq, id, status, username, email, role, create_dt, create_user_seq, update_dt, update_user_seq');			

			$builder = $this->condition($builder, array( 'type'=>$stype, 'value'=>$svalue ));

			$builder->limit($listSize, ($page - 1) * $listSize);
			
			$query = $builder->get();

			$data['list'] = $query->getResultArray();

			$builder = $db->table('tb_users');

			$builder->select('count(*) as cnt');
			
			$builder = $this->condition($builder, array( 'type'=>$stype, 'value'=>$svalue ));

			$query = $builder->get();
			$row = $query->getRow();

			$data['total_count'] = isset($row) ? $row->cnt : 0;

		} catch(\Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
		
		echo view('templates/header', $data);
		echo view('templates/nav', $data);
		echo view('user/list', $data);
		echo view('templates/footer', $data);

	}



	
	public function idcheck(){

		$request = \Config\Services::request();

		$method = $request->getMethod();

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		$user_id = $request->getVar('id');

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_users');

			$builder->select('seq, id');
			$query = $builder->getWhere(['id'=> $user_id]);
			$row = $query->getRow();

			if (isset($row)) {

				$return['error'] = 1;
				$return['message'] = "duplicated";
				
				echo json_encode($return);
				
			} else {
				
				echo json_encode($return);

			}

		} catch(\Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
		
	}


}
