<?php
$projects_documents_name = "Files";

$lang["PROJECT_DOCUMENT_TITLE"] = 'File';
$lang["PROJECT_DOCUMENT_DOCUMENTS"] = 'Files';
$lang["PROJECT_DOCUMENT_NEW"] = 'New File';
$lang["PROJECT_DOCUMENT_ACTION_NEW"] = 'new File';
$lang["PROJECT_DOCUMENT_DESCRIPTION"] = 'Description';
$lang["PROJECT_DOCUMENT_UPLOAD"] = 'File / Upload';
$lang["PROJECT_DOCUMENT_FILENAME"] = 'Filename/Format';
$lang["PROJECT_DOCUMENT_FILESIZE"] = 'Filesize';
$lang["PROJECT_DOCUMENT_FILES"] = 'Files';

$lang["PROJECT_DOCUMENT_HELP"] = 'manual_projekte_aktenmappen.pdf';

$lang["PROJECT_PRINT_DOCUMENT"] = 'document.png';

// check for custom lang file
$custom_lang = CO_PATH_BASE . "/lang/projects/documents/en.php";
if(file_exists($custom_lang)) {
	include_once($custom_lang);
}
?>