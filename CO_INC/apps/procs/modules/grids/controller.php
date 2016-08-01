<?php

class ProcsGrids extends Procs {
	var $module;

	function __construct($name) {
			$this->module = $name;
			$this->form_url = "apps/procs/modules/$name/";
			$this->model = new ProcsGridsModel();
			$this->binDisplay = true;
	}


	function getList($id,$sort,$fid=0) {
		global $system, $lang;
		$arr = $this->model->getList($id,$sort,$fid);
		$grids = $arr["grids"];
		ob_start();
			include('view/list.php');
			$data["html"] = ob_get_contents();
		ob_end_clean();
		$data["items"] = $arr["items"];
		$data["sort"] = $arr["sort"];
		$data["perm"] = $arr["perm"];
		$data["title"] = $lang["PROC_GRID_ACTION_NEW"];
		return $system->json_encode($data);
	}


	function getDetails($id,$fid=0) {
		global $lang;
		if($arr = $this->model->getDetails($id,$fid)) {
			$grid = $arr["grid"];
			$cols = $arr["cols"];
			$console_items = $arr["console_items"];
			$sendto = $arr["sendto"];
			$colheight = $arr["colheight"];
			$listheight = $arr["listheight"];
			$projects = $arr["projects"];
			ob_start();
				include 'view/edit.php';
				$data["html"] = ob_get_contents();
			ob_end_clean();
			$data["access"] = $arr["access"];
			return json_encode($data);
		} else {
			ob_start();
				include CO_INC .'/view/default.php';
				$data["html"] = ob_get_contents();
			ob_end_clean();
			return json_encode($data);
		}
	}
	
	function getPrintOptions() {
		global $lang;
			ob_start();
				include 'view/print_options.php';
				$html = ob_get_contents();
			ob_end_clean();
			return $html;
	}
	
	function getSendToOptions() {
		global $lang;
			ob_start();
				include 'view/sendto_options.php';
				$html = ob_get_contents();
			ob_end_clean();
			return $html;
	}

	function printDetails($id,$t,$option) {
		global $session,$date,$lang;
		$title = "";
		$html = "";
		switch($option) {
			case 'grid':
				if($arr = $this->model->getDetails($id)) {
					$grid = $arr["grid"];
					$cols = $arr["cols"];
					$console_items = $arr["console_items"];
					$sendto = $arr["sendto"];
					$colheight = $arr["colheight"];
					$listheight = $arr["listheight"];
					$projects = $arr["projects"];
					
					$page_width = sizeof($cols)*203+100+100;
					$page_height = $grid->max_items*20+5+20+142+100+100;
					if($page_width < 896) {
						$page_width = 896;
					}
					if($page_height < 595) {
						$page_height = 595;
					}
					ob_start();
						include 'view/print.php';
						$html = ob_get_contents();
					ob_end_clean();
					$title = $grid->title;
							
							
					$this->printGrid($title,$html,$page_width,$page_height);
				}
			break;
			case 'list':
				if($arr = $this->model->getDetails($id)) {
					$grid = $arr["grid"];
					$cols = $arr["cols"];
					$console_items = $arr["console_items"];
					ob_start();
							include 'view/print_list.php';
						$html = ob_get_contents();
					ob_end_clean();
					$title = $grid->title;
					$GLOBALS['SECTION'] = $session->userlang . "/" . $lang["PROC_PRINT_GRID"];
					$this->printPDF($title,$html);
				}
			break;
		}
	}
	
	function printGrid($title,$text,$width,$height) {
		global $lang;
		ob_start();
			include(CO_INC . "/view/printheader.php");
			$header = ob_get_contents();
		ob_end_clean();		
		$footer = "</body></html>";
        $html = $header . $text . $footer;
		require_once(CO_INC . "/classes/dompdf_60_beta2/dompdf_config.inc.php");
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		/*$dompdf->set_paper('a4', 'portrait');  change 'a4' to whatever you want 
         breite, h�he pixel dividiert durch 96 * 72*/
        $dompdf->set_paper( array(0,0, $width / 96 * 72, $height / 96 * 72), "portrait" );
		$dompdf->render();
		$options['Attachment'] = 1;
		$options['Accept-Ranges'] = 0;
		$options['compress'] = 1;
		$dompdf->stream($title.".pdf", $options);
	}
	
	function getSend($id) {
		global $lang;
		if($arr = $this->model->getDetails($id)) {
			$grid = $arr["grid"];
			$cols = $arr["cols"];
			$console_items = $arr["console_items"];
			$sendto = $arr["sendto"];
			$colheight = $arr["colheight"];
			$projects = $arr["projects"];
			
			$form_url = $this->form_url;
			$request = "sendDetails";
			$to = "";
			$cc = "";
			$subject = $grid->title;
			$variable = "";
			
			include CO_INC .'/view/dialog_send.php';
		}
		else {
			include CO_INC .'/view/default.php';
		}
	}
	
	
	function sendDetails($id,$variable,$to,$cc,$subject,$body) {
		global $session, $date, $users, $lang;
		$title = "";
		$html = "";
		$attachment = "";
		if($arr = $this->model->getDetails($id)) {
			$grid = $arr["grid"];
			$cols = $arr["cols"];
			$console_items = $arr["console_items"];
			$sendto = $arr["sendto"];
			$colheight = $arr["colheight"];
			$listheight = $arr["listheight"];
			$projects = $arr["projects"];
			
			$page_width = sizeof($cols)*203+100+100;
			$page_height = $grid->max_items*20+5+20+142+100+100;
			if($page_width < 896) {
				$page_width = 896;
			}
			if($page_height < 595) {
				$page_height = 595;
			}
			ob_start();
				include 'view/print.php';
				$html = ob_get_contents();
			ob_end_clean();
			$title = $grid->title;
			$GLOBALS['SECTION'] = $session->userlang . "/" . $lang["PROC_PRINT_GRID"];
			$att = CO_PATH_PDF . "/" . $this->normal_chars($title) . ".pdf";
			$pdf = $this->saveTimeline($title,$html,$att,$page_width,$page_height);
			$attachment[] = $att;
			
			ob_start();
				include 'view/print_list.php';
				$html = ob_get_contents();			
			ob_end_clean();
			$title = $grid->title;
			$GLOBALS['SECTION'] = $session->userlang . "/" . $lang["PROC_PRINT_GRID"];
			$att = CO_PATH_PDF . "/" . $this->normal_chars($title) . "_list.pdf";
			$pdf = $this->savePDF($title,$html,$att);
			$attachment[] = $att;
		}

		// write sento log
		$this->writeSendtoLog("procs_grids",$id,$to,$subject,$body);
		
		//$to,$from,$fromName,$subject,$body,$attachment
		return $this->sendEmail($to,$cc,$session->email,$session->firstname . " " . $session->lastname,$subject,$body,$attachment);
	}
	
	function checkinGrid($id) {
		if($id != "undefined") {
			return $this->model->checkinGrid($id);
		} else {
			return true;
		}
	}
	

	function setDetails($pid,$id,$title,$owner,$owner_ct,$management,$management_ct,$team,$team_ct,$grid_access,$grid_access_orig) {
		if($arr = $this->model->setDetails($pid,$id,$title,$owner,$owner_ct,$management,$management_ct,$team,$team_ct,$grid_access,$grid_access_orig)){
			if($arr["what"] == "edit") {
				//return '{ "action": "edit" , "id": "' . $arr["id"] . '", "access": "' . $grid_access . '", "status": "' . $grid_status . '"}';
				return '{ "action": "edit" , "id": "' . $arr["id"] . '", "access": "' . $grid_access . '"}';
			} else {
				return '{ "action": "reload" , "id": "' . $arr["id"] . '", "access": "' . $grid_access . '"}';
			}
		} else{
			return "error";
		}
	}


	function saveGridColumns($cols) {
			$retval = $this->model->saveGridColumns($cols);
			if($retval){
			 return 'true';
		  } else{
			 return "error";
		  }
	}
	
	function saveGridColDays($id,$days) {
			$retval = $this->model->saveGridColDays($id,$days);
			if($retval){
			 return 'true';
		  } else{
			 return "error";
		  }
	}
	
	function newGridColumn($id,$sort) {
			$retval = $this->model->newGridColumn($id,$sort);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}
	
	function binGridColumn($id) {
			$retval = $this->model->binGridColumn($id);
			if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}
	
	function saveGridItems($col,$items) {
			if($items != "") {
				$retval = $this->model->saveGridItems($col,$items);
				if($retval){
					 return 'true';
				  } else{
					 return "error";
				  }
			} else {
				return 'true';
			}
	}
	
	function getGridNote($id) {
		global $lang;
		if($note = $this->model->getGridNote($id)){
			$data["title"] = $note->title;
			$data["text"] = $note->text;
			$data["info"] = $lang["EDITED_BY_ON"] . ' ' . $note->edited_user.', ' . $note->edited_date . '<br>'
. $lang["CREATED_BY_ON"]  . ' ' . $note->created_user . ', ' . $note->created_date;
			return json_encode($data);
		} else{
			return "error";
		}
	}	
	
	function saveGridNewNote($pid,$id) {
			$retval = $this->model->saveGridNewNote($pid,$id);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}	
	
	function saveGridNewNoteTitle($pid,$id,$col) {
			$retval = $this->model->saveGridNewNoteTitle($pid,$id,$col);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNoteTitle($id,$col) {
			$retval = $this->model->saveGridNoteTitle($id,$col);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNewNoteStagegate($pid,$id,$col) {
			$retval = $this->model->saveGridNewNoteStagegate($pid,$id,$col);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNoteStagegate($id,$col) {
			$retval = $this->model->saveGridNoteStagegate($id,$col);
			if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNewManualNote($pid) {
			global $lang;
			$retval = $this->model->saveGridNewManualNote($pid);
			if($retval){
				$html = '<div id="procsgriditem_' . $retval . '" class="droppable showCoPopup" request="note" style="display: none;"><div class="statusItem"><input name="" type="checkbox" value="' . $retval . '" class="cbx jNiceHidden" /></div><div id="procsgriditem-title-' . $retval . '" class="itemTitle">' . $lang["PROC_GRID_ITEM_NEW"] . '</div><div style="display: none;" id="procsgriditem-text-' . $retval . '"></div><div id="procsgriditem-team-' . $retval . '" style="display: none;"></div><div id="procsgriditem-costs_employees-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_materials-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_external-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_other-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-hours-' . $retval . '" style="display: none;" class="hours">0</div><div id="procsgriditem-team_ct-' . $retval . '" style="display: none;"><a class="ct-content" field="coPopup-team_ct"></a></div></div>';
			 return $html;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNewManualTitle($pid,$col) {
			global $lang;
			$retval = $this->model->saveGridNewManualTitle($pid,$col);
			if($retval){
				$html = '<div id="procsgriditem_' . $retval . '" class="droppable colTitle planned showCoPopup" request="title" rel="' . $retval . '"><div class="statusItem"><span class="jNiceWrapper"><input type="checkbox" class="cbx jNiceHidden " value="' . $retval . '" name=""><span class="jNiceCheckbox"></span></span></div><div id="procsgriditem-title-' . $retval . '" class="itemTitle">' . $lang["PROC_GRID_TITLE_NEW"] . '</div><div style="display: none;" id="procsgriditem-text-' . $retval . '"></div><div id="procsgriditem-team-' . $retval . '" style="display: none;"></div><div id="procsgriditem-costs_employees-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_materials-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_external-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_other-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-hours-' . $retval . '" style="display: none;" class="hours">0</div><div id="procsgriditem-team_ct-' . $retval . '" style="display: none;"><a class="ct-content" field="coPopup-team_ct"></a></div></div>';
			 return $html;
		  } else{
			 return "error";
		  }
	}
	
	function saveGridNewManualStagegate($pid,$col) {
			global $lang;
			$retval = $this->model->saveGridNewManualStagegate($pid,$col);
			if($retval){
				$html = '<div id="procsgriditem_' . $retval . '" class="droppable colStagegate showCoPopup" request="stagegate" rel="' . $retval . '" id="item_' . $retval . '"><div class="statusItem"><span class="jNiceWrapper"><input type="checkbox" class="cbx jNiceHidden " value="' . $retval . '" name=""><span class="jNiceCheckbox"></span></span></div><div id="procsgriditem-title-' . $retval . '" class="itemTitle">' . $lang["PROC_GRID_STAGEGATE_NEW"] . '</div><div style="display: none;" id="procsgriditem-text-' . $retval . '"></div><div id="procsgriditem-team-' . $retval . '" style="display: none;"></div><div id="procsgriditem-costs_employees-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_materials-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_external-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-costs_other-' . $retval . '" style="display: none;" class="costs">0</div><div id="procsgriditem-hours-' . $retval . '" style="display: none;" class="hours">0</div><div id="procsgriditem-team_ct-' . $retval . '" style="display: none;"><a class="ct-content" field="coPopup-team_ct"></a></div></div>';
			 return $html;
		  } else{
			 return "error";
		  }
	}
	
	function setItemStatus($id,$status) {
		$retval = $this->model->setItemStatus($id,$status);
		if($retval){
			return "true";
		} else{
			return "error";
		}
	}

	function saveGridNote($proc_id,$id,$title,$team,$team_ct,$text,$hours,$costs_employees,$costs_materials,$costs_external,$costs_other) {
		$retval = $this->model->saveGridNote($proc_id,$id,$title,$team,$team_ct,$text,$hours,$costs_employees,$costs_materials,$costs_external,$costs_other);
		if($retval){
			 return $title;
		  } else{
			 return "error";
		  }
	}
	
	/*function toggleMilestone($id,$ms) {
		$retval = $this->model->toggleMilestone($id,$ms);
		if($retval){
			 return true;
		  } else{
			 return "error";
		  }
	}*/

	function createNew($id) {
		$retval = $this->model->createNew($id);
		if($retval){
			 return '{ "what": "grid" , "action": "new", "id": "' . $retval . '" }';
		  } else{
			 return "error";
		  }
	}


	function createDuplicate($id) {
		$retval = $this->model->createDuplicate($id);
		if($retval){
			 return $retval;
		  } else{
			 return "error";
		  }
	}


	function binGrid($id) {
		$retval = $this->model->binGrid($id);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}

	function restoreGrid($id) {
		$retval = $this->model->restoreGrid($id);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}
	
	function deleteGrid($id) {
		$retval = $this->model->deleteGrid($id);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}
	
	function restoreGridColumn($id) {
		$retval = $this->model->restoreGridColumn($id);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}
	
	function deleteGridColumn($id) {
		$retval = $this->model->deleteGridColumn($id);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}
	
	function toggleIntern($id,$status) {
		$retval = $this->model->toggleIntern($id,$status);
		if($retval){
			 return "true";
		  } else{
			 return "error";
		  }
	}


	function addTask($mid,$num,$sort) {
		$task = $this->model->addTask($mid,$num,$sort);
		$grid->canedit = 1;
		foreach($task as $value) {
			$checked = '';
			if($value->status == 1) {
				$checked = ' checked="checked"';
			}
			include 'view/task.php';
		}
	}


	function deleteTask($id) {
		$retval = $this->model->deleteTask($id);
		if($retval){
			return "true";
		} else{
			return "error";
		}
	}
	
	function restoreGridTask($id) {
		$retval = $this->model->restoreGridTask($id);
		if($retval){
			return "true";
		} else{
			return "error";
		}
	}
	
	function deleteGridTask($id) {
		$retval = $this->model->deleteGridTask($id);
		if($retval){
			return "true";
		} else{
			return "error";
		}
	}
	
	
		function binItem($id) {
		$retval = $this->model->binItem($id);
		if($retval){
			return "true";
		} else{
			return "error";
		}
	}
	
	function getGridStatusDialog() {
		global $lang;
		include 'view/dialog_status.php';
	}
	
	
	function convertToProject($id,$kickoff,$folder,$protocol) {
		if($data = $this->model->convertToProject($id,$kickoff,$folder,$protocol)){
			return json_encode($data);
		} else{
			return "error";
		}
	}

	function getCoPopup() {
		global $system, $lang;
		ob_start();
			include('view/copopup.php');
			$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function getHelp() {
		global $lang;
		$data["file"] =  $lang["PROC_GRID_HELP"];
		$data["app"] = "procs";
		$data["module"] = "/modules/grids";
		$this->openHelpPDF($data);
	}
	
	function toggleCurrency($id,$cur) {
		$retval = $this->model->toggleCurrency($id,$cur);
		if($retval){
			 return 'true';
		  } else{
			 return "error";
		  }
	}
	
	function getListArchive($id,$sort) {
		global $system, $lang;
		$arr = $this->model->getListArchive($id,$sort);
		$grids = $arr["grids"];
		ob_start();
			include('view/list.php');
			$data["html"] = ob_get_contents();
		ob_end_clean();
		$data["items"] = $arr["items"];
		$data["sort"] = $arr["sort"];
		$data["perm"] = $arr["perm"];
		$data["title"] = $lang["PROC_GRID_ACTION_NEW"];
		return $system->json_encode($data);
	}
   
   function getDetailsArchive($id) {
		global $lang;
		if($arr = $this->model->getDetailsArchive($id)) {
			$grid = $arr["grid"];
			$cols = $arr["cols"];
			$console_items = $arr["console_items"];
			$sendto = $arr["sendto"];
			$colheight = $arr["colheight"];
			$listheight = $arr["listheight"];
			$projects = $arr["projects"];
			ob_start();
				include 'view/edit.php';
				$data["html"] = ob_get_contents();
			ob_end_clean();
			$data["access"] = $arr["access"];
			return json_encode($data);
		} else {
			ob_start();
				include CO_INC .'/view/default.php';
				$data["html"] = ob_get_contents();
			ob_end_clean();
			return json_encode($data);
		}
	}

}

$procsGrids = new ProcsGrids("grids");
?>