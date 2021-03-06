<?php
include_once(CO_INC . "/classes/session.php");
if(!$session->logged_in){
	include(CO_INC . "/login/login.php");
	exit();
}
if($session->pwd_pick != 1) {
	include(CO_INC . "/login/firstlogin.php");
	exit();
}
include_once(CO_INC . "/model.php");
include_once(CO_INC . "/controller.php");
foreach($controller->applications as $app => $display) {
	include_once(CO_INC . "/apps/".$app."/config.php");
	include_once(CO_INC . "/apps/".$app."/lang/" . $session->userlang . ".php");
	include_once(CO_INC . "/apps/".$app."/model.php");
	include_once(CO_INC . "/apps/".$app."/controller.php");
}
// build user apps array
$num_apps = 0;
$userapps = array();
if($session->isSysadmin()) {
	foreach($controller->applications as $app => $display) {
		$userapps[] = $app;
		$num_apps++;
	}
} else {
	$adminstatus = 0;
	foreach($controller->applications as $app => $display) {
		if($app == "desktop") {
			$userapps[] = $app;	
		}
		if($app == "contacts") {
			$userapps[] = $app;	
		}
		if($app == "calendar") {
			$userapps[] = $app;	
		}
		if($app == "archives") {
			$userapps[] = $app;	
		}
		foreach(${$app}->modules as $module => $value) {
			if($module == 'access') {
				if(${$app}->isAdmin()) {
					$adminstatus = 1;
					$userapps[] = $app;				
				} else if(${$app}->isGuest()) {
					$userapps[] = $app;
				} else {
					
				}
			}
		}
		$num_apps++;
	}
	if($adminstatus == 0) {
		$userapps = array_values(array_diff($userapps,array('calendar')));
		$userapps = array_values(array_diff($userapps,array('contacts')));
		$userapps = array_values(array_diff($userapps,array('desktop')));
	}
}
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="msapplication-TileImage" content="<?php echo CO_FILES;?>/img/favicon_touch.png"> 
<title><?php echo $lang["APPLICATION_NAME"];?></title>
<link rel="icon" type="image/x-icon" href="<?php echo CO_FILES;?>/img/favicon.ico" sizes="64x64" />
<link rel="apple-touch-icon" href="<?php echo CO_FILES;?>/img/favicon_touch.png"/>
<link href="<?php echo CO_FILES;?>/css/reset.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/styles.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/content.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/jNice.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/impromptu.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/fileuploader.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/jquery.hoverscroll.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/popup.css" rel="stylesheet" type="text/css" media="screen,projection" />
<link href="<?php echo CO_FILES;?>/css/mobile.css" type="text/css" rel="stylesheet" media="only screen and (min-device-width: 768px) and (max-device-width: 1024px)" />
<!-- include app specific css -->
<?php
foreach($controller->applications as $app => $display) {
	echo '<link href="' . CO_FILES . '/css/apps/' . $app . '.css" rel="stylesheet" type="text/css" />';
} 
?>
<!--[if gte IE 9]>
<link href="<?php echo CO_FILES;?>/css/ie.css" rel="stylesheet" type="text/css" media="screen,projection" />
<![endif]-->
<script type="text/javascript">
var num_apps = <?php echo($num_apps);?>;
var co_files = '<?php echo CO_FILES;?>';
var co_lang = '<?php echo $session->userlang;?>';
var co_invoice_start = <?php echo CO_INVOICE_START;?>;
</script>
<script src="<?php echo $protocol;?>ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript"></script>
<!--<script src="<?php echo CO_FILES;?>/js/libraries/jquery-migrate-1.2.1.js"></script>-->
<!--<script>$.uiBackCompat = false;</script>-->
<script type="text/javascript" src="<?php echo $protocol;?>ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.onerror.js"></script>-->
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/datejs/date.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/datejs/de-AT.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/datejs/time.js"></script>
<?php if($session->userlang != 'en') { ?>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/ui.datepicker-<?php echo($session->userlang); ?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.livequery.min.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.layout-1.3.0.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.jNice.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery-impromptu.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/fileuploader.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.scrollTo.min.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.elastic.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.hoverscroll.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.ba-throttle-debounce.min.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/libraries/jquery.number.min.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/lang/<?php echo($session->userlang); ?>.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/init.js"></script>
<?php // include mobile js
if($controller->isIOS()){ ?>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/mobile/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/mobile/jquery.doubletap.js"></script>
<script type="text/javascript" src="<?php echo CO_FILES;?>/js/mobile/init.js"></script>
<?php }?>

<script type="text/javascript">
<?php // set app init display vars
foreach($controller->applications as $app => $display) {
echo "var " . $app . "_num_modules = " . ${$app}->num_modules . ";\n";
}
?>
</script>
<?php // build used modules array
$modules_toload = array();
foreach($userapps as $key => $app) {
	foreach(${$app}->modules as $module => $value) {
		$modules_toload[] = $module;
	}
}
// include app specific js
foreach(array_unique($modules_toload) as $key => $value) {
	if($value == 'meetings' || $value == 'services' || $value == 'documents' || $value == 'phonecalls' || $value == 'vdocs' || $value == 'access' || $value == 'controlling' || $value == 'phases' || $value == 'timelines') {
	echo '<script type="text/javascript" src="' . CO_FILES . '/js/modules/' . $value . '.js"></script>'."\n";
	}
}
foreach($userapps as $key => $app) {
	echo '<script type="text/javascript" src="' . CO_FILES . '/js/apps/' . $app . '/init.js"></script>';
	foreach(${$app}->modules as $module => $value) {
		if($module != 'meetings' && $module != 'services' && $module != 'documents' && $module != 'phonecalls' && $module != 'vdocs' && $module != 'access' && $module != 'controlling' && $module != 'phases' && $module != 'timelines') {
			echo '<script type="text/javascript" src="' . CO_FILES . '/js/apps/' . $app . '/' . $module . '.js"></script>';
		} else { ?>
        <script>
			var <?php echo $app;?>_<?php echo $module;?> = new <?php echo ucfirst($module);?>('<?php echo $app;?>');</script>
		<?php }
	}
}
// watermark
$watermark = CO_FILES .'/img/watermark.png';
if(file_exists(CO_PATH_DATA . '/watermark.png')) {
	$watermark =  CO_PATH_URL . '/data/watermark.png';
}
?>
</head>
<body>
<div id="background"><img src="<?php echo CO_FILES;?>/img/background.jpg" style="width: 100%; height: 100%; min-height: 616px; min-width: 1024px;" /><img id="watermark" src="<?php echo $watermark;?>" /></div>
<div id="intro"><div id="intro-content"><p><img src="<?php echo CO_FILES;?>/img/ajax-loader.gif" alt="Loading" width="32" height="32" /></p></div></div>
<div id="container">
<div id="container-inner">
<?php
foreach($userapps as $key => $app) {
	include(CO_INC . "/apps/".$app . "/view.php");
}?>
</div>
<?php
include(CO_INC . "/view/footer.php");
?>
</div>
<div id="modalDialog"></div>
<div id="modalDialogTime"></div>
<div id="modalDialogTimeCalendar"></div>
<div id="modalDialogForward" title="<?php echo $lang["GLOBAL_FORWARD"];?>"></div>
<div id="co-popup" class="switch-left"></div>
<div id="co-splitActions" class="switch-left"></div>
<iframe id="documentloader" name="documentloader" src="about:blank" style="position: absolute; top: -1000px; left: -1000px;" /></iframe>
</body>
</html>