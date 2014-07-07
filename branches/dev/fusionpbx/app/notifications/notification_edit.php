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

require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists("notification_edit")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

// add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

// retrieve software uuid
	$sql = "select software_uuid, software_url, software_version from v_software";
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$software_uuid = $row["software_uuid"];
			$software_url = $row["software_url"];
			$software_version = $row["software_version"];
			break; // limit to 1 row
		}
	}
	unset($sql, $prep_statement);

	if (count($_POST) > 0) {

		// retrieve submitted values
		$project_notifications = check_str($_POST["project_notifications"]);
		$project_security = check_str($_POST["project_security"]);
		$project_releases = check_str($_POST["project_releases"]);
		$project_events = check_str($_POST["project_events"]);
		$project_news = check_str($_POST["project_news"]);
		$project_notification_method = check_str($_POST["project_notification_method"]);
		$project_notification_recipient = check_str($_POST["project_notification_recipient"]);

		// get local project notification participation flag
		$sql = "select project_notifications from v_notifications";
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach ($result as &$row) {
				$current_project_notifications = $row["project_notifications"];
				break; // limit to 1 row
			}
		}
		unset($sql, $prep_statement);

		// check if remote record should be removed
		if ($project_notifications == 'false') {

			if ($current_project_notifications == 'true') {
				// remove remote server record
				$url = "https://".$software_url."/app/notifications/notifications_manage.php?id=".$software_uuid."&action=delete";
				if (function_exists('curl_version')) {
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$response = curl_exec($curl);
					curl_close($curl);
				}
				else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
					$response = file_get_contents($url);
				}

				// parse response
				$response = json_decode($response, true);

				if ($response['result'] == 'deleted') {
					// set local project notification participation flag to false
					$sql = "update v_notifications set project_notifications = 'false'";
					$db->exec(check_sql($sql));
					unset($sql);
				}
			}
			// redirect
			$_SESSION["message"] = $text['message-update'];
			header("Location: notification_edit.php");
			exit;
		}

		// check for invalid values
		if ($project_notifications == 'true') {
			if (
				($project_notification_method == 'email' && !valid_email($project_notification_recipient)) ||
				($project_notification_method == 'email' && $project_notification_recipient == '')
				) {
					$_SESSION["postback"] = $_POST;
					$_SESSION["message"] = $text['message-invalid_recipient'];
					header("Location: notification_edit.php");
					exit;
			}
		}

		// collect demographic information **********************************************

			// fusionpbx version
			$software_ver = $software_version;

			// php version
			$php_ver = phpversion();

			// webserver name & version
			$web_server = $_SERVER['SERVER_SOFTWARE'];

			// switch version
			$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
			if ($fp) {
				$switch_result = event_socket_request($fp, 'api version');
			}
			$switch_ver = trim($switch_result);

			// database name & version
			switch ($db_type) {
				case "pgsql" :	$db_ver_query = "select version() as db_ver;";			break;
				case "mysql" :	$db_ver_query = "select version() as db_ver;";			break;
				case "sqlite" :	$db_ver_query = "select sqlite_version() as db_ver;";	break;
			}
			$prep_statement = $db->prepare($db_ver_query);
			if ($prep_statement) {
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
				foreach ($result as &$row) {
					$database_version = $row["db_ver"];
					break; // limit to 1 row
				}
			}
			unset($db_ver_query, $prep_statement);
			$db_ver = $database_version;

			// operating system name & version
			$os_platform = PHP_OS;
			$os_info_1 = php_uname("a");
			if ($os_platform == "Linux") {
				$os_info_2 = shell_exec("cat /etc/*{release,version}");
				$os_info_2 .= shell_exec("lsb_release -d -s");
			}
			else if (substr(strtoupper($os_platform), 0, 3) == "WIN") {
				$os_info_2 = trim(shell_exec("ver"));
			}

		// **************************************************************************

		// update remote server record with new values
		$url = "https://".$software_url."/app/notifications/notifications_manage.php";
		$url .= "?id=".$software_uuid;
		$url .= "&security=".$project_security;
		$url .= "&releases=".$project_releases;
		$url .= "&events=".$project_events;
		$url .= "&news=".$project_news;
		$url .= "&method=".$project_notification_method;
		$url .= "&recipient=".urlencode($project_notification_recipient);
		$url .= "&software_ver=".urlencode($software_ver);
		$url .= "&php_ver=".urlencode($php_ver);
		$url .= "&web_server=".urlencode($web_server);
		$url .= "&switch_ver=".urlencode($switch_ver);
		$url .= "&db_type=".urlencode($db_type);
		$url .= "&db_ver=".urlencode($db_ver);
		$url .= "&os_platform=".urlencode($os_platform);
		$url .= "&os_info_1=".urlencode($os_info_1);
		$url .= "&os_info_2=".urlencode($os_info_2);

		if (function_exists('curl_version')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($curl);
			curl_close($curl);
		}
		else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
			$response = file_get_contents($url);
		}

		// parse response
		$response = json_decode($response, true);

		if ($response['result'] == 'updated' || $response['result'] == 'inserted') {
			// set local project notification participation flag to true
			$sql = "update v_notifications set project_notifications = 'true'";
			$db->exec(check_sql($sql));
			unset($sql);
			// set message
			$_SESSION["message"] = $text['message-update'];
			if (
				$project_security == 'false' &&
				$project_releases == 'false' &&
				$project_events == 'false' &&
				$project_news == 'false'
				) {
				$_SESSION["message"] = $_SESSION["message"]." - ".$text['message-no_channels'];
			}
			// redirect
			header("Location: notification_edit.php");
			exit;
		}

	}

// check postback session
	if (!isset($_SESSION["postback"])) {

		// check local project notification participation flag
		$sql = "select project_notifications from v_notifications";
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach ($result as &$row) {
				$setting["project_notifications"] = $row["project_notifications"];
				break; // limit to 1 row
			}
		}
		unset($sql, $prep_statement);

		// if participation enabled
		if ($setting["project_notifications"] == 'true') {

			// get current project notification preferences
			$url = "https://".$software_url."/app/notifications/notifications_manage.php?id=".$software_uuid;
			if (function_exists('curl_version')) {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($curl);
				curl_close($curl);
			}
			else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
				$response = file_get_contents($url);
			}

			// parse response
			$setting = json_decode($response, true);
			$setting["project_notifications"] = 'true';
		}

	}
	else {

		// load postback variables
		$setting = fix_postback($_SESSION["postback"]);
		unset($_SESSION["postback"]);

	}

require_once "resources/header.php";
$page["title"] = $text['title-notifications'];

// show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br />";

	echo "<form method='post' name='frm' action=''>\n";

	echo "<table cellpadding='6' cellspacing='0' width='100%' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td align='left' nowrap='nowrap'><b>".$text['header-notifications']."</b><br><br></td>\n";
	echo "		<td align='right'>";
	if (permission_exists('notification_edit')) {
		echo "		<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	}
	echo "			<br><br>";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			".$text['description-notifications']."<br /><br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";

	echo "	<tr>\n";
	echo "		<td width='30%' class='vncellreq' valign='top' align='left' nowrap>\n";
	echo 			$text['label-project_notifications']."\n";
	echo "		</td>\n";
	echo "		<td width='70%' class='vtable' align='left'>\n";
	echo "			<select name='project_notifications' class='formfld' style='width: auto;' onchange=\"$('#notification_channels').slideToggle();\">\n";
	echo "				<option value='false' ".(($setting["project_notifications"] == 'false') ? "selected='selected'" : null).">".$text['option-disabled']."</option>\n";
	echo "				<option value='true' ".(($setting["project_notifications"] == 'true') ? "selected='selected'" : null).">".$text['option-enabled']."</option>\n";
	echo "			</select><br />\n";
	echo 			$text['description-project_notifications']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";

	echo "</table>\n";

	echo "<div id='notification_channels' ".(($setting["project_notifications"] != 'true') ? "style='display: none;'" : null).">\n";
		echo "<table cellpadding='6' cellspacing='0' width='100%' border='0'>\n";

		echo "	<tr>\n";
		echo "		<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
		echo 			$text['label-project_security']."\n";
		echo "		</td>\n";
		echo "		<td width='70%' class='vtable' align='left'>\n";
		echo "			<select name='project_security' class='formfld' style='width: auto;'>\n";
		echo "				<option value='false' ".(($setting["project_security"] == 'false') ? "selected='selected'" : null).">".$text['option-disabled']."</option>\n";
		echo "				<option value='true' ".(($setting["project_security"] == 'true') ? "selected='selected'" : null).">".$text['option-enabled']."</option>\n";
		echo "			</select><br />\n";
		echo 			$text['description-project_security']."\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo 			$text['label-project_releases']."\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		echo "			<select name='project_releases' class='formfld' style='width: auto;'>\n";
		echo "				<option value='false' ".(($setting["project_releases"] == 'false') ? "selected='selected'" : null).">".$text['option-disabled']."</option>\n";
		echo "				<option value='true' ".(($setting["project_releases"] == 'true') ? "selected='selected'" : null).">".$text['option-enabled']."</option>\n";
		echo "			</select><br />\n";
		echo 			$text['description-project_releases']."\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
		echo 			$text['label-project_events']."\n";
		echo "		</td>\n";
		echo "		<td width='70%' class='vtable' align='left'>\n";
		echo "			<select name='project_events' class='formfld' style='width: auto;'>\n";
		echo "				<option value='false' ".(($setting["project_events"] == 'false') ? "selected='selected'" : null).">".$text['option-disabled']."</option>\n";
		echo "				<option value='true' ".(($setting["project_events"] == 'true') ? "selected='selected'" : null).">".$text['option-enabled']."</option>\n";
		echo "			</select><br />\n";
		echo 			$text['description-project_events']."\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td class='vncell' valign='top' align='left' nowrap>\n";
		echo 			$text['label-project_news']."\n";
		echo "		</td>\n";
		echo "		<td class='vtable' align='left'>\n";
		echo "			<select name='project_news' class='formfld' style='width: auto;'>\n";
		echo "				<option value='false' ".(($setting["project_news"] == 'false') ? "selected='selected'" : null).">".$text['option-disabled']."</option>\n";
		echo "				<option value='true' ".(($setting["project_news"] == 'true') ? "selected='selected'" : null).">".$text['option-enabled']."</option>\n";
		echo "			</select><br />\n";
		echo 			$text['description-project_news']."\n";
		echo "		</td>\n";
		echo "	</tr>\n";

		echo "</table>\n";
	echo "</div>\n";

	echo "<table cellpadding='6' cellspacing='0' width='100%' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo 			$text['label-project_notification_method']."\n";
	echo "		</td>\n";
	echo "		<td width='70%' class='vtable' align='left'>\n";
	echo "			<select name='project_notification_method' class='formfld' style='width: auto;'>\n";
	echo "				<option value='email' ".(($setting["project_notification_method"] == 'email') ? "selected='selected'" : null).">".$text['option-email']."</option>\n";
	echo "			</select><br />\n";
	echo 			$text['description-project_notification_method']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";

	echo "	<tr>\n";
	echo "		<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo 			$text['label-project_notification_recipient']."\n";
	echo "		</td>\n";
	echo "		<td class='vtable' align='left'>\n";
	echo "			<input class='formfld' type='text' name='project_notification_recipient' maxlength='50' value='".$setting["project_notification_recipient"]."'><br />\n";
	echo 			$text['description-project_notification_recipient']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";

	if (permission_exists('notification_edit')) {
		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
		echo "		</td>\n";
		echo "	</tr>";
	}

	echo "</table>\n";

	echo "</form>\n";

	echo "<br><br>";
	echo "<div align='left'>".$text['message-disclaimer']."</div>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br /><br />";

// include the footer
	require_once "resources/footer.php";
?>