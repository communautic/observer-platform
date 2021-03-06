<?php
class ProcsModel extends Model {
	
	// Get all Proc Folders
   function getFolderList($sort) {
      global $session;
	  if($sort == 0) {
		  $sortstatus = $this->getSortStatus("procs-folder-sort-status");
		  if(!$sortstatus) {
		  	$order = "order by a.title";
			$sortcur = '1';
		  } else {
			  switch($sortstatus) {
				  case "1":
				  		$order = "order by a.title";
						$sortcur = '1';
				  break;
				  case "2":
				  		$order = "order by a.title DESC";
						$sortcur = '2';
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-folder-sort-order");
				  		if(!$sortorder) {
						  	$order = "order by a.title";
							$sortcur = '1';
						  } else {
							$order = "order by field(a.id,$sortorder)";
							$sortcur = '3';
						  }
				  break;
			  }
		  }
	  } else {
		  switch($sort) {
				  case "1":
				  		$order = "order by a.title";
						$sortcur = '1';
				  break;
				  case "2":
				  		$order = "order by a.title DESC";
						$sortcur = '2';
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-folder-sort-order");
				  		if(!$sortorder) {
						  	$order = "order by a.title";
							$sortcur = '1';
						  } else {
							$order = "order by field(a.id,$sortorder)";
							$sortcur = '3';
						  }
				  break;	
			  }
	  }
	  
		if(!$session->isSysadmin()) {
			$q ="select a.id, a.title from " . CO_TBL_PROCS_FOLDERS . " as a where a.status='0' and a.bin = '0' and (SELECT count(*) FROM co_procs_access as b, co_procs as c WHERE (b.admins REGEXP '[[:<:]]" . $session->uid . "[[:>:]]' or b.guests REGEXP '[[:<:]]" . $session->uid . "[[:>:]]') and c.folder=a.id and b.pid=c.id) > 0 " . $order;
		} else {
			$q ="select a.id, a.title from " . CO_TBL_PROCS_FOLDERS . " as a where a.status='0' and a.bin = '0' " . $order;
		}
		
	  $this->setSortStatus("procs-folder-sort-status",$sortcur);
      $result = mysql_query($q, $this->_db->connection);
	  $folders = "";
	  while ($row = mysql_fetch_array($result)) {

		foreach($row as $key => $val) {
				$array[$key] = $val;
				if($key == "id") {
				$array["numProcs"] = $this->getNumProcs($val);
				}
			}
			$folders[] = new Lists($array);
		  
	  }
	  
	  $perm = "guest";
	  if($session->isSysadmin()) {
		  $perm = "sysadmin";
	  }
	  
	  $arr = array("folders" => $folders, "sort" => $sortcur, "access" => $perm);
	  
	  return $arr;
   }


  /**
   * get details for the proc folder
   */
   function getFolderDetails($id) {
		global $session, $contactsmodel, $procsControllingModel;
		$q = "SELECT * FROM " . CO_TBL_PROCS_FOLDERS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_assoc($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		/*$array["allprocs"] = $this->getNumProcs($id);
		$array["plannedprocs"] = $this->getNumProcs($id, $status="0");
		$array["activeprocs"] = $this->getNumProcs($id, $status="1");
		$array["inactiveprocs"] = $this->getNumProcs($id, $status="2");*/
		
		/*$array["created_date"] = $this->_date->formatDate($array["created_date"],CO_DATETIME_FORMAT);
		$array["edited_date"] = $this->_date->formatDate($array["edited_date"],CO_DATETIME_FORMAT);
		$array["created_user"] = $this->_users->getUserFullname($array["created_user"]);
		$array["edited_user"] = $this->_users->getUserFullname($array["edited_user"]);*/
		$array["today"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		
		$array["canedit"] = true;
		$array["access"] = "sysadmin";
 		if(!$session->isSysadmin()) {
			$array["canedit"] = false;
			$array["access"] = "guest";
		}
		
		$folder = new Lists($array);
		
		// get proc details
		$access="";
		if(!$session->isSysadmin()) {
			//$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
			$access = " and (id IN (" . implode(',', $this->canAccess($session->uid)) . ") or link IN (" . implode(',', $this->canAccess($session->uid)) . ")) ";
	  	}
		
		 $sortstatus = $this->getSortStatus("procs-sort-status",$id);
		if(!$sortstatus) {
		  	$order = "order by title";
		  } else {
			  switch($sortstatus) {
				  case "1":
				  		$order = "order by title";
				  break;
				  case "2":
				  		$order = "order by title DESC";
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-sort-order",$id);
				  		if(!$sortorder) {
						  	$order = "order by title";
						  } else {
							$order = "order by field(id,$sortorder)";
						  }
				  break;	
			  }
		  }
		
		
		$q = "SELECT link, title, id, created_date, created_user FROM " . CO_TBL_PROCS . " where folder='$id' and bin='0'" . $access . " " . $order;

		$result = mysql_query($q, $this->_db->connection);
	  	$procs = "";
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$proc[$key] = $val;
			}
			
			$proc["created_date"] = $this->_date->formatDate($proc["created_date"],CO_DATE_FORMAT);
			$proc["created_user"] = $this->_users->getUserFullname($proc["created_user"]);
			$proc["perm"] = $this->getProcAccess($proc["id"]);
			
			
			if($proc['link'] != 0) {
				$array["itemstatus"] = ' module-item-active-link';
				$linkid = $proc['link'];
				$qlink = "select title,created_date,created_user from " . CO_TBL_PROCS . " where id='$linkid'";
				$rlink = mysql_query($qlink, $this->_db->connection);
				$rrow = mysql_fetch_row($rlink);
				$proc['id'] = $linkid;
				$proc['title'] = '<span class="proclink">&nbsp;</span>' . $rrow[0];
				$proc["created_date"] = $this->_date->formatDate($rrow[1],CO_DATE_FORMAT);
				$proc["created_user"] = $this->_users->getUserFullname($rrow[2]);
				$proc["perm"] = $this->getProcAccess($linkid);
			}
			
			$procs[] = new Lists($proc);
	  	}
		
		
		
		
		$access = "guest";
		  if($session->isSysadmin()) {
			  $access = "sysadmin";
		  }
		
		$arr = array("folder" => $folder, "procs" => $procs, "access" => $access);
		return $arr;
   }


   /**
   * get details for the proc folder
   */
   function setFolderDetails($id,$title,$procstatus) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_FOLDERS . " set title = '$title', status = '$procstatus', edited_user = '$session->uid', edited_date = '$now' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			return true;
		}
   }


   /**
   * create new proc folder
   */
	function newFolder() {
		global $session, $lang;
		$now = gmdate("Y-m-d H:i:s");
		$title = $lang["PROC_FOLDER_NEW"];
		
		$q = "INSERT INTO " . CO_TBL_PROCS_FOLDERS . " set title = '$title', status = '0', created_user = '$session->uid', created_date = '$now', edited_user = '$session->uid', edited_date = '$now'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	$id = mysql_insert_id();
			return $id;
		}
	}


   /**
   * delete proc folder
   */
   function binFolder($id) {
		global $session;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_FOLDERS . " set bin = '1', bintime = '$now', binuser= '$session->uid' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
   
   
   function restoreFolder($id) {
		global $session;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_FOLDERS . " set bin = '0' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
   
   function deleteFolder($id) {
		$q = "SELECT id FROM " . CO_TBL_PROCS . " where folder = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		while($row = mysql_fetch_array($result)) {
			$pid = $row["id"];
			$this->deleteProc($pid);
		}
		
		$q = "DELETE FROM " . CO_TBL_PROCS_FOLDERS . " WHERE id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }


  /**
   * get number of procs for a proc folder
   * status: 0 = all, 1 = active, 2 = abgeschlossen
   */   
   function getNumProcs($id) {
		global $session;
		
		$access = "";
		 if(!$session->isSysadmin()) {
			//$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
			$access = " and (id IN (" . implode(',', $this->canAccess($session->uid)) . ") or link IN (" . implode(',', $this->canAccess($session->uid)) . ")) ";
		  }
		
		$q = "select id from " . CO_TBL_PROCS . " where folder='$id' " . $access . " and bin != '1'";

		$result = mysql_query($q, $this->_db->connection);
		$row = mysql_num_rows($result);
		return $row;
	}


	function getProcTitle($id){
		global $session;
		$q = "SELECT title FROM " . CO_TBL_PROCS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		$title = mysql_result($result,0);
		return $title;
   }


   	function getProcTitleFromIDs($array){
		//$string = explode(",", $string);
		$total = sizeof($array);
		$data = '';
		
		if($total == 0) { 
			return $data; 
		}
		
		// check if proc is available and build array
		$arr = array();
		foreach ($array as &$value) {
			$q = "SELECT id,title FROM " . CO_TBL_PROCS . " where id = '$value' and bin='0'";
			//$q = "SELECT id, firstname, lastname FROM ".CO_TBL_USERS." where id = '$value' and bin='0'";
			$result = mysql_query($q, $this->_db->connection);
			if(mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_assoc($result)) {
					$arr[$row["id"]] = $row["title"];		
				}
			}
		}
		$arr_total = sizeof($arr);
		
		// build string
		$i = 1;
		foreach ($arr as $key => &$value) {
			$data .= $value;
			if($i < $arr_total) {
				$data .= ', ';
			}
			$data .= '';	
			$i++;
		}
		return $data;
   }
   
   
	function getProcTitleLinkFromIDs($array,$target){
		$total = sizeof($array);
		$data = '';
		if($total == 0) { 
			return $data; 
		}
		$arr = array();
		$i = 0;
		foreach ($array as &$value) {
			$q = "SELECT id,folder,title FROM " . CO_TBL_PROCS . " where id = '$value' and bin='0'";
			$result = mysql_query($q, $this->_db->connection);
			if(mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_assoc($result)) {
					$arr[$i]["id"] = $row["id"];
					$arr[$i]["title"] = $row["title"];
					$arr[$i]["folder"] = $row["folder"];
					$i++;
				}
			}
		}
		$arr_total = sizeof($arr);
		$i = 1;
		foreach ($arr as $key => &$value) {
			$data .= '<a class="externalLoadThreeLevels" rel="' . $target. ','.$value["folder"].','.$value["id"].',1,procs">' . $value["title"] . '</a>';
			if($i < $arr_total) {
				$data .= '<br />';
			}
			$data .= '';	
			$i++;
		}
		return $data;
   }


	function getProcTitleFromMeetingIDs($array,$target, $link = 0){
		$total = sizeof($array);
		$data = '';
		if($total == 0) { 
			return $data; 
		}
		$arr = array();
		$i = 0;
		foreach ($array as &$value) {
			$qm = "SELECT pid,created_date FROM " . CO_TBL_PROCS_MEETINGS . " where id = '$value' and bin='0'";
			$resultm = mysql_query($qm, $this->_db->connection);
			if(mysql_num_rows($resultm) > 0) {
				$rowm = mysql_fetch_row($resultm);
				$pid = $rowm[0];
				$date = $this->_date->formatDate($rowm[1],CO_DATETIME_FORMAT);
				$q = "SELECT id,folder,title FROM " . CO_TBL_PROCS . " where id = '$pid' and bin='0'";
				$result = mysql_query($q, $this->_db->connection);
				if(mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_assoc($result)) {
						$arr[$i]["id"] = $row["id"];
						$arr[$i]["item"] = $value;
						$arr[$i]["access"] = $this->getProcAccess($row["id"]);
						$arr[$i]["title"] = $row["title"];
						$arr[$i]["folder"] = $row["folder"];
						$arr[$i]["date"] = $date;
						$i++;
					}
				}
			}
		}
		$arr_total = sizeof($arr);
		$i = 1;
		foreach ($arr as $key => &$value) {
			if($value["access"] == "" || $link == 0) {
				$data .= $value["title"] . ', ' . $value["date"];
			} else {
				$data .= '<a class="externalLoadThreeLevels" rel="' . $target. ','.$value["folder"].','.$value["id"].',' . $value["item"] . ',procs">' . $value["title"] . '</a>';
			}
			if($i < $arr_total) {
				$data .= '<br />';
			}
			$data .= '';	
			$i++;
		}
		return $data;
   }



   	function getProcField($id,$field){
		global $session;
		$q = "SELECT $field FROM " . CO_TBL_PROCS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		$title = mysql_result($result,0);
		return $title;
   }


  /**
   * get the list of procs for a proc folder
   */ 
   function getProcList($id,$sort) {
      global $session;
	  
	  if($sort == 0) {
		  $sortstatus = $this->getSortStatus("procs-sort-status",$id);
		  if(!$sortstatus) {
		  	$order = "order by title";
			$sortcur = '1';
		  } else {
			  switch($sortstatus) {
				  case "1":
				  		$order = "order by title";
						$sortcur = '1';
				  break;
				  case "2":
				  		$order = "order by title DESC";
						$sortcur = '2';
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-sort-order",$id);
				  		if(!$sortorder) {
						  	$order = "order by title";
							$sortcur = '1';
						  } else {
							$order = "order by field(id,$sortorder)";
							$sortcur = '3';
						  }
				  break;	
			  }
		  }
	  } else {
		  switch($sort) {
				  case "1":
				  		$order = "order by title";
						$sortcur = '1';
				  break;
				  case "2":
				  		$order = "order by title DESC";
						$sortcur = '2';
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-sort-order",$id);
				  		if(!$sortorder) {
						  	$order = "order by title";
							$sortcur = '1';
						  } else {
							$order = "order by field(id,$sortorder)";
							$sortcur = '3';
						  }
				  break;	
			  }
	  }
	  
	  $access = "";
	  if(!$session->isSysadmin()) {
		$access = " and (id IN (" . implode(',', $this->canAccess($session->uid)) . ") or link IN (" . implode(',', $this->canAccess($session->uid)) . ")) ";
	  }	  
	  $q ="select id,link,title,checked_out,checked_out_user from " . CO_TBL_PROCS . " where folder='$id' and bin = '0' " . $access . $order;

	  $this->setSortStatus("procs-sort-status",$sortcur,$id);
      $result = mysql_query($q, $this->_db->connection);
	  $procs = "";
	  
	  while ($row = mysql_fetch_array($result)) {
		foreach($row as $key => $val) {
			$array[$key] = $val;
			if($key == "id") {
				if($this->getProcAccess($val) == "guest") {
					$array["access"] = "guest";
					$array["iconguest"] = ' icon-guest-active"';
					$array["checked_out_status"] = "";
				} else {
					$array["iconguest"] = '';
					$array["access"] = "";
				}
			}
		}
		
		$array["itemstatus"] = "";
		$array['outerid'] = $array['id'];
		
		//check for processlink
		/*if($array['folder'] != $id) {
			$array['title'] = $array['title'] . ' - Link!';
		}*/
		
		
		$checked_out_status = "";
		if($array["access"] != "guest" && $array["checked_out"] == 1 && $array["checked_out_user"] != $session->uid) {
			if($session->checkUserActive($array["checked_out_user"])) {
				$checked_out_status = "icon-checked-out-active";
			} else {
				$this->checkinProcOverride($id);
			}
		}
		$array["checked_out_status"] = $checked_out_status;
		
		if($array['link'] != 0) {
			$array["itemstatus"] = ' module-item-active-link';
			$linkid = $array['link'];
			$qlink = "select title,checked_out,checked_out_user from " . CO_TBL_PROCS . " where id='$linkid'";
			$rlink = mysql_query($qlink, $this->_db->connection);
			$rrow = mysql_fetch_row($rlink);
			$array['id'] = $linkid;
			$array['title'] = $rrow[0];
		}
		
		$procs[] = new Lists($array);
	  }
	  $arr = array("procs" => $procs, "sort" => $sortcur);
	  return $arr;
   }
	
	
	function checkoutProc($id) {
		global $session;
		
		$q = "UPDATE " . CO_TBL_PROCS . " set checked_out = '1', checked_out_user = '$session->uid' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
			return true;
		}
	}
	
	
	function checkinProc($id) {
		global $session;
		
		$q = "SELECT checked_out_user FROM " . CO_TBL_PROCS . " where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		$user = mysql_result($result,0);

		if($user == $session->uid) {
			$q = "UPDATE " . CO_TBL_PROCS . " set checked_out = '0', checked_out_user = '0' where id='$id'";
			$result = mysql_query($q, $this->_db->connection);
		}
		if ($result) {
			return true;
		}
	}
	
	function checkinProcOverride($id) {
		global $session;
		
		$q = "UPDATE " . CO_TBL_PROCS . " set checked_out = '0', checked_out_user = '0' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
			return true;
		}
	}
	

   function getProcDetails($id, $fid=0) {
		global $session, $contactsmodel, $lang;
		$q = "SELECT * FROM " . CO_TBL_PROCS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_array($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		// perms
		$array["access"] = $this->getProcAccess($id);

		if($array["access"] == "guest") {
			// check if this user is admin in some other proc
			$canEdit = $this->getEditPerms($session->uid);
			if(!empty($canEdit)) {
					$array["access"] = "guestadmin";
			}
		}
		$array['proclink'] = false;
		if($fid !=0 && $fid != $array['folder']) {
			$array['proclink'] = true;
			//$array["access"] = "guestadmin";
		}
		if($array['proclink'] == true) {
			$array["canedit"] = false;
			$array["showCheckout"] = false;
			$array["access"] = 'linkaccess';
		}
		
		// check if there are links to this original
		$array["linktoproclink"] = false;
		if(!$array['proclink']) {
			// are there any 
			$qlink = "SELECT a.id,a.folder,b.title FROM " . CO_TBL_PROCS . " as a, " . CO_TBL_PROCS_FOLDERS . " as b WHERE a.link='$id' and a.folder = b.id and a.bin='0' and b.bin='0'";
			$result_links = mysql_query($qlink, $this->_db->connection);
			if(mysql_num_rows($result_links) > 0) {
				$array["linktoproclink"] = true;
				$arr = $this->getFolderList(0);
				$folders = $arr['folders'];
				$folder_ids = array();
				foreach ($folders as $folder) {
					$folder_ids[] = $folder->id;
				}
				$i = 0;
				$array["proclinksdetails"] = array();
				while($row_link = mysql_fetch_array($result_links)) {
					if(in_array($row_link['folder'], $folder_ids)) {
						$array["proclinksdetails"][$i]['folder'] = $row_link['folder'];
						$array["proclinksdetails"][$i]['folder_title'] = $row_link['title'];
						$array["proclinksdetails"][$i]['id'] = $id;
					}
					$i++;
				}
				if(empty($array["proclinksdetails"])) {
					$array["linktoproclink"] = false;
				}
			}
		}
		
		$array["canedit"] = false;
		$array["showCheckout"] = false;
		$array["checked_out_user_text"] = $contactsmodel->getUserListPlain($array['checked_out_user']);

		if($array["access"] == "sysadmin" || $array["access"] == "admin") {
			//if($array["checked_out"] == 1 && $session->checkUserActive($array["checked_out_user"])) {
			if($array["checked_out"] == 1) {
				if($array["checked_out_user"] == $session->uid) {
					$array["canedit"] = true;
				} else if(!$session->checkUserActive($array["checked_out_user"])) {
					$array["canedit"] = $this->checkoutProc($id);
					$array["canedit"] = true;
				} else {
					$array["canedit"] = false;
					$array["showCheckout"] = true;
					$array["checked_out_user_phone1"] = $contactsmodel->getContactFieldFromID($array['checked_out_user'],"phone1");
					$array["checked_out_user_email"] = $contactsmodel->getContactFieldFromID($array['checked_out_user'],"email");
				}
			} else {
				$array["canedit"] = $this->checkoutProc($id);
			}
		} // EOF perms
		
		
		
		// dates
		$array["created_date"] = $this->_date->formatDate($array["created_date"],CO_DATETIME_FORMAT);
		$array["edited_date"] = $this->_date->formatDate($array["edited_date"],CO_DATETIME_FORMAT);
		$array["today"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		
		// other functions
		$array["folder_id"] = $array["folder"];
		$array["folder"] = $this->getProcFolderDetails($array["folder"],"folder");
		$array["created_user"] = $this->_users->getUserFullname($array["created_user"]);
		$array["edited_user"] = $this->_users->getUserFullname($array["edited_user"]);
		$array["current_user"] = $session->uid;
		
		$proc = new Lists($array);
		
		$sql="";
		if($array["access"] == "guest") {
			$sql = " and a.access = '1' ";
		}
		$now = gmdate("Y-m-d H:i:s");
		// Notes
		$q = "select * from " . CO_TBL_PROCS_NOTES . " where pid = '$id' and bin = '0'";
		
		$result = mysql_query($q, $this->_db->connection);
	  	$notes = "";
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$note[$key] = $val;
			}
			
			$days = $this->_date->dateDiff($note['edited_date'],$now);
			switch($days) {
				case 0:
					$note["days"] = $lang["GLOBAL_TODAY"];
				break;
				case 1:
					$note["days"] = $lang["GLOBAL_YESTERDAY"];
				break;
				default:
				$note["days"] = sprintf($lang["GLOBAL_DAYS_AGO"], $days);
			}
			
			$note["date"] = $this->_date->formatDate($note['edited_date'],CO_DATETIME_FORMAT);
			
			// dates
			$note["created_date"] = $this->_date->formatDate($note["created_date"],CO_DATETIME_FORMAT);
			$note["edited_date"] = $this->_date->formatDate($note["edited_date"],CO_DATETIME_FORMAT);
			
			// other functions
			$note["created_user"] = $this->_users->getUserFullname($note["created_user"]);
			$note["edited_user"] = $this->_users->getUserFullname($note["edited_user"]);
			
			$notes[] = new Lists($note);
	  	}
		
		$arr = array("proc" => $proc, "notes" => $notes, "access" => $array["access"]);
		return $arr;
   }
   
   
   function getProcPrintDetails($id) {
		global $session, $contactsmodel, $lang;
		$q = "SELECT * FROM " . CO_TBL_PROCS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_array($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		// dates
		$array["created_date"] = $this->_date->formatDate($array["created_date"],CO_DATETIME_FORMAT);
		$array["edited_date"] = $this->_date->formatDate($array["edited_date"],CO_DATETIME_FORMAT);
		
		// other functions
		$array["created_user"] = $this->_users->getUserFullname($array["created_user"]);
		$array["edited_user"] = $this->_users->getUserFullname($array["edited_user"]);
		$array["folder"] = $this->getProcFolderDetails($array["folder"],"folder");
		
		
		// Notes
		$q = "select * from " . CO_TBL_PROCS_NOTES . " where pid = '$id' and bin = '0'";
		
		$result = mysql_query($q, $this->_db->connection);
	  	$notes = array();
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$note[$key] = $val;
			}
			$coord = explode('x',$note["xyz"]);
				$note['x'] = $coord[0];
				$note['y'] = $coord[1];
				$note['z'] = $coord[2];

					//list($width,$height) = explode('x',$val);
			/*$coord = explode('x',$note["wh"]);
					$note['w'] = $coord[0];
					$note['h'] = $coord[1];
					$height[] = $note['y']+$note['h'];
					$width[] = $note['x']+$note['w'];*/
			
			$note['h'] = 100;
			$note['w'] = 100;
					$height[] = $note['y']+$note['h'];
					$width[] = $note['x']+$note['w'];
			
			$notes[] = new Lists($note);
	  	}
		
		$array["css_height"] = max($height);
		$array["css_width"] = max($width);
		
		//$proc = new Lists($array);
		
		$arr = array("proc" => $array, "notes" => $notes);
		return $arr;
   }



   function getDates($id) {
		global $session, $contactsmodel;
		$q = "SELECT a.startdate,(SELECT MAX(enddate) FROM " . CO_TBL_PROCS_PHASES_TASKS . " as b WHERE b.pid=a.id and b.bin = '0') as enddate FROM " . CO_TBL_PROCS . " as a where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_array($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		// dates
		$array["startdate"] = $this->_date->formatDate($array["startdate"],CO_DATE_FORMAT);
		$array["enddate"] = $this->_date->formatDate($array["enddate"],CO_DATE_FORMAT);

		$proc = new Lists($array);
		return $proc;
	}


   // Create proc folder title
	function getProcFolderDetails($string,$field){
		$users_string = explode(",", $string);
		$users_total = sizeof($users_string);
		$users = '';
		if($users_total == 0) { return $users; }
		$i = 1;
		foreach ($users_string as &$value) {
			$q = "SELECT id, title from " . CO_TBL_PROCS_FOLDERS . " where id = '$value'";
			$result_user = mysql_query($q, $this->_db->connection);
			while($row_user = mysql_fetch_assoc($result_user)) {
				$users .= '<span class="listmember" uid="' . $row_user["id"] . '">' . $row_user["title"] . '</span>';
				if($i < $users_total) {
					$users .= ', ';
				}
			}
			$i++;
		}
		return $users;
   }


   /**
   * get details for the proc folder
   */
   function setProcDetails($id,$title,$folder) {
		global $session, $contactsmodel;

		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS . " set title = '$title', folder = '$folder', edited_user = '$session->uid', edited_date = '$now' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
			return true;
		}
	}
	
	function setProcEdited($id) {
		global $session, $contactsmodel;

		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS . " set edited_user = '$session->uid', edited_date = '$now' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
			return true;
		}
	}


	function newProc($id) {
		global $session, $contactsmodel, $lang;
		
		$now = gmdate("Y-m-d H:i:s");
		$title = $lang["PROC_NEW"];
		
		$q = "INSERT INTO " . CO_TBL_PROCS . " set folder = '$id', title = '$title', created_user = '$session->uid', created_date = '$now', edited_user = '$session->uid', edited_date = '$now'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			$id = mysql_insert_id();
			// if admin insert him to access
			if(!$session->isSysadmin()) {
				$procsAccessModel = new ProcsAccessModel();
				$procsAccessModel->setDetails($id,$session->uid,"");
			}
			return $id;
		}
	}
	
	function newProcLink($id,$fid) {
		global $session, $contactsmodel, $lang;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "INSERT INTO " . CO_TBL_PROCS . " set folder = '$fid', link='$id', created_user = '$session->uid', created_date = '$now'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			$id = mysql_insert_id();
			// if admin insert him to access
			/*if(!$session->isSysadmin()) {
				$procsAccessModel = new ProcsAccessModel();
				$procsAccessModel->setDetails($id,$session->uid,"");
			}*/
			return $id;
		}
	}
	
	
	function newProcNote($id,$x,$y,$z,$what) {
		global $session, $contactsmodel, $lang;
		
		$now = gmdate("Y-m-d H:i:s");
		$title = $lang['PROC_NOTE_NEW'];
		
		$shape = 1;
		if($what == 'arrow') {
			$shape = 10;
			$title = '';
		}
		if($what == 'arrow2') {
			$shape = 27;
			$title = '';
		}
		if($what == 'arrow3') {
			$shape = 18;
			$title = '';
		}
		if($what == 'text') {
			$shape = 34;
			//$title = '';
		}
		
		$q = "INSERT INTO " . CO_TBL_PROCS_NOTES . " set pid = '$id', title = '$title', xyz = '".$x."x".$y."x".$z."', shape='$shape', created_user = '$session->uid', created_date = '$now', edited_user = '$session->uid', edited_date = '$now'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			$nid = mysql_insert_id();
			$this->setProcEdited($id);
			return $nid;
		}
	}


	function saveProcNote($proc_id,$id,$title,$text) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set title = '$title', text = '$text', edited_user = '$session->uid', edited_date = '$now' WHERE id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			$this->setProcEdited($proc_id);
			return true;
		}
	}
	
	function binItems($id) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set bin = '1', bintime = '$now', binuser= '$session->uid' where pid='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
  
  
	function deleteProcNote($id,$proc_id) {
		global $session;
		//$now = gmdate("Y-m-d H:i:s");
		$this->setProcEdited($proc_id);
		$q = "DELETE FROM " . CO_TBL_PROCS_NOTES . " where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
    function restoreItem($id) {
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set bin = '0' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
  
  function saveItemStyle($id,$shape,$color) {
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set shape='$shape', color='$color' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			$this->resetItemWH($id);
		  	return true;
		}
   }
   
   function saveItemWidth($id,$width) {
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set width='$width' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
   
   function saveItemHeight($id,$height) {
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set height='$height' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
   
   function resetItemWH($id) {
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set width='0', height='0' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
  
   	function deleteItem($id) {
		$q = "DELETE FROM " . CO_TBL_PROCS_NOTES . " where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
   }
	
	function updateNotePosition($id,$x,$y,$z) {
		global $session;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set xyz='".$x."x".$y."x".$z."' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			return true;
		}
	}


	/*function updateNoteSize($id,$w,$h) {
		global $session;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set wh='".$w."x".$h."' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			return true;
		}
	}*/


	/*function setProcNoteToggle($id,$t) {
		global $session;
		
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS_NOTES . " set toggle='$t' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
			return true;
		}
	}*/
	
	function createDuplicate($id) {
		global $session, $lang;
		
		$now = gmdate("Y-m-d H:i:s");
		// proc
		$q = "INSERT INTO " . CO_TBL_PROCS . " (folder,title,created_date,created_user,edited_date,edited_user) SELECT folder,CONCAT(title,' ".$lang["GLOBAL_DUPLICAT"]."'),'$now','$session->uid','$now','$session->uid' FROM " . CO_TBL_PROCS . " where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		$id_new = mysql_insert_id();
		
		if(!$session->isSysadmin()) {
			$procsAccessModel = new ProcsAccessModel();
			$procsAccessModel->setDetails($id_new,$session->uid,"");
		}
		// notes
		$q = "SELECT id,title,text,xyz,shape,color,width,height FROM " . CO_TBL_PROCS_NOTES . " WHERE pid = '$id' and bin='0'";
		$result = mysql_query($q, $this->_db->connection);
		while($row = mysql_fetch_array($result)) {
			$noteid = $row["id"];
			$title = mysql_real_escape_string($row["title"]);
			$text = mysql_real_escape_string($row["text"]);
			$xyz = $row["xyz"];
			$shape = $row["shape"];
			$color = $row["color"];
			$width = $row["width"];
			$height = $row["height"];
			
			$qp = "INSERT INTO " . CO_TBL_PROCS_NOTES . " set pid='$id_new',title='$title',text='$text',xyz='$xyz',width='$width',height='$height',shape='$shape',color='$color',created_date='$now',created_user='$session->uid',edited_date='$now',edited_user='$session->uid'";
			$rp = mysql_query($qp, $this->_db->connection);
			$id_p_new = mysql_insert_id();
		}		
		if ($result) {
			return $id_new;
		}
	}


	function binProc($id) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS . " set bin = '1', bintime = '$now', binuser= '$session->uid' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
	}
	
	function restoreProc($id) {
		$q = "UPDATE " . CO_TBL_PROCS . " set bin = '0' WHERE id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		if ($result) {
		  	return true;
		}
	}
	
	function deleteProc($id) {
		global $procs;
		
		$active_modules = array();
		foreach($procs->modules as $module => $value) {
			if(CONSTANT('procs_'.$module.'_bin') == 1) {
				$active_modules[] = $module;
				$arr[$module] = "";
				$arr[$module . "_tasks"] = "";
				$arr[$module . "_folders"] = "";
			}
		}
		
		
		if(in_array("documents",$active_modules)) {
			$procsDocumentsModel = new ProcsDocumentsModel();
			$q = "SELECT id FROM co_procs_documents_folders where pid = '$id'";
			$result = mysql_query($q, $this->_db->connection);
			while($row = mysql_fetch_array($result)) {
				$did = $row["id"];
				$procsDocumentsModel->deleteDocument($did);
			}
		}
		
		if(in_array("meetings",$active_modules)) {
			$procsMeetingsModel = new ProcsMeetingsModel();
			$q = "SELECT id FROM co_procs_meetings where pid = '$id'";
			$result = mysql_query($q, $this->_db->connection);
			while($row = mysql_fetch_array($result)) {
				$mid = $row["id"];
				$procsMeetingsModel->deleteMeeting($mid);
			}
		}
		
		if(in_array("pspgrids",$active_modules)) {
			$procsPspgridsModel = new ProcsPspgridsModel();
			$q = "SELECT id FROM co_procs_pspgrids where pid = '$id'";
			$result = mysql_query($q, $this->_db->connection);
			while($row = mysql_fetch_array($result)) {
				$mid = $row["id"];
				$procsPspgridsModel->deletePspgrid($mid);
			}
		}
		
		if(in_array("grids",$active_modules)) {
			$procsGridsModel = new ProcsGridsModel();
			$q = "SELECT id FROM co_procs_grids where pid = '$id'";
			$result = mysql_query($q, $this->_db->connection);
			while($row = mysql_fetch_array($result)) {
				$mid = $row["id"];
				$procsGridsModel->deleteGrid($mid);
			}
		}
		
		
		if(in_array("vdocs",$active_modules)) {
			$procsVDocsmodel = new ProcsVDocsModel();
			$q = "SELECT id FROM co_procs_vdocs where pid = '$id'";
			$result = mysql_query($q, $this->_db->connection);
			while($row = mysql_fetch_array($result)) {
				$vid = $row["id"];
				$procsVDocsmodel->deleteVDoc($vid);
			}
		}
		
		
		$q = "DELETE FROM co_log_sendto WHERE what='procs' and whatid='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		$q = "DELETE FROM co_procs_access WHERE pid='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		$q = "DELETE FROM co_procs_desktop WHERE pid='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		$q = "DELETE FROM " . CO_TBL_PROCS_NOTES . " WHERE pid='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		$q = "DELETE FROM " . CO_TBL_PROCS . " WHERE id='$id' or link='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
		  	return true;
		}
		
	}


   function moveProc($id,$startdate,$movedays) {
		global $session, $contactsmodel;
		
		$startdate = $this->_date->formatDate($_POST['startdate']);
		
		$now = gmdate("Y-m-d H:i:s");
		$q = "UPDATE " . CO_TBL_PROCS . " set startdate = '$startdate', edited_user = '$session->uid', edited_date = '$now' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
			$qt = "SELECT id, startdate, enddate FROM " . CO_TBL_PROCS_PHASES_TASKS . " where pid='$id'";
			$resultt = mysql_query($qt, $this->_db->connection);
			while ($rowt = mysql_fetch_array($resultt)) {
				$tid = $rowt["id"];
				$startdate = $this->_date->addDays($rowt["startdate"],$movedays);
				$enddate = $this->_date->addDays($rowt["enddate"],$movedays);
				$qtk = "UPDATE " . CO_TBL_PROCS_PHASES_TASKS . " set startdate = '$startdate', enddate = '$enddate' where id='$tid'";
				$retvaltk = mysql_query($qtk, $this->_db->connection);
			}
		if ($result) {
			return true;
		}
	}


	function getProcFolderDialog($field,$title) {
		global $session;
		$str = '<div class="dialog-text">';
		//$q ="select id, title from " . CO_TBL_PROCS_FOLDERS . " where status='0' and bin = '0' ORDER BY title";
		if(!$session->isSysadmin()) {
			$q ="select a.id, a.title from " . CO_TBL_PROCS_FOLDERS . " as a where a.status='0' and a.bin = '0' and (SELECT count(*) FROM co_procs_access as b, co_procs as c WHERE (b.admins REGEXP '[[:<:]]" . $session->uid . "[[:>:]]' or b.guests REGEXP '[[:<:]]" . $session->uid . "[[:>:]]') and c.folder=a.id and b.pid=c.id) > 0 ORDER BY title";
		} else {
			$q ="select id, title from " . CO_TBL_PROCS_FOLDERS . " where status='0' and bin = '0' ORDER BY title";
		}
		$result = mysql_query($q, $this->_db->connection);
		while ($row = mysql_fetch_array($result)) {
			$str .= '<a href="#" class="insertProcFolderfromDialog" title="' . $row["title"] . '" field="'.$field.'" gid="'.$row["id"].'">' . $row["title"] . '</a>';
		}
		$str .= '</div>';	
		return $str;
	 }



	// STATISTIKEN
   
   
	function numPhases($id,$status = 0, $sql="") {
	   //$sql = "";
	   if ($status == 2) {
		   $sql .= "and status='2'";
	   }
	   $q = "SELECT COUNT(id) FROM " .  CO_TBL_PROCS_PHASES. " WHERE pid='$id' $sql and bin='0'";
	   $result = mysql_query($q, $this->_db->connection);
	   $count = mysql_result($result,0);
	   return $count;
   }
   
   function numPhasesOnTime($id) {
	   //$q = "SELECT COUNT(id) FROM " .  CO_TBL_PROCS_PHASES. " WHERE pid='$id' $sql and bin='0'";
	   $q = "SELECT a.id,(SELECT MAX(enddate) FROM " . CO_TBL_PROCS_PHASES_TASKS . " as b WHERE b.phaseid=a.id and b.bin='0') as enddate FROM " . CO_TBL_PROCS_PHASES . " as a where a.pid= '$id' and a.status='2' and a.finished_date <= enddate";

	   $result = mysql_query($q, $this->_db->connection);
	   $count = mysql_result($result,0);
	   return $count;
   }
   
   function numPhasesTasks($id,$status = 0,$sql="") {
	   //$sql = "";
	   if ($status == 1) {
		   $sql .= " and status='1' ";
	   }
	   $q = "SELECT COUNT(id) FROM " .  CO_TBL_PROCS_PHASES_TASKS. " WHERE pid='$id' $sql and bin='0'";
	   $result = mysql_query($q, $this->_db->connection);
	   $count = mysql_result($result,0);
	   return $count;
   }
   
   function getRest($value) {
		return round(100-$value,2);
   }


	function getChartFolder($id, $what) { 
		global $procsControllingModel, $lang;
		switch($what) {
			case 'stability':
				$chart = $this->getChartFolder($id, 'timeing');
				$timeing = $chart["real"];
				
				$chart = $this->getChartFolder($id, 'tasks');
				$tasks = $chart["real"];
				
				$chart["real"] = round(($timeing+$tasks)/2,0);
				$chart["title"] = $lang["PROC_FOLDER_CHART_STABILITY"];
				$chart["img_name"] = $id . "_stability.png";
				$chart["url"] = 'https://chart.googleapis.com/chart?chs=150x90&cht=gm&chd=t:' . $chart["real"];
				
				$chart["tendency"] = "tendency_negative.png";
				if($chart["real"] >= 50) {
					$chart["tendency"] = "tendency_positive.png";
				}
				
				$image = self::saveImage($chart["url"],CO_PATH_BASE . '/data/charts/',$chart["img_name"]);
				
			break;
			case 'realisation':
				$realisation = 0;
				$id_array = "";
				
				$q = "SELECT id FROM " . CO_TBL_PROCS. " WHERE folder = '$id' and status != '0' and bin = '0'";
				$result = mysql_query($q, $this->_db->connection);
				$num = mysql_num_rows($result);
				$i = 1;
				while($row = mysql_fetch_assoc($result)) {
					$pid = $row["id"];
					$calc = $procsControllingModel->getChart($pid,'realisation',0);
					$realisation += $calc["real"];

					if($i == 1) {
						$id_array .= " and (pid='".$pid."'";
					} else {
						$id_array .= " or pid='".$pid."'";
					}
					if($i == $num) {
						$id_array .= ")";
					}
					//$id_array .= " and pid='".$pid."'";
					$i++;
				}
				if($num == 0) {
					$chart["real"] = 0;
				} else {
					$chart["real"] = round(($realisation)/$num,0);
				}
				$chart["tendency"] = "tendency_negative.png";
				$qt = "SELECT MAX(donedate) as dt,enddate FROM " . CO_TBL_PROCS_PHASES_TASKS. " WHERE status='1' $id_array and bin='0'";
				$resultt = mysql_query($qt, $this->_db->connection);
				$ten = mysql_fetch_assoc($resultt);
				if($ten["dt"] <= $ten["enddate"]) {
					$chart["tendency"] = "tendency_positive.png";
				}
				
				$chart["rest"] = $this->getRest($chart["real"]);
				$chart["title"] = $lang["PROC_FOLDER_CHART_REALISATION"];
				$chart["img_name"] = $id . "_realisation.png";
				$chart["url"] = 'https://chart.googleapis.com/chart?cht=p3&chd=t:' . $chart["real"]. ',' .$chart["rest"] . '&chs=150x90&chco=82aa0b&chf=bg,s,FFFFFF';
				
				$image = self::saveImage($chart["url"],CO_PATH_BASE . '/data/charts/',$chart["img_name"]);
			break;
			

			case 'timeing':
				$realisation = 0;
				$id_array = "";
				
				$q = "SELECT id FROM " . CO_TBL_PROCS. " WHERE folder = '$id' and status != '0' and bin = '0'";
				$result = mysql_query($q, $this->_db->connection);
				$num = mysql_num_rows($result);
				$i = 1;
				while($row = mysql_fetch_assoc($result)) {
					$pid = $row["id"];
					$calc = $procsControllingModel->getChart($pid,'timeing',0);
					$realisation += $calc["real"];

					if($i == 1) {
						$id_array .= " and (pid='".$pid."'";
					} else {
						$id_array .= " or pid='".$pid."'";
					}
					if($i == $num) {
						$id_array .= ")";
					}
					//$id_array .= " and pid='".$pid."'";
					$i++;
				}
					
				if($num == 0) {
					$chart["real"] = 0;
				} else {
					$chart["real"] = round(($realisation)/$num,0);
				}
				
				$today = date("Y-m-d");
				
				$chart["tendency"] = "tendency_positive.png";
				$qt = "SELECT COUNT(id) FROM " . CO_TBL_PROCS_PHASES_TASKS. " WHERE status='0' and startdate <= '$today' and enddate >= '$today' $id_array and bin='0'";
				$resultt = mysql_query($qt, $this->_db->connection);
				$tasks_active = mysql_result($resultt,0);
				
				$qo = "SELECT COUNT(id) FROM " . CO_TBL_PROCS_PHASES_TASKS. " WHERE status='0' and enddate < '$today' $id_array and bin='0'";
				$resulto = mysql_query($qo, $this->_db->connection);
				$tasks_overdue = mysql_result($resulto,0);
				if($tasks_active + $tasks_overdue == 0) {
					$tendency = 0;
				} else {
					$tendency = round((100/($tasks_active + $tasks_overdue)) * $tasks_overdue,2);
				}
				
				if($tendency > 10) {
					$chart["tendency"] = "tendency_negative.png";
				}
				
				$chart["rest"] = $this->getRest($chart["real"]);
				$chart["title"] = $lang["PROC_FOLDER_CHART_ADHERANCE"];
				$chart["img_name"] = $id . "_timeing.png";
				$chart["url"] = 'https://chart.googleapis.com/chart?cht=p3&chd=t:' . $chart["real"]. ',' .$chart["rest"] . '&chs=150x90&chco=82aa0b&chf=bg,s,FFFFFF';
			
				$image = self::saveImage($chart["url"],CO_PATH_BASE . '/data/charts/',$chart["img_name"]);
			break;
			
			case 'tasks':
				$realisation = 0;
				$id_array = "";
				
				$q = "SELECT id FROM " . CO_TBL_PROCS. " WHERE folder = '$id' and status != '0' and bin = '0'";
				$result = mysql_query($q, $this->_db->connection);
				$num = mysql_num_rows($result);
				$i = 1;
				while($row = mysql_fetch_assoc($result)) {
					$pid = $row["id"];
					$calc = $procsControllingModel->getChart($pid,'tasks',0);
					$realisation += $calc["real"];

					if($i == 1) {
						$id_array .= " and (pid='".$pid."'";
					} else {
						$id_array .= " or pid='".$pid."'";
					}
					if($i == $num) {
						$id_array .= ")";
					}
					$i++;
				}
				if($num == 0) {
					$chart["real"] = 0;
				} else {
					$chart["real"] = round(($realisation)/$num,0);
				}
				
				$today = date("Y-m-d");
				
				$chart["tendency"] = "tendency_positive.png";
				$qt = "SELECT status,donedate,enddate FROM " . CO_TBL_PROCS_PHASES_TASKS. " WHERE enddate < '$today' $id_array and bin='0' ORDER BY enddate DESC LIMIT 0,1";
				$resultt = mysql_query($qt, $this->_db->connection);
				$rowt = mysql_fetch_assoc($resultt);
				if(mysql_num_rows($resultt) != 0) {
					$status = $rowt["status"];
					$enddate = $rowt["enddate"];
					$donedate = $rowt["donedate"];
					if($status == "1" && $donedate > $enddate) {
						$chart["tendency"] = "tendency_negative.png";
					}
					if($status == "0") {
						$chart["tendency"] = "tendency_negative.png";
					}
				}
				
				$chart["rest"] = $this->getRest($chart["real"]);
				$chart["title"] = $lang["PROC_FOLDER_CHART_TASKS"];
				$chart["img_name"] = $id . "_tasks.png";
				$chart["url"] = 'https://chart.googleapis.com/chart?cht=p3&chd=t:' . $chart["real"]. ',' .$chart["rest"] . '&chs=150x90&chco=82aa0b&chf=bg,s,FFFFFF';
			
				$image = self::saveImage($chart["url"],CO_PATH_BASE . '/data/charts/',$chart["img_name"]);
				
			break;
		}
		
		return $chart;
   }


   function getBin() {
		global $procs,$lang;
		
		$bin = array();
		$bin["datetime"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		$arr = array();
		$arr["bin"] = $bin;
		
		$arr["folders"] = "";
		$arr["pros"] = "";
		$arr["procs_tasks"] = "";
		$arr["files"] = "";
		$arr["tasks"] = "";
		
		$active_modules = array();
		foreach($procs->modules as $module => $value) {
			if(CONSTANT('procs_'.$module.'_bin') == 1) {
				$active_modules[] = $module;
				$arr[$module] = "";
				$arr[$module . "_tasks"] = "";
				$arr[$module . "_folders"] = "";
				$arr[$module . "_cols"] = "";
			}
		}
				
		$q ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_FOLDERS;
		$result = mysql_query($q, $this->_db->connection);
	  	while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			if($row["bin"] == "1") { // deleted folders
				foreach($row as $key => $val) {
					$folder[$key] = $val;
				}
				$folder["bintime"] = $this->_date->formatDate($folder["bintime"],CO_DATETIME_FORMAT);
				$folder["binuser"] = $this->_users->getUserFullname($folder["binuser"]);
				$folders[] = new Lists($folder);
				$arr["folders"] = $folders;
			} else { // folder not binned
				
				$qp ="select id, title, bin, bintime, binuser, link from " . CO_TBL_PROCS . " where folder = '$id'";
				$resultp = mysql_query($qp, $this->_db->connection);
				while ($rowp = mysql_fetch_array($resultp)) {
					$pid = $rowp["id"];
					if($rowp["bin"] == "1") { // deleted procs
						foreach($rowp as $key => $val) {
							$pro[$key] = $val;
						}
						$pro["binheading"] = $lang["PROC_TITLE"];
						// prclink check
						if($pro["link"] != 0) {
							$link = $pro["link"];
							$qpl ="select title from " . CO_TBL_PROCS . " where id='$link'";
							$resultpl = mysql_query($qpl, $this->_db->connection);
							$pro["title"] = mysql_result($resultpl,0);
							$pro["binheading"] = $lang["PROC_NEW_OPTION_PROCESSLINK"];
						}
						$pro["bintime"] = $this->_date->formatDate($pro["bintime"],CO_DATETIME_FORMAT);
						$pro["binuser"] = $this->_users->getUserFullname($pro["binuser"]);
						$pros[] = new Lists($pro);
						$arr["pros"] = $pros;
					} else {
						
						// proc notes
						// remove in future .. in there for compatibility as notes get immediatly deleted
						$qt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_NOTES . " WHERE pid = '$pid'";
						$resultt = mysql_query($qt, $this->_db->connection);
						while ($rowt = mysql_fetch_array($resultt)) {
							if($rowt["bin"] == "1") { // deleted phases
								foreach($rowt as $key => $val) {
									$procs_task[$key] = $val;
								}
								$procs_task["bintime"] = $this->_date->formatDate($procs_task["bintime"],CO_DATETIME_FORMAT);
								$procs_task["binuser"] = $this->_users->getUserFullname($procs_task["binuser"]);
								$procs_tasks[] = new Lists($procs_task);
								$arr["procs_tasks"] = $procs_tasks;
							} 
						}
						
						
						// pspgrids
						if(in_array("pspgrids",$active_modules)) {
							$qf ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_PSPGRIDS . " where pid = '$pid'";
							$resultf = mysql_query($qf, $this->_db->connection);
							while ($rowf = mysql_fetch_array($resultf)) {
								$fid = $rowf["id"];
								if($rowf["bin"] == "1") { // deleted phases
									foreach($rowf as $key => $val) {
										$forum[$key] = $val;
									}
									$forum["bintime"] = $this->_date->formatDate($forum["bintime"],CO_DATETIME_FORMAT);
									$forum["binuser"] = $this->_users->getUserFullname($forum["binuser"]);
									$forums[] = new Lists($forum);
									$arr["pspgrids"] = $forums;
								} else {
									// columns
									
									$qc ="select id, bin, bintime, binuser from " . CO_TBL_PROCS_PSPGRIDS_COLUMNS . " where pid = '$fid'";
									$resultc = mysql_query($qc, $this->_db->connection);
									while ($rowc = mysql_fetch_array($resultc)) {
										$cid = $rowc["id"];
										if($rowc["bin"] == "1") { // deleted phases
											foreach($rowc as $key => $val) {
												$pspgrids_col[$key] = $val;
											}
											
											$items = '';
											$qn = "SELECT title FROM " . CO_TBL_PROCS_PSPGRIDS_NOTES . " where cid = '$cid' and bin='0' ORDER BY sort";
											$resultn = mysql_query($qn, $this->_db->connection);
											while($rown = mysql_fetch_object($resultn)) {
												$items .= $rown->title . ', ';
													//$items_used[] = $rown->id;
											}
											$pspgrids_col["items"] = rtrim($items,", ");
											
											
											$pspgrids_col["bintime"] = $this->_date->formatDate($pspgrids_col["bintime"],CO_DATETIME_FORMAT);
											$pspgrids_col["binuser"] = $this->_users->getUserFullname($pspgrids_col["binuser"]);
											$pspgrids_cols[] = new Lists($pspgrids_col);
											$arr["pspgrids_cols"] = $pspgrids_cols;
										} else {
											// notes
											$qt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_PSPGRIDS_NOTES . " WHERE cid = '$cid' ORDER BY sort";
											$resultt = mysql_query($qt, $this->_db->connection);
											while ($rowt = mysql_fetch_array($resultt)) {
												if($rowt["bin"] == "1") { // deleted phases
													foreach($rowt as $key => $val) {
														$pspgrids_task[$key] = $val;
													}
													$pspgrids_task["bintime"] = $this->_date->formatDate($pspgrids_task["bintime"],CO_DATETIME_FORMAT);
													$pspgrids_task["binuser"] = $this->_users->getUserFullname($pspgrids_task["binuser"]);
													$pspgrids_tasks[] = new Lists($pspgrids_task);
													$arr["pspgrids_tasks"] = $pspgrids_tasks;
												} 
											}
										}
									}
								}
							}
						}
						
						// grids
						if(in_array("grids",$active_modules)) {
							$qf ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_GRIDS . " where pid = '$pid'";
							$resultf = mysql_query($qf, $this->_db->connection);
							while ($rowf = mysql_fetch_array($resultf)) {
								$fid = $rowf["id"];
								if($rowf["bin"] == "1") { // deleted phases
									foreach($rowf as $key => $val) {
										$forum[$key] = $val;
									}
									$forum["bintime"] = $this->_date->formatDate($forum["bintime"],CO_DATETIME_FORMAT);
									$forum["binuser"] = $this->_users->getUserFullname($forum["binuser"]);
									$forums[] = new Lists($forum);
									$arr["grids"] = $forums;
								} else {
									// columns
									
									$qc ="select id, bin, bintime, binuser from " . CO_TBL_PROCS_GRIDS_COLUMNS . " where pid = '$fid'";
									$resultc = mysql_query($qc, $this->_db->connection);
									while ($rowc = mysql_fetch_array($resultc)) {
										$cid = $rowc["id"];
										if($rowc["bin"] == "1") { // deleted phases
											foreach($rowc as $key => $val) {
												$grids_col[$key] = $val;
											}
											
											$items = '';
											$qn = "SELECT title FROM " . CO_TBL_PROCS_GRIDS_NOTES . " where cid = '$cid' and bin='0' ORDER BY sort";
											$resultn = mysql_query($qn, $this->_db->connection);
											while($rown = mysql_fetch_object($resultn)) {
												$items .= $rown->title . ', ';
													//$items_used[] = $rown->id;
											}
											$grids_col["items"] = rtrim($items,", ");
											
											
											$grids_col["bintime"] = $this->_date->formatDate($grids_col["bintime"],CO_DATETIME_FORMAT);
											$grids_col["binuser"] = $this->_users->getUserFullname($grids_col["binuser"]);
											$grids_cols[] = new Lists($grids_col);
											$arr["grids_cols"] = $grids_cols;
										} else {
											// notes
											$qt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_GRIDS_NOTES . " WHERE cid = '$cid' ORDER BY sort";
											$resultt = mysql_query($qt, $this->_db->connection);
											while ($rowt = mysql_fetch_array($resultt)) {
												if($rowt["bin"] == "1") { // deleted phases
													foreach($rowt as $key => $val) {
														$grids_task[$key] = $val;
													}
													$grids_task["bintime"] = $this->_date->formatDate($grids_task["bintime"],CO_DATETIME_FORMAT);
													$grids_task["binuser"] = $this->_users->getUserFullname($grids_task["binuser"]);
													$grids_tasks[] = new Lists($grids_task);
													$arr["grids_tasks"] = $grids_tasks;
												} 
											}
										}
									}
								}
							}
						}
	
						// meetings
						if(in_array("meetings",$active_modules)) {
							$qm ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_MEETINGS . " where pid = '$pid'";
							$resultm = mysql_query($qm, $this->_db->connection);
							while ($rowm = mysql_fetch_array($resultm)) {
								$mid = $rowm["id"];
								if($rowm["bin"] == "1") { // deleted meeting
									foreach($rowm as $key => $val) {
										$meeting[$key] = $val;
									}
									$meeting["bintime"] = $this->_date->formatDate($meeting["bintime"],CO_DATETIME_FORMAT);
									$meeting["binuser"] = $this->_users->getUserFullname($meeting["binuser"]);
									$meetings[] = new Lists($meeting);
									$arr["meetings"] = $meetings;
								} else {
									// meetings_tasks
									$qmt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_MEETINGS_TASKS . " where mid = '$mid'";
									$resultmt = mysql_query($qmt, $this->_db->connection);
									while ($rowmt = mysql_fetch_array($resultmt)) {
										if($rowmt["bin"] == "1") { // deleted phases
											foreach($rowmt as $key => $val) {
												$meetings_task[$key] = $val;
											}
											$meetings_task["bintime"] = $this->_date->formatDate($meetings_task["bintime"],CO_DATETIME_FORMAT);
											$meetings_task["binuser"] = $this->_users->getUserFullname($meetings_task["binuser"]);
											$meetings_tasks[] = new Lists($meetings_task);
											$arr["meetings_tasks"] = $meetings_tasks;
										}
									}
								}
							}
						}
							
	
						// documents_folder
						if(in_array("documents",$active_modules)) {
							$qd ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_DOCUMENTS_FOLDERS . " where pid = '$pid'";
							$resultd = mysql_query($qd, $this->_db->connection);
							while ($rowd = mysql_fetch_array($resultd)) {
								$did = $rowd["id"];
								if($rowd["bin"] == "1") { // deleted meeting
									foreach($rowd as $key => $val) {
										$documents_folder[$key] = $val;
									}
									$documents_folder["bintime"] = $this->_date->formatDate($documents_folder["bintime"],CO_DATETIME_FORMAT);
									$documents_folder["binuser"] = $this->_users->getUserFullname($documents_folder["binuser"]);
									$documents_folders[] = new Lists($documents_folder);
									$arr["documents_folders"] = $documents_folders;
								} else {
									// files
									$qf ="select id, filename, bin, bintime, binuser from " . CO_TBL_PROCS_DOCUMENTS . " where did = '$did'";
									$resultf = mysql_query($qf, $this->_db->connection);
									while ($rowf = mysql_fetch_array($resultf)) {
										if($rowf["bin"] == "1") { // deleted phases
											foreach($rowf as $key => $val) {
												$file[$key] = $val;
											}
											$file["bintime"] = $this->_date->formatDate($file["bintime"],CO_DATETIME_FORMAT);
											$file["binuser"] = $this->_users->getUserFullname($file["binuser"]);
											$files[] = new Lists($file);
											$arr["files"] = $files;
										}
									}
								}
							}
						}
	
	
						// vdocs
						if(in_array("vdocs",$active_modules)) {
							$qv ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_VDOCS . " where pid = '$pid' and bin='1'";
							$resultv = mysql_query($qv, $this->_db->connection);
							while ($rowv = mysql_fetch_array($resultv)) {
								$vid = $rowv["id"];
									foreach($rowv as $key => $val) {
										$vdoc[$key] = $val;
									}
									$vdoc["bintime"] = $this->_date->formatDate($vdoc["bintime"],CO_DATETIME_FORMAT);
									$vdoc["binuser"] = $this->_users->getUserFullname($vdoc["binuser"]);
									$vdocs[] = new Lists($vdoc);
									$arr["vdocs"] = $vdocs;
							}
						}
						
				
					}
				}
			}
	  	}
		
		//print_r($arr);
		//$mod = new Lists($mods);

		return $arr;
   }
   
   
   function emptyBin() {
		global $procs;
		
		$bin = array();
		$bin["datetime"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		$arr = array();
		$arr["bin"] = $bin;
		
		$arr["folders"] = "";
		$arr["pros"] = "";
		$arr["procs_tasks"] = "";
		$arr["files"] = "";
		$arr["tasks"] = "";
		
		$active_modules = array();
		foreach($procs->modules as $module => $value) {
			if(CONSTANT('procs_'.$module.'_bin') == 1) {
				$active_modules[] = $module;
				$arr[$module] = "";
				$arr[$module . "_tasks"] = "";
				$arr[$module . "_folders"] = "";
				$arr[$module . "_cols"] = "";
			}
		}
		
		$q ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_FOLDERS;
		$result = mysql_query($q, $this->_db->connection);
	  	while ($row = mysql_fetch_array($result)) {
			$id = $row["id"];
			if($row["bin"] == "1") { // deleted folders
				$this->deleteFolder($id);
			} else { // folder not binned
				
				$qp ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS . " where folder = '$id'";
				$resultp = mysql_query($qp, $this->_db->connection);
				while ($rowp = mysql_fetch_array($resultp)) {
					$pid = $rowp["id"];
					if($rowp["bin"] == "1") { // deleted procs
						$this->deleteProc($pid);
					} else {
					
					
					// proc notes
					// remove in future .. in there for compatibility as notes get immediatly deleted
					$qt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_NOTES . " WHERE pid = '$pid'";
					$resultt = mysql_query($qt, $this->_db->connection);
					while ($rowt = mysql_fetch_array($resultt)) {
						$tid = $rowt["id"];
						if($rowt["bin"] == "1") { // deleted phases
							$this->deleteItem($tid);
							$arr["procs_tasks"] = "";
						} 
					}
					
					// pspgrids
					if(in_array("pspgrids",$active_modules)) {
						$procsPspgridsModel = new ProcsPspgridsModel();
						$qf ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_PSPGRIDS . " where pid = '$pid'";
						$resultf = mysql_query($qf, $this->_db->connection);
						while ($rowf = mysql_fetch_array($resultf)) {
							$fid = $rowf["id"];
							if($rowf["bin"] == "1") { // deleted phases
								$procsPspgridsModel->deletePspGrid($fid);
								$arr["pspgrids"] = "";
							} else {
								// columns
								
								$qc ="select id,bin from " . CO_TBL_PROCS_PSPGRIDS_COLUMNS . " where pid = '$fid'";
								$resultc = mysql_query($qc, $this->_db->connection);
								while ($rowc = mysql_fetch_array($resultc)) {
									$cid = $rowc["id"];
									if($rowc["bin"] == "1") { // deleted phases
										$procsPspgridsModel->deletePspgridColumn($cid);
										$arr["pspgrids_cols"] = "";
									} else {
										// notes
										$qt ="select id,bin from " . CO_TBL_PROCS_PSPGRIDS_NOTES . " where cid = '$cid'";
										$resultt = mysql_query($qt, $this->_db->connection);
										while ($rowt = mysql_fetch_array($resultt)) {
											if($rowt["bin"] == "1") { // deleted phases
												$tid = $rowt["id"];
												$procsPspgridsModel->deletePspgridTask($tid);
												$arr["pspgrids_tasks"] = "";
											} 
										}
									}
								}
							}
						}
					}
					
					// grids
					if(in_array("grids",$active_modules)) {
						$procsGridsModel = new ProcsGridsModel();
						$qf ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_GRIDS . " where pid = '$pid'";
						$resultf = mysql_query($qf, $this->_db->connection);
						while ($rowf = mysql_fetch_array($resultf)) {
							$fid = $rowf["id"];
							if($rowf["bin"] == "1") { // deleted phases
								$procsGridsModel->deleteGrid($fid);
								$arr["grids"] = "";
							} else {
								// columns
								
								$qc ="select id,bin from " . CO_TBL_PROCS_GRIDS_COLUMNS . " where pid = '$fid'";
								$resultc = mysql_query($qc, $this->_db->connection);
								while ($rowc = mysql_fetch_array($resultc)) {
									$cid = $rowc["id"];
									if($rowc["bin"] == "1") { // deleted phases
										$procsGridsModel->deleteGridColumn($cid);
										$arr["grids_cols"] = "";
									} else {
										// notes
										$qt ="select id,bin from " . CO_TBL_PROCS_GRIDS_NOTES . " where cid = '$cid'";
										$resultt = mysql_query($qt, $this->_db->connection);
										while ($rowt = mysql_fetch_array($resultt)) {
											if($rowt["bin"] == "1") { // deleted phases
												$tid = $rowt["id"];
												$procsGridsModel->deleteGridTask($tid);
												$arr["grids_tasks"] = "";
											} 
										}
									}
								}
							}
						}
					}

						// meetings
						if(in_array("meetings",$active_modules)) {
							$procsMeetingsModel = new ProcsMeetingsModel();
							$qm ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_MEETINGS . " where pid = '$pid'";
							$resultm = mysql_query($qm, $this->_db->connection);
							while ($rowm = mysql_fetch_array($resultm)) {
								$mid = $rowm["id"];
								if($rowm["bin"] == "1") { // deleted meeting
									$procsMeetingsModel->deleteMeeting($mid);
									$arr["meetings"] = "";
								} else {
									// meetings_tasks
									$qmt ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_MEETINGS_TASKS . " where mid = '$mid'";
									$resultmt = mysql_query($qmt, $this->_db->connection);
									while ($rowmt = mysql_fetch_array($resultmt)) {
										if($rowmt["bin"] == "1") { // deleted phases
											$mtid = $rowmt["id"];
											$procsMeetingsModel->deleteMeetingTask($mtid);
											$arr["meetings_tasks"] = "";
										}
									}
								}
							}
						}


						// documents_folder
						if(in_array("documents",$active_modules)) {
							$procsDocumentsModel = new ProcsDocumentsModel();
							$qd ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_DOCUMENTS_FOLDERS . " where pid = '$pid'";
							$resultd = mysql_query($qd, $this->_db->connection);
							while ($rowd = mysql_fetch_array($resultd)) {
								$did = $rowd["id"];
								if($rowd["bin"] == "1") { // deleted meeting
									$procsDocumentsModel->deleteDocument($did);
									$arr["documents_folders"] = "";
								} else {
									// files
									$qf ="select id, filename, bin, bintime, binuser from " . CO_TBL_PROCS_DOCUMENTS . " where did = '$did'";
									$resultf = mysql_query($qf, $this->_db->connection);
									while ($rowf = mysql_fetch_array($resultf)) {
										if($rowf["bin"] == "1") { // deleted phases
											$fid = $rowf["id"];
											$procsDocumentsModel->deleteFile($fid);
											$arr["files"] = "";
										}
									}
								}
							}
						}
	
	
						// vdocs
						if(in_array("vdocs",$active_modules)) {
							$procsVDocsModel = new ProcsVDocsModel();
							$qv ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS_VDOCS . " where pid = '$pid'";
							$resultv = mysql_query($qv, $this->_db->connection);
							while ($rowv = mysql_fetch_array($resultv)) {
								$vid = $rowv["id"];
								if($rowv["bin"] == "1") {
									$procsVDocsModel->deleteVDoc($vid);
									$arr["vdocs"] = "";
								}
							}
						}


					}
				}
			}
	  	}
		return $arr;
   }


	// User Access
	function getEditPerms($id) {
		global $session;
		$perms = array();
		$q = "SELECT a.pid FROM co_procs_access as a, co_procs as b WHERE a.pid=b.id and b.bin='0' and a.admins REGEXP '[[:<:]]" . $id . "[[:>:]]' ORDER by b.title ASC";
      	$result = mysql_query($q, $this->_db->connection);
		while($row = mysql_fetch_array($result)) {
			$perms[] = $row["pid"];
		}
		return $perms;
   }


   function getViewPerms($id) {
		global $session;
		$perms = array();
		$q = "SELECT a.pid FROM co_procs_access as a, co_procs as b WHERE a.pid=b.id and b.bin='0' and a.guests REGEXP '[[:<:]]" . $id. "[[:>:]]' ORDER by b.title ASC";
      	$result = mysql_query($q, $this->_db->connection);
		while($row = mysql_fetch_array($result)) {
			$perms[] = $row["pid"];
		}
		return $perms;
   }


   function canAccess($id) {
	   global $session;
	   return array_merge($this->getViewPerms($id),$this->getEditPerms($id));
   }


   function getProcAccess($pid) {
		global $session;
		$access = "";
		if(in_array($pid,$this->getViewPerms($session->uid))) {
			$access = "guest";
		}
		if(in_array($pid,$this->getEditPerms($session->uid))) {
			$access = "admin";
		}
		/*if($this->isOwnerPerms($pid,$session->uid)) {
			$access = "owner";
		}*/
		if($session->isSysadmin()) {
			$access = "sysadmin";
		}
		return $access;
   }
   
   
   /*function isOwnerPerms($id,$uid) {
	   	$q = "SELECT id FROM co_procs where id = '$id' and created_user ='$uid'";
      	$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
   }*/
   
   /*function isProcOwner($uid) {
	   	$q = "SELECT id FROM co_procs where created_user = '$uid'";
      	$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
   }*/
   
   
     function existUserProcsWidgets() {
		global $session;
		$q = "select count(*) as num from " . CO_TBL_PROCS_DESKTOP_SETTINGS . " where uid='$session->uid'";
		$result = mysql_query($q, $this->_db->connection);
		$row = mysql_fetch_assoc($result);
		if($row["num"] < 1) {
			return false;
		} else {
			return true;
		}
	}
	
	
	function getUserProcsWidgets() {
		global $session;
		$q = "select * from " . CO_TBL_PROCS_DESKTOP_SETTINGS . " where uid='$session->uid'";
		$result = mysql_query($q, $this->_db->connection);
		$row = mysql_fetch_assoc($result);
		return $row;
	}


   function getWidgetAlerts() {
		global $session, $date;
	  	
		$now = new DateTime("now");
		$today = $date->formatDate("now","Y-m-d");
		$tomorrow = $date->addDays($today, 1);
		$string = "";
		
		// procs notices for this user
		$q ="select a.id as pid,a.folder,a.title as proctitle,b.perm from " . CO_TBL_PROCS . " as a,  " . CO_TBL_PROCS_DESKTOP . " as b where a.id = b.pid and a.bin = '0' and b.uid = '$session->uid' and b.status = '0'";
		$result = mysql_query($q, $this->_db->connection);
		$notices = "";
		$array = "";
		while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$array[$key] = $val;
			}
			$string .= $array["folder"] . "," . $array["pid"] . ",";
			$notices[] = new Lists($array);
		}
		

		if(!$this->existUserProcsWidgets()) {
			$q = "insert into " . CO_TBL_PROCS_DESKTOP_SETTINGS . " set uid='$session->uid', value='$string'";
			$result = mysql_query($q, $this->_db->connection);
			$widgetaction = "open";
		} else {
			$row = $this->getUserProcsWidgets();
			$id = $row["id"];
			if($string == $row["value"]) {
				$widgetaction = "";
			} else {
				$widgetaction = "open";
			}
			$q = "UPDATE " . CO_TBL_PROCS_DESKTOP_SETTINGS . " set value='$string' WHERE id = '$id'";
			$result = mysql_query($q, $this->_db->connection);
		}
		
		$arr = array("notices" => $notices, "widgetaction" => $widgetaction);
		return $arr;
   }

   
	function markNoticeRead($pid) {
		global $session, $date;
		
		$q ="UPDATE " . CO_TBL_PROCS_DESKTOP . " SET status = '1' WHERE uid = '$session->uid' and pid = '$pid'";
		$result = mysql_query($q, $this->_db->connection);
		return true;

	}

  
	function getNavModulesNumItems($id) {
		global $procs;
		$active_modules = array();
		foreach($procs->modules as $module => $value) {
			$active_modules[] = $module;
		}
		if(in_array("pspgrids",$active_modules)) {
			$procsPspgridsModel = new ProcsPspgridsModel();
			$data["procs_pspgrids_items"] = $procsPspgridsModel->getNavNumItems($id);
		}
		if(in_array("grids",$active_modules)) {
			$procsGridsModel = new ProcsGridsModel();
			$data["procs_grids_items"] = $procsGridsModel->getNavNumItems($id);
		}
		if(in_array("drawings",$active_modules)) {
			$procsDrawingsModel = new ProcsDrawingsModel();
			$data["procs_drawings_items"] = $procsDrawingsModel->getNavNumItems($id);
		}
		if(in_array("meetings",$active_modules)) {
			$procsMeetingsModel = new ProcsMeetingsModel();
			$data["procs_meetings_items"] = $procsMeetingsModel->getNavNumItems($id);
		}
		if(in_array("documents",$active_modules)) {
			$procsDocumentsModel = new ProcsDocumentsModel();
			$data["procs_documents_items"] = $procsDocumentsModel->getNavNumItems($id);
		}
		if(in_array("vdocs",$active_modules)) {
			$procsVDocsModel = new ProcsVDocsModel();
			$data["procs_vdocs_items"] = $procsVDocsModel->getNavNumItems($id);
		}
		return $data;
	}


    function getCheckpointDetails($app,$module,$id){
		global $lang, $session, $procs;
		$row = "";
		if($app =='procs' && $module == 'procs') {
			$q = "SELECT title,folder FROM " . CO_TBL_PROCS . " WHERE id='$id' and bin='0'";
			$result = mysql_query($q, $this->_db->connection);
			$row = mysql_fetch_array($result);
			if(mysql_num_rows($result) > 0) {
				$row['checkpoint_app_name'] = $lang["PROC_TITLE"];
				$row['app_id_app'] = '0';
			}
			return $row;
		} else {
			$active_modules = array();
			foreach($procs->modules as $m => $v) {
					$active_modules[] = $m;
			}
			if($module == 'meetings' && in_array("meetings",$active_modules)) {
				include_once("modules/".$module."/config.php");
				include_once("modules/".$module."/lang/" . $session->userlang . ".php");
				include_once("modules/".$module."/model.php");
				$procsMeetingsModel = new ProcsMeetingsModel();
				$row = $procsMeetingsModel->getCheckpointDetails($id);
				return $row;
			}
		}
   }
   
   
	function getGlobalSearch($term){
		global $system, $session, $procs;
		$num=0;
		//$term = utf8_decode($term);
		$access=" ";
		if(!$session->isSysadmin()) {
			$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
	  	}
		$rows = array();
		$r = array();
		
		// get all active modules
		$active_modules = array();
		foreach($procs->modules as $m => $v) {
			$active_modules[] = $m;
		}
		

		$q = "SELECT id, folder, CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS . " WHERE title like '%$term%' and  bin='0'" . $access ."ORDER BY title";
		$result = mysql_query($q, $this->_db->connection);
		//$num=mysql_affected_rows();
		while($row = mysql_fetch_array($result)) {
			 $rows['value'] = htmlspecialchars_decode($row['title']);
			 $rows['id'] = 'procs,' .$row['folder']. ',' . $row['id'] . ',0,procs';
			 $r[] = $rows;
		}
		
		// loop through projects
		$q = "SELECT id, folder FROM " . CO_TBL_PROCS . " WHERE bin='0'" . $access ."ORDER BY title";
		$result = mysql_query($q, $this->_db->connection);
		while($row = mysql_fetch_array($result)) {
			$pid = $row['id'];
			$folder = $row['folder'];
			$sql = "";
			$perm = $this->getProcAccess($pid);
			if($perm == 'guest') {
				$sql = "and access = '1'";
			}
			// Notes
			$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_NOTES . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
			$resultp = mysql_query($qp, $this->_db->connection);
			while($rowp = mysql_fetch_array($resultp)) {
				$rows['value'] = htmlspecialchars_decode($rowp['title']);
			 	$rows['id'] = 'procs,' .$folder. ',' . $pid . ',0,procs';
			 	$r[] = $rows;
			}
			// PSP Grids
			$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_PSPGRIDS . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
			$resultp = mysql_query($qp, $this->_db->connection);
			while($rowp = mysql_fetch_array($resultp)) {
				$rows['value'] = htmlspecialchars_decode($rowp['title']);
			 	$rows['id'] = 'pspgrids,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
			 	$r[] = $rows;
			}
			// Grids
			$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_GRIDS . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
			$resultp = mysql_query($qp, $this->_db->connection);
			while($rowp = mysql_fetch_array($resultp)) {
				$rows['value'] = htmlspecialchars_decode($rowp['title']);
			 	$rows['id'] = 'grids,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
			 	$r[] = $rows;
			}
			// Meetings
			if(in_array("meetings",$active_modules)) {
				$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_MEETINGS . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
				$resultp = mysql_query($qp, $this->_db->connection);
				while($rowp = mysql_fetch_array($resultp)) {
					$rows['value'] = htmlspecialchars_decode($rowp['title']);
					$rows['id'] = 'meetings,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
					$r[] = $rows;
				}
				// Meeting Tasks
				$qp = "SELECT b.id,CONVERT(a.title USING latin1) as title FROM " . CO_TBL_PROCS_MEETINGS_TASKS . " as a, " . CO_TBL_PROCS_MEETINGS . " as b WHERE b.pid = '$pid' and a.mid = b.id and a.bin = '0' and b.bin = '0' $sql and a.title like '%$term%' ORDER BY a.title";
				$resultp = mysql_query($qp, $this->_db->connection);
				while($rowp = mysql_fetch_array($resultp)) {
					$rows['value'] = htmlspecialchars_decode($rowp['title']);
					$rows['id'] = 'meetings,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
					$r[] = $rows;
				}
			}
			// Doc Folders
			if(in_array("documents",$active_modules)) {
				$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_DOCUMENTS_FOLDERS . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
				$resultp = mysql_query($qp, $this->_db->connection);
				while($rowp = mysql_fetch_array($resultp)) {
					$rows['value'] = htmlspecialchars_decode($rowp['title']);
					$rows['id'] = 'documents,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
					$r[] = $rows;
				}
				// Documents
				$qp = "SELECT b.id,CONVERT(a.filename USING latin1) as title FROM " . CO_TBL_PROCS_DOCUMENTS . " as a, " . CO_TBL_PROCS_DOCUMENTS_FOLDERS . " as b WHERE b.pid = '$pid' and a.did = b.id and a.bin = '0' and b.bin = '0' and a.filename like '%$term%' ORDER BY a.filename";
				$resultp = mysql_query($qp, $this->_db->connection);
				while($rowp = mysql_fetch_array($resultp)) {
					$rows['value'] = htmlspecialchars_decode($rowp['title']);
					$rows['id'] = 'documents,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
					$r[] = $rows;
				}
			}
			// vDocs
			if(in_array("vdocs",$active_modules)) {
				$qp = "SELECT id,CONVERT(title USING latin1) as title FROM " . CO_TBL_PROCS_VDOCS . " WHERE pid = '$pid' and bin = '0' $sql and title like '%$term%' ORDER BY title";
				$resultp = mysql_query($qp, $this->_db->connection);
				while($rowp = mysql_fetch_array($resultp)) {
					$rows['value'] = htmlspecialchars_decode($rowp['title']);
					$rows['id'] = 'vdocs,' .$folder. ',' . $pid . ',' .$rowp['id'].',procs';
					$r[] = $rows;
				}
			}
		}
		return json_encode($r);
	}


	function getProcsSearch($term,$exclude){
		global $system, $session;
		$num=0;
		$access=" ";
		if(!$session->isSysadmin()) {
			$access = " and a.id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
	  	}
		
		$q = "SELECT a.id,a.title as label FROM " . CO_TBL_PROCS . " as a WHERE a.id != '$exclude' and a.title like '%$term%' and  a.bin='0' and a.folder!='0'" . $access ."ORDER BY a.title";
		
		$result = mysql_query($q, $this->_db->connection);
		$num=mysql_affected_rows();
		$rows = array();
		$r = array();
		/*while($r = mysql_fetch_assoc($result)) {
			 $rows[] = $r;
		}*/
		while($row = mysql_fetch_array($result)) {
			$rows['value'] = htmlspecialchars_decode($row['label']);
			$rows['id'] = $row['id'];
			$r[] = $rows;
		}
		return json_encode($r);
	}

	
	function getProcArray($string){
		$string = explode(",", $string);
		$total = sizeof($string);
		$items = '';
		
		if($total == 0) { 
			return $items; 
		}
		
		// check if user is available and build array
		$items_arr = "";
		foreach ($string as &$value) {
			$q = "SELECT id, title FROM ".CO_TBL_PROCS." where id = '$value' and bin='0'";
			$result = mysql_query($q, $this->_db->connection);
			if(mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_assoc($result)) {
					$items_arr[] = array("id" => $row["id"], "title" => $row["title"]);		
				}
			}
		}

		return $items_arr;
}
	
	function getLast10Procs() {
		global $session;
		$procs = $this->getProcArray($this->getUserSetting("last-used-procs"));
	  return $procs;
	}
	
	
	function saveLastUsedProcs($id) {
		global $session;
		$string = $id . "," .$this->getUserSetting("last-used-procs");
		$string = rtrim($string, ",");
		$ids_arr = explode(",", $string);
		$res = array_unique($ids_arr);
		foreach ($res as $key => $value) {
			$ids_rtn[] = $value;
		}
		array_splice($ids_rtn, 7);
		$str = implode(",", $ids_rtn);
		
		$this->setUserSetting("last-used-procs",$str);
	  return true;
	}
	
	
	function appCheckProcesslink($folder,$pid) {
		$q = "SELECT folder FROM " . CO_TBL_PROCS . " where id = '$pid'";
		$result = mysql_query($q, $this->_db->connection);
		$orig_folder = mysql_result($result,0);
		if($folder != 0 && $orig_folder != $folder) {
			return true;
		}
	}
	
	
	function moveFolderToArchive($fid) {
		global $session;
		
		$q = "SELECT title FROM " . CO_TBL_PROCS_FOLDERS . " WHERE id='$fid'";
		$result = mysql_query($q, $this->_db->connection);
		$folder = mysql_result($result,0);
		$now = gmdate("Y-m-d H:i:s");

		/*$access="";
		if(!$session->isSysadmin()) {
			$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
	  	}*/

		$q = "SELECT id FROM " . CO_TBL_PROCS . " WHERE folder='$fid' and bin='0' and link='0'";
		$result = mysql_query($q, $this->_db->connection);
	  	while ($row = mysql_fetch_array($result)) {
			$id = $row['id'];
			$qp = "UPDATE " . CO_TBL_PROCS . " set folder='0', checked_out='0', archive_folder='$folder', archive_time = '$now', archive_user= '$session->uid' where id='$id'";
			$resultp = mysql_query($qp, $this->_db->connection);
	  	}
		return true;
	}

	
	function movetoArchive($id,$fid) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		$q = "SELECT title FROM " . CO_TBL_PROCS_FOLDERS . " WHERE id='$fid'";
		$result = mysql_query($q, $this->_db->connection);
		$folder = mysql_result($result,0);
		
		$q = "UPDATE " . CO_TBL_PROCS . " set folder='0', checked_out='0', archive_folder='$folder', archive_time = '$now', archive_user= '$session->uid' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
		  	return true;
		}
	}

	
	
	function getArchiveList() {
       global $session;

	  $access = "";
	  if(!$session->isSysadmin()) {
		$access = " and (id IN (" . implode(',', $this->canAccess($session->uid)) . ") or link IN (" . implode(',', $this->canAccess($session->uid)) . ")) ";
	  }	  
	  $q ="select id,link,title,checked_out,checked_out_user from " . CO_TBL_PROCS . " where folder='0' and bin = '0' " . $access . " ORDER BY title";

      $result = mysql_query($q, $this->_db->connection);
	  $procs = "";
	  
	  while ($row = mysql_fetch_array($result)) {
		foreach($row as $key => $val) {
			$array[$key] = $val;
			if($key == "id") {
				if($this->getProcAccess($val) == "guest") {
					$array["access"] = "guest";
					$array["iconguest"] = ' icon-guest-active"';
					$array["checked_out_status"] = "";
				} else {
					$array["iconguest"] = '';
					$array["access"] = "";
				}
			}
		}
		
		$array["itemstatus"] = "";
		$array['outerid'] = $array['id'];
		
		//check for processlink
		/*if($array['folder'] != $id) {
			$array['title'] = $array['title'] . ' - Link!';
		}*/
		
		
		$checked_out_status = "";
		if($array["access"] != "guest" && $array["checked_out"] == 1 && $array["checked_out_user"] != $session->uid) {
			if($session->checkUserActive($array["checked_out_user"])) {
				$checked_out_status = "icon-checked-out-active";
			} else {
				$this->checkinProcOverride($id);
			}
		}
		$array["checked_out_status"] = $checked_out_status;
		
		if($array['link'] != 0) {
			$array["itemstatus"] = ' module-item-active-link';
			$linkid = $array['link'];
			$qlink = "select title,checked_out,checked_out_user from " . CO_TBL_PROCS . " where id='$linkid'";
			$rlink = mysql_query($qlink, $this->_db->connection);
			$rrow = mysql_fetch_row($rlink);
			$array['id'] = $linkid;
			$array['title'] = $rrow[0];
		}
		
		$procs[] = new Lists($array);
	  }
	  $arr = array("procs" => $procs, "sort" => "0");
	  return $arr;
   }
   
   function getArchive() {
		global $session, $contactsmodel, $procsControllingModel;
		/*$q = "SELECT * FROM " . CO_TBL_PROCS_FOLDERS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_assoc($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		$array["allprocs"] = $this->getNumProcs($id);
		$array["plannedprocs"] = $this->getNumProcs($id, $status="0");
		$array["activeprocs"] = $this->getNumProcs($id, $status="1");
		$array["inactiveprocs"] = $this->getNumProcs($id, $status="2");*/
		
		/*$array["created_date"] = $this->_date->formatDate($array["created_date"],CO_DATETIME_FORMAT);
		$array["edited_date"] = $this->_date->formatDate($array["edited_date"],CO_DATETIME_FORMAT);
		$array["created_user"] = $this->_users->getUserFullname($array["created_user"]);
		$array["edited_user"] = $this->_users->getUserFullname($array["edited_user"]);*/
		$array["today"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		
		$array["canedit"] = true;
		$array["access"] = "sysadmin";
 		if(!$session->isSysadmin()) {
			$array["canedit"] = false;
			$array["access"] = "guest";
		}
		
		$folder = new Lists($array);
		
		// get proc details
		$access="";
		if(!$session->isSysadmin()) {
			//$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
			$access = " and (id IN (" . implode(',', $this->canAccess($session->uid)) . ") or link IN (" . implode(',', $this->canAccess($session->uid)) . ")) ";
	  	}
		
		 /*$sortstatus = $this->getSortStatus("procs-sort-status",$id);
		if(!$sortstatus) {
		  	$order = "order by title";
		  } else {
			  switch($sortstatus) {
				  case "1":
				  		$order = "order by title";
				  break;
				  case "2":
				  		$order = "order by title DESC";
				  break;
				  case "3":
				  		$sortorder = $this->getSortOrder("procs-sort-order",$id);
				  		if(!$sortorder) {
						  	$order = "order by title";
						  } else {
							$order = "order by field(id,$sortorder)";
						  }
				  break;	
			  }
		  }*/
		$order = "order by title";

		$q = "SELECT link, title, id, created_date, created_user FROM " . CO_TBL_PROCS . " where folder='0' and bin='0'" . $access . " " . $order;

		$result = mysql_query($q, $this->_db->connection);
	  	$procs = "";
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$proc[$key] = $val;
			}
			
			$proc["created_date"] = $this->_date->formatDate($proc["created_date"],CO_DATE_FORMAT);
			$proc["created_user"] = $this->_users->getUserFullname($proc["created_user"]);
			$proc["perm"] = $this->getProcAccess($proc["id"]);
			
			
			if($proc['link'] != 0) {
				$array["itemstatus"] = ' module-item-active-link';
				$linkid = $proc['link'];
				$qlink = "select title,created_date,created_user from " . CO_TBL_PROCS . " where id='$linkid'";
				$rlink = mysql_query($qlink, $this->_db->connection);
				$rrow = mysql_fetch_row($rlink);
				$proc['id'] = $linkid;
				$proc['title'] = '<span class="proclink">&nbsp;</span>' . $rrow[0];
				$proc["created_date"] = $this->_date->formatDate($rrow[1],CO_DATE_FORMAT);
				$proc["created_user"] = $this->_users->getUserFullname($rrow[2]);
				$proc["perm"] = $this->getProcAccess($linkid);
			}
			
			$procs[] = new Lists($proc);
	  	}
		
		
		
		
		$access = "guest";
		  if($session->isSysadmin()) {
			  $access = "sysadmin";
		  }
		
		$arr = array("folder" => $folder, "procs" => $procs, "access" => $access);
		return $arr;
   }
   
   
   function getProcDetailsArchive($id, $fid=0) {
		global $session, $contactsmodel, $lang;
		$q = "SELECT * FROM " . CO_TBL_PROCS . " where id = '$id'";
		$result = mysql_query($q, $this->_db->connection);
		if(mysql_num_rows($result) < 1) {
			return false;
		}
		$row = mysql_fetch_array($result);
		foreach($row as $key => $val) {
			$array[$key] = $val;
		}
		
		// perms
		$array["access"] = $this->getProcAccess($id);

		if($array["access"] == "guest") {
			// check if this user is admin in some other proc
			$canEdit = $this->getEditPerms($session->uid);
			if(!empty($canEdit)) {
					$array["access"] = "guestadmin";
			}
		}
		$array['proclink'] = false;
		$array["canedit"] = false;
		$array["showCheckout"] = false;
		//$array["access"] = 'linkaccess';
		
		// check if there are links to this original
		$array["linktoproclink"] = false;
		if(!$array['proclink']) {
			// are there any 
			$qlink = "SELECT a.id,a.folder,b.title FROM " . CO_TBL_PROCS . " as a, " . CO_TBL_PROCS_FOLDERS . " as b WHERE a.link='$id' and a.folder = b.id and a.bin='0' and b.bin='0'";
			$result_links = mysql_query($qlink, $this->_db->connection);
			if(mysql_num_rows($result_links) > 0) {
				$array["linktoproclink"] = true;
				$arr = $this->getFolderList(0);
				$folders = $arr['folders'];
				$folder_ids = array();
				foreach ($folders as $folder) {
					$folder_ids[] = $folder->id;
				}
				$i = 0;
				$array["proclinksdetails"] = array();
				while($row_link = mysql_fetch_array($result_links)) {
					if(in_array($row_link['folder'], $folder_ids)) {
						$array["proclinksdetails"][$i]['folder'] = $row_link['folder'];
						$array["proclinksdetails"][$i]['folder_title'] = $row_link['title'];
						$array["proclinksdetails"][$i]['id'] = $id;
					}
					$i++;
				}
				if(empty($array["proclinksdetails"])) {
					$array["linktoproclink"] = false;
				}
			}
		}
		
		$array["canedit"] = false;
		$array["showCheckout"] = false;
		$array["checked_out_user_text"] = $contactsmodel->getUserListPlain($array['checked_out_user']);
		// dates
		$array["created_date"] = $this->_date->formatDate($array["created_date"],CO_DATETIME_FORMAT);
		$array["edited_date"] = $this->_date->formatDate($array["edited_date"],CO_DATETIME_FORMAT);
		$array["archive_time"] = $this->_date->formatDate($array["archive_time"],CO_DATETIME_FORMAT);
		$array["today"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		
		// other functions
		$array["folder_id"] = $array["folder"];
		//$array["folder"] = $array["archive_folder"];
		$array["folder"] = $array["archive_folder"];
		$array["created_user"] = $this->_users->getUserFullname($array["created_user"]);
		$array["edited_user"] = $this->_users->getUserFullname($array["edited_user"]);
		$array["archive_user"] = $this->_users->getUserFullname($array["archive_user"]);
		$array["current_user"] = $session->uid;
		
		// metas
		$metas_string = explode(",", $array["archive_meta"]);
		$metas_total = sizeof($metas_string);
		$meta = '';
		if($metas_total == 0) { 
			$array["archive_meta"] = "";
		} else {
			$i = 1;
			foreach ($metas_string as $key => &$value) {
				if(trim($value) != "") {
					$meta .= '<span class="meta-outer"><a href="archives" class="meta showItemContext">' . $value;		
					if($i < $metas_total) {
						$meta .= ', ';
					}
					$meta .= '</a></span>';	
				}
				$i++;
			}
			$array["archive_meta"] = $meta;
		}
		
		
		$proc = new Lists($array);
		
		$sql="";
		$now = gmdate("Y-m-d H:i:s");
		// Notes
		$q = "select * from " . CO_TBL_PROCS_NOTES . " where pid = '$id' and bin = '0'";
		
		$result = mysql_query($q, $this->_db->connection);
	  	$notes = "";
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$note[$key] = $val;
			}
			
			$days = $this->_date->dateDiff($note['edited_date'],$now);
			switch($days) {
				case 0:
					$note["days"] = $lang["GLOBAL_TODAY"];
				break;
				case 1:
					$note["days"] = $lang["GLOBAL_YESTERDAY"];
				break;
				default:
				$note["days"] = sprintf($lang["GLOBAL_DAYS_AGO"], $days);
			}
			
			$note["date"] = $this->_date->formatDate($note['edited_date'],CO_DATETIME_FORMAT);
			
			// dates
			$note["created_date"] = $this->_date->formatDate($note["created_date"],CO_DATETIME_FORMAT);
			$note["edited_date"] = $this->_date->formatDate($note["edited_date"],CO_DATETIME_FORMAT);
			
			// other functions
			$note["created_user"] = $this->_users->getUserFullname($note["created_user"]);
			$note["edited_user"] = $this->_users->getUserFullname($note["edited_user"]);
			
			$notes[] = new Lists($note);
	  	}
		
		$arr = array("proc" => $proc, "notes" => $notes, "access" => $array["access"]);
		return $arr;
   }
   
   
   function archiveRevive($id,$folder) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		
		$q = "UPDATE " . CO_TBL_PROCS . " set folder='$folder', archive_folder='', edited_date = '$now', edited_user= '$session->uid' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
		  	return true;
		}
	}
	
	function archiveDuplicate($id,$folder) {
		global $session;
		$now = gmdate("Y-m-d H:i:s");
		
		$dup = $this->createDuplicate($id);
		
		$q = "UPDATE " . CO_TBL_PROCS . " set folder='$folder', archive_folder='', edited_date = '$now', edited_user= '$session->uid' where id='$dup'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
		  	return true;
		}
	}
	
	function archiveSaveMeta($id,$meta) {
		global $session;
		
		$q = "UPDATE " . CO_TBL_PROCS . " set archive_meta='$meta' where id='$id'";
		$result = mysql_query($q, $this->_db->connection);
		
		if ($result) {
		  	return true;
		}
	}
	
	function getBinArchive() {
		global $procs,$lang;
		
		$bin = array();
		$bin["datetime"] = $this->_date->formatDate("now",CO_DATETIME_FORMAT);
		$arr = array();
		$arr["bin"] = $bin;
		
		$arr["pros"] = "";
		$pro["binheading"] = $lang["PROC_TITLE"];
		

				$qp ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS . " where bin='1' and folder='0'";
				$resultp = mysql_query($qp, $this->_db->connection);
				while ($rowp = mysql_fetch_array($resultp)) {
					$pid = $rowp["id"];
					if($rowp["bin"] == "1") { // deleted projects
						foreach($rowp as $key => $val) {
							$pro[$key] = $val;
						}
						$pro["bintime"] = $this->_date->formatDate($pro["bintime"],CO_DATETIME_FORMAT);
						$pro["binuser"] = $this->_users->getUserFullname($pro["binuser"]);
						$pros[] = new Lists($pro);
						$arr["pros"] = $pros;
					}
				//}
			//}
	  	}
		//print_r($arr);
		//$mod = new Lists($mods);
		return $arr;
   }
	
	function emptyBinArchive() {
		global $procs;
				$qp ="select id, title, bin, bintime, binuser from " . CO_TBL_PROCS . " where bin='1' and folder = '0'";
				$resultp = mysql_query($qp, $this->_db->connection);
				while ($rowp = mysql_fetch_array($resultp)) {
					$pid = $rowp["id"];
					if($rowp["bin"] == "1") { // deleted projects
						$this->deleteProc($pid);
					}
			}
		return true;
   }
	
	function doArchiveSearch($meta,$title,$folder,$who,$start,$end){
		global $session, $contactsmodel, $projectsControllingModel, $lang;
		 $access = "";
	  if(!$session->isSysadmin()) {
		$access = " and id IN (" . implode(',', $this->canAccess($session->uid)) . ") ";
	  }
	  $order = "order by a.title";
	  
	  $meta_sql = "";
	  if($meta != '') {
		  $meta_sql = " and archive_meta LIKE '%$meta%' ";
	  }
	  
	  $title_sql = "";
	  if($title != '') {
		  $title_sql = " and title LIKE '%$title%' ";
	  }
	  
	  $folder_sql = "";
	  if($folder != '') {
		  $folder_sql = " and archive_folder LIKE '%$folder%' ";
	  }
	  
	  $who_sql = "";
	  if($who != 0) {
		  $who_sql = " and created_user LIKE '%$who%' ";
	  }
	  
	  $date_sql = "";
	  if($start != '') {
	  	$start = $this->_date->formatDate($start, "Y-m-d");
	  	if($end != '') {
			$end = $this->_date->formatDate($end, "Y-m-d");
		} else {
			$end = date("Y-m-d");
		}
		$date_sql = " and (created_date >= '$start' && created_date <= '$end')";
	  }
		
		//$q = "SELECT a.title,a.id,a.created_user,a.status, (SELECT MIN(startdate) FROM " . CO_TBL_PROJECTS_PHASES_TASKS . " as b WHERE b.pid=a.id and b.bin = '0') as startdate ,(SELECT MAX(enddate) FROM " . CO_TBL_PROJECTS_PHASES_TASKS . " as b WHERE b.pid=a.id and b.bin = '0') as enddate FROM " . CO_TBL_PROJECTS . " as a where a.folder='0' and a.bin='0'" .$title_sql.$folder_sql.$who_sql.$meta_sql .$date_sql . $access . " " . $order;
		$q = "SELECT a.title,a.id,a.created_date, a.created_user FROM " . CO_TBL_PROCS . " as a WHERE a.folder='0' and a.bin='0'" .$title_sql.$folder_sql.$who_sql.$meta_sql .$date_sql . $access . " " . $order;

		$result = mysql_query($q, $this->_db->connection);
	  	$procs = "";
	  	while ($row = mysql_fetch_array($result)) {
			foreach($row as $key => $val) {
				$proc[$key] = $val;
			}
			$proc["created_date"] = $this->_date->formatDate($proc["created_date"],CO_DATE_FORMAT);
			//$proc["enddate"] = $this->_date->formatDate($project["enddate"],CO_DATE_FORMAT);
			//$proc["realisation"] = $projectsControllingModel->getChart($project["id"], "realisation", 0);
			$proc["created_user"] = $contactsmodel->getUserListPlain($proc['created_user']);
			/*$proc["perm"] = $this->getProjectAccess($project["id"]);
			
			switch($project["status"]) {
				case "0":
					$project["status_text"] = $lang["GLOBAL_STATUS_PLANNED"];
				break;
				case "1":
					$project["status_text"] = $lang["GLOBAL_STATUS_INPROGRESS"];
				break;
				case "2":
					$project["status_text"] = $lang["GLOBAL_STATUS_FINISHED"];
				break;
				case "3":
					$project["status_text"] = $lang["GLOBAL_STATUS_STOPPED"];
				break;
			}*/
			
			$procs[] = new Lists($proc);
	  	}
	  
	  $arr = array("procs" => $procs, "sort" => "0");
	  
	  return $arr;
	}
   
   
   
   function getProcFolderArchiveDialog($field,$title) {
		global $session;
		$str = '<div class="dialog-text">';
		//$q ="select id, title from " . CO_TBL_PROCS_FOLDERS . " where status='0' and bin = '0' ORDER BY title";
		if(!$session->isSysadmin()) {
			$q ="select a.id, a.title from " . CO_TBL_PROCS_FOLDERS . " as a where a.status='0' and a.bin = '0' and (SELECT count(*) FROM co_procs_access as b, co_procs as c WHERE (b.admins REGEXP '[[:<:]]" . $session->uid . "[[:>:]]' or b.guests REGEXP '[[:<:]]" . $session->uid . "[[:>:]]') and c.folder=a.id and b.pid=c.id) > 0 ORDER BY title";
		} else {
			$q ="select id, title from " . CO_TBL_PROCS_FOLDERS . " where status='0' and bin = '0' ORDER BY title";
		}
		$result = mysql_query($q, $this->_db->connection);
		while ($row = mysql_fetch_array($result)) {
			$str .= '<a href="#" class="insertProcFolderfromArchiveDialog" title="' . $row["title"] . '" field="'.$field.'" gid="'.$row["id"].'">' . $row["title"] . '</a>';
		}
		$str .= '</div>';	
		return $str;
	 }
	 
	 

	
	
}

$procsmodel = new ProcsModel(); // needed for direct calls to functions eg echo $procsmodel ->getProcTitle(1);
?>