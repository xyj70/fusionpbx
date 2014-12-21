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
if (permission_exists('contact_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//require_once "resources/header.php";
require_once "resources/paging.php";

//get variables used to control the order
// 	$order_by = $_GET["order_by"];
// 	$order = $_GET["order"];

//show the content

	echo "<table width='100%' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%' align='left' nowrap='nowrap'><b>".$text['label-urls']."</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	//prepare to page the results
// 		$sql = " select count(*) as num_rows from v_contact_urls ";
// 		$sql .= " where domain_uuid = '".$_SESSION['domain_uuid']."' ";
// 		$sql .= " and contact_uuid = '$contact_uuid' ";
// 		$prep_statement = $db->prepare($sql);
// 		if ($prep_statement) {
// 		$prep_statement->execute();
// 			$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
// 			if ($row['num_rows'] > 0) {
// 				$num_rows = $row['num_rows'];
// 			}
// 			else {
// 				$num_rows = '0';
// 			}
// 		}

	//prepare to page the results
// 		$rows_per_page = 10;
// 		$param = "";
// 		$page = $_GET['page'];
// 		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
// 		list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page);
// 		$offset = $rows_per_page * $page;

	//get the contact list
		$sql = "select * from v_contact_urls ";
		$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "and contact_uuid = '$contact_uuid' ";
		$sql .= "order by url_primary desc, url_label asc ";
// 		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
// 		$sql .= " limit $rows_per_page offset $offset ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		$result_count = count($result);
		unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<th>".$text['label-url_label']."</th>\n";
	echo "<th>".$text['label-url_address']."</th>\n";
	echo "<th>".$text['label-url_description']."</th>\n";
	echo "<td class='list_control_icons'>";
	echo 	"<a href='contact_url_edit.php?contact_uuid=".$_GET['id']."' alt='".$text['button-add']."'>$v_link_label_add</a>";
	echo "</td>\n";
	echo "</tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$tr_link = "href='contact_url_edit.php?contact_uuid=".$row['contact_uuid']."&id=".$row['contact_url_uuid']."'";
			echo "<tr ".$tr_link." ".(($row['url_primary']) ? "style='font-weight: bold;'" : null).">\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['url_label']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]." tr_link_void' style='width: 40%; max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'><a href='".$row['url_address']."' target='_blank'>".str_replace("http://", "", str_replace("https://", "", $row['url_address']))."</a>&nbsp;</td>\n";
			echo "	<td valign='top' class='row_stylebg'>".$row['address_description']."&nbsp;</td>\n";
			echo "	<td class='list_control_icons'>";
			echo 		"<a href='contact_url_edit.php?contact_uuid=".$row['contact_uuid']."&id=".$row['contact_url_uuid']."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			echo 		"<a href='contact_url_delete.php?contact_uuid=".$row['contact_uuid']."&id=".$row['contact_url_uuid']."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='11' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
// 	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
// 	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td class='list_control_icons'>";
	echo 			"<a href='contact_url_edit.php?contact_uuid=".$_GET['id']."' alt='".$text['button-add']."'>$v_link_label_add</a>";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";

?>