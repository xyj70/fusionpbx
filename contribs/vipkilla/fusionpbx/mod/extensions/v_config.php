<?php
	//application details
		$apps[$x]['name'] = "Extensions";
		$apps[$x]['guid'] = 'E68D9689-2769-E013-28FA-6214BF47FCA3';
		$apps[$x]['category'] = 'PBX';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Used Configure extensions. ';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Quick Add';
		$apps[$x]['menu'][0]['guid'] = 'F893EDFF-B243-3E29-C7C7-6BF6C48441CB';
		$apps[$x]['menu'][0]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/extensions/v_quickadd.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';
		$apps[$x]['menu'][1]['title']['en'] = 'Extensions';
		$apps[$x]['menu'][1]['guid'] = 'D3036A99-9A9F-2AD6-A82A-1FE7BEBBE2D3';
		$apps[$x]['menu'][1]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
		$apps[$x]['menu'][1]['category'] = 'internal';
		$apps[$x]['menu'][1]['path'] = '/mod/extensions/v_extensions.php';
		$apps[$x]['menu'][1]['groups'][] = 'admin';
		$apps[$x]['menu'][1]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'extension_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'extension_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'extension_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'extension_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][4]['name'] = 'extension_toll';
		$apps[$x]['permissions'][4]['groups'][] = 'superadmin';

	//schema details
		$z = 0;
		$y = 0;
		$apps[$x]['db'][$z]['table'] = 'v_extensions';
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'extension_id';
		$apps[$x]['db'][$z]['fields'][$y]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$z]['fields'][$y]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][$z]['fields'][$y]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'v_id';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'extension';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'number_alias';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'password';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'user_list';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'provisioning_list';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'mailbox';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'vm_password';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'accountcode';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'effective_caller_id_name';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'effective_caller_id_number';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'outbound_caller_id_name';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'outbound_caller_id_number';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'limit_max';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'limit_destination';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'vm_enabled';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'vm_mailto';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'vm_attach_file';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'vm_keep_local_after_email';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'user_context';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'toll_allow';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'callgroup';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'hold_music';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;		
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'auth_acl';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'cidr';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'sip_force_contact';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'nibble_account';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'sip_force_expires';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'enabled';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'description';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'mwi_account';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'sip_bypass_media';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		// if lua_route_enabled is true, override voicemail with lua_route_id as destination if called party does not answer
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'lua_route_enabled';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][$z]['fields'][$y]['name'] = 'lua_route_id';
		$apps[$x]['db'][$z]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][$z]['fields'][$y]['description'] = '';
?>
