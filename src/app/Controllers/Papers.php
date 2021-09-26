<?php

namespace App\Controllers;

// use App\Controllers\BaseController;

class Papers extends BaseController
{

	public function index()
	{

		$data['title'] = ':: 문서 관리 ::';
		
		$this->list();

	}
	
	public function write ($paper_id = '')
	{

		$data['title'] = ':: 문서 관리 ::';

		// $data['seq'] = $seq;

		echo view('templates/header', $data);
		echo view('templates/nav', $data);
		echo view('papers/write', $data);
		echo view('templates/footer', $data);
	}

	public function detail($paper_id)
	{

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_papers');

			$builder->select('tb_papers.*, tb_users.username');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');

			$query = $builder->getWhere(['tb_papers.paper_id'=> $paper_id]);
			$row = $query->getRow();

			if (isset($row)) {

				$data['papers'] = array(
					'paper_id'=>$row->paper_id,
					'title'=>$row->title,
					'username'=>$row->username,
					'status'=>$row->status,
					'create_dt'=>$row->create_dt,
					'create_user_seq'=>$row->create_user_seq,
					'update_dt'=>$row->update_dt,
					'update_user_seq'=>$row->update_user_seq,
				);

				$builder = $db->table('tb_sheets');
				$builder->select('seq, paper_id, ordering, content');
				$query = $builder->getWhere(['paper_id'=> $paper_id]);

				$data['sheets'] = $query->getResultArray();

				echo view('templates/header', $data);
				echo view('templates/nav', $data);
				echo view('papers/detail', $data);
				echo view('templates/footer', $data);

			} else {
				// password except
				return redirect()->to('/papers/list?error=1');
			}

		} catch(\Exception $e) {
			// echo 'Message: ' . $e->getMessage();
			$data['error'] = 2;
			$data['message'] = $e->getMessage();
			return redirect()->to('/papers/list?error=1');
		}

	}

	public function modify($paper_id)
	{

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_papers');

			$builder->select('tb_papers.*, tb_users.username');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');

			$query = $builder->getWhere(['tb_papers.paper_id'=> $paper_id]);
			$row = $query->getRow();

			$return = array('error' => 0);
			header('Content-Type: application/json; charset=utf-8');

			if (isset($row)) {

				$data['papers'] = array(
					'paper_id'=>$row->paper_id,
					'title'=>$row->title,
					'username'=>$row->username,
					'status'=>$row->status,
					'create_dt'=>$row->create_dt,
					'create_user_seq'=>$row->create_user_seq,
					'update_dt'=>$row->update_dt,
					'update_user_seq'=>$row->update_user_seq,
				);
				
				$builder = $db->table('tb_sheets');
				$builder->select('seq, paper_id, ordering, content');
				$query = $builder->getWhere(['paper_id'=> $paper_id]);

				$data['sheets'] = $query->getResultArray();

				echo view('templates/header', $data);
				echo view('templates/nav', $data);
				echo view('papers/write', $data);
				echo view('templates/footer', $data);
				
			} else {
				throw new \Exception("not_found");
			}

		} catch(\Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}

	}

	public function save(){
		
		$request = \Config\Services::request();

		$method = $request->getMethod();

		if($method !== 'post') {
			return redirect()->to('/papers/list');
		}

		$post = $request->getPost();

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		try {
			
			$db = \Config\Database::connect();

			$db->transBegin();
			$builder = $db->table('tb_papers');

			$session = session();

			$loginUser = $session->get('login_user');

			$today = date("Y-m-d H:i:s");

			$paperData = [
				'title'=> $post['title'],
				'status'=> $post['status'],
			];
			
			$paper_id = uniqid();

			if(isset($post['paper_id']) && $post['paper_id'] != '') {
				$paperData['update_dt'] = $today;
				$paperData['update_user_seq'] = $loginUser['seq'];
				
				$builder->set($paperData);
				$builder->where('paper_id', $post['paper_id']);
				$builder->update();
			} else {
				
				$paperData['paper_id'] = $paper_id;
				$paperData['create_dt'] = $today;
				$paperData['create_user_seq'] = $loginUser['seq'];
				
				$builder->set($paperData);
				$builder->insert();
			}

			$builder = $db->table('tb_sheets');
			$builder->where('paper_id', $paper_id);
			$builder->delete();

			$sheets = $post['sheets'];

			foreach($sheets as $sheet){
				
				$sheet['paper_id'] = $paper_id;
				$builder = $db->table('tb_sheets');
				$builder->set($sheet);
				$builder->insert();

			}
			
			if ($db->transStatus() === false) {
				$db->transRollback();
			} else {
				$db->transCommit();
			}

			echo json_encode($return);

		} catch(\Exception $e){
			$return['error'] = 1;
			$return['message'] = $e->getMessage();
			echo json_encode($return);
		}

	}

	public function delete($paper_id){
		
		$request = \Config\Services::request();

		$method = $request->getMethod();

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		if($method !== 'delete') {
			$return['error'] = 2;
			return json_encode($return);
		}

		$session = session();

		$loginUser = $session->get('login_user');

		try {
			
			$db = \Config\Database::connect();

			$db->transBegin();

			$builder = $db->table('tb_sheets');
			$builder->where('paper_id', $paper_id);
			$builder->delete();

			$builder = $db->table('tb_papers');
			$builder->where('paper_id', $paper_id);
			$builder->delete();

			if ($db->transStatus() === false) {
				$db->transRollback();
			} else {
				$db->transCommit();
			}

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

		if($svalue && $svalue != ''){
			if($stype == 'paper_id'){
				$builder->like('paper_id', $svalue);			
			} else if ($stype == 'username') {
				$builder->like('username', $svalue);
			} else {
				$builder->like('title', $svalue);
			}
		}

		return $builder;
	}

	public function list()
	{

		$data['title'] = ':: 문서 관리 ::';

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

			$builder = $db->table('tb_papers');

			$builder->select('tb_papers.*, tb_users.username');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');

			$builder = $this->condition($builder, array( 'type'=>$stype, 'value'=>$svalue ));

			$builder->limit($listSize, ($page - 1) * $listSize);
			
			$query = $builder->get();

			$data['list'] = $query->getResultArray();

			$builder = $db->table('tb_papers');

			$builder->select('count(*) as cnt');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');
			$builder->orderBy('tb_papers.create_dt', 'DESC');
			
			$builder = $this->condition($builder, array( 'type'=>$stype, 'value'=>$svalue ));

			$query = $builder->get();
			$row = $query->getRow();

			$data['total_count'] = isset($row) ? $row->cnt : 0;

		} catch(\Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
		
		echo view('templates/header', $data);
		echo view('templates/nav', $data);
		echo view('papers/list', $data);
		echo view('templates/footer', $data);

	}

	
	public function convert($paper_id)
	{
		try {
						
			$db = \Config\Database::connect();

			$builder = $db->table('tb_papers');

			$builder->select('tb_papers.*, tb_users.username');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');

			$query = $builder->getWhere(['tb_papers.paper_id'=> $paper_id]);
			$row = $query->getRow();

			if (isset($row)) {

				$data['papers'] = array(
					'paper_id'=>$row->paper_id,
					'title'=>$row->title,
					'username'=>$row->username,
					'status'=>$row->status,
					'create_dt'=>$row->create_dt,
					'create_user_seq'=>$row->create_user_seq,
					'update_dt'=>$row->update_dt,
					'update_user_seq'=>$row->update_user_seq,
				);

				$builder = $db->table('tb_sheets');
				$builder->select('seq, paper_id, ordering, content');
				$query = $builder->getWhere(['paper_id'=> $paper_id]);

				$data['sheets'] = $query->getResultArray();

				echo view('papers/convert_pdf', $data);

			} else {
				return view('/common/alert', $data);
			}

		} catch(\Exception $e) {
			
			$data['error'] = 2;
			$data['message'] = $e->getMessage();
			
			return view('/common/alert', $data);
		}
	}

	public function download (){
		
		$data['title'] = ':: 문서 관리 ::';

		$request = \Config\Services::request();

		$method = $request->getMethod();

		if($method !== 'post') {
			return redirect()->to('/papers/list');
		}

		$post = $request->getPost();

		$return = array('error' => 0);
		header('Content-Type: application/json; charset=utf-8');

		try {
			
			$db = \Config\Database::connect();

			$db->transBegin();
			$builder = $db->table('tb_papers');

			$session = session();

			$loginUser = $session->get('login_user');

			$today = date("Y-m-d H:i:s");

			$paperData = [
				'title'=> $post['title'],
				'status'=> $post['status'],
			];
			
			$paper_id = uniqid();

			if(isset($post['paper_id']) && $post['paper_id'] != '') {
				$paperData['update_dt'] = $today;
				$paperData['update_user_seq'] = $loginUser['seq'];
				
				$builder->set($paperData);
				$builder->where('paper_id', $post['paper_id']);
				$builder->update();
			} else {
				
				$paperData['paper_id'] = $paper_id;
				$paperData['create_dt'] = $today;
				$paperData['create_user_seq'] = $loginUser['seq'];
				
				$builder->set($paperData);
				$builder->insert();
			}

			$builder = $db->table('tb_sheets');
			$builder->where('paper_id', $paper_id);
			$builder->delete();

			// $sheets = $post['sheets'];

			$sheets = array();

			$sheets[] = array("content"=>view('papers/paper1', $data), "ordering"=>1);
			$sheets[] = array("content"=>view('papers/paper2', $data), "ordering"=>2);

			foreach($sheets as $sheet){
				
				$sheet['paper_id'] = $paper_id;
				$builder = $db->table('tb_sheets');
				$builder->set($sheet);
				$builder->insert();

			}
			
			if ($db->transStatus() === false) {
				$db->transRollback();
			} else {
				$db->transCommit();
			}

			$return['paper_id'] = $paper_id;

			echo json_encode($return);

		} catch(\Exception $e){
			$return['error'] = 1;
			$return['message'] = $e->getMessage();
			echo json_encode($return);
		}

	}

	public function download_($paper_id){

		try {
			
			$db = \Config\Database::connect();

			$builder = $db->table('tb_papers');
			
			$builder->select('tb_papers.*, tb_users.username');
			$builder->join('tb_users', 'tb_users.seq = tb_papers.create_user_seq');

			$query = $builder->getWhere(['tb_papers.paper_id'=> $paper_id]);
			$row = $query->getRow();

			if (isset($row)) {

				$title = $row->title;

				$data['papers'] = array(
					'paper_id'=>$row->paper_id,
					'title'=>$row->title,
					'username'=>$row->username,
					'content'=>$row->content,
					'status'=>$row->status,
					'create_dt'=>$row->create_dt,
					'create_user_seq'=>$row->create_user_seq,
					'update_dt'=>$row->update_dt,
					'update_user_seq'=>$row->update_user_seq,
				);

				return $this->response->setHeader('Cache-Control', 'no-cache')->download($title . ".pdf", "");

			} else {
				// password except
				// return redirect()->to('/common/alert?error=1');
				$data['error'] = 1;
				$data['message'] = "";
				return view('/common/alert', $data);
			}

		} catch(\Exception $e) {
			// echo 'Message: ' . $e->getMessage();
			$data['error'] = 2;
			$data['message'] = $e->getMessage();
			return view('/common/alert', $data);
		}

	}

	public function formated($paper_id = ''){

		$data['title'] = ':: 문서 관리 ::';

		$sheets = array();

		$sheets[] = array("content"=>view('papers/paper1', $data));
		$sheets[] = array("content"=>view('papers/paper2', $data));

		$data['sheets'] = $sheets;

		echo view('templates/header', $data);
		echo view('templates/nav', $data);
		echo view('papers/formated', $data);
		echo view('templates/footer', $data);

	}
	
	public function pdf($paper_id = ''){

		$data['title'] = ':: 문서 관리 ::';

		$sheets = array();

		$sheets[] = array("content"=>view('papers/paper1', $data));
		$sheets[] = array("content"=>view('papers/paper2', $data));

		$data['papers'] = array('username'=>"test1", 'title'=>"test1");
		$data['sheets'] = $sheets;

		echo view('papers/pdf', $data);

	}

	public function paper1 ($paper_id = '')
	{

		$data['title'] = ':: 문서 관리 ::';

		echo view('papers/paper1', $data);

	}

	public function paper2 ($paper_id = '')
	{

		$data['title'] = ':: 문서 관리 ::';

		echo view('papers/paper2', $data);

	}

}
