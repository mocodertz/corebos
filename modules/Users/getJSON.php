<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'Users.php';
global $app_strings, $current_user;
if (!is_admin($current_user)) {
	echo '<br><br>';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
	$smarty->assign('ERROR_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('applicationmessage.tpl');
	exit;
}

$response = array(
	'total' => 0,
	'data' => array(),
	'error' => true,
);
$focus = new Users();
if (isset($_REQUEST['page'])) {
	$page = vtlib_purify($_REQUEST['page']);
} else {
	$page = 1;
}
if (isset($_REQUEST['adminstatus'])) {
	$adminstatus = vtlib_purify($_REQUEST['adminstatus']);
} else {
	$adminstatus = 'all';
}
if (isset($_REQUEST['userstatus'])) {
	$userstatus = vtlib_purify($_REQUEST['userstatus']);
	if ($_REQUEST['userstatus'] == 'loggedin') {
		$userstatus = 'all';
		$loggedinstatus = true;
	}
} else {
	$userstatus = 'all';
	$loggedinstatus = false;
}
if (isset($_REQUEST['email_search'])) {
	$email_search = vtlib_purify($_REQUEST['email_search']);
} else {
	$email_search = '';
}
if (isset($_REQUEST['namerole_search'])) {
	$namerole_search = vtlib_purify($_REQUEST['namerole_search']);
} else {
	$namerole_search = '';
}
if (isset($_REQUEST['order_by']) && is_numeric($_REQUEST['order_by'])) {
	$order_by = vtlib_purify($_REQUEST['order_by']);
	switch ($order_by) {
		case 0:
			$order_by = $focus->list_fields_name['User Name'];
			break;
		case 1:
			$order_by = $focus->list_fields_name['User Name'];
			break;
		case 2:
			$order_by = $focus->list_fields_name['Email'];
			break;
		case 3:
			$order_by = $focus->list_fields_name['Admin'];
			break;
		case 4:
			$order_by = $focus->list_fields_name['Email2'];
			break;
		case 5:
			$order_by = $focus->list_fields_name['Status'];
			break;
		default:
			$order_by = $focus->list_fields_name['User Name'];
			break;
	}
} else {
	$order_by = $focus->default_order_by;
}
if (isset($_REQUEST['order_rule'])) {
	$sorder = vtlib_purify($_REQUEST['order_rule']);
} else {
	$sorder = 'ASC';
}
$response = $focus->getUsersJSON($adminstatus, $userstatus, $page, $order_by, $sorder, $email_search, $namerole_search, $loggedinstatus);
echo $response;