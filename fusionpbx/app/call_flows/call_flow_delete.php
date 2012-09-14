<?php
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('call_flow_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
}

if (strlen($id)>0) {

	//get the dialplan uuid
		$sql = "select * from v_call_flows ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and call_flow_uuid = '$id' ";
		$prep_statement = $db->prepare($sql);
		$prep_statement->execute();
		while($row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
			$dialplan_uuid = $row['dialplan_uuid'];
		}

	//delete call_flow
		$sql = "delete from v_call_flows ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and call_flow_uuid = '$id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		unset($sql);

	//delete the dialplan entry
		$sql = "delete from v_dialplans ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$db->query($sql);
		unset($sql);

	//delete the dialplan details
		$sql = "delete from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$db->query($sql);
		unset($sql);

	//syncrhonize configuration
		save_dialplan_xml();

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
}

//redirect the user
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=call_flows.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>