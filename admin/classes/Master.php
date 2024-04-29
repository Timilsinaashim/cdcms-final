<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_service(){
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_list` set {$data} ";
		}else{
			$sql = "UPDATE `service_list` set {$data} where id = '{$id}' ";
		}
		$check = $this->conn->query("SELECT * FROM `service_list` where `name`='{$name}' ".($id > 0 ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Service Name Already Exists.";
		}else{
			$save = $this->conn->query($sql);
			if($save){
				$rid = !empty($id) ? $id : $this->conn->insert_id;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "Service details successfully added.";
				else
					$resp['msg'] = "Service details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occured.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_service(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `service_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_babysitter(){
		// Check if the required fields are present in the POST data
		if(!isset($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['contact'], $_POST['address'], $_POST['skills'], $_POST['years_experience'])){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Required fields are missing.';
			return json_encode($resp);
		}
	
		// Validate and sanitize input data
		$firstname = $this->conn->real_escape_string($_POST['firstname']);
		$lastname = $this->conn->real_escape_string($_POST['lastname']);
		$email = $this->conn->real_escape_string($_POST['email']);
		$contact = $this->conn->real_escape_string($_POST['contact']);
		$address = $this->conn->real_escape_string($_POST['address']);
		$skills = $this->conn->real_escape_string($_POST['skills']);
		$years_experience = intval($_POST['years_experience']); // Ensure it's an integer
	
		// Construct SQL query
		$data = " `firstname`='{$firstname}', `lastname`='{$lastname}', `email`='{$email}', `contact`='{$contact}', `address`='{$address}', `skills`='{$skills}', `years_experience`={$years_experience} ";
	
		if(empty($_POST['id'])){
			$sql = "INSERT INTO `babysitter_list` SET {$data}";
		}else{
			$id = intval($_POST['id']); // Ensure it's an integer
			$sql = "UPDATE `babysitter_list` SET {$data} WHERE `id` = {$id}";
		}
	
		// Execute the SQL query
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$resp['msg'] = empty($_POST['id']) ? "Babysitter details successfully added." : "Babysitter details has been updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while saving the data.";
			$resp['err'] = $this->conn->error;
		}
	
		// Return the response
		return json_encode($resp);
	}
	
	
	function delete_babysitter(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `babysitter_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Babysitter has been deleted successfully.");
			if(is_file(base_app."uploads/babysitters/{$id}.png")){
				unlink(base_app."uploads/babysitters/{$id}.png");
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_enrollment(){
		if(empty($_POST['id'])){
			$alpha = range("A","Z");
			shuffle($alpha);
			$prefix = (substr(implode("",$alpha),0,3))."-".(date('Ym'));
			$code = sprintf("%'.04d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM enrollment_list where `code` = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.04d",ceil($code)+1);
				}else{
					break;
				}
			}
			$_POST['code'] = "{$prefix}{$code}";
		}
		$_POST['child_fullname'] = "{$_POST['child_lastname']}, {$_POST['child_firstname']} {$_POST['child_middlename']}";
		$_POST['parent_fullname'] = "{$_POST['parent_lastname']}, {$_POST['parent_firstname']} {$_POST['parent_middlename']}";
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('code','child_fullname','parent_fullname','status'))){
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `enrollment_list` set {$data} ";
		}else{
			$sql = "UPDATE `enrollment_list` set {$data} where id = '{$id}' ";
		}
		
		$save = $this->conn->query($sql);
		if($save){
			$eid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "Enrollment Details has successfully submitted. Your Enrollment Code is <b>{$code}</b>.";
			else
				$resp['msg'] = "Enrollment details has been updated successfully.";
				$data = "";
				foreach($_POST as $k =>$v){
					if(!in_array($k,array('id','code','fullname','status'))){
						if(!is_numeric($v))
							$v = $this->conn->real_escape_string($v);
						if(!empty($data)) $data .=",";
						$data .= " ('{$eid}', '{$k}', '{$v}')";
					}
				}
				if(!empty($data)){
					$sql2 = "INSERT INTO `enrollment_details` (`enrollment_id`,`meta_field`,`meta_value`) VALUES {$data}";
					$this->conn->query("DELETE FROM `enrollment_details` FROM where enrollment_id = '{$eid}'");
					$save_meta = $this->conn->query($sql2);
					if($save_meta){
						$resp['status'] = 'success';
					}else{
						$this->conn->query("DELETE FROM `enrollment_list` FROM where id = '{$eid}'");
						$resp['status'] = 'failed';
						$resp['msg'] = "An error occurred while saving the data. Error: ".$this->conn->error;
					}
				}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occured.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] =='success' && !empty($id))
		$this->settings->set_flashdata('success',$resp['msg']);
		if($resp['status'] =='success' && empty($id))
		$this->settings->set_flashdata('pop_msg',$resp['msg']);
		return json_encode($resp);
	}
	function delete_enrollment(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `enrollment_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Enrollment has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function update_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `Enrollment_list` set status  = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Enrollment Status has successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred. Error: " .$this->conn->error;
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'save_babysitter':
		echo $Master->save_babysitter();
	break;
	case 'delete_babysitter':
		echo $Master->delete_babysitter();
	break;
	case 'save_enrollment':
		echo $Master->save_enrollment();
	break;
	case 'delete_enrollment':
		echo $Master->delete_enrollment();
	break;
	case 'update_status':
		echo $Master->update_status();
	break;
	default:
		// echo $sysset->index();
		break;
}