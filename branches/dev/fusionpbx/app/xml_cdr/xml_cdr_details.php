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
require_once "resources/require.php";
require_once "resources/check_auth.php";

if (permission_exists('xml_cdr_view')) {
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

//get the http values and set them to a variable
	if (strlen($_REQUEST["uuid"]) > 0) {
		$uuid = trim($_REQUEST["uuid"]);
	}

//get the cdr string from the database
	$sql = "select * from v_xml_cdr ";
	$sql .= "where domain_uuid  = '$domain_uuid' ";
	$sql .= "and uuid  = '$uuid' ";
	$row = $db->query($sql)->fetch();
	$start_stamp = trim($row["start_stamp"]);
	$xml_string = trim($row["xml"]);
	$json_string = trim($row["json"]);
	//print_r($row);

//get the format
	if (strlen($xml_string) > 0) {
		$format = "xml";
	}
	if (strlen($json_string) > 0) {
		$format = "json";
	}

//get cdr from the file system
	if ($format != "xml" || $format != "json") {
		$tmp_time = strtotime($start_stamp);
		$tmp_year = date("Y", $tmp_time);
		$tmp_month = date("M", $tmp_time);
		$tmp_day = date("d", $tmp_time);
		$tmp_dir = $_SESSION['switch']['log']['dir'].'/xml_cdr/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day;
		if (file_exists($tmp_dir.'/'.$uuid.'.json')) {
			$format = "json";
			$xml_string = file_get_contents($tmp_dir.'/'.$uuid.'.json');
		}
		if (file_exists($tmp_dir.'/'.$uuid.'.xml')) {
			$format = "xml";
			$xml_string = file_get_contents($tmp_dir.'/'.$uuid.'.xml');
		}
	}

//parse the xml to get the call detail record info
	try {
		if ($format == 'json') {
			$xml = json_decode($json_string);
		}
		if ($format == 'json') {
			$xml = simplexml_load_string($xml_string);
		}
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}

//get the header
	require_once "resources/header.php";

//page title and description
	echo "<br>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td width='30%' align='left' valign='top' nowrap='nowrap'><b>".$text['title2']."</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='xml_cdr.php'\" value='".$text['button-back']."'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "".$text['description-5']." \n";
	echo "".$text['description-6']." \n";
	echo "".$text['description-7']." \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br />\n";
	echo "<br />\n";

//detail summary
	//get the variables from the xml
		$uuid = check_str(urldecode($xml->variables->uuid));
		$direction = check_str(urldecode($xml->channel_data->direction));
		$language = check_str(urldecode($xml->variables->language));
		$xml_string = check_str($xml_string);
		$start_epoch = check_str(urldecode($xml->variables->start_epoch));
		$start_stamp = check_str(urldecode($xml->variables->start_stamp));
		$start_uepoch = check_str(urldecode($xml->variables->start_uepoch));
		$answer_stamp = check_str(urldecode($xml->variables->answer_stamp));
		$answer_epoch = check_str(urldecode($xml->variables->answer_epoch));
		$answer_uepoch = check_str(urldecode($xml->variables->answer_uepoch));
		$end_epoch = check_str(urldecode($xml->variables->end_epoch));
		$end_uepoch = check_str(urldecode($xml->variables->end_uepoch));
		$end_stamp = check_str(urldecode($xml->variables->end_stamp));
		$duration = check_str(urldecode($xml->variables->duration));
		$mduration = check_str(urldecode($xml->variables->mduration));
		$billsec = check_str(urldecode($xml->variables->billsec));
		$billmsec = check_str(urldecode($xml->variables->billmsec));
		$bridge_uuid = check_str(urldecode($xml->variables->bridge_uuid));
		$read_codec = check_str(urldecode($xml->variables->read_codec));
		$write_codec = check_str(urldecode($xml->variables->write_codec));
		$remote_media_ip = check_str(urldecode($xml->variables->remote_media_ip));
		$hangup_cause = check_str(urldecode($xml->variables->hangup_cause));
		$hangup_cause_q850 = check_str(urldecode($xml->variables->hangup_cause_q850));
		$x = 0;
		foreach ($xml->callflow as $row) {
			if ($x == 0) {
				$destination_number = check_str(urldecode($row->caller_profile->destination_number));
				$context = check_str(urldecode($row->caller_profile->context));
				$network_addr = check_str(urldecode($row->caller_profile->network_addr));
			}
			$caller_id_name = check_str(urldecode($row->caller_profile->caller_id_name));
			$caller_id_number = check_str(urldecode($row->caller_profile->caller_id_number));
			$x++;
		}
		unset($x);

	$tmp_year = date("Y", strtotime($start_stamp));
	$tmp_month = date("M", strtotime($start_stamp));
	$tmp_day = date("d", strtotime($start_stamp));

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>".$text['label-summary']."</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>".$text['table-direction']."</th>\n";
	//echo "<th>Language</th>\n";
	//echo "<th>Context</th>\n";
	echo "<th>".$text['table-name']."</th>\n";
	echo "<th>".$text['table-download']."</th>\n";
	echo "<th>".$text['label-destination']."</th>\n";
	echo "<th>".$text['label-start']."</th>\n";
	echo "<th>".$text['table-end']."</th>\n";
	echo "<th>".$text['label-length']."</th>\n";
	echo "<th>".$text['label-status']."</th>\n";
	echo "</tr>\n";

	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'><a href='xml_cdr_details.php?uuid=".$uuid."'>".$direction."</a></td>\n";
	//echo "	<td valign='top' class='".$row_style[$c]."'>".$language."</td>\n";
	//echo "	<td valign='top' class='".$row_style[$c]."'>".$context."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>";
	if (file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')) {
		//echo "		<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."\">\n";
		//echo "	  </a>";

		echo "	  <a href=\"javascript:void(0);\" onclick=\"window.open('../recordings/v_recording_play.php?a=download&type=moh&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
		//$tmp_file_array = explode("\.",$file);
		echo 	$caller_id_name.' ';
		echo "	  </a>";
	}
	else {
		echo 	$caller_id_name.' ';
	}
	echo "	</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>";
	if (file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')) {
		echo "		<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."\">\n";
		echo 	$caller_id_number.' ';
		echo "	  </a>";
	}
	else {
		echo 	$caller_id_number.' ';
	}
	echo "	</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$destination_number."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$start_stamp."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$end_stamp."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$duration."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$hangup_cause."</td>\n";
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//channel data loop
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>".$text['label-channel']."</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Name</th>\n";
	echo "<th>Value</th>\n";
	echo "</tr>\n";
	foreach($xml->channel_data->children() as $child) {
		$key = $child->getName();
		$value = urldecode($child);
		echo "<tr >\n";
		echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
		echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//variable loop
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<td align='left'><b>Variables</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>".$text['label-name']."</th>\n";
	echo "<th>".$text['label-value']."</th>\n";
	echo "</tr>\n";
	foreach($xml->variables->children() as $child) {
		$key = $child->getName();
		$value = urldecode($child);
		if ($key != "digits_dialed" && $key != "dsn") {
			echo "<tr >\n";
			echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".$key."</td>\n";
			if ($key == "bridge_uuid" || $key == "signal_bond") {
				echo "	<td valign='top' align='left' class='".$row_style[$c]."'>\n";
				echo "		<a href='xml_cdr_details.php?uuid=$value'>".$value."</a>&nbsp;\n";
				$tmp_dir = $_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day;
				$tmp_name = '';
				if (file_exists($tmp_dir.'/'.$value.'.wav')) {
					$tmp_name = $value.".wav";
				}
				elseif (file_exists($tmp_dir.'/'.$value.'_1.wav')) {
					$tmp_name = $value."_1.wav";
				}
				elseif (file_exists($tmp_dir.'/'.$value.'.mp3')) {
					$tmp_name = $value.".mp3";
				}
				elseif (file_exists($tmp_dir.'/'.$value.'_1.mp3')) {
					$tmp_name = $value."_1.mp3";
				}
				if (strlen($tmp_name) > 0 && file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)) {
					echo "	<a href=\"javascript:void(0);\" onclick=\"window.open('../recordings/v_recording_play.php?a=download&type=moh&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)."', 'play',' width=420,height=150,menubar=no,status=no,toolbar=no')\">\n";
					echo "		play";
					echo "	</a>&nbsp;";
				}
				if (strlen($tmp_name) > 0 && file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)) {
					echo "	<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode("archive/".$tmp_year."/".$tmp_month."/".$tmp_day."/".$tmp_name)."\">\n";
					echo "		download";
					echo "	</a>";
				}
				echo "</td>\n";
			}
			else {
				echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
			}
			echo "</tr>\n";
		}
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//app_log
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>".$text['label-application-log']."</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>".$text['label-name']."</th>\n";
	echo "<th>".$text['label-data']."</th>\n";
	echo "</tr>\n";

	foreach ($xml->app_log->application as $row) {
		$app_name = $row->attributes()->app_name;
		$app_data = $row->attributes()->app_data;
		echo "<tr >\n";
		echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".$app_name."&nbsp;</td>\n";
		echo "	<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($app_data,75,"<br />\n", TRUE)."&nbsp;</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//callflow
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	foreach ($xml->callflow as $row) {

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "	<td align='left'>\n";

		//attributes
			echo "	<table width='95%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>".$text['label-call-flow']."</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "	</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>".$text['label-name']."</th>\n";
			echo "			<th>".$text['label-value']."</th>\n";
			echo "		</tr>\n";
			foreach($row->attributes() as $key => $value) {
				echo "		<tr>\n";
				echo "				<td valign='top' align='left' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
				echo "				<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//extension->attributes
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>".$text['label-call-flow-2']."</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>".$text['label-name']."</th>\n";
			echo "			<th>".$text['label-value']."</th>\n";
			echo "		</tr>\n";
			foreach($row->extension->attributes() as $key => $value) {
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//extension->application
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>".$text['label-call-flow-3']."</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>".$text['label-name']."</th>\n";
			echo "			<th>".$text['label-data']."</th>\n";
			echo "		</tr>\n";
			foreach ($row->extension->application as $tmp_row) {
				$app_name = $tmp_row->attributes()->app_name;
				$app_data = $tmp_row->attributes()->app_data;
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".$app_name."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($app_data,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//caller_profile
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>".$text['label-call-flow-4']."</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>".$text['label-name']."</th>\n";
			echo "			<th>".$text['label-value']."</th>\n";
			echo "		</tr>\n";
			foreach($row->caller_profile->children() as $child) {
				$key = $child->getName();
				echo "		<tr >\n";
				if ($key != "originatee") {
					$value = urldecode($child);
					echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
					echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				}
				else {
					echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".$key."</td>\n";
					echo "			<td>\n";
					echo "				<table width='100%'>\n";
					foreach($child->originatee_caller_profile->children() as $tmp_child) {
						//print_r($tmp_child);
						$key = $tmp_child->getName();
						$value = urldecode($tmp_child);
						echo "				<tr >\n";
						echo "					<td valign='top' align='left' width='20%' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
						if ($key != "uuid") {
							echo "					<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
						}
						else {
							echo "					<td valign='top' align='left' class='".$row_style[$c]."'><a href='xml_cdr_details.php?uuid=$value'>".$value."</a>&nbsp;</td>\n";
						}
						echo "				</tr>\n";
					}
					echo "				</table>\n";
					echo "			</td>\n";
				}
				echo "</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//times
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>".$text['label-call-flow-5']."</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";

			echo "		<tr>\n";
			echo "			<th>".$text['label-name']."</th>\n";
			echo "			<th>".$text['label-value']."</th>\n";
			echo "		</tr>\n";
			foreach($row->times->children() as $child) {
				$key = $child->getName();
				$value = urldecode($child);
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".$key."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$row_style[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}

			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";

			echo "	</table>";
			echo "	<br /><br />\n";
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>";

//testing
	//echo "<pre>\n";
	//echo htmlentities($xml_string);
	//print_r($xml);
	//echo "</pre>\n";

//get the footer
	require_once "resources/footer.php";
?>