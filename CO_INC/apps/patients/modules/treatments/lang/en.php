<?php
$patients_treatments_name = "Treatments";

$lang["PATIENT_TREATMENT_TITLE"] = 'Treatment';
$lang["PATIENT_TREATMENTS"] = 'Treatments';

$lang["PATIENT_TREATMENT_NEW"] = 'New Treatment';
$lang["PATIENT_TREATMENT_ACTION_NEW"] = 'create new Treatment';
$lang["PATIENT_TREATMENT_TASK_NEW"] = 'new Session';

$lang["PATIENT_TREATMENT_STATUS_PLANNED"] = 'in diagnosis';
$lang["PATIENT_TREATMENT_STATUS_PLANNED_TIME"] = '';
$lang["PATIENT_TREATMENT_STATUS_INPROGRESS"] = 'in treatment';
$lang["PATIENT_TREATMENT_STATUS_INPROGRESS_TIME"] = '';
$lang["PATIENT_TREATMENT_STATUS_FINISHED"] = 'completed';
$lang["PATIENT_TREATMENT_STATUS_FINISHED_TIME"] = '';
$lang["PATIENT_TREATMENT_STATUS_STOPPED"] = 'cancelled';
$lang["PATIENT_TREATMENT_STATUS_STOPPED_TIME"] = '';

$lang["PATIENT_TREATMENT_DURATION"] = 'Treatment duration';
$lang["PATIENT_TREATMENT_DATE"] = 'Date of diagnosis';
$lang["PATIENT_TREATMENT_DOCTOR"] = 'Doctor';
$lang["PATIENT_TREATMENT_DOCTOR_DIAGNOSE"] = 'Medical findings';
$lang["PATIENT_TREATMENT_METHOD"] = 'Treatment method';
$lang["PATIENT_TREATMENT_PRESCRIPTION_PHYSIO"] = 'Treatments';
$lang["PATIENT_TREATMENT_PRESCRIPTION_THERAPY"] = 'Treatments';
$lang["PATIENT_TREATMENT_ACHIEVMENT_STATUS_PHYSIO"] = 'Sitzungsstatus';
$lang["PATIENT_TREATMENT_ACHIEVMENT_STATUS_THERAPY"] = 'Leistungsstatus';
$lang["PATIENT_TREATMENT_DESCRIPTION"] = 'Description';
$lang["PATIENT_TREATMENT_PROTOCOL2"] = 'Prescription';

$lang["PATIENT_TREATMENT_AMOUNT"] = 'Amount';
$lang["PATIENT_TREATMENT_DISCOUNT"] = 'Discount';
$lang["PATIENT_TREATMENT_DISCOUNT_SHORT"] = 'Discount';
$lang["PATIENT_TREATMENT_VAT"] = 'VAT';
$lang["PATIENT_TREATMENT_VAT_SHORT"] = 'VAT';

$lang["PATIENT_TREATMENT_DIAGNOSE"] = 'Diagnosis';
$lang["PATIENT_TREATMENT_DIAGNOSES"] = 'Diagnosis';
$lang["PATIENT_TREATMENT_PLAN"] = 'Treatment Plan';

$lang["PATIENT_TREATMENT_GOALS"] = 'Sessions';
$lang["PATIENT_TREATMENT_GOALS_SINGUAL"] = 'Session';
$lang["PATIENT_TREATMENT_TASKS_TYPE"] = 'Treatment Type';
$lang["PATIENT_TREATMENT_TASKS_THERAPIST"] = 'Therapist';
$lang["PATIENT_TREATMENT_TASKS_PLACE"] = 'Location';
$lang["PATIENT_TREATMENT_TASKS_PLACE2"] = 'Ort';
$lang["PATIENT_TREATMENT_TASKS_DATE"] = 'Date';
$lang["PATIENT_TREATMENT_TASKS_DATE_CALENDAR"] = 'Calendar date';
$lang["PATIENT_TREATMENT_TASKS_TIME"] = 'Time';
$lang["PATIENT_TREATMENT_TASKS_DATE_INVOICE"] = 'Date of invoice';
$lang["PATIENT_TREATMENT_TASKS_DURATION"] = 'Duration';

$lang["PATIENT_TREATMENT_PRINT_OPTION"] = 'Treatment';
$lang["PATIENT_TREATMENT_PRINT_OPTION_DATES"] = 'List';

$lang["PATIENT_TREATMENT_HELP"] = 'manual_patients_treatments.pdf';

$lang["PATIENT_PRINT_TREATMENT"] = 'treatment.png';
$lang["PATIENT_PRINT_TREATMENT_LIST"] = 'terminliste.png';

// check for custom lang file
$custom_lang = CO_PATH_BASE . "/lang/patients/treatments/en.php";
if(file_exists($custom_lang)) {
	include_once($custom_lang);
}
?>