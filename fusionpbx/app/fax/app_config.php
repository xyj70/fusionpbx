<?php
	//application details
		$apps[$x]['name'] = "Fax";
		$apps[$x]['uuid'] = '24108154-4ac3-1db6-1551-4731703a4440';
		$apps[$x]['category'] = 'Switch';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en-us'] = 'To receive a FAX setup a fax extension and then direct the incoming FAX with a dedicated number or you can detect the FAX tone by using on the Public tab.';
		$apps[$x]['description']['es-mx'] = 'Para recibir un FAX, configure una extensiÃ³n y despuÃ©s asigne un nÃºmero a esta.';
		$apps[$x]['description']['de-de'] = '';
		$apps[$x]['description']['de-ch'] = '';
		$apps[$x]['description']['de-at'] = '';
		$apps[$x]['description']['fr-fr'] = 'Pour recevoir un fax, configurez une extension du FAX et après diriger un numéro vers lui.';
		$apps[$x]['description']['fr-ca'] = 'Pour recevoir un fax, configurez un Ã©xtension du FAX et aprÃ¨s dirigers un nombre vers lui.';
		$apps[$x]['description']['fr-ch'] = '';
		$apps[$x]['description']['pt-pt'] = 'Para receber uma configuração de fax uma extensão de fax e, em seguida, direcionar o fax com um número dedicado ou você pode detectar o tom de fax, usando na guia Pública.';
		$apps[$x]['description']['pt-br'] = '';

	//menu details
		$apps[$x]['menu'][0]['title']['en-us'] = 'Fax Server';
		$apps[$x]['menu'][0]['title']['es-mx'] = 'Servidor de Fax';
		$apps[$x]['menu'][0]['title']['de-de'] = 'Faxserver';
		$apps[$x]['menu'][0]['title']['de-ch'] = 'Faxserver';
		$apps[$x]['menu'][0]['title']['de-at'] = 'Faxserver';
		$apps[$x]['menu'][0]['title']['fr-fr'] = 'Serveur Fax';
		$apps[$x]['menu'][0]['title']['fr-ca'] = 'Serveur du fax';
		$apps[$x]['menu'][0]['title']['fr-ch'] = '';
		$apps[$x]['menu'][0]['title']['pt-pt'] = 'Servidor de Fax';
		$apps[$x]['menu'][0]['title']['pt-br'] = '';
		$apps[$x]['menu'][0]['uuid'] = '9c9642e4-2b9b-2785-18d0-6c0a4ede2b2f';
		$apps[$x]['menu'][0]['parent_uuid'] = 'fd29e39c-c936-f5fc-8e2b-611681b266b5';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/app/fax/fax.php';
		$apps[$x]['menu'][0]['groups'][] = 'user';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][$y]['name'] = 'fax_extension_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_extension_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_extension_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_extension_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_inbox_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_inbox_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_sent_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_sent_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_send';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$apps[$x]['permissions'][$y]['groups'][] = 'user';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_user_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';;
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_user_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_user_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_user_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_log_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_log_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_log_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'fax_log_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][$y]['groups'][] = 'admin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_fax';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'fax_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'dialplan_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_dialplans';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'dialplan_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'fax_extension';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'faxextension';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'fax_destination_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'fax_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'faxname';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'fax_email';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'faxemail';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_pin_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_caller_id_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_caller_id_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_user_list';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_forward_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'fax_description';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'faxdescription';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';

		$y = 1; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_fax_users';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_user_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_fax';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'fax_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_users';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'user_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';

		$y = 2; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_fax_logs';
		//$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_log_uuid';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		//$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
		//$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		//$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_success';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_result_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_result_text';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_file';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_ecm_used';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_local_station_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_document_transferred_pages';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_document_total_pages';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_image_resolution';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_image_size';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_bad_rows';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_transfer_rate';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_retry_attempts';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_retry_limit';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_retry_sleep';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'fax_uri';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = '';

?>