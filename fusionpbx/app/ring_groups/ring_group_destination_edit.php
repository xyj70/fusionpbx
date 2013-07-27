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
	Portions created by the Initial Developer are Copyright (C) 2013
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('ring_group_add') || permission_exists('ring_group_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$ring_group_destination_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//set the parent uuid
	if (strlen($_GET["ring_group_uuid"]) > 0) {
		$ring_group_uuid = check_str($_GET["ring_group_uuid"]);
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$ring_group_uuid = check_str($_POST["ring_group_uuid"]);
		$destination_number = check_str($_POST["destination_number"]);
		$destination_delay = check_str($_POST["destination_delay"]);
		$destination_timeout = check_str($_POST["destination_timeout"]);
		$destination_prompt = check_str($_POST["destination_prompt"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$ring_group_destination_uuid = check_str($_POST["ring_group_destination_uuid"]);
	}

	//check for all required data
		//if (strlen($domain_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-domain_uuid']."<br>\n"; }
		//if (strlen($ring_group_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-ring_group_uuid']."<br>\n"; }
		//if (strlen($destination_number) == 0) { $msg .= $text['message-required']." ".$text['label-destination_number']."<br>\n"; }
		//if (strlen($destination_delay) == 0) { $msg .= $text['message-required']." ".$text['label-destination_delay']."<br>\n"; }
		//if (strlen($destination_timeout) == 0) { $msg .= $text['message-required']." ".$text['label-destination_timeout']."<br>\n"; }
		//if (strlen($destination_prompt) == 0) { $msg .= $text['message-required']." ".$text['label-destination_prompt']."<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add" && permission_exists('ring_group_add')) {
				$sql = "insert into v_ring_group_destinations ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "ring_group_destination_uuid, ";
				$sql .= "ring_group_uuid, ";
				$sql .= "domain_uuid, ";
				$sql .= "ring_group_uuid, ";
				$sql .= "destination_number, ";
				$sql .= "destination_delay, ";
				$sql .= "destination_timeout, ";
				$sql .= "destination_prompt ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'".uuid()."', ";
				$sql .= "'$ring_group_uuid', ";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$ring_group_uuid', ";
				$sql .= "'$destination_number', ";
				$sql .= "'$destination_delay', ";
				$sql .= "'$destination_timeout', ";
				$sql .= "'$destination_prompt' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				require_once "resources/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=ring_group_edit.php?id=$ring_group_uuid\">\n";
				echo "<div align='center'>\n";
				echo "	".$text['message-add']."\n";
				echo "</div>\n";
				require_once "resources/footer.php";
				return;
			} //if ($action == "add")

			if ($action == "update" && permission_exists('ring_group_edit')) {
				$sql = "update v_ring_group_destinations set ";
				$sql .= "ring_group_uuid = '$ring_group_uuid', ";
				$sql .= "domain_uuid = '$domain_uuid', ";
				$sql .= "ring_group_uuid = '$ring_group_uuid', ";
				$sql .= "destination_number = '$destination_number', ";
				$sql .= "destination_delay = '$destination_delay', ";
				$sql .= "destination_timeout = '$destination_timeout', ";
				$sql .= "destination_prompt = '$destination_prompt' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and ring_group_destination_uuid = '$ring_group_destination_uuid'";
				$db->exec(check_sql($sql));
				unset($sql);

				require_once "resources/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=ring_group_edit.php?id=$ring_group_uuid\">\n";
				echo "<div align='center'>\n";
				echo "	".$text['message-update']."\n";
				echo "</div>\n";
				require_once "resources/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true") 
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$ring_group_destination_uuid = check_str($_GET["id"]);
		$sql = "select * from v_ring_group_destinations ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and ring_group_destination_uuid = '$ring_group_destination_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$ring_group_uuid = $row["ring_group_uuid"];
			$destination_number = $row["destination_number"];
			$destination_delay = $row["destination_delay"];
			$destination_timeout = $row["destination_timeout"];
			$destination_prompt = $row["destination_prompt"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "resources/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['title-ring_group_destination']."</b></td>\n";
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"window.location='ring_group_edit.php?id=$ring_group_uuid'\" value='".$text['button-back']."'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-destination_number'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_number' maxlength='255' value=\"$destination_number\">\n";
	echo "<br />\n";
	echo $text['description-destination_number']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-destination_delay'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='destination_delay' maxlength='255' value='$destination_delay'>\n";
	echo "<br />\n";
	echo $text['description-destination_delay']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-destination_timeout'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='destination_timeout' maxlength='255' value='$destination_timeout'>\n";
	echo "<br />\n";
	echo $text['description-destination_timeout']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-destination_prompt'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "					<select class='formfld' style='width: 90px;' name='destination_prompt'>\n";
	echo "					<option value=''></option>\n";
	echo "					<option value='1'>".$text['label-destination_prompt_confirm']."</option>\n";
	//echo "					<option value='2'>".$text['label-destination_prompt_announce]."</option>\n";
	echo "					</select>\n";
	echo "<br />\n";
	echo $text['description-destination_prompt']."\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='ring_group_uuid' value='$ring_group_uuid'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='ring_group_destination_uuid' value='$ring_group_destination_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//include the footer
	require_once "resources/footer.php";
?>