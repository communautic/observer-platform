<?php
$complaints_forums_name = "Forums";

$lang["COMPLAINT_FORUM_TITLE"] = 'Forum';
$lang["COMPLAINT_FORUMS"] = 'Forums';

$lang["COMPLAINT_FORUM_NEW"] = 'New Forum';
$lang["COMPLAINT_FORUM_ACTION_NEW"] = 'new Forum';
$lang["COMPLAINT_FORUM_TASK_NEW"] = 'New Item';
//define('FORUM_RELATES_TO', 'bezogen auf');
$lang["COMPLAINT_FORUM_DATE"] = 'Date';
$lang["COMPLAINT_FORUM_PLACE"] = 'Location';
$lang["COMPLAINT_FORUM_TIME_START"] = 'Start';
$lang["COMPLAINT_FORUM_TIME_END"] = 'End';

$lang["COMPLAINT_FORUM_ATTENDEES"] = 'Attendees';
$lang["COMPLAINT_FORUM_MANAGEMENT"] = 'Minuted by';
$lang["COMPLAINT_FORUM_GOALS"] = 'Agenda';

$lang["COMPLAINT_FORUM_HELP"] = 'manual_reklamationen_diskussionen.pdf';

$lang["COMPLAINT_PRINT_FORUM"] = 'discussion.png';

// check for custom lang file
$custom_lang = CO_PATH_BASE . "/lang/complaints/forums/en.php";
if(file_exists($custom_lang)) {
	include_once($custom_lang);
}
?>