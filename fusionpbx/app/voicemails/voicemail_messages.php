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
 Portions created by the Initial Developer are Copyright (C) 2008-2014
 the Initial Developer. All Rights Reserved.

 Contributor(s):
 Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "resources/require.php";
if (!(check_str($_REQUEST["action"]) == "download" && check_str($_REQUEST["src"]) == "email")) {
	require_once "resources/check_auth.php";
	if (permission_exists('voicemail_message_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//set the voicemail_uuid
	if (strlen($_REQUEST["id"]) > 0) {
		$voicemail_uuid = check_str($_REQUEST["id"]);
	}

//download the message
	if (check_str($_REQUEST["action"]) == "download") {
		$voicemail_message_uuid = check_str($_REQUEST["uuid"]);
		$voicemail_id = check_str($_REQUEST["id"]);
		$voicemail_uuid = check_str($_REQUEST["voicemail_uuid"]);
		//require_once "resources/classes/voicemail.php";
		if ($voicemail_message_uuid != '' && $voicemail_id != '' && $voicemail_uuid != '') {
			$voicemail = new voicemail;
			$voicemail->db = $db;
			$voicemail->domain_uuid = $_SESSION['domain_uuid'];
			$voicemail->voicemail_id = $voicemail_id;
			$voicemail->voicemail_uuid = $voicemail_uuid;
			$voicemail->voicemail_message_uuid = $voicemail_message_uuid;
			$result = $voicemail->message_download();
			unset($voicemail);
			header("Location: voicemail_edit.php?id=".$voicemail_uuid);
		}
		exit;
	}

//get the html values and set them as variables
	$order_by = check_str($_GET["order_by"]);
	$order = check_str($_GET["order"]);

//get the voicemail
	require_once "app/voicemails/resources/classes/voicemail.php";
	$vm = new voicemail;
	$vm->db = $db;
	$vm->domain_uuid = $_SESSION['domain_uuid'];
	$vm->voicemail_uuid = $voicemail_uuid;
	$vm->order_by = $order_by;
	$vm->order = $order;
	$voicemails = $vm->messages();

//additional includes
	require_once "resources/header.php";
	require_once "resources/paging.php";

//show the content
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align='left' nowrap='nowrap' valign='top'>";
	echo "			<b>".$text['title-voicemail_messages']."</b>";
	echo "			<br><br>";
	echo "			".$text['description-voicemail_message'];
	echo "			<br><br>";
	echO "		</td>\n";
	echo "		<td width='50%' align='right' valign='top'>&nbsp;</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	$row_style["2"] = "row_style2";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

//set the table header
	$table_header = "<tr>\n";
	$table_header .= th_order_by('created_epoch', $text['label-created_epoch'], $order_by, $order);
	$table_header .= th_order_by('caller_id_name', $text['label-caller_id_name'], $order_by, $order);
	$table_header .= th_order_by('caller_id_number', $text['label-caller_id_number'], $order_by, $order);
	$table_header .= "<th>".$text['label-tools']."</th>\n";
	$table_header .= th_order_by('message_length', $text['label-message_length'], $order_by, $order, null, "style='text-align: right;'");
	$table_header .= "<th style='text-align: right;'>".$text['label-message_size']."</th>\n";
	$table_header .= "<td align='right' width='21'>\n";
	$table_header .= "	&nbsp;\n";
	$table_header .= "</td>\n";
	$table_header .= "</tr>\n";

//loop through the voicemail messages
	if (count($voicemails) > 0) {
		$previous_voicemail_id = '';
		foreach($voicemails as $field) {
			if ($previous_voicemail_id != $field['voicemail_id']) {
				echo "<tr>\n";
				echo "	<td colspan='3' align='left'>\n";
				echo "		<br>";
				if ($previous_voicemail_id != '') {
					echo "	<br /><br /><br />\n";
				}
				echo "		<b>".$text['label-mailbox'].": ".$field['voicemail_id']." </b><br />&nbsp;\n";
				echo "	</td>\n";
				echo "	<td colspan='3' valign='bottom' align='right'>\n";
				if (permission_exists('voicemail_greeting_view')) {
					echo "		<input type='button' class='btn' name='' alt='greetings' onclick=\"window.location='".PROJECT_PATH."/app/voicemail_greetings/voicemail_greetings.php?id=".$field['voicemail_id']."'\" value='".$text['button-greetings']."'>\n";
				}
				if (permission_exists('voicemail_view')) {
					echo "		<input type='button' class='btn' name='' alt='settings' onclick=\"window.location='".PROJECT_PATH."/app/voicemails/voicemail_edit.php?id=".$field['voicemail_uuid']."'\" value='".$text['button-settings']."'>\n";
				}
				echo "	<br /><br /></td>\n";
				echo "	<td>&nbsp;</td>\n";
				echo "</tr>\n";
				echo $table_header;
			}

			foreach($field['messages'] as &$row) {
				if ($row['message_status'] == '') { $style = "font-weight: bold;"; } else { $style = ''; }
				echo "<td valign='top' class='".$row_style[$c]."' style=\"".$style."\" nowrap=\"nowrap\">";
				echo "	".$row['created_date'];
				echo "</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."' style=\"".$style."\">".$row['caller_id_name']."&nbsp;</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."' style=\"".$style."\">".$row['caller_id_number']."&nbsp;</td>\n";
				echo "	<td valign='top' class='".$row_style["2"]." tr_link_void'>";
					$recording_file_path = $file;
					$recording_file_name = strtolower(pathinfo($recording_file_path, PATHINFO_BASENAME));
					$recording_file_ext = pathinfo($recording_file_name, PATHINFO_EXTENSION);
					switch ($recording_file_ext) {
						case "wav" : $recording_type = "audio/wav"; break;
						case "mp3" : $recording_type = "audio/mpeg"; break;
						case "ogg" : $recording_type = "audio/ogg"; break;
					}
					echo "<audio id='recording_audio_".$row['voicemail_message_uuid']."' style='display: none;' preload='none' onended=\"recording_reset('".$row['voicemail_message_uuid']."');\" src=\"voicemail_messages.php?action=download&type=vm&id=".$row['voicemail_id']."&voicemail_uuid=".$row['voicemail_uuid']."&uuid=".$row['voicemail_message_uuid']."\" type='".$recording_type."'></audio>";
					echo "<span id='recording_button_".$row['voicemail_message_uuid']."' onclick=\"recording_play('".$row['voicemail_message_uuid']."')\" title='".$text['label-play']." / ".$text['label-pause']."'>".$v_link_label_play."</span>";
					echo "<a href=\"voicemail_messages.php?action=download&type=vm&t=bin&id=".$row['voicemail_id']."&voicemail_uuid=".$row['voicemail_uuid']."&uuid=".$row['voicemail_message_uuid']."\" title='".$text['label-download']."'>".$v_link_label_download."</a>";
				echo "	</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."' style=\"".$style." text-align: right;\">".$row['message_length_label']."&nbsp;</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."' style=\"".$style." text-align: right;\">".$row['file_size_label']."</td>\n";
				echo "	<td class='list_control_icon'>\n";
				if (permission_exists('voicemail_message_delete')) {
					echo "		<a href='voicemail_message_delete.php?voicemail_uuid=".$row['voicemail_uuid']."&id=".$row['voicemail_message_uuid']."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>\n";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			} //end foreach
			unset($row);

			$previous_voicemail_id = $field['voicemail_id'];
			unset($sql, $result, $result_count);
		}
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='9' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap='nowrap'>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			&nbsp;\n";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "<br /><br />";

//autoplay message
	if (check_str($_REQUEST["action"]) == "autoplay" && check_str($_REQUEST["uuid"]) != '') {
		echo "<script>recording_play('".check_str($_REQUEST["uuid"])."');</script>";
	}

//include the footer
	require_once "resources/footer.php";
?>