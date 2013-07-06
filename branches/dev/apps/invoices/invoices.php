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
if (permission_exists('invoice_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//get the contact id
	$contact_uuid = check_str($_REQUEST["id"]);

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br />";

	echo "<table width='100%' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align='left' nowrap='nowrap'><b>".$text['title-invoices']."</b></td>\n";
	echo "		<td width='50%' align=\"right\">\n";
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	//prepare to page the results
		$sql = "SELECT count(*) as num_rows FROM v_invoices ";
		$sql .= "LEFT OUTER JOIN v_contacts ";
		$sql .= "ON v_invoices.contact_uuid_to = v_contacts.contact_uuid ";
		$sql .= "where v_invoices.domain_uuid = '$domain_uuid' ";
		if (strlen($contact_uuid) > 0) {
			$sql .= "and v_invoices.contact_uuid_to = '$contact_uuid' ";
		}
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
		$prep_statement->execute();
			$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}

	//prepare to page the results
		$rows_per_page = 150;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
		list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page); 
		$offset = $rows_per_page * $page; 

	//get the list
		$sql = "SELECT * FROM v_invoices ";
		$sql .= "LEFT OUTER JOIN v_contacts ";
		$sql .= "ON v_invoices.contact_uuid_to = v_contacts.contact_uuid ";
		$sql .= "where v_invoices.domain_uuid = '$domain_uuid' ";
		if (strlen($contact_uuid) > 0) {
			$sql .= "and v_invoices.contact_uuid_to = '$contact_uuid' ";
		}
		if (strlen($order_by) == 0) { 
			$sql .= "order by v_invoices.invoice_number asc ";
		}
		else { 
			$sql .= "order by v_invoices.$order_by $order ";
		}
		$sql .= "limit $rows_per_page offset $offset ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		$result_count = count($result);
		unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('invoice_number', $text['label-invoice_number'], $order_by, $order);
	echo th_order_by('contact_organization', $text['label-contact_to_organization'], $order_by, $order);
	echo th_order_by('contact_name_given', $text['label-contact_to_given_name'], $order_by, $order);
	echo th_order_by('contact_name_family', $text['label-contact_to_family_name'], $order_by, $order);
	echo th_order_by('invoice_date', $text['label-invoice_date'], $order_by, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('invoice_add')) {
		echo "	<a href='invoice_edit.php?contact_uuid=".$_GET['id']."' alt='".$text['button-add']."'>$v_link_label_add</a>\n";
	}
	else {
		echo "	&nbsp;\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['invoice_number']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['contact_organization']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['contact_name_given']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['contact_name_family']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['invoice_date']."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('invoice_edit')) {
				echo "		<a href='invoice_edit.php?contact_uuid=".$row['contact_uuid']."&id=".$row['invoice_uuid']."' alt='".$text['button-edit']."'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('invoice_delete')) {
				echo "		<a href='invoices_delete.php?contact_uuid=".$row['contact_uuid']."&id=".$row['invoice_uuid']."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='8' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap='nowrap'>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('invoice_add')) {
	echo "			<a href='invoice_edit.php?contact_uuid=".$_GET['id']."' alt='".$text['button-add']."'>$v_link_label_add</a>\n";
	}
	else {
		echo "			&nbsp;\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br /><br />";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br /><br />";

//include the footer
	require_once "includes/footer.php";
?>