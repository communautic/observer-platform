<?php
$procs_documents_name = "Files";

$lang["PROC_DOCUMENT_TITLE"] = 'File';
$lang["PROC_DOCUMENT_DOCUMENTS"] = 'Files';
$lang["PROC_DOCUMENT_NEW"] = 'New File';
$lang["PROC_DOCUMENT_ACTION_NEW"] = 'new File';
$lang["PROC_DOCUMENT_DESCRIPTION"] = 'Description';
$lang["PROC_DOCUMENT_UPLOAD"] = 'File / Upload';
$lang["PROC_DOCUMENT_FILENAME"] = 'Filename/Format';
$lang["PROC_DOCUMENT_FILESIZE"] = 'Filesize';
$lang["PROC_DOCUMENT_FILES"] = 'Files';

$lang["PROC_DOCUMENT_HELP"] = 'manual_prozesse_aktenmappen.pdf';

$lang["PROC_PRINT_DOCUMENT"] = 'document.png';

// check for custom lang file
$custom_lang = CO_PATH_BASE . "/lang/procs/documents/en.php";
if(file_exists($custom_lang)) {
	include_once($custom_lang);
}
?>