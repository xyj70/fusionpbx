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
include "root.php";
require_once "includes/require.php";

//get the http values and set as variables
	$path = check_str($_GET["path"]);
	$msg = check_str($_GET["msg"]);

//set a default login url
	//if (strlen($_SESSION['login']['url']['text']) == 0) {
	//	$_SESSION['login']['url']['text'] = PROJECT_PATH."/index2.php";
	//}

//add the header
	include "includes/header.php";

//show the message
	if (strlen($msg) > 0) {
		echo "<br><br>";
		echo "<div align='center'>\n";
		echo "<table width='50%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='row_style1'>\n";

		switch ($msg) {
			case "username required":
				echo "<strong>Please provide a username.</strong>";
				break;
			case "incorrect account information":
			   echo "<strong>The username or password was incorrect. Please try again.</strong>";
				break;
			case "install complete":
				echo "<br />\n";
				echo "Installation is complete. <br />";
				echo "<br /> ";
				echo  "<strong>Getting Started:</strong><br /> ";
				echo "<ul><li>There are two levels of admins 1. superadmin 2. admin.<br />";
				echo "<br />\n";
				echo "username: <strong>superadmin</strong> <br />password: <strong>fusionpbx</strong> <br />\n";
				echo "<br />\n";
				echo "username: <strong>admin</strong> <br />password: <strong>fusionpbx</strong> <br/><br/>\n";
				echo "</li>\n";
				echo "<li>\n";
				echo "The database connection settings have been saved to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php.<br />\n";
				echo "</li>\n";
				echo "</ul>\n";
				echo "<strong>\n";
				break;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo "<br /><br />\n\n";
	}

//show the content
	echo "<br><br>";
	echo "<form name='login' METHOD=\"POST\" action=\"".PROJECT_PATH."/index2.php."\">\n";
	echo "<input type='hidden' name='path' value='$path'>\n";
	echo "<table width='200' border='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'>\n";
	echo "	<strong>UserName:</strong>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "  <input type=\"text\" style='width: 150px;' class='formfld' name=\"username\">\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='left'>\n";
	echo "	<strong>Password:</strong>\n";
	echo "</td>\n";
	echo "<td align='left'>\n";
	echo "	<input type=\"password\" style='width: 150px;' class='formfld' name=\"password\">\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($_SESSION['login']['domain_name.visible']['boolean'] == "true") {
		echo "<tr>\n";
		echo "<td align='left'>\n";
		echo "	<strong>Domain:</strong>\n";
		echo "</td>\n";
		echo "<td>\n";
		if (count($_SESSION['login']['domain_name']) > 0) {
			echo "    <select style='width: 150px;' class='formfld' name='domain_name'>\n";
			echo "    <option value=''></option>\n";
			foreach ($_SESSION['login']['domain_name'] as &$row) {
				echo "    <option value='$row'>$row</option>\n";
			}
			echo "    </select>\n";
		}
		else {
			echo "  <input type=\"text\" style='width: 150px;' class='formfld' name=\"domain_name\">\n";
		}
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td>\n";
	echo "</td>\n";
	echo "<td align=\"right\">\n";
	echo "  <input type=\"submit\" class='btn' value=\"Login\">\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>";
	echo "</div>";

//add the footer
	include "includes/footer.php";
?>