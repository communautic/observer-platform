<?php
include("database.php");
include("users.php");
include("system.php");
include("form.php");
include("date.php");
include("class.phpmailer-lite.php");
include("PasswordHash.php");

class Session
{
   var $username;     //Username given on sign-up
   var $userid;       //Random value generated on current login
   var $userlevel;    //The level to which the user pertains
   var $uid;
   var $userlang;
   var $firstname;
   var $lastname;
   var $timezone;      	//users timezone
   var $time;         //Time user was last active (page loaded)
   var $logged_in;    //True if user is logged in, false otherwise
   var $userinfo = array();  //The array holding all user info
   var $url;          //The page url current being viewed
   var $referrer;     //Last recorded site page viewed
    var $pwd_pick;     //Last recorded site page viewed
	var $calendar;
	var $calendarViewAll;
   //var $canView = array();
   //var $canEdit = array();
   //var $canAccess = array();
   /**
    * Note: referrer should really only be considered the actual
    * page referrer in process.php, any other time it may be
    * inaccurate.
    */

   /* Class constructor */
   function Session(){
      $this->time = time();
      $this->startSession();
   }

   /**
    * startSession - Performs all the actions necessary to 
    * initialize this session object. Tries to determine if the
    * the user has logged in already, and sets the variables 
    * accordingly. Also takes advantage of this page load to
    * update the active visitors tables.
    */
   function startSession(){
      global $database;  //The database connection
      session_start();   //Tell PHP to start the session

      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /**
       * Set guest value to users not logged in, and update
       * active guests table accordingly.
       */
      if(!$this->logged_in){
         //$this->username = $_SESSION['username'] = GUEST_NAME;
         $this->userlevel = GUEST_LEVEL;
		 $this->userlang = CO_DEFAULT_LANGUAGE;
		 $this->timezone = "Europe/Vienna";
         $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      }
      /* Update users last active timestamp */
      else{
         $database->addActiveUser($this->uid, $this->username, $this->time);
      }
      
      /* Remove inactive visitors from database */
      $database->removeInactiveUsers();
      $database->removeInactiveGuests();
      
      /* Set referrer page */
      if(isset($_SESSION['url'])){
         $this->referrer = $_SESSION['url'];
      }else{
         $this->referrer = "/";
      }

      /* Set current url */
      $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
   }

   /**
    * checkLogin - Checks if the user has already previously
    * logged in, and a session with the user has already been
    * established. Also checks to see if user has been remembered.
    * If so, the database is queried to make sure of the user's 
    * authenticity. Returns true if the user has logged in.
    */
   function checkLogin(){
      global $database;  //The database connection
      /* Check if user has been remembered */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
		 $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
         $this->userid   = $_SESSION['userid']   = $_COOKIE['cookid'];
      }

      /* Username and userid have been set and not guest */
      if(isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != GUEST_NAME){
         /* Confirm that username and userid are valid */
         if($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0){
            //unset($_SESSION['username']);
            unset($_SESSION['userid']);
			if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
				setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
				setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
      		}
            return false;
         }

         /* User is logged in, set class variables */
         $this->userinfo  = $database->getUserInfo($_SESSION['username']);
         $this->username  = $this->userinfo['username'];
         $this->userid    = $this->userinfo['userid'];
         $this->userlevel = $this->userinfo['userlevel'];
		 $this->firstname = $this->userinfo['firstname'];
		 $this->lastname = $this->userinfo['lastname'];
		 $this->email = $this->userinfo['email'];
		 $this->uid = $this->userinfo['id'];
		 $this->userlang = $this->userinfo['lang'];
		 if($this->userlang == '') { $this->userlang = CO_DEFAULT_LANGUAGE; }
		 $this->useroffset = $this->userinfo['offset'];
		 $this->timezone = $this->userinfo['timezone'];
		 $this->pwd_pick = $this->userinfo['pwd_pick'];
		 $this->calendar = $this->userinfo['calendar'];
		 $this->calendarViewAll = $this->userinfo['calendars_view_all'];
		 //$this->canView = "";
		 /*if (!$this->isSysadmin()) {
			 $this->canView = $database->getViewPerms($this->uid);
			 $this->canEdit = $database->getEditPerms($this->uid);
			 $this->canAccess = array_merge($this->canView,$this->canEdit);
		 }*/
		 
         return true;
      }
      /* User not logged in */
      else{
         return false;
      }
   }

   /**
    * login - The user has submitted his username and password
    * through the login form, this function checks the authenticity
    * of that information in the database and creates the session.
    * Effectively logging in the user if all goes well.
    */
   function login($subuser, $subpass, $subremember){
      global $database, $form;  //The database and form object

      /* Username error checking */
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered");
      }
      else{
         /* Check if username is not alphanumeric */
         if(!preg_match("/^([0-9a-z])*$/i", $subuser)) {
            $form->setError($field, "* Username not alphanumeric");
         }
      }

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if(!$subpass){
         $form->setError($field, "* Password not entered");
      }
      
      /* Return if form errors exist */
      if($form->num_errors > 0){
         return false;
      }

      /* Checks that username is in database and password is correct */
      $subuser = stripslashes($subuser);
      //$result = $database->confirmUserPass($subuser, md5($subpass));
	  $result = $database->confirmUserPass($subuser, $subpass);

      /* Check error codes */
      if($result == 1){
         $field = "user";
         $form->setError($field, "* Username not found");
      }
      else if($result == 2){
         $field = "pass";
         $form->setError($field, "* Invalid password");
      }
      
      /* Return if form errors exist */
      if($form->num_errors > 0){
         return false;
      }

      /* Username and password correct, register session variables */
      $this->userinfo  = $database->getUserInfo($subuser);
      $this->username  = $_SESSION['username'] = $this->userinfo['username'];
      $this->userid    = $_SESSION['userid']   = $this->generateRandID();
      $this->userlevel = $this->userinfo['userlevel'];
	  $this->uid = $this->userinfo['id'];
      
      /* Insert userid into database and update active users table */
      $database->updateUserField($this->username, "userid", $this->userid);
      $database->addActiveUser($this->uid, $this->username, $this->time);
      $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      /**
       * This is the cool part: the user has requested that we remember that
       * he's logged in, so we set two cookies. One to hold his username,
       * and one to hold his random value userid. It expires by the time
       * specified in constants.php. Now, next time he comes to our site, we will
       * log him in automatically, but only if he didn't log out before he left.
       */
      if($subremember){
         setcookie("cookname", $this->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   $this->userid,   time()+COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Login completed successfully */
      return true;
   }
   
   
	function changeLogin($username, $password){
		global $database;  //The database and form object 
		$database->updateUser($this->uid, "username", $username);
		//$database->updateUser($this->uid, "password", md5($password));
		$hasher = new PasswordHash(8, 0);
		$hash = $hasher->HashPassword($password.PASSWORDSALT);
		$database->updateUser($this->uid, "password", $hash);
		$database->updateUser($this->uid, "pwd_pick", '1');
		
		//check for calendar
		if($database->checkCalendar($this->uid)) {
			$database->updateUserCalendar($this->uid,$username,$hash);
		}
		
		return true;
	}

   /**
    * logout - Gets called when the user wants to be logged out of the
    * website. It deletes any cookies that were stored on the users
    * computer as a result of him wanting to be remembered, and also
    * unsets session variables and demotes his user level to guest.
    */
   function logout(){
      global $database;  //The database connection
      /**
       * Delete cookies - the time must be in the past,
       * so just negate what you added when creating the
       * cookie.
       */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
         setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['userid']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;
      
      /**
       * Remove from active users table and add to
       * active guests tables.
       */
      $database->removeActiveUser($this->username);
      $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      
      /* Set user level to guest */
      $this->username  = GUEST_NAME;
      $this->userlevel = GUEST_LEVEL;
   }
   
   function checkUsername($username) {
	   global $database;
	   return $database->usernameTaken($username);
   }

   
   /**
    * editAccount - Attempts to edit the user's account information
    * including the password, which it first makes sure is correct
    * if entered, if so and the new password is in the right
    * format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($subcurpass, $subnewpass, $subemail){
      global $database, $form;  //The database and form object
      /* New password entered */
      if($subnewpass){
         /* Current Password error checking */
         $field = "curpass";  //Use field name for current password
         if(!$subcurpass){
            $form->setError($field, "* Current Password not entered");
         }
         else{
            /* Check if password too short or is not alphanumeric */
            $subcurpass = stripslashes($subcurpass);
            if(strlen($subcurpass) < 4 ||
			   !preg_match("/^([0-9a-z])+$/i", ($subcurpass = trim($subcurpass)))){
               $form->setError($field, "* Current Password incorrect");
            }
            /* Password entered is incorrect */
            if($database->confirmUserPass($this->username,md5($subcurpass)) != 0){
               $form->setError($field, "* Current Password incorrect");
            }
         }
         
         /* New Password error checking */
         $field = "newpass";  //Use field name for new password
         /* Spruce up password and check length*/
         $subpass = stripslashes($subnewpass);
         if(strlen($subnewpass) < 4){
            $form->setError($field, "* New Password too short");
         }
         /* Check if password is not alphanumeric */
	     else if(!preg_match("/^([0-9a-z])+$/i", ($subnewpass = trim($subnewpass)))){
            $form->setError($field, "* New Password not alphanumeric");
         }
      }
      /* Change password attempted */
      else if($subcurpass){
         /* New Password error reporting */
         $field = "newpass";  //Use field name for new password
         $form->setError($field, "* New Password not entered");
      }
      
      /* Email error checking */
      $field = "email";  //Use field name for email
      if($subemail && strlen($subemail = trim($subemail)) > 0){
         /* Check if valid email address */
         $regex = "/^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                 ."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                 ."\.([a-z]{2,}){1}$/i";
         if(!preg_match($regex,$subemail)){
            $form->setError($field, "* Email invalid");
         }
         $subemail = stripslashes($subemail);
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         return false;  //Errors with form
      }
      
      /* Update password since there were no errors */
      if($subcurpass && $subnewpass){
         $database->updateUserField($this->username,"password",md5($subnewpass));
      }
      
      /* Change Email */
      if($subemail){
         $database->updateUserField($this->username,"email",$subemail);
      }
      
      /* Success! */
      return true;
   }
   
   
   	/*function formatDate($date, $format = '%Y-%m-%d %H:%M:%S') {
		$offset = $this->useroffset * 3600;
		$dates = ($date !== false && $date != '' && $date != '0000-00-00') ? strftime($format, strtotime($date) + $offset) : null;
		return $dates;
	}
	
	function formatDateReverse($date, $format = '%Y-%m-%d %H:%M:%S') {
		$offset = $this->useroffset * 3600;
		$dates = ($date !== false && $date != '' && $date != '0000-00-00') ? strftime($format, strtotime($date) - $offset) : null;
		return $dates;
	}*/
	
	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
	  
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
	  
		$bytes /= pow(1024, $pow);
	  
		return round($bytes, $precision) . ' ' . $units[$pow];
	}
   
   
   
  /* function editUser($id, $lastname, $firstname, $title, $position, $phone1, $phone2, $fax, $address){
      global $database;
	  
         $database->updateUser($this->uid, $id, "lastname", $lastname);
		 $database->updateUser($this->uid, $id, "firstname", $firstname);
		 $database->updateUser($this->uid, $id, "title", $title);
		 $database->updateUser($this->uid, $id, "position", $position);
		 $database->updateUser($this->uid, $id, "phone1", $phone1);
		 $database->updateUser($this->uid, $id, "phone2", $phone2);
		 $database->updateUser($this->uid, $id, "fax", $fax);
		 $database->updateUser($this->uid, $id, "address", $address);
      
      return true;
   }*/
   
	function editGroup($id, $name, $users2delete='', $users2add=''){
		global $database;
		
		$database->updateGroup($this->uid, $id, "name", $name);
		
		if($users2delete != '') {
			$users2delete = explode(",", $users2delete);
			foreach ($users2delete as &$value) {
				$database->delUserFromGroup($id,$value);
			}
		}
		
		if($users2add != '') {
			$users2add = explode(",", $users2add);
			foreach ($users2add as &$value) {
				$database->addUserToGroup($id,$value);
			}
		}
		
		
      
      /* Success! */
      return true;
   }

   // check if user is admin in any of the projects
   //function isAdmin(){
	 // return !empty($this->canEdit);
	  //return "test";
      //return ($this->userlevel == ADMIN_LEVEL || $this->username  == ADMIN_NAME);
   //}
   
   function isSysadmin(){
      return ($this->userlevel == SYSADMIN_LEVEL ||
              $this->username  == SYSADMIN_NAME);
   }
   
   function getAccess($pid) {
		$access = "";
		if(in_array($pid,$this->canView)) {
			$access = "guest";
		}
		if(in_array($pid,$this->canEdit)) {
			$access = "admin";
		}
		if($this->isSysadmin()) {
			$access = "sysadmin";
		}
		return $access;
   }
   
   /**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a userid.
    */
   function generateRandID(){
      return md5($this->generateRandStr(16));
   }
   
   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(0,61);
         if($randnum < 10){
            $randstr .= chr($randnum+48);
         }else if($randnum < 36){
            $randstr .= chr($randnum+55);
         }else{
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }



   function generateAccessUsername($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(65,90);
		 $randstr .= chr($randnum);
      }
      return $randstr;
   }
   
   function generateAccessPassword($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(1,9);
         $randstr .= chr($randnum+48);
      }
      return $randstr;
   }
   
   function checkUserActive($id) {
	   global $database;
	   return $database->checkUserActive($id);
   }
   
   function strToSafeURL($str) {
		setlocale(LC_ALL, 'en_US.UTF8');
		$str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
		return $str;
   }


}

/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;
/* Initialize form object */
$form = new Form;
include_once(CO_INC . "/lang/" . $session->userlang . ".php");
?>