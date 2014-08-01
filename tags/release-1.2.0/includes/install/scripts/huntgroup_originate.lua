--get the argv values
	uuid = argv[1];
	sipuri = argv[2];
	extension = argv[3];
	caller_id_name = argv[4];
	caller_id_number = argv[5];
	caller_announce = argv[6];

--variable preparation
	tmp_sipuri = '';
	caller_id_name = string.gsub(caller_id_name, "+", " ");

function explode ( seperator, str ) 
	local pos, arr = 0, {}
	for st, sp in function() return string.find( str, seperator, pos, true ) end do -- for each divider found
		table.insert( arr, string.sub( str, pos, st-1 ) ) -- attach chars left of current divider
		pos = sp + 1 -- jump past current divider
	end
	table.insert( arr, string.sub( str, pos ) ) -- attach chars right of last divider
	return arr
end

function originate (session, sipuri, extension, caller_announce, caller_id_name, caller_id_number)

	cid = ",origination_caller_id_name="..caller_id_name..",origination_caller_id_number="..caller_id_number;
	local new_session = freeswitch.Session("{ignore_early_media=true"..cid.."}"..sipuri);
	new_session:execute("set", "call_timeout=30");

	if ( new_session:ready() ) then

		--set the sounds path for the language, dialect and voice
			default_language = new_session:getVariable("default_language");
			default_dialect = new_session:getVariable("default_dialect");
			default_voice = new_session:getVariable("default_voice");
			if (not default_language) then default_language = 'en'; end
			if (not default_dialect) then default_dialect = 'us'; end
			if (not default_voice) then default_voice = 'callie'; end

		--caller announce
			if (caller_announce) then
				new_session:streamFile(caller_announce);
			end

		--set the sounds directory
			sounds_dir = new_session:getVariable("sounds_dir");

		--promt user for action
			dtmf_digits = new_session:playAndGetDigits(1, 1, 3, 3000, "#", sounds_dir.."/"..default_language.."/"..default_dialect.."/"..default_voice.."/custom/8000/press_1_to_accept_2_to_reject_or_3_for_voicemail.wav", "", "\\d+");
			freeswitch.consoleLog("NOTICE", "followme: "..dtmf_digits.."\n");

			if ( dtmf_digits == "1" ) then
				freeswitch.consoleLog("NOTICE", "followme: call accepted\n");
				freeswitch.consoleLog("NOTICE", extension.."@${domain_name} out nowait\n");
				new_session:execute("fifo", extension.."@${domain_name} out nowait");
				return true;
			end
			if ( dtmf_digits == "2" ) then
				freeswitch.consoleLog("NOTICE", "followme: call rejected\n");
				new_session:hangup();
				return false;
			end
			if ( dtmf_digits == "3" ) then
				freeswitch.consoleLog("NOTICE", "followme: call sent to voicemail\n");
				cmd = "uuid_transfer "..uuid.." *99"..extension.." XML default";
				api = freeswitch.API();
				reply = api:executeString(cmd);
				return true;
			end
			if ( dtmf_digits == "" ) then
				freeswitch.consoleLog("NOTICE", "followme: no dtmf detected\n");
				return false;
			end
	end
end

sipuri_table = explode(",",sipuri);
for index,tmp_sip_uri in pairs(sipuri_table) do
	freeswitch.consoleLog("NOTICE", "sip_uri: "..tmp_sip_uri.."\n");
	result = originate (session, tmp_sip_uri, extension, caller_announce, caller_id_name, caller_id_number);
	if (result) then
		break;
		--exit;
	end
end