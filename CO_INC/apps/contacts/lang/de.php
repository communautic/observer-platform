<?php
$contacts_name = "Kontakte";

$lang['CONTACTS_GROUPS'] = 'Gruppen';
$lang['CONTACTS_CONTACT'] = 'Kontakt';
$lang['CONTACTS_CONTACTS'] = 'Kontakte';

$lang["CONTACTS_CONTACTS_NEW"] = 'Neuer Kontakt';
$lang["CONTACTS_CONTACTS_ACTION_NEW"] = 'neuen Kontakt anlegen';

$lang["CONTACTS_GROUPS_NEW"] = 'Neue Gruppe';
$lang["CONTACTS_GROUPS_ACTION_NEW"] = 'neue Gruppe anlegen';

$lang['CONTACTS_GROUP_TITLE'] = 'Gruppe';
$lang['CONTACTS_SINGLE_CONTACTS'] = 'Kontakte';
$lang['CONTACTS_SYSTEM_GROUP'] = 'Alle Kontakte';
define('CONTACTS_ADD_CONTACT_TO_GROUP', 'Einzelkontakt integrieren');
define('CONTACTS_ADD_GROUP_TO_GROUP', 'Gruppe integrieren');
$lang['CONTACTS_GROUP_MEMBERS'] = 'Mitglieder';
$lang['CONTACTS_GROUP_MEMBERS_LIST'] = 'Gruppenliste';

$lang['CONTACTS_LASTNAME'] = 'Nachname';
$lang['CONTACTS_FIRSTNAME'] = 'Vorname';
$lang['CONTACTS_CONTACT_TITLE'] = 'Anrede';
$lang['CONTACTS_CONTACT_TITLE2'] = 'Titel';
$lang['CONTACTS_COMPANY'] = 'Firma';
$lang['CONTACTS_POSITION'] = 'Position';
$lang['CONTACTS_EMAIL'] = 'E-mail (Standard)';
$lang['CONTACTS_EMAIL_ALT'] = 'E-mail (Alternative)';
$lang['CONTACTS_TEL'] = 'Telefon 1';
$lang['CONTACTS_TEL2'] = 'Telefon 2';
$lang['CONTACTS_FAX'] = 'Fax';
$lang['CONTACTS_WEBSITE'] = 'Website Adresse';

$lang["CONTACT_TAB_ADDRESS"] = "Adressen";
$lang["CONTACT_TAB_ACCESS"] = "Berechtigungen";
$lang["CONTACT_TAB_CALENDAR"] = "Kalender";

$lang['CONTACTS_ADDRESS'] = 'Anschrift';
$lang['CONTACTS_ADDRESS_LINE1'] = 'Straße';
$lang['CONTACTS_ADDRESS_LINE2'] = 'Straße 2';
$lang['CONTACTS_TOWN'] = 'Ort';
$lang['CONTACTS_POSTCODE'] = 'Plz';
$lang['CONTACTS_COUNTRY'] = 'Land';

$lang['CONTACTS_BANK_NAME'] = 'Bank';
$lang['CONTACTS_BANK_SORT_CODE'] = 'BLZ';
$lang['CONTACTS_BANK_ACCOUNT_NBR'] = 'Konto';
$lang['CONTACTS_BANK_ACCOUNT_BIC'] = 'BIC';
$lang['CONTACTS_BANK_ACCOUNT_IBAN'] = 'IBAN';

$lang['CONTACTS_VAT_NO'] = 'UID-Nummer';
$lang['CONTACTS_COMPANY_NO'] = 'FN';
$lang['CONTACTS_LEGAL_PLACE'] = 'Gerichtsstand';
$lang['CONTACTS_DVR_NUMBER'] = 'DVR-Nummer';

$lang['CONTACTS_LANGUAGE'] = 'Systemsprache';
$lang['CONTACTS_TIMEZONE'] = 'Systemzeitzone';
$lang["CONTACTS_DESCRIPTION"] = 'Notiz';

$lang['CONTACTS_GROUPMEMBERSHIP'] = 'Gruppenmitglied';

$lang['CONTACTS_ACCESSCODES'] = 'Zugangscodes';
$lang['CONTACTS_ACCESSCODES_NO'] = 'keine Zugangscodes';
$lang['CONTACTS_ACCESSCODES_SEND'] = 'Zugangscodes übermitteln';
$lang['CONTACTS_ACCESSCODES_REMOVE'] = 'Zugangscodes entfernen';
$lang['CONTACTS_ACCESS_ACTIVE'] = 'übermittelt am %s durch %s';
$lang['CONTACTS_ACCESS_REMOVE'] = 'entfernt am %s durch %s';
$lang['CONTACTS_SYSADMIN_NORIGHTS'] = 'keine Berechtigung';
$lang['CONTACTS_SYSADMIN_ACTIVE'] = 'Berechtigung erteilt am %s durch %s';
$lang['CONTACTS_SYSADMIN_REMOVE'] = 'Berechtigung entfernt am %s durch %s';
$lang['CONTACTS_SYSADMIN_GIVE_RIGHT'] = 'Berechtigung erteilen';
$lang['CONTACTS_SYSADMIN_REMOVE_RIGHT'] = 'Berechtigung entfernen';

$lang['CONTACTS_CALENDAR_NO_ACCESS'] = 'Dieser Kontakt verfügt über keine Zugangsberechtigung, daher kann kein  Kalender freigeschalten werden. <br />Gehen Sie wie folgt vor: <br />1. Zugangsberechtigung vergeben (Reiter Berechtigungen)<br />2. Aktualisieren des Moduls durchführen <br />3. Kalender aktivieren';
$lang['CONTACTS_CALENDAR_GIVE_RIGHT'] = 'aktivieren';
$lang['CONTACTS_CALENDAR_REMOVE_RIGHT'] = 'deaktivieren';
$lang['CONTACTS_CALENDAR_ACTIVE'] = 'aktiviert';
$lang['CONTACTS_CALENDAR_DEACTIVE'] = 'deaktiviert';
$lang['CONTACTS_CALENDAR_OTHERS'] = 'Andere / Caldav';
$lang['CONTACTS_CALENDAR_ALL_URL'] = 'gemeinschaftskalender';

$lang['CONTACTS_CUSTOM'] = 'Text';

$lang['CONTACTS_AVATARS'] = 'Kontakte / Bilder';

// Access codes Email
$lang['ACCESS_CODES_EMAIL_SUBJECT'] = $lang["APPLICATION_NAME_CAPS"].'© Zugangscodes';
$lang['ACCESS_CODES_EMAIL'] =	'<p style="font-face: Arial, Verdana; font-size: small">Hiermit erhalten Sie Ihre Zugangscodes zur erstmaligen Anmeldung für die Online-Managementplattform ' . $lang["APPLICATION_NAME_CAPS"] . ' ©:</p>' .
								'<p style="font-face: Arial, Verdana; font-size: small">Bitte gehen Sie jetzt zur Portalseite: <a href="%1$s">%1$s</a></p>' .
								'<p style="font-face: Arial, Verdana; font-size: small">Geben Sie folgende Zugangsdaten ein:</p>' .
    							'<p style="font-face: Arial, Verdana; font-size: small">Benutzername: %2$s<br />' .
    							'Passwort: %3$s</p>' .
								'<p style="font-face: Arial, Verdana; font-size: small;">Nachdem Sie Benutzername und Passwort eingegeben haben, klicken Sie auf weiter. Danach werden Sie aufgefordert, Ihre eigenen Zugangscodes zu kreieren. Bitte beachten Sie, dass damit alle vorangegangenen Codes nicht mehr gelten. Es gelten ab sofort nur mehr Ihre persönlich kreierten Zugangsdaten.</p>' .
								'<p style="font-face: Arial, Verdana; font-size: small;">Bei Fragen zu diesem Einstiegsprozess kontaktieren Sie uns bitte unter der Emailadresse: ' . $lang["APPLICATION_SUPPORT_EMAIL"] . '</p>' .
								'<p style="font-face: Arial, Verdana; font-size: small;">Danke und viel Erfolg!</p>';

$lang["CONTACTS_HELP"] = 'manual_kontakte_kontakte.pdf';
$lang["PCONTACTS_GROUPS_HELP"] = 'manual_kontakte_gruppen.pdf';

$lang["PRINT_GROUP"] = 'gruppe.png';
$lang["PRINT_CONTACT"] = 'kontakt.png';
$lang["PRINT_EXTERNAL_CALENDAR"] = 'externekalender.png';
?>