<?php
class PatientsAccess extends Patients {
	var $module;
	
	// get all available apps
	function __construct($name) {
			$this->module = $name;
			$this->form_url = "apps/patients/modules/$name/";
			$this->model = new PatientsAccessModel();
			$this->binDisplay = false;
	}
	
	function getList($id,$sort) {
		global $system;
		$arr = $this->model->getList($id,$sort);
		$access = $arr["access"];
		ob_start();
			include('view/list.php');
			$data["html"] = ob_get_contents();
		ob_end_clean();
		$data["sort"] = $arr["sort"];
		$data["title"] = "";
		return $system->json_encode($data);
	}
	
	function getDetails($id) {
		global $lang;
		if($access = $this->model->getDetails($id)) {
			include('view/edit.php');
		} else {
			include CO_INC .'/view/default.php';
		}
	}
	
	function setDetails($pid,$admins,$guests) {
		$retval = $this->model->setDetails($pid,$admins,$guests);
		if($retval){
			 return '{ "action": "edit" , "id": "' . $retval . '"}';
		  } else{
			 return "error";
		  }
	}
	
	function getHelp() {
		global $lang;
		$data["file"] =  $lang["PATIENT_ACCESS_HELP"];
		$data["app"] = "patients";
		$data["module"] = "/modules/access";
		$this->openHelpPDF($data);
	}
	

}

$patientsAccess = new PatientsAccess("access");
?>