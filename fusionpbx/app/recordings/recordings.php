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
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//set the max php execution time
	ini_set(max_execution_time,7200);

//get the http get values and set them as php variables
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//download the recordings
	if ($_GET['a'] == "download" && (permission_exists('recording_play') || permission_exists('recording_download'))) {
		session_cache_limiter('public');
		if ($_GET['type'] = "rec") {
			$recording_uuid = check_str($_GET['id']);
			$path = $_SESSION['switch']['recordings']['dir'];
			//get recording details from db
			$sql = "select recording_filename, recording_base64 from v_recordings ";
			$sql .= "where domain_uuid = '".$domain_uuid."' ";
			$sql .= "and recording_uuid = '".$recording_uuid."' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			if (count($result) > 0) {
				foreach($result as &$row) {
					$recording_filename = $row['recording_filename'];
					if ($_SESSION['recordings']['storage_type']['text'] == 'base64' && $row['recording_base64'] != '') {
						$recording_decoded = base64_decode($row['recording_base64']);
						file_put_contents($path.'/'.$recording_filename, $recording_decoded);
					}
					break;
				}
			}
			unset ($sql, $prep_statement, $result, $recording_decoded);

			if (file_exists($path.'/'.$recording_filename)) {
				$fd = fopen($path.'/'.$recording_filename, "rb");
				if ($_GET['t'] == "bin") {
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					header("Content-Description: File Transfer");
				}
				else {
					$file_ext = substr($recording_filename, -3);
					if ($file_ext == "wav") {
						header("Content-Type: audio/x-wav");
					}
					if ($file_ext == "mp3") {
						header("Content-Type: audio/mpeg");
					}
				}
				header('Content-Disposition: attachment; filename="'.$recording_filename.'"');
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header("Content-Length: " . filesize($path.'/'.$recording_filename));
				ob_clean();
				fpassthru($fd);
			}

			//if base64, remove temp recording file
			if ($_SESSION['recordings']['storage_type']['text'] == 'base64' && $row['recording_base64'] != '') {
				@unlink($path.'/'.$recording_filename);
			}
		}
		exit;
	}

//upload the recording
	if (permission_exists('recording_upload')) {
		if ($_POST['submit'] == "Upload" && $_POST['type'] == 'rec') {
			if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
				move_uploaded_file($_FILES['ulfile']['tmp_name'], $_SESSION['switch']['recordings']['dir'].'/'.$_FILES['ulfile']['name']);
				unset($_POST['txtCommand']);

				$_SESSION['message'] = $text['message-uploaded'].": ".htmlentities($_FILES['ulfile']['name']);
			}
			header("Location: recordings.php");
			exit;
		}
	}

//check the permission
	if (permission_exists('recording_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//get existing recordings
	$sql = "select recording_uuid, recording_filename, recording_base64 from v_recordings ";
	$sql .= "where domain_uuid = '".$domain_uuid."' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$array_recordings[$row['recording_uuid']] = $row['recording_filename'];
		$array_base64_exists[$row['recording_uuid']] = ($row['recording_base64'] != '') ? true : false;
		//if not base64, convert back to local files and remove base64 from db
		if ($_SESSION['recordings']['storage_type']['text'] != 'base64' && $row['recording_base64'] != '') {
			if (!file_exists($_SESSION['switch']['recordings']['dir'].'/'.$row['recording_filename'])) {
				$recording_decoded = base64_decode($row['recording_base64']);
				file_put_contents($_SESSION['switch']['recordings']['dir'].'/'.$row['recording_filename'], $recording_decoded);
				$sql = "update v_recordings set recording_base64 = null where domain_uuid = '".$domain_uuid."' and recording_uuid = '".$row['recording_uuid']."' ";
				$db->exec(check_sql($sql));
				unset($sql);
			}
		}
	}
	unset ($prep_statement);

//add recordings to the database
	if (is_dir($_SESSION['switch']['recordings']['dir'].'/')) {
		if ($dh = opendir($_SESSION['switch']['recordings']['dir'].'/')) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($_SESSION['switch']['recordings']['dir']."/".$file) == "file") {

					if (!in_array($file, $array_recordings)) {
						//file not found, add it to the database
						$a_file = explode("\.", $file);
						$recording_uuid = uuid();
						$sql = "insert into v_recordings ";
						$sql .= "(";
						$sql .= "domain_uuid, ";
						$sql .= "recording_uuid, ";
						$sql .= "recording_filename, ";
						$sql .= "recording_name, ";
						$sql .= "recording_description ";
						if ($_SESSION['recordings']['storage_type']['text'] == 'base64') {
							$sql .= ", recording_base64 ";
						}
						$sql .= ")";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'".$domain_uuid."', ";
						$sql .= "'".$recording_uuid."', ";
						$sql .= "'".$file."', ";
						$sql .= "'".$a_file[0]."', ";
						$sql .= "'' ";
						if ($_SESSION['recordings']['storage_type']['text'] == 'base64') {
							$recording_base64 = base64_encode(file_get_contents($_SESSION['switch']['recordings']['dir'].'/'.$file));
							$sql .= ", '".$recording_base64."' ";
						}
						$sql .= ")";
						$db->exec(check_sql($sql));
						unset($sql);
					}
					else {
						//file found, check if base64 present
						if ($_SESSION['recordings']['storage_type']['text'] == 'base64') {
							$found_recording_uuid = array_search($file, $array_recordings);
							if (!$array_base64_exists[$found_recording_uuid]) {
								$recording_base64 = base64_encode(file_get_contents($_SESSION['switch']['recordings']['dir'].'/'.$file));
								$sql = "update v_recordings set ";
								$sql .= "recording_base64 = '".$recording_base64."' ";
								$sql .= "where domain_uuid = '".$domain_uuid."' ";
								$sql .= "and recording_uuid = '".$found_recording_uuid."' ";
								$db->exec(check_sql($sql));
								unset($sql);
							}
						}
					}

					//if base64, remove local file
					if ($_SESSION['recordings']['storage_type']['text'] == 'base64' && file_exists($_SESSION['switch']['recordings']['dir'].'/'.$file)) {
						@unlink($_SESSION['switch']['recordings']['dir'].'/'.$file);
					}

				}
			} //while
			closedir($dh);
		} //if
	} //if


//add paging
	require_once "resources/paging.php";

//include the header
	$document['title'] = $text['title'];
	require_once "resources/header.php";

//begin the content
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<b>".$text['title']."</b>";
	echo "			<br /><br />\n";
	echo "			".stripslashes($text['description'])."\n";
	echo "			<br /><br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>";
	echo "<br /><br />\n";

	if (permission_exists('recording_upload')) {
		echo "<b>".$text['header']."</b>";
		echo "<br><br>";
		echo "<form action='' method='post' enctype='multipart/form-data' name='frmUpload'>\n";
		echo "<input name='type' type='hidden' value='rec'>\n";
		echo "".$text['label-upload']."\n";
		echo "<input name='ulfile' type='file' class='formfld fileinput' style='width: 260px;' id='ulfile'>\n";
		echo "<input name='submit' type='submit'  class='btn' id='upload' value=\"".$text['button-upload']."\">\n";
		echo "</form>";
		echo "<br><br>\n";
	}

	$sql = "select * from v_recordings ";
	$sql .= "where domain_uuid = '".$domain_uuid."' ";
	if (strlen($order_by)> 0) { $sql .= "order by ".$order_by." ".$order." "; }
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);

	$rows_per_page = 100;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

	$sql = "select * from v_recordings ";
	$sql .= "where domain_uuid = '".$domain_uuid."' ";
	$sql .= "order by ".((strlen($order_by) > 0) ? $order_by." ".$order : "recording_name asc")." ";
	$sql .= "limit ".$rows_per_page." offset ".$offset." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	$row_style["2"] = "row_style2";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('recording_name', $text['label-recording_name'], $order_by, $order);
	echo th_order_by('recording_filename', $text['label-file_name'], $order_by, $order);
	echo "<th class='listhdr' nowrap>".$text['label-tools']."</th>\n";
	if ($_SESSION['recordings']['storage_type']['text'] != 'base64') {
		echo "<th class='listhdr' style='text-align: center;' nowrap>".$text['label-file-size']."</th>\n";
	}
	echo th_order_by('recording_description', $text['label-description'], $order_by, $order);
	echo "<td class='list_control_icons'>&nbsp;</td>\n";
	echo "</tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			if ($_SESSION['recordings']['storage_type']['text'] != 'base64') {
				$tmp_filesize = filesize($_SESSION['switch']['recordings']['dir'].'/'.$row['recording_filename']);
				$tmp_filesize = byte_convert($tmp_filesize);
			}

			//playback progress bar
			echo "<tr id='recording_progress_bar_".$row['recording_uuid']."' style='display: none;'><td colspan='5'><span class='playback_progress_bar' id='recording_progress_".$row['recording_uuid']."'></span></td></tr>\n";

			$tr_link = (permission_exists('recording_edit')) ? "href='recording_edit.php?id=".$row['recording_uuid']."'" : null;
			echo "<tr ".$tr_link.">\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			echo 		$row['recording_name'];
			echo 	"</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			echo "		\n";
			echo $row['recording_filename'];
			echo "	  </a>";
			echo "	</td>\n";
			if (strlen($row['recording_filename']) > 0) {
				echo "	<td valign='top' class='".$row_style["2"]." ".((!$c) ? "row_style_hor_mir_grad" : null)." tr_link_void'>";
				$recording_file_path = $row['recording_filename'];
				$recording_file_name = strtolower(pathinfo($recording_file_path, PATHINFO_BASENAME));
				$recording_file_ext = pathinfo($recording_file_name, PATHINFO_EXTENSION);
				switch ($recording_file_ext) {
					case "wav" : $recording_type = "audio/wav"; break;
					case "mp3" : $recording_type = "audio/mpeg"; break;
					case "ogg" : $recording_type = "audio/ogg"; break;
				}
				echo "<audio id='recording_audio_".$row['recording_uuid']."' style='display: none;' preload='none' ontimeupdate=\"update_progress('".$row['recording_uuid']."')\" onended=\"recording_reset('".$row['recording_uuid']."');\" src=\"".PROJECT_PATH."/app/recordings/recordings.php?a=download&type=rec&id=".$row['recording_uuid']."\" type='".$recording_type."'></audio>";
				echo "<span id='recording_button_".$row['recording_uuid']."' onclick=\"recording_play('".$row['recording_uuid']."')\" title='".$text['label-play']." / ".$text['label-pause']."'>".$v_link_label_play."</span>";
				echo "<a href=\"".PROJECT_PATH."/app/recordings/recordings.php?a=download&type=rec&t=bin&id=".$row['recording_uuid']."\" title='".$text['label-download']."'>".$v_link_label_download."</a>";
			}
			else {
				echo "	<td valign='top' class='".$row_style[$c]."'>";
				echo "&nbsp;";
			}
			echo "	</td>\n";
			if ($_SESSION['recordings']['storage_type']['text'] != 'base64') {
				echo "	<td class='".$row_style[$c]."' style='text-align: center;'>".$tmp_filesize."</td>\n";
			}
			echo "	<td valign='top' class='row_stylebg' width='30%'>".$row['recording_description']."&nbsp;</td>\n";
			echo "	<td class='list_control_icons'>";
			if (permission_exists('recording_edit')) {
				echo "<a href='recording_edit.php?id=".$row['recording_uuid']."' alt='edit'>$v_link_label_edit</a>";
			}
			if (permission_exists('recording_delete')) {
				echo "<a href='recording_delete.php?id=".$row['recording_uuid']."' alt='delete' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results
	echo "</table>\n";

	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td class='list_control_icons'>";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "<br><br>\n";

//include the footer
	require_once "resources/footer.php";

?>