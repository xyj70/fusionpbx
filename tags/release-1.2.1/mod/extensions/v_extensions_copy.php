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
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
require_once "includes/paging.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the http get/post variable(s) to a php variable
	if (isset($_REQUEST["id"])) {
		$extension_id = check_str($_REQUEST["id"]);
	}

//get the v_dialplan_includes data 
	$extension_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and extension_id = '$extension_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$v_id = $row["v_id"];
		$extension = $row["extension"];
		$password = $row["password"];
		$user_list = $row["user_list"];
		$provisioning_list = $row["provisioning_list"];
		$provisioning_list = strtolower($provisioning_list);
		$vm_password = $row["vm_password"];
		$vm_password = str_replace("#", "", $vm_password); //preserves leading zeros
		$accountcode = $row["accountcode"];
		$effective_caller_id_name = $row["effective_caller_id_name"];
		$effective_caller_id_number = $row["effective_caller_id_number"];
		$outbound_caller_id_name = $row["outbound_caller_id_name"];
		$outbound_caller_id_number = $row["outbound_caller_id_number"];
		$vm_mailto = $row["vm_mailto"];
		$vm_attach_file = $row["vm_attach_file"];
		$vm_keep_local_after_email = $row["vm_keep_local_after_email"];
		$user_context = $row["user_context"];
		$toll_allow = $row["toll_allow"];
		$callgroup = $row["callgroup"];
		$auth_acl = $row["auth_acl"];
		$cidr = $row["cidr"];
		$sip_force_contact = $row["sip_force_contact"];
		$enabled = $row["enabled"];
		$description = 'copy: '.$row["description"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//copy the extension
	$password = generate_password();
	$sql = "insert into v_extensions ";
	$sql .= "(";
	$sql .= "v_id, ";
	$sql .= "extension, ";
	$sql .= "password, ";
	$sql .= "user_list, ";
	$sql .= "provisioning_list, ";
	$sql .= "vm_password, ";
	$sql .= "accountcode, ";
	$sql .= "effective_caller_id_name, ";
	$sql .= "effective_caller_id_number, ";
	$sql .= "outbound_caller_id_name, ";
	$sql .= "outbound_caller_id_number, ";
	$sql .= "vm_mailto, ";
	$sql .= "vm_attach_file, ";
	$sql .= "vm_keep_local_after_email, ";
	$sql .= "user_context, ";
	$sql .= "toll_allow, ";
	$sql .= "callgroup, ";
	$sql .= "auth_acl, ";
	$sql .= "cidr, ";
	$sql .= "sip_force_contact, ";
	$sql .= "enabled, ";
	$sql .= "description ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$v_id', ";
	$sql .= "'$extension', ";
	$sql .= "'$password', ";
	$sql .= "'$user_list', ";
	$sql .= "'$provisioning_list', ";
	$sql .= "'#".generate_password(4, 1)."', ";
	$sql .= "'$extension', ";
	$sql .= "'$effective_caller_id_name', ";
	$sql .= "'$effective_caller_id_number', ";
	$sql .= "'$outbound_caller_id_name', ";
	$sql .= "'$outbound_caller_id_number', ";
	$sql .= "'$vm_mailto', ";
	$sql .= "'$vm_attach_file', ";
	$sql .= "'$vm_keep_local_after_email', ";
	$sql .= "'$user_context', ";
	$sql .= "'$toll_allow', ";
	$sql .= "'$callgroup', ";
	$sql .= "'$auth_acl', ";
	$sql .= "'$cidr', ";
	$sql .= "'$sip_force_contact', ";
	$sql .= "'$enabled', ";
	$sql .= "'$description' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

//synchronize the xml config
	sync_package_v_extensions();

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_extensions.php\">\n";
	echo "<div align='center'>\n";
	echo "Copy Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>