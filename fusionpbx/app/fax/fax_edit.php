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
	Portions created by the Initial Developer are Copyright (C) 2008-2015
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Luis Daniel Lucio Quiroz <dlucio@okay.com.mx>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('fax_extension_add') || permission_exists('fax_extension_edit') || permission_exists('fax_extension_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//detect billing app
	$billing_app_exists = file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/app/billing/app_config.php");

	if ($billing_app_exists) {
		require_once "app/billing/resources/functions/currency.php";
		require_once "app/billing/resources/functions/rating.php";
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the fax_extension and save it as a variable
	if (strlen($_REQUEST["fax_extension"]) > 0) {
		$fax_extension = check_str($_REQUEST["fax_extension"]);
	}

//set the fax directory
	if (count($_SESSION["domains"]) > 1) {
		$fax_dir = $_SESSION['switch']['storage']['dir'].'/fax/'.$_SESSION['domain_name'];
	}
	else {
		$fax_dir = $_SESSION['switch']['storage']['dir'].'/fax';
	}

//get the fax extension
	if (strlen($fax_extension) > 0) {
		//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
			$dir_fax_inbox = $fax_dir.'/'.$fax_extension.'/inbox';
			$dir_fax_sent = $fax_dir.'/'.$fax_extension.'/sent';
			$dir_fax_temp = $fax_dir.'/'.$fax_extension.'/temp';

		//make sure the directories exist
			if (!is_dir($_SESSION['switch']['storage']['dir'])) {
				mkdir($_SESSION['switch']['storage']['dir']);
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($fax_dir.'/'.$fax_extension)) {
				mkdir($fax_dir.'/'.$fax_extension,0774,true);
				chmod($fax_dir.'/'.$fax_extension,0774);
			}
			if (!is_dir($dir_fax_inbox)) {
				mkdir($dir_fax_inbox,0774,true);
				chmod($dir_fax_inbox,0774);
			}
			if (!is_dir($dir_fax_sent)) {
				mkdir($dir_fax_sent,0774,true);
				chmod($dir_fax_sent,0774);
			}
			if (!is_dir($dir_fax_temp)) {
				mkdir($dir_fax_temp,0774,true);
				chmod($dir_fax_temp,0774);
			}
	}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$fax_uuid = check_str($_REQUEST["id"]);
		$dialplan_uuid = check_str($_REQUEST["dialplan_uuid"]);
	}
	else {
		$action = "add";
	}

//get the http post values and set them as php variables
	if (count($_POST)>0) {
		$fax_name = check_str($_POST["fax_name"]);
		$fax_extension = check_str($_POST["fax_extension"]);
		$fax_accountcode = check_str($_POST["accountcode"]);
		$fax_destination_number = check_str($_POST["fax_destination_number"]);
		$fax_prefix = check_str($_POST["fax_prefix"]);
		$fax_email = check_str($_POST["fax_email"]);
		$fax_email_connection_type = check_str($_POST["fax_email_connection_type"]);
		$fax_email_connection_host = check_str($_POST["fax_email_connection_host"]);
		$fax_email_connection_port = check_str($_POST["fax_email_connection_port"]);
		$fax_email_connection_security = check_str($_POST["fax_email_connection_security"]);
		$fax_email_connection_validate = check_str($_POST["fax_email_connection_validate"]);
		$fax_email_connection_username = check_str($_POST["fax_email_connection_username"]);
		$fax_email_connection_password = check_str($_POST["fax_email_connection_password"]);
		$fax_email_connection_mailbox = check_str($_POST["fax_email_connection_mailbox"]);
		$fax_email_inbound_subject_tag = check_str($_POST["fax_email_inbound_subject_tag"]);
		$fax_email_outbound_subject_tag = check_str($_POST["fax_email_outbound_subject_tag"]);
		$fax_email_outbound_authorized_senders = $_POST["fax_email_outbound_authorized_senders"];
		$fax_caller_id_name = check_str($_POST["fax_caller_id_name"]);
		$fax_caller_id_number = check_str($_POST["fax_caller_id_number"]);
		$fax_forward_number = check_str($_POST["fax_forward_number"]);
		if (strlen($fax_destination_number) == 0) {
			$fax_destination_number = $fax_extension;
		}
		if (strlen($fax_forward_number) > 3) {
			//$fax_forward_number = preg_replace("~[^0-9]~", "",$fax_forward_number);
			$fax_forward_number = str_replace(" ", "", $fax_forward_number);
			$fax_forward_number = str_replace("-", "", $fax_forward_number);
		}
		if (strripos($fax_forward_number, '$1') === false) {
			$forward_prefix = ''; //not found
		} else {
			$forward_prefix = $forward_prefix.$fax_forward_number.'#'; //found
		}
		$fax_description = check_str($_POST["fax_description"]);
	}

//delete the user from the fax users
	if ($_GET["a"] == "delete" && permission_exists("fax_extension_delete")) {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$fax_uuid = check_str($_REQUEST["id"]);

		//delete the group from the users
			$sql = "delete from v_fax_users ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and fax_uuid = '".$fax_uuid."' ";
			$sql .= "and user_uuid = '".$user_uuid."' ";
			$db->exec(check_sql($sql));

		//redirect the browser
			$_SESSION["message"] = $text['message-delete'];
			header("Location: fax_edit.php?id=".$fax_uuid);
			return;
	}

//add the user to the fax users
	if (strlen($_REQUEST["user_uuid"]) > 0 && strlen($_REQUEST["id"]) > 0 && $_GET["a"] != "delete") {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$fax_uuid = check_str($_REQUEST["id"]);
		//assign the user to the fax extension
			$sql_insert = "insert into v_fax_users ";
			$sql_insert .= "(";
			$sql_insert .= "fax_user_uuid, ";
			$sql_insert .= "domain_uuid, ";
			$sql_insert .= "fax_uuid, ";
			$sql_insert .= "user_uuid ";
			$sql_insert .= ")";
			$sql_insert .= "values ";
			$sql_insert .= "(";
			$sql_insert .= "'".uuid()."', ";
			$sql_insert .= "'".$_SESSION['domain_uuid']."', ";
			$sql_insert .= "'".$fax_uuid."', ";
			$sql_insert .= "'".$user_uuid."' ";
			$sql_insert .= ")";
			$db->exec($sql_insert);

		//redirect the browser
			$_SESSION["message"] = $text['confirm-add'];
			header("Location: fax_edit.php?id=".$fax_uuid);
			return;
	}

//clear file status cache
	clearstatcache();

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update" && permission_exists('fax_extension_edit')) {
		$fax_uuid = check_str($_POST["fax_uuid"]);
	}

	//check for all required data
		if (strlen($fax_extension) == 0) { $msg .= "".$text['confirm-ext']."<br>\n"; }
		if (strlen($fax_name) == 0) { $msg .= "".$text['confirm-fax']."<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persist_form_var.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

	//replace the spaces with a dash
		$fax_name = str_replace(" ", "-", $fax_name);

	//escape the commas with a backslash and remove the spaces
		$fax_email = str_replace(" ", "", $fax_email);

	//set the $php_bin
		//if (file_exists(PHP_BINDIR."/php")) { $php_bin = 'php'; }
		if (substr(strtoupper(PHP_OS), 0, 3) == "WIN") {
			$php_bin = 'php.exe';
		}
		else {
			$php_bin = 'php';
		}

	//add or update the database
		if ($_POST["persistformvar"] != "true") {

			//prep authorized senders
				if (sizeof($fax_email_outbound_authorized_senders) > 0) {
					foreach ($fax_email_outbound_authorized_senders as $sender_num => $sender) {
						$sender = check_str($sender);
						if ($sender == '' || !valid_email($sender)) { unset($fax_email_outbound_authorized_senders[$sender_num]); }
					}
					$fax_email_outbound_authorized_senders = implode(',', $fax_email_outbound_authorized_senders);
				}

			if ($action == "add" && permission_exists('fax_extension_add')) {
				//prepare the unique identifiers
					$fax_uuid = uuid();
					$dialplan_uuid = uuid();

				//add the fax extension to the database
					$sql = "insert into v_fax ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "fax_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "fax_extension, ";
					$sql .= "accountcode, ";
					$sql .= "fax_destination_number, ";
					$sql .= "fax_prefix, ";
					$sql .= "fax_name, ";
					$sql .= "fax_email, ";
					if (permission_exists('fax_extension_advanced') && function_exists("imap_open") && file_exists("fax_files_remote.php")) {
						$sql .= "fax_email_connection_type, ";
						$sql .= "fax_email_connection_host, ";
						$sql .= "fax_email_connection_port, ";
						$sql .= "fax_email_connection_security, ";
						$sql .= "fax_email_connection_validate, ";
						$sql .= "fax_email_connection_username, ";
						$sql .= "fax_email_connection_password, ";
						$sql .= "fax_email_connection_mailbox, ";
						$sql .= "fax_email_inbound_subject_tag, ";
						$sql .= "fax_email_outbound_subject_tag, ";
						$sql .= "fax_email_outbound_authorized_senders, ";
					}
					$sql .= "fax_caller_id_name, ";
					$sql .= "fax_caller_id_number, ";
					if (strlen($fax_forward_number) > 0) {
						$sql .= "fax_forward_number, ";
					}
					$sql .= "fax_description ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'".$_SESSION['domain_uuid']."', ";
					$sql .= "'$fax_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$fax_extension', ";
					$sql .= "'$fax_accountcode', ";
					$sql .= "'$fax_destination_number', ";
					$sql .= "'$fax_prefix', ";
					$sql .= "'$fax_name', ";
					$sql .= "'$fax_email', ";
					if (permission_exists('fax_extension_advanced') && function_exists("imap_open") && file_exists("fax_files_remote.php")) {
						$sql .= "'$fax_email_connection_type', ";
						$sql .= "'$fax_email_connection_host', ";
						$sql .= "'$fax_email_connection_port', ";
						$sql .= "'$fax_email_connection_security', ";
						$sql .= "'$fax_email_connection_validate', ";
						$sql .= "'$fax_email_connection_username', ";
						$sql .= "'$fax_email_connection_password', ";
						$sql .= "'$fax_email_connection_mailbox', ";
						$sql .= "'$fax_email_inbound_subject_tag', ";
						$sql .= "'$fax_email_outbound_subject_tag', ";
						$sql .= "'$fax_email_outbound_authorized_senders', ";
					}
					$sql .= "'$fax_caller_id_name', ";
					$sql .= "'$fax_caller_id_number', ";
					if (strlen($fax_forward_number) > 0) {
						$sql .= "'$fax_forward_number', ";
					}
					$sql .= "'$fax_description' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//set the dialplan action
					$dialplan_type = "add";
			}

			if ($action == "update" && permission_exists('fax_extension_edit')) {
				//update the fax extension in the database
					$dialplan_type = "";
					$sql = "update v_fax set ";
					if (strlen($dialplan_uuid) > 0) {
						$sql .= "dialplan_uuid = '".$dialplan_uuid."', ";
					}
					$sql .= "fax_extension = '$fax_extension', ";
					$sql .= "accountcode = '$fax_accountcode', ";
					$sql .= "fax_destination_number = '$fax_destination_number', ";
					$sql .= "fax_prefix = '$fax_prefix', ";
					$sql .= "fax_name = '$fax_name', ";
					$sql .= "fax_email = '$fax_email', ";
					if (permission_exists('fax_extension_advanced') && function_exists("imap_open") && file_exists("fax_files_remote.php")) {
						$sql .= "fax_email_connection_type = '$fax_email_connection_type', ";
						$sql .= "fax_email_connection_host = '$fax_email_connection_host', ";
						$sql .= "fax_email_connection_port = '$fax_email_connection_port', ";
						$sql .= "fax_email_connection_security = '$fax_email_connection_security', ";
						$sql .= "fax_email_connection_validate = '$fax_email_connection_validate', ";
						$sql .= "fax_email_connection_username = '$fax_email_connection_username', ";
						$sql .= "fax_email_connection_password = '$fax_email_connection_password', ";
						$sql .= "fax_email_connection_mailbox = '$fax_email_connection_mailbox', ";
						$sql .= "fax_email_inbound_subject_tag = '$fax_email_inbound_subject_tag', ";
						$sql .= "fax_email_outbound_subject_tag = '$fax_email_outbound_subject_tag', ";
						$sql .= "fax_email_outbound_authorized_senders = '$fax_email_outbound_authorized_senders', ";
					}
					$sql .= "fax_caller_id_name = '$fax_caller_id_name', ";
					$sql .= "fax_caller_id_number = '$fax_caller_id_number', ";
					if (strlen($fax_forward_number) > 0) {
						$sql .= "fax_forward_number = '$fax_forward_number', ";
					}
					else {
						$sql .= "fax_forward_number = null, ";
					}
					$sql .= "fax_description = '$fax_description' ";
					$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
					$sql .= "and fax_uuid = '$fax_uuid' ";
					$db->exec(check_sql($sql));
					unset($sql);
			}

			//if there are no variables in the vars table then add them
				if ($dialplan_type != "add") {
					$sql = "select count(*) as num_rows from v_dialplans ";
					$sql .= "where dialplan_uuid = '".$dialplan_uuid."' ";
					$prep_statement = $db->prepare(check_sql($sql));
					if ($prep_statement) {
						$prep_statement->execute();
						$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
						if ($row['num_rows'] == 0) {
							$dialplan_type = "add";
						}
						else {
							$dialplan_type = "update";
						}
					}
				}

				if ($dialplan_type == "add") {
					//add the dialplan entry for fax
						$dialplan_name = $fax_name;
						$dialplan_order ='310';
						$dialplan_context = $_SESSION['context'];
						$dialplan_enabled = 'true';
						$dialplan_description = $fax_description;
						$app_uuid = '24108154-4ac3-1db6-1551-4731703a4440';
						dialplan_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

						//<!-- default ${domain_name} -->
						//<condition field="destination_number" expression="^\*9978$">
							$dialplan_detail_tag = 'condition'; //condition, action, antiaction
							$dialplan_detail_type = 'destination_number';
							$dialplan_detail_data = '^'.$fax_destination_number.'$';
							$dialplan_detail_order = '010';
							$dialplan_detail_group = '';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="answer" />
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'answer';
							$dialplan_detail_data = '';
							$dialplan_detail_order = '020';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="set" data="fax_uuid="/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'set';
							$dialplan_detail_data = 'fax_uuid='.$fax_uuid;
							$dialplan_detail_order = '030';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="system" data="$switch_scripts_dir/emailfax.sh USER DOMAIN {$_SESSION['switch']['scripts']['dir']}/fax/inbox/9872/${last_fax}.tif"/>
							$dialplan_detail_tag = 'action'; //condition, action, anti-action
							$dialplan_detail_type = 'set';
							$dialplan_detail_data = "api_hangup_hook=lua app/fax/resources/scripts/hangup_rx.lua";
							$dialplan_detail_order = '040';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="set" data="fax_enable_t38=true"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'set';
							$dialplan_detail_data = 'fax_enable_t38=true';
							$dialplan_detail_order = '050';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="set" data="fax_enable_t38_request=false"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'set';
							$dialplan_detail_data = 'fax_enable_t38_request=false';
							$dialplan_detail_order = '060';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="set" data="last_fax=${caller_id_number}-${strftime(%Y-%m-%d-%H-%M-%S)}"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'set';
							$dialplan_detail_data = 'last_fax=${caller_id_number}-${strftime(%Y-%m-%d-%H-%M-%S)}';
							$dialplan_detail_order = '070';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="playback" data="silence_stream://2000"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'playback';
							$dialplan_detail_data = 'silence_stream://2000';
							$dialplan_detail_order = '080';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="rxfax" data="$switch_storage_dir/fax/inbox/${last_fax}.tif"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'rxfax';
							if (count($_SESSION["domains"]) > 1) {
								$dialplan_detail_data = $_SESSION['switch']['storage']['dir'].'/fax/'.$_SESSION['domain_name'].'/'.$fax_extension.'/inbox/'.$forward_prefix.'${last_fax}.tif';
							}
							else {
								$dialplan_detail_data = $_SESSION['switch']['storage']['dir'].'/fax/'.$fax_extension.'/inbox/'.$forward_prefix.'${last_fax}.tif';
							}
							$dialplan_detail_order = '090';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

						//<action application="hangup"/>
							$dialplan_detail_tag = 'action'; //condition, action, antiaction
							$dialplan_detail_type = 'hangup';
							$dialplan_detail_data = '';
							$dialplan_detail_order = '100';
							dialplan_detail_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);
				}
				if ($dialplan_type == "update") {
					//update the fax dialplan entry
						$sql = "update v_dialplans set ";
						$sql .= "dialplan_name = '$fax_name', ";
						if (strlen($dialplan_order) > 0) {
							$sql .= "dialplan_order = '333', ";
						}
						$sql .= "dialplan_context = '".$_SESSION['context']."', ";
						$sql .= "dialplan_enabled = 'true', ";
						$sql .= "dialplan_description = '$fax_description' ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$db->query($sql);
						unset($sql);

					//update dialplan detail condition
						$sql = "update v_dialplan_details set ";
						$sql .= "dialplan_detail_data = '^".$fax_destination_number."$' ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and dialplan_detail_tag = 'condition' ";
						$sql .= "and dialplan_detail_type = 'destination_number' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$db->query($sql);
						unset($sql);

					//update dialplan detail action
						if (count($_SESSION["domains"]) > 1) {
							$dialplan_detail_data = $_SESSION['switch']['storage']['dir'].'/fax/'.$_SESSION['domain_name'].'/'.$fax_extension.'/inbox/'.$forward_prefix.'${last_fax}.tif';
						}
						else {
							$dialplan_detail_data = $_SESSION['switch']['storage']['dir'].'/fax/'.$fax_extension.'/inbox/'.$forward_prefix.'${last_fax}.tif';
						}
						$sql = "update v_dialplan_details set ";
						$sql .= "dialplan_detail_data = '".$dialplan_detail_data."' ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and dialplan_detail_tag = 'action' ";
						$sql .= "and dialplan_detail_type = 'rxfax' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$db->query($sql);

					//update dialplan detail action
						$dialplan_detail_tag = 'action'; //condition, action, antiaction
						$dialplan_detail_type = 'set';
						$dialplan_detail_data = "api_hangup_hook=lua app/fax/resources/scripts/hangup_rx.lua";
						$sql = "update v_dialplan_details set ";
						$sql .= "dialplan_detail_data = '".check_str($dialplan_detail_data)."' ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and dialplan_detail_tag = 'action' ";
						$sql .= "and dialplan_detail_type = 'set' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$sql .= "and dialplan_detail_data like 'api_hangup_hook=%' ";
						$db->query(check_sql($sql));
				}

			//save the xml
				save_dialplan_xml();

			//apply settings reminder
				$_SESSION["reload_xml"] = true;

			//clear the cache
				$cache = new cache;
				$cache->delete("dialplan:".$_SESSION["context"]);

			//redirect the browser
				if ($action == "update" && permission_exists('fax_extension_edit')) {
					$_SESSION["message"] = $text['confirm-update'];
				}
				if ($action == "add" && permission_exists('fax_extension_add')) {
					$_SESSION["message"] = $text['confirm-add'];
				}
				header("Location: fax.php");
				return;

		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (strlen($_GET['id']) > 0 && $_POST["persistformvar"] != "true") {
		$fax_uuid = check_str($_GET["id"]);
		$sql = "select * from v_fax ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and fax_uuid = '$fax_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		if (count($result) == 0) {
			echo "access denied";
			exit;
		}
		foreach ($result as &$row) {
			$dialplan_uuid = $row["dialplan_uuid"];
			$fax_extension = $row["fax_extension"];
			$fax_accountcode = $row["accountcode"];
			$fax_destination_number = $row["fax_destination_number"];
			$fax_prefix = $row["fax_prefix"];
			$fax_name = $row["fax_name"];
			$fax_email = $row["fax_email"];
			$fax_email_connection_type = $row["fax_email_connection_type"];
			$fax_email_connection_host = $row["fax_email_connection_host"];
			$fax_email_connection_port = $row["fax_email_connection_port"];
			$fax_email_connection_security = $row["fax_email_connection_security"];
			$fax_email_connection_validate = $row["fax_email_connection_validate"];
			$fax_email_connection_username = $row["fax_email_connection_username"];
			$fax_email_connection_password = $row["fax_email_connection_password"];
			$fax_email_connection_mailbox = $row["fax_email_connection_mailbox"];
			$fax_email_inbound_subject_tag = $row["fax_email_inbound_subject_tag"];
			$fax_email_outbound_subject_tag = $row["fax_email_outbound_subject_tag"];
			$fax_email_outbound_authorized_senders = $row["fax_email_outbound_authorized_senders"];
			$fax_caller_id_name = $row["fax_caller_id_name"];
			$fax_caller_id_number = $row["fax_caller_id_number"];
			$fax_forward_number = $row["fax_forward_number"];
			$fax_description = $row["fax_description"];
		}
		unset ($prep_statement);
	}

//replace the dash with a space
	$fax_name = str_replace("-", " ", $fax_name);

//set the dialplan_uuid
	if (strlen($dialplan_uuid) == 0) {
		$dialplan_uuid = uuid();
	}

//show the header
	require_once "resources/header.php";

//advanced button js
	echo "<script type='text/javascript' language='JavaScript'>\n";
	echo "	function toggle_advanced(advanced_id) {\n";
	echo "		$('#'+advanced_id).toggle();\n";
	echo "		if ($('#'+advanced_id).is(':visible')) {\n";
	echo "			$('#page').animate({scrollTop: $('#'+advanced_id).offset().top - 80}, 'slow');\n";
	echo "		}\n";
	echo "	}\n";
	echo "	function add_sender() {\n";
	echo "		var newdiv = document.createElement('div');\n";
	echo "		newdiv.innerHTML = \"<input type='text' class='formfld' style='width: 225px; min-width: 225px; max-width: 225px; margin-top: 3px;' name='fax_email_outbound_authorized_senders[]' maxlength='255'>\";";
	echo "		document.getElementById('authorized_senders').appendChild(newdiv);";
	echo "	}\n";
	echo "</script>\n";

//fax extension form
	echo "<form method='post' name='frm' action=''>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "	<td align='left' width='30%' valign='top' nowrap='nowrap'><b>".$text['header-fax_server_settings']."</b><br><br></td>\n";
	echo "	<td width='70%' valign='top' align='right'>\n";
	echo "		<input type='button' class='btn' name='' alt=\"".$text['button-back']."\" onclick=\"window.location='fax.php'\" value=\"".$text['button-back']."\">\n";
	if ((if_group("admin") || if_group("superadmin")) && $action == "update") {
		echo "	<input type='button' class='btn' alt=\"".$text['button-copy']."\" onclick=\"if (confirm('".$text['confirm-copy-info']."')){window.location='fax_copy.php?id=".$fax_uuid."';}\" value=\"".$text['button-copy']."\">\n";
	}
	echo "		<input type='submit' class='btn' name='submit' value='".$text['button-save']."'>\n";
	echo "	</td>\n";
	echo "	</tr>\n";

	if (!permission_exists('fax_extension_delete')) {

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-email']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='email' name='fax_email' maxlength='255' value=\"$fax_email\">\n";
		echo "<br />\n";
		echo "	".$text['description-email']."\n";
		echo "</td>\n";
		echo "</tr>\n";

	}
	else { //admin, superadmin, etc

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-name']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_name' maxlength='255' value=\"$fax_name\" required='required'>\n";
		echo "<br />\n";
		echo "".$text['description-name']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-extension']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_extension' maxlength='255' value=\"$fax_extension\" required='required'>\n";
		echo "<br />\n";
		echo "".$text['description-extension']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		if (if_group("superadmin") || (if_group("admin") && $billing_app_exists)) {
			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'='nowrap='nowrap''>\n";
			echo "    ".$text['label-accountcode']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			if ($billing_app_exists) {
				$sql_accountcode = "SELECT type_value FROM v_billings WHERE domain_uuid = '".$domain_uuid."'";
				echo "<select name='accountcode' id='accountcode' class='formfld'>\n";
				$prep_statement_accountcode = $db->prepare(check_sql($sql_accountcode));
				$prep_statement_accountcode->execute();
				$result_accountcode = $prep_statement_accountcode->fetchAll(PDO::FETCH_NAMED);
				foreach ($result_accountcode as &$row_accountcode) {
					$selected = '';
					if (($action == "add") && ($row_accountcode['type_value'] == $_SESSION['domain_name'])){
						$selected='selected="selected"';
					}
					elseif ($row_accountcode['type_value'] == $fax_accountcode){
						$selected='selected="selected"';
					}
					echo "<option value=\"".$row_accountcode['type_value']."\" $selected>".$row_accountcode['type_value']."</option>\n";
				}
				unset($sql_accountcode, $prep_statement_accountcode, $result_accountcode);
				echo "</select>";
			}
			else {
				if ($action == "add") { $fax_accountcode = $_SESSION['domain_name']; }
				echo "<input class='formfld' type='text' name='accountcode' maxlength='255' value=\"".$fax_accountcode."\">\n";
			}

			echo "<br />\n";
			echo $text['description-accountcode']."\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-destination-number']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_destination_number' maxlength='255' value=\"$fax_destination_number\">\n";
		echo "<br />\n";
		echo " ".$text['description-destination-number']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-fax_prefix']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_prefix' maxlength='255' value=\"$fax_prefix\">\n";
		echo "<br />\n";
		echo " ".$text['description-fax_prefix']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-email']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_email' maxlength='255' value=\"$fax_email\">\n";
		if (permission_exists('fax_extension_advanced') && function_exists("imap_open") && file_exists("fax_files_remote.php")) {
			echo "<input type='button' class='btn' value='".$text['button-advanced']."' onclick=\"toggle_advanced('advanced_email_connection');\">\n";
		}
		echo "<br />\n";
		echo "	".$text['description-email']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td width='30%' class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-caller-id-name']."\n";
		echo "</td>\n";
		echo "<td width='70%' class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_caller_id_name' maxlength='255' value=\"$fax_caller_id_name\">\n";
		echo "<br />\n";
		echo "".$text['description-caller-id-name']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-caller-id-number']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='number' name='fax_caller_id_number' maxlength='255' min='0' step='1' value=\"$fax_caller_id_number\">\n";
		echo "<br />\n";
		echo "".$text['description-caller-id-number']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-forward']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_forward_number' maxlength='255' value=\"".((is_numeric($fax_forward_number)) ? format_phone($fax_forward_number) : $fax_forward_number)."\">\n";
		echo "<br />\n";
		echo "".$text['description-forward-number']."\n";
		echo "</td>\n";
		echo "</tr>\n";

		if (if_group("admin") || if_group("superadmin")) {
			if ($action == "update") {
				echo "	<tr>";
				echo "		<td class='vncell' valign='top'>".$text['label-user-list']."</td>";
				echo "		<td class='vtable'>";

				$sql = "SELECT * FROM v_fax_users as e, v_users as u ";
				$sql .= "where e.user_uuid = u.user_uuid  ";
				$sql .= "and e.domain_uuid = '".$_SESSION['domain_uuid']."' ";
				$sql .= "and e.fax_uuid = '".$fax_uuid."' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				$result_count = count($result);
				if ($result_count > 0) {
					echo "		<table width='52%'>\n";
					foreach($result as $field) {
						echo "		<tr>\n";
						echo "			<td class='vtable'>".$field['username']."</td>\n";
						echo "			<td>\n";
						echo "				<a href='fax_edit.php?id=".$fax_uuid."&domain_uuid=".$_SESSION['domain_uuid']."&user_uuid=".$field['user_uuid']."&a=delete' alt='delete' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>\n";
						echo "			</td>\n";
						echo "		</tr>\n";
						$assigned_user_uuids[] = $field['user_uuid'];
					}
					echo "		</table>\n";
					echo "			<br />\n";
				}
				$sql = "SELECT * FROM v_users ";
				$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
				foreach($assigned_user_uuids as $assigned_user_uuid) {
					$sql .= "and user_uuid <> '".$assigned_user_uuid."' ";
				}
				unset($assigned_user_uuids);
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				echo "			<select name=\"user_uuid\" class='formfld' style='width: auto;'>\n";
				echo "			<option value=\"\"></option>\n";
				$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
				foreach($result as $field) {
					echo "			<option value='".$field['user_uuid']."'>".$field['username']."</option>\n";
				}
				echo "			</select>";
				echo "			<input type=\"submit\" class='btn' value=\"".$text['button-add']."\">\n";
				unset($sql, $result);
				echo "			<br>\n";
				echo "			".$text['description-user-add']."\n";
				echo "			<br />\n";
				echo "		</td>";
				echo "	</tr>";
			}
		}

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
		echo "	".$text['label-description']."\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='fax_description' maxlength='255' value=\"$fax_description\">\n";
		echo "<br />\n";
		echo "".$text['description-info']."\n";
		echo "</td>\n";
		echo "</tr>\n";

	}

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "			<br>";
	if ($action == "update") {
		if (if_group("user")) {
			echo "	<input type='hidden' name='fax_name' value=\"$fax_name\">\n";
			echo "	<input type='hidden' name='fax_extension' value=\"$fax_extension\">\n";
			echo "	<input type='hidden' name='fax_destination_number' value=\"$fax_destination_number\">\n";
			echo "	<input type='hidden' name='fax_caller_id_name' value=\"$fax_caller_id_name\">\n";
			echo "	<input type='hidden' name='fax_caller_id_number' value=\"$fax_caller_id_number\">\n";
			echo "	<input type='hidden' name='fax_forward_number' value=\"".((is_numeric($fax_forward_number)) ? format_phone($fax_forward_number) : $fax_forward_number)."\">\n";
			echo "	<input type='hidden' name='fax_description' value=\"$fax_description\">\n";
		}
		echo "		<input type='hidden' name='fax_uuid' value='$fax_uuid'>\n";
		echo "		<input type='hidden' name='dialplan_uuid' value='$dialplan_uuid'>\n";
	}
	echo "			<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "<br />\n";

	if (permission_exists('fax_extension_advanced') && function_exists("imap_open") && file_exists("fax_files_remote.php")) {

		echo "<div id='advanced_email_connection' ".(($fax_email_connection_host == '') ? "style='display: none;'" : null).">\n";

		echo "<b>".$text['label-advanced_settings']."</b><br><br>";
		echo $text['description-advanced_settings']."<br><br>";

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "	<tr>";
		echo "		<td width='50%' valign='top'>";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

			echo "<tr>";
			echo "<td colspan='2'>";
			echo "	<span style='font-weight: bold; color: #000;'>".$text['label-email_account_connection']."</span><br><br>";
			echo "</td>";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_type']."\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<select class='formfld' name='fax_email_connection_type'>\n";
			echo "		<option value='imap'>IMAP</option>\n";
			echo "		<option value='pop3' ".(($fax_email_connection_type == 'pop3') ? "selected" : null).">POP3</option>\n";
			echo "	</select>\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_type']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_server']."\n";
			echo "</td>\n";
			echo "<td class='vtable' style='white-space: nowrap='nowrap';' align='left'>\n";
			echo "	<input class='formfld' type='text' name='fax_email_connection_host' maxlength='255' value=\"$fax_email_connection_host\">&nbsp;:&nbsp;";
			echo 	"<input class='formfld' style='width: 50px; min-width: 50px; max-width: 50px;' type='text' name='fax_email_connection_port' maxlength='5' value=\"$fax_email_connection_port\">\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_server']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_security']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<select class='formfld' name='fax_email_connection_security'>\n";
			echo "		<option value=''></option>\n";
			echo "		<option value='ssl' ".(($fax_email_connection_security == 'ssl') ? "selected" : null).">SSL</option>\n";
			echo "		<option value='tls' ".(($fax_email_connection_security == 'tls') ? "selected" : null).">TLS</option>\n";
			echo "	</select>\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_security']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_validate']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<select class='formfld' name='fax_email_connection_validate'>\n";
			echo "		<option value='true'>".$text['option-true']."</option>\n";
			echo "		<option value='false' ".(($fax_email_connection_validate == 'false') ? "selected" : null).">".$text['option-false']."</option>\n";
			echo "	</select>\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_validate']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_username']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input class='formfld' type='text' name='fax_email_connection_username' maxlength='255' value=\"$fax_email_connection_username\">\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_username']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_password']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input class='formfld' type='password' name='fax_email_connection_password' onmouseover=\"this.type='text';\" onfocus=\"this.type='text';\" onmouseout=\"if (!$(this).is(':focus')) { this.type='password'; }\" onblur=\"this.type='password';\" autocomplete='off' maxlength='50' value=\"$fax_email_connection_password\">\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_password']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_connection_mailbox']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input class='formfld' type='text' name='fax_email_connection_mailbox' maxlength='255' value=\"$fax_email_connection_mailbox\">\n";
			echo "<br />\n";
			echo "	".$text['description-email_connection_mailbox']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "</table>\n";

		echo "		</td>";
		echo "		<td style='white-space: nowrap='nowrap';'>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "		<td width='50%' valign='top'>";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

			echo "<tr>";
			echo "<td colspan='2'>";
			echo "	<span style='font-weight: bold; color: #000;'>".$text['label-email_remote_inbox']."</span><br><br>";
			echo "</td>";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-email_inbound_subject_tag']."\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<span style='font-size: 18px;'>[ <input class='formfld' type='text' name='fax_email_inbound_subject_tag' maxlength='255' value=\"$fax_email_inbound_subject_tag\"> ]</span>\n";
			echo "<br />\n";
			echo "	".$text['description-email_inbound_subject_tag']."\n";
			echo "</td>\n";
			echo "</tr>\n";

			if (file_exists("fax_emails.php")) {

				echo "<tr>";
				echo "<td colspan='2'>";
				echo "	<br><br>";
				echo "	<span style='font-weight: bold; color: #000;'>".$text['label-email_email-to-fax']."</span><br><br>";
				echo "</td>";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td width='30%' class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
				echo "	".$text['label-email_outbound_subject_tag']."\n";
				echo "</td>\n";
				echo "<td width='70%' class='vtable' align='left'>\n";
				echo "	<span style='font-size: 18px;'>[ <input class='formfld' type='text' name='fax_email_outbound_subject_tag' maxlength='255' value=\"$fax_email_outbound_subject_tag\"> ]</span>\n";
				echo "<br />\n";
				echo "	".$text['description-email_outbound_subject_tag']."\n";
				echo "</td>\n";
				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
				echo "	".$text['label-email_outbound_authorized_senders']."\n";
				echo "</td>\n";
				echo "<td class='vtable' align='left' valign='top'>\n";
				echo "	<table cellpadding='0' cellspacing='0' border='0'>";
				echo "		<tr>";
				echo "			<td id='authorized_senders'>";
				if (substr_count($fax_email_outbound_authorized_senders, ',') > 0) {
					$senders = explode(',', $fax_email_outbound_authorized_senders);
				}
				else {
					$senders[] = $fax_email_outbound_authorized_senders;
				}
				$senders[] = ''; // empty one
				foreach ($senders as $sender_num => $sender) {
					echo "	<input class='formfld' style='width: 225px; min-width: 225px; max-width: 225px; ".(($sender_num > 0) ? "margin-top: 3px;" : null)."' type='text' name='fax_email_outbound_authorized_senders[]' maxlength='255' value=\"$sender\">".((sizeof($senders) > 0 && $sender_num < (sizeof($senders) - 1) ) ? "<br>" : null);
				}
				echo "			</td>";
				echo "			<td style='vertical-align: bottom;'>";
				echo "				<a href='javascript:void(0);' onclick='add_sender();'>$v_link_label_add</a>";
				echo "			</td>";
				echo "		</tr>";
				echo "	</table>";
				echo "	".$text['description-email_outbound_authorized_senders']."\n";
				echo "</td>\n";
				echo "</tr>\n";

			}

			echo "</table>\n";

		echo "		</td>";
		echo "	</tr>";
		echo "	<tr>";
		echo "		<td colspan='3' style='text-align: right;'>";
		echo "			<br><input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
		echo "		</td>";
		echo "	<tr>";
		echo "</table>";
		echo "<br>";
		echo "</div>\n";

	}

	echo "</form>";

//show the footer
	require_once "resources/footer.php";
?>
