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
	T_Dot_Zilla <vipkilla@gmail.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	T_Dot_Zilla <vipkilla@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";

if (ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

$name = check_str($_POST["name"]);
$description = check_str($_POST["description"]);
$areacode = check_str($_POST["areacode"]);
$country = check_str($_POST["country"]);

if (count($_POST)>0 && check_str($_POST["persistform"]) != "1") {

	$msgerror = '';

	//--- begin captcha verification ---------------------
		//session_start(); //make sure sessions are started
		if (strtolower($_SESSION["captcha"]) != strtolower($_REQUEST["captcha"]) || strlen($_SESSION["captcha"]) == 0) {
			//$msgerror .= "Captcha Verification Failed<br>\n";
		}
		else {
			//echo "verified";
		}
	//--- end captcha verification -----------------------

	//username is already used.
	if (strlen($name) == 0) {
		$msgerror .= "Please provide a name.<br>\n";
	}
	else {
		$sql = "SELECT * FROM v_ingroups ";
		$sql .= "where name = '$name' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		if (count($prepstatement->fetchAll()) > 0) {
			$msgerror .= "Please choose a different name.<br>\n";
		}
	}

	if (strlen($description) == 0) { $msgerror .= "Description cannot be blank.<br>\n"; }
	if (strlen($areacode) == 0) { $msgerror .= "Please provide an area code.<br>\n"; }
	if (strlen($country) == 0) { $msgerror .= "Please provide a country.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	$sql = "insert into v_ingroups ";
	$sql .= "(";
	$sql .= "name, ";
	$sql .= "description, ";
	$sql .= "areacode, ";
	$sql .= "country ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$name', ";
	$sql .= "'$description', ";
	$sql .= "'$areacode', ";
	$sql .= "'$country' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	//log the success
	//$logtype = 'user'; $logstatus='add'; $logadduser=$_SESSION["username"]; $logdesc= "username: ".$username." user added.";
	//logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"3;url=index.php\">\n";
	echo "<div align='center'>Add Complete</div>";
	require_once "includes/footer.php";
	return;
}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='90%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<div class='borderlight' style='padding:10px;'>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "		<td width='80%'>\n";
	echo "			<b>To add an inbound group, please fill out this form completely. All fields are required. </b><br>";
	echo "		</td>\n";
	echo "		<td width='20%' align='right'>\n";
	echo "			<input type='button' class='btn' name='back' alt='back' onclick=\"window.history.back()\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncellreq' width='40%'>Name:</td>";
	echo "		<td class='vtable' width='60%'><input type='text' class='formfld' autocomplete='off' name='name' value='$name'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Description</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='description' value='$description'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Area Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='areacode' value='$areacode'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Country:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='country' value='$country'></td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "       <input type='submit' name='submit' class='btn' value='Create Inbound Group'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//show the footer
	require_once "includes/footer.php";
?>
