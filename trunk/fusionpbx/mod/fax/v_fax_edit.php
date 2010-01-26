<?php
/* $Id$ */
/*
	v_fax_edit.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
if ($_GET['a'] == "download") {

	session_cache_limiter('public');
	//Test to see if it is in the inbox or sent directory.
	if ($_GET['type'] == "fax_inbox") {
		if (file_exists($v_storage_dir.'/fax/'.$_GET['ext'].'/inbox/'.$_GET['filename'])) {
			$tmp_faxdownload_file = "".$v_storage_dir.'/fax/'.$_GET['ext'].'/inbox/'.$_GET['filename'];
		}
	}else if ($_GET['type'] == "fax_sent") {
		if  (file_exists($v_storage_dir.'/fax/'.$_GET['ext'].'/sent/'.$_GET['filename'])) {
			$tmp_faxdownload_file = "".$v_storage_dir.'/fax/'.$_GET['ext'].'/sent/'.$_GET['filename'];
		}
	}
	//Let's see if we found it.
	if (strlen($tmp_faxdownload_file) > 0) {
		$fd = fopen($tmp_faxdownload_file, "rb");

		if ($_GET['t'] == "bin") {
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="'.$_GET['filename'].'"');
		}
		else {
			$file_ext = substr($_GET['filename'], -3);
			if ($file_ext == "tif") {
			  header("Content-Type: image/tiff");
			}else if ($file_ext == "png") {
			  header("Content-Type: image/png");
			}else if ($file_ext == "pdf") {
			  header("Content-Type: application/pdf");
			}
		}
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		header("Content-Length: " . filesize($tmp_faxdownload_file));
		fpassthru($fd);
	}else {
		echo "File not found.";
	}

	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$fax_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//POST to PHP variables
	if (count($_POST)>0) {
		//$v_id = check_str($_POST["v_id"]);
		$faxextension = check_str($_POST["faxextension"]);
		$faxname = check_str($_POST["faxname"]);
		$faxemail = check_str($_POST["faxemail"]);
		$faxdomain = check_str($_POST["faxdomain"]);
		$faxdescription = check_str($_POST["faxdescription"]);
	}

//clear file status cache
	clearstatcache(); 

//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
	$dir_fax_inbox = $v_storage_dir.'/fax/'.$faxextension.'/inbox';
	$dir_fax_sent = $v_storage_dir.'/fax/'.$faxextension.'/sent';
	$dir_fax_temp = $v_storage_dir.'/fax/'.$faxextension.'/temp';

//make sure the directories exist
	if (!is_dir($v_storage_dir)) {
		mkdir($v_storage_dir);
		chmod($dir_fax_sent,0777);
	}
	if (!is_dir($v_storage_dir.'/fax/'.$faxextension)) {
		mkdir($v_storage_dir.'/fax/'.$faxextension,0777,true);
		chmod($v_storage_dir.'/fax/'.$faxextension,0777);
	}
	if (!is_dir($dir_fax_inbox)) { 
		mkdir($dir_fax_inbox,0777,true); 
		chmod($dir_fax_inbox,0777);
	}
	if (!is_dir($dir_fax_sent)) { 
		mkdir($dir_fax_sent,0777,true); 
		chmod($dir_fax_sent,0777);
	}
	if (!is_dir($dir_fax_temp)) {
		mkdir($dir_fax_temp);
		chmod($dir_fax_temp,0777);
	}

//upload and send the fax
if (($_POST['type'] == "fax_send") && is_uploaded_file($_FILES['fax_file']['tmp_name'])) {

	$fax_number = $_POST['fax_number'];
	$fax_name = $_FILES['fax_file']['name'];
	$fax_name = str_replace(".tif", "", $fax_name);
	$fax_name = str_replace(".tiff", "", $fax_name);
	$fax_name = str_replace(".pdf", "", $fax_name);
	$fax_gateway = $_POST['fax_gateway'];

	//get event socket connection information
		$sql = "";
		$sql .= "select * from v_settings ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$event_socket_ip_address = $row["event_socket_ip_address"];
			$event_socket_port = $row["event_socket_port"];
			$event_socket_password = $row["event_socket_password"];
			break; //limit to 1 row
		}

	//upload the file
		move_uploaded_file($_FILES['fax_file']['tmp_name'], $dir_fax_temp.$_FILES['fax_file']['name']);

		$fax_file_extension = substr($dir_fax_temp.$_FILES['fax_file']['name'], -4);
		if ($fax_file_extension == ".pdf") {
			chdir($dir_fax_temp);
			exec("gs -q -sDEVICE=tiffg3 -r204x98 -dNOPAUSE -sOutputFile=".$fax_name.".tif -- ".$fax_name.".pdf -c quit");
			//exec("rm ".$dir_fax_temp.$fax_name.".pdf");		
		}
		if ($fax_file_extension == ".tiff") {
			chdir($dir_fax_temp);
			exec("cp ".$dir_fax_temp.$fax_name.".tiff ".$dir_fax_temp.$fax_name.".tif");
			exec("rm ".$dir_fax_temp.$fax_name.".tiff");
		}

	//send the fax
		$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
		$cmd = "api originate [absolute_codec_string=PCMU]sofia/gateway/".$fax_gateway."/".$fax_number." &txfax(".$dir_fax_temp.$fax_name.".tif)";
		$response = event_socket_request($fp, $cmd);
		$response = str_replace("\n", "", $response);
		$uuid = str_replace("+OK ", "", $response);
		fclose($fp);

		//if ($response >= 1) {
		//	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
		//	$cmd = "api uuid_getvar ".$uuid." fax_result_text";
		//	echo $cmd."\n";
		//	$response = event_socket_request($fp, $cmd);
		//	$response = trim($response);
		//	fclose($fp);
		//}

	sleep(5);

	//copy the .tif to the sent directory
		exec("cp ".$dir_fax_temp.$fax_name.".tif ".$dir_fax_sent.$fax_name.".tif");
	
	//delete the .tif from the temp directory
		//exec("rm ".$dir_fax_temp.$fax_name.".tif");
	
	//convert the tif to pdf and png
		chdir($dir_fax_sent);
		//which tiff2pdf
		if (isfile("/usr/local/bin/tiff2png")) {
			exec("".bin_dir."/tiff2png ".$dir_fax_sent.$fax_name.".tif");
			exec("".bin_dir."/tiff2pdf -f -o ".$fax_name.".pdf ".$dir_fax_sent.$fax_name.".tif");
		}

	header("Location: v_fax_edit.php?id=".$id."&msg=".$response);
	exit;
} //end upload and send fax

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$fax_id = check_str($_POST["fax_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($faxextension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($faxname) == 0) { $msg .= "Please provide: Name<br>\n"; }
		//if (strlen($faxemail) == 0) { $msg .= "Please provide: Email<br>\n"; }
		if (strlen($faxdomain) == 0) { $msg .= "Please provide: Domain<br>\n"; }
		//if (strlen($faxdescription) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	$tmp = "\n";
	$tmp .= "v_id: $v_id\n";
	$tmp .= "Extension: $faxextension\n";
	$tmp .= "Name: $faxname\n";
	$tmp .= "Email: $faxemail\n";
	$tmp .= "Domain: $faxdomain\n";
	$tmp .= "Description: $faxdescription\n";



//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_fax ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "faxextension, ";
		$sql .= "faxname, ";
		$sql .= "faxemail, ";
		$sql .= "faxdomain, ";
		$sql .= "faxdescription ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$faxextension', ";
		$sql .= "'$faxname', ";
		$sql .= "'$faxemail', ";
		$sql .= "'$faxdomain', ";
		$sql .= "'$faxdescription' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

		sync_package_v_fax();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_fax set ";
		$sql .= "v_id = '$v_id', ";
		$sql .= "faxextension = '$faxextension', ";
		$sql .= "faxname = '$faxname', ";
		$sql .= "faxemail = '$faxemail', ";
		$sql .= "faxdomain = '$faxdomain', ";
		$sql .= "faxdescription = '$faxdescription' ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fax_id = '$fax_id' ";
		$db->exec(check_sql($sql));
		unset($sql);

		sync_package_v_fax();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fax.php\">\n";
		echo "<div align='center'>\n";
		echo "Update Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "update")
} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//Pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$fax_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_fax ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and fax_id = '$fax_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$v_id = $row["v_id"];
		$faxextension = $row["faxextension"];
		$faxname = $row["faxname"];
		$faxemail = $row["faxemail"];
		$faxdomain = $row["faxdomain"];
		$faxdescription = $row["faxdescription"];

		//set the fax directories. example /usr/local/freeswitch/storage/fax/329/inbox
			$dir_fax_inbox = $v_storage_dir.'/fax/'.$faxextension.'/inbox';
			$dir_fax_sent = $v_storage_dir.'/fax/'.$faxextension.'/sent';

		//make sure the directories exist
			if (!is_dir($v_storage_dir.'/fax/'.$faxextension)) {
				mkdir($v_storage_dir.'/fax/'.$faxextension,0777,true);
				chmod($v_storage_dir.'/fax/'.$faxextension,0777);
			}
			if (!is_dir($dir_fax_inbox)) { 
				mkdir($dir_fax_inbox,0777,true); 
				chmod($dir_fax_inbox,0777);
			}
			if (!is_dir($dir_fax_sent)) { 
				mkdir($dir_fax_sent,0777,true); 
				chmod($dir_fax_sent,0777);
			}

		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";

	echo "<script language='javascript' src='/includes/calendar_popcalendar.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_layers.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_menu.js'></script>\n";

	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";



	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Fax Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Fax Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_fax.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxextension' maxlength='255' value=\"$faxextension\">\n";
	echo "<br />\n";
	echo "Enter the fax extension here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxname' maxlength='255' value=\"$faxname\">\n";
	echo "<br />\n";
	echo "Enter the name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Email:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxemail' maxlength='255' value=\"$faxemail\">\n";
	echo "<br />\n";
	echo "Optional: Enter the email address to send the FAX to.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Domain:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxdomain' maxlength='255' value=\"$faxdomain\">\n";
	echo "<br />\n";
	echo "Enter the domain here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='faxdescription' maxlength='255' value=\"$faxdescription\">\n";
	echo "<br />\n";
	echo "Enter the description here.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='fax_id' value='$fax_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "<br />\n";
	echo "<br />\n";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left' width='30%'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Send</strong></span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	//pkg_add -r ghostscript8-nox11; rehash
	echo "			To send a fax you can upload a .tif file or if ghost script has been installed then you can also send a fax by uploading a PDF. \n";
	echo "			When sending a fax you can view status of the transmission by viewing the logs from the Status tab or by watching the response from the console.\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='right' nowrap>\n";
	echo "			<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\" name=\"frmUpload\" onSubmit=\"\">\n";
	echo "			  <table border='0' cellpadding='3' cellspacing='0' width='100%'>\n";
	echo "				<tr>\n";
	echo "					<td width='30%' align='left' valign=\"middle\" class=\"label\">\n";
	echo "						Fax Number\n";
	//echo "					</td>\n";
	//echo "					<td width='30%' valign=\"top\" class=\"label\">\n";
	echo "						<input type=\"text\" name=\"fax_number\" class='formfld' style='width: 175px' value=\"\">\n";
	echo "					</td>\n";
	//echo "					<td align=\"right\">Upload:</td>\n";
	echo "					<td width='30%' valign=\"middle\" align='center' class=\"label\">\n";
	echo "						Upload:\n";
	echo "						<input name=\"id\" type=\"hidden\" value=\"\$id\">\n";
	echo "						<input name=\"type\" type=\"hidden\" value=\"fax_send\">\n";
	echo "						<input name=\"fax_file\" type=\"file\" class=\"btn\" id=\"fax_file\">\n";
	echo "					</td>\n";
	//echo "						<td class=\"label\">\n";
	//echo "					</td>\n";
	echo "					<td width='30%' align='right' valign=\"middle\" class=\"label\">";
	echo "						Gateway\n";
	$tablename = 'v_gateways'; $fieldname = 'gateway'; $sqlwhereoptional = "where v_id = $v_id"; $fieldcurrentvalue = '$fax_gateway';
	echo htmlselect($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue);

	echo "					</td>\n";
	echo "					<td align='right'>\n";
	echo "						<input name=\"submit\" type=\"submit\" class=\"btn\" id=\"upload\" value=\"Send\">\n";
	echo "					</td>\n";
	echo "				</tr>\n";
	echo "			  </table>\n";
	echo "			</div>\n";
	echo "			</form>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";
	echo "\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "\n";
	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Inbox</strong></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>";

	if ($v_path_show) {
		echo "<b>location:</b> ";
		echo $dir_fax_inbox;
	}

	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "	<div id=\"\">\n";
	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<th width=\"50%\" class=\"listhdrr\">File Name (download)</td>\n";
	echo "		<th width=\"10%\" class=\"listhdrr\">Download</td>\n";
	echo "		<th width=\"10%\" class=\"listhdrr\">View</td>\n";
	echo "		<th width=\"20%\" class=\"listhdr\">Last Modified</td>\n";
	echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Size</td>\n";
	echo "	</tr>";

	if ($handle = opendir($dir_fax_inbox)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_file($dir_fax_inbox.'/'.$file)) {

				$tmp_filesize = filesize($dir_fax_inbox.'/'.$file);
				$tmp_filesize = byte_convert($tmp_filesize);

				$tmp_file_array = explode(".",$file);
				//print_r($tmp_file_array);
				$file_name = $tmp_file_array[0];
				$file_ext = $tmp_file_array[count($tmp_file_array)-1];
				if (strtolower($file_ext) == "tif") {

					echo "<tr>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=bin&ext=".$faxextension."&filename=".$file."\">\n";
					echo "    	$file";
					echo "	  </a>";
					echo "  </td>\n";

					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					if (file_exists($dir_fax_inbox.'/'.$file_name.".pdf")) {
						echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=bin&ext=".$faxextension."&filename=".$file_name.".pdf\">\n";
						echo "    	pdf";
						echo "	  </a>";
					}
					else {
						echo "&nbsp;\n";
					}
					echo "  </td>\n";

					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					if (file_exists($dir_fax_inbox.'/'.$file_name.".png")) {
						echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_inbox&t=png&ext=".$faxextension."&filename=".$file_name.".png\" target=\"_blank\">\n";
						echo "    	png";
						echo "	  </a>";
					}
					else {
						echo "&nbsp;\n";
					}
					echo "  </td>\n";

					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo 		date ("F d Y H:i:s", filemtime($dir_fax_inbox.'/'.$file));
					echo "  </td>\n";

					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	".$tmp_filesize;
					echo "  </td>\n";

					echo "  <td valign=\"middle\" nowrap class=\"list\">\n";
					echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
					echo "      <tr>\n";
					echo "        <td><a href=\"v_fax_edit.php?id=".$id."&type=fax_inbox&act=del&filename=".$file."\" onclick=\"return confirm('Do you really want to delete this file?')\"><img src=\"$v_icon_delete\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "      </tr>\n";
					echo "   </table>\n";
					echo "  </td>\n";
					echo "</tr>\n";
				}

			}
		}
		closedir($handle);
	}


	echo "	<tr>\n";
	echo "		<td class=\"list\" colspan=\"3\"></td>\n";
	echo "		<td class=\"list\"></td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "\n";
	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<span class=\"vexpl\"><span class=\"red\"><strong>Sent</strong></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";

	if ($v_path_show) {
		echo "<b>location:</b>\n";
		echo $dir_fax_sent."\n";
	}

	echo "		</td>\n";
	echo "	</tr>\n";
	echo "    </table>\n";
	echo "\n";
	echo "    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "    <tr>\n";
	echo "		<th width=\"50%\">File Name (download)</td>\n";
	echo "		<th width=\"10%\">Download</td>\n";
	echo "		<th width=\"10%\">View</td>\n";
	echo "		<th width=\"20%\">Last Modified</td>\n";
	echo "		<th width=\"10%\" nowrap>Size</td>\n";
	echo "		</tr>";

	if ($handle = opendir($dir_fax_sent)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_file($dir_fax_sent.'/'.$file)) {

				$tmp_filesize = filesize($dir_fax_sent.$file);
				$tmp_filesize = byte_convert($tmp_filesize);

				$tmp_file_array = explode(".",$file);
				//print_r($tmp_file_array);
				$file_name = $tmp_file_array[0];
				$file_ext = $tmp_file_array[count($tmp_file_array)-1];
				if (strtolower($file_ext) == "tif") {

					echo "<tr>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=bin&ext=".$faxextension."&filename=".$file."\">\n";
					echo "    	$file";
					echo "	  </a>";
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					if (file_exists($dir_fax_sent.'/'.$file_name.".pdf")) {
						echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=bin&ext=".$faxextension."&filename=".$file_name.".pdf\">\n";
						echo "    	pdf";
						echo "	  </a>";
					}
					else {
						echo "&nbsp;\n";
					}
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					if (file_exists($dir_fax_sent.'/'.$file_name.".png")) {
						echo "	  <a href=\"v_fax_edit.php?id=".$id."&a=download&type=fax_sent&t=png&ext=".$faxextension."&filename=".$file_name.".png\" target=\"_blank\">\n";
						echo "    	png";
						echo "	  </a>";
					}
					else {
						echo "&nbsp;\n";
					}
					echo "  </td>\n";
					echo "  <td class='".$rowstyle[$c]."' ondblclick=\"\">\n";
					echo 		date ("F d Y H:i:s", filemtime($dir_fax_sent.$file));
					echo "  </td>\n";

					echo "  <td class=\"".$rowstyle[$c]."\" ondblclick=\"list\">\n";
					echo "	".$tmp_filesize;
					echo "  </td>\n";

					echo "  <td class='' valign=\"middle\" nowrap>\n";
					echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
					echo "      <tr>\n";
					echo "        <td><a href=\"v_fax_edit.php?id=".$id."&type=fax_sent&act=del&filename=".$file."\" onclick=\"return confirm('Do you really want to delete this file?')\"><img src=\"$v_icon_delete\" width=\"17\" height=\"17\" border=\"0\"></a></td>\n";
					echo "      </tr>\n";
					echo "   </table>\n";
					echo "  </td>\n";
					echo "</tr>\n";
					if ($c==0) { $c=1; } else { $c=0; }
				} //check if the file is a .tif file

			}
		} //end while
		closedir($handle);
	}


	echo "     <tr>\n";
	echo "       <td class=\"list\" colspan=\"3\"></td>\n";
	echo "       <td class=\"list\"></td>\n";
	echo "     </tr>\n";
	echo "     </table>\n";
	echo "\n";
	echo "\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";
	echo "	<br />\n";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
