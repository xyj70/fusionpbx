<?php
	//application details
		$apps[$x]['name'] = "XML CDR";
		$apps[$x]['uuid'] = "4a085c51-7635-ff03-f67b-86e834422848";
		$apps[$x]['category'] = "Switch";;
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "Call Detail Records with all information about the call.";
		$apps[$x]['description']['es-cl'] = "Registro de detalle de llamados con toda la información de la llamada";
		$apps[$x]['description']['es-mx'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['fr-fr'] = "Historique des Appels complets.";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-ch'] = "";
		$apps[$x]['description']['pt-pt'] = "Detalhes das Gravações de Voz com todas as informações sobre a chamada.";
		$apps[$x]['description']['pt-br'] = "";

	//menu details
		$y = 0;
		$apps[$x]['menu'][$y]['title']['en-us'] = "Call Detail Records";
		$apps[$x]['menu'][$y]['title']['es-cl'] = "Registro de detalle de llamada";
		$apps[$x]['menu'][$y]['title']['es-mx'] = "";
		$apps[$x]['menu'][$y]['title']['de-de'] = "";
		$apps[$x]['menu'][$y]['title']['de-ch'] = "";
		$apps[$x]['menu'][$y]['title']['de-at'] = "";
		$apps[$x]['menu'][$y]['title']['fr-fr'] = "Historiques Appels";
		$apps[$x]['menu'][$y]['title']['fr-ca'] = "";
		$apps[$x]['menu'][$y]['title']['fr-ch'] = "";
		$apps[$x]['menu'][$y]['title']['pt-pt'] = "Detalhes das Gravações de Voz";
		$apps[$x]['menu'][$y]['title']['pt-br'] = "";
		$apps[$x]['menu'][$y]['uuid'] = "8f80e71a-31a5-6432-47a0-7f5a7b271f05";
		$apps[$x]['menu'][$y]['parent_uuid'] = "fd29e39c-c936-f5fc-8e2b-611681b266b5";
		$apps[$x]['menu'][$y]['category'] = "internal";
		$apps[$x]['menu'][$y]['path'] = "/app/xml_cdr/xml_cdr.php";
		$apps[$x]['menu'][$y]['groups'][] = "user";
		$apps[$x]['menu'][$y]['groups'][] = "admin";
		$apps[$x]['menu'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['menu'][$y]['title']['en-us'] = "CDR Statistics";
		$apps[$x]['menu'][$y]['title']['es-cl'] = "Statistics CDR";
		$apps[$x]['menu'][$y]['title']['es-mx'] = "";
		$apps[$x]['menu'][$y]['title']['de-de'] = "";
		$apps[$x]['menu'][$y]['title']['de-ch'] = "";
		$apps[$x]['menu'][$y]['title']['de-at'] = "";
		$apps[$x]['menu'][$y]['title']['fr-fr'] = "Statistics CDR";
		$apps[$x]['menu'][$y]['title']['fr-ca'] = "";
		$apps[$x]['menu'][$y]['title']['fr-ch'] = "";
		$apps[$x]['menu'][$y]['title']['pt-pt'] = "Statistics CDR";
		$apps[$x]['menu'][$y]['title']['pt-br'] = "";
		$apps[$x]['menu'][$y]['uuid'] = "032887d2-2315-4e10-b3a2-8989f719c80c";
		$apps[$x]['menu'][$y]['parent_uuid'] = "0438b504-8613-7887-c420-c837ffb20cb1";
		$apps[$x]['menu'][$y]['category'] = "internal";
		$apps[$x]['menu'][$y]['path'] = "/app/xml_cdr/xml_cdr_statistics.php";
		$apps[$x]['menu'][$y]['groups'][] = "user";
		$apps[$x]['menu'][$y]['groups'][] = "admin";
		$apps[$x]['menu'][$y]['groups'][] = "superadmin";

	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_view";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "8f80e71a-31a5-6432-47a0-7f5a7b271f05";
		$apps[$x]['permissions'][$y]['groups'][] = "user";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_search";
		$apps[$x]['permissions'][$y]['groups'][] = "user";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_search_advanced";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_domain";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_add";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_edit";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_delete";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_pdd";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "xml_cdr_mos";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = "v_xml_cdr";
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "id";
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = "xml_cdr_id";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "serial";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "integer";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "INT NOT NULL AUTO_INCREMENT";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = "true";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_domains";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "domain_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "extension_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_extensions";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "extension_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "v_id";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = "true";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_name";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "accountcode";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "direction";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "default_language";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "context";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "xml";
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = "xml_cdr";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "json";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "json";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "caller_id_name";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "caller_id_number";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "destination_number";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "start_epoch";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "bigint";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "start_stamp";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "timestamp";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "date";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "timestamp";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "answer_stamp";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "timestamp";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "date";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "timestamp";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "answer_epoch";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "bigint";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "end_epoch";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "bigint";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "end_stamp";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "duration";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "mduration";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "billsec";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "billmsec";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "bridge_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "read_codec";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "read_rate";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "write_codec";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "write_rate";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "remote_media_ip";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "network_addr";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "recording_file";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Indicates if a recording was made. If a recording exists set this value to true.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "leg";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "char(1)";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(1)";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "The leg of the call a or b.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "pdd_ms";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "smallint";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Post Dial Delay (PDD) in miliseconds. Divide by 1000 for seconds.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "rtp_audio_in_mos";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "last_app";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Save the last application in the leg.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "last_arg";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Save the last application data.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_side";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Queue side is either member or agent";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_member_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Unique member identifier";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_queue_joined_epoch";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Epoch when caller joined the queue";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_queue";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Queue";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_member_session_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Unique session identifier";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_agent";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "agent name";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "cc_agent_type";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "agent type";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "waitsec";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "wait seconds";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "conference_name";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "conference name";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "conference_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "conference unique identifier";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "conference_member_id";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "conference member id";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "digits_dialed";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "digits dialed";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "pin_number";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "pin number";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "hangup_cause";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "hangup_cause_q850";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "sip_hangup_disposition";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Save who hung up or cancelled the leg.";

?>