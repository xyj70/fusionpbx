<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
if (!$included) {
	include "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";
	if (permission_exists('group_edit')) {
		//access granted
	}
	else {
		echo "access denied";
		return;
	}

	//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}
}

//permission restore default
	require_once "core/users/resources/classes/permission.php";
	$permission = new permission;
	$permission->db = $db;
	$permission->restore();

if (!$included) {
	//show a message to the user
	$_SESSION["message"] = $text['message-restore'];
	header("Location: groups.php");
	return;
}

?>