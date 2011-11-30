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
	T_Dot_Zilla <vipkilla@gmail.com>
	T_Dot_Zilla
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	T_Dot_Zilla <vipkilla@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists("ingroup_add") ||
	permission_exists("ingroup_edit") || 
	permission_exists("ingroup_delete") ||
	ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get data from the db
	if (strlen($_REQUEST["id"])> 0) {
		$id = $_REQUEST["id"];
	}
	if(strlen($_GET["ingroup"])) {
		$cid = $_GET["ingroup"];
	}
//get the username from v_users
	$sql = "";
	$sql .= "select * from v_numbers ";
	$sql .= "where id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$number = $row["number"];
		$ingroup = $row["ingroup"];
		$class = $row["class"];
		$t_id = $row["v_id"];
		$domain = $row["domain"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}

if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_REQUEST["id"];
	$ingroup = check_str($_POST["ingroup"]);
	$class = check_str($_POST["class"]);
	$domain = check_str($_POST["domain"]);
	if (strlen($ingroup) == 0) { $msgerror .= "Please select an inbound group. <br>\n"; }
	if (strlen($class) == 0) { $msgerror .= "Please select a number class. <br>\n"; }
	if (strlen($domain) == 0) { $msgerror .= "Please select a domain.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		echo "<br />\n";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	//if the template has not been assigned by the superadmin
		if (strlen($_SESSION["v_template_name"]) == 0) {
			//set the session theme for the active user
			if ($_SESSION["username"] == $username) {
				$_SESSION["template_name"] = $ingroup_template_name;
			}
		}
	//sql get v_id for the domain
        $sql = "";
        $sql .= "select v_id from v_system_settings where v_domain='$domain'";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        $result = $prepstatement->fetchAll();
        foreach ($result as &$row) {
                $t_id = $row["v_id"];
                break; //limit to 1 row
        }
        unset ($prepstatement);

	//check if number is in a dialplan

	$sql = "select v_id, public_include_id, condition_expression_1 from v_numbers where number='".$number."'";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$nv_id = $row["v_id"];
		$fdata = $row["condition_expression_1"];
		$public_include_id = $row["public_include_id"];
		break;
	}
	unset($prepstatement);

	//sql update
	$sql  = "update v_numbers set ";
	//if number changes domain remove from dialplan
	if($t_id!=$nv_id) {
                $dsql = "delete from v_public_includes where public_include_id='".$public_include_id."' and v_id='".$nv_id."'";
                $count = $db->exec(check_sql($dsql));
                $dsql = "delete from v_public_includes_details where public_include_id='".$public_include_id."' and v_id='".$nv_id."'";
                $count = $db->exec(check_sql($dsql));
		$v_id = $nv_id;
		$sql .= "v_id = '$t_id', ";
		$sql .= "public_include_id = '0', ";
	}
	$sql .= "domain = '$domain', ";
	$sql .= "ingroup = '$ingroup', ";
	$sql .= "class = '$class' ";
	$sql .= "where id = '$id' ";
	$count = $db->exec(check_sql($sql));


	//clear the template so it will rebuild in case the template was changed
	$_SESSION["template_content"] = '';

	sync_package_v_public_includes();

	//redirect the user
	require_once "includes/header.php";
	if(isset($cid)) {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=listgroupnumbers.php?cid=$cid\">\n";
	}
	else {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=listnumbers.php?id=$id\">\n";
	}
	echo "<div align='center'>Update Complete</div>";
	require_once "includes/footer.php";
	return;
}
//include the header
	require_once "includes/header.php";

//show the content
	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<br />\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	echo "<table $tablewidth cellpadding='3' cellspacing='0' border='0'>";
	echo "<td align='left' width='90%' nowrap><b>Inbound Number Manager</b></td>\n";
	echo "<td nowrap='nowrap'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='Save'>";
	echo "	<input type='button' class='btn' onclick=\"window.location='index.php'\" value='Back'>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	Edit inbound number information. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

	echo "<table $tablewidth cellpadding='6' cellspacing='0' border='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Inbound Number Info</th>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Number:</td>";
	echo "		<td width='70%' class='vtable'><input type=\"hidden\" name=\"t_id\" value=\"${t_id}\">$number</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncellreq'>Inbound Group: </td>";
        echo "          <td class='vtable'>";
        $sql = "select name from v_ingroups order by name";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        
        echo "                  <select name=\"ingroup\" style='width: 200px;' class='formfld'>\n";
        echo "                          <option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
                if($field[name]==$ingroup)
                        echo "                  <option value='".$field[name]."' selected>".$field[name]."</option>\n";
                else    
                        echo "                  <option value='".$field[name]."'>".$field[name]."</option>\n";
        }
        echo "                  </select>";
        unset($sql, $result);

        echo "          </td>";
	echo "	</tr>";

        echo "  <tr>";
        echo "          <td class='vncellreq'>Class:</td>";
        echo "          <td class='vtable'>";
        $sql = "select name from v_number_classes order by name";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        echo "                  <select name=\"class\" style='width: 200px;' class='formfld'>\n";
        echo "                          <option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
		if($field[name]==$class)
			echo "                  <option value='".$field[name]."' selected>".$field[name]."</option>\n";
		else
	                echo "                  <option value='".$field[name]."'>".$field[name]."</option>\n";
        }
        echo "                  </select>";
        unset($sql, $result);
	echo "		</td>";

	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Domain:</td>";
        echo "          <td class='vtable'>";
        $sql = "select v_domain from v_system_settings order by v_domain";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        echo "                  <select name=\"domain\" style='width: 200px;' class='formfld'>\n";
        echo "                          <option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
                if($field[v_domain]==$domain)
                        echo "                  <option value='".$field[v_domain]."' selected>".$field[v_domain]."</option>\n";
                else    
                        echo "                  <option value='".$field[v_domain]."'>".$field[v_domain]."</option>\n";
        }
        echo "                  </select>";
        unset($sql, $result);
        echo "          </td>";
	echo "	</tr>";

	echo "</table>";

	echo "<br>";
	echo "<br>";
	echo "</div>";
	echo "</form>";

//include the footer
	require_once "includes/footer.php";

?>
