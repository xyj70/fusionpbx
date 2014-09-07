--
--	FusionPBX
--	Version: MPL 1.1
--
--	The contents of this file are subject to the Mozilla Public License Version
--	1.1 (the "License"); you may not use this file except in compliance with
--	the License. You may obtain a copy of the License at
--	http://www.mozilla.org/MPL/
--
--	Software distributed under the License is distributed on an "AS IS" basis,
--	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
--	for the specific language governing rights and limitations under the
--	License.
--
--	The Original Code is FusionPBX
--
--	The Initial Developer of the Original Code is
--	Mark J Crane <markjcrane@fusionpbx.com>
--	Copyright (C) 2010 - 2014
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--		Mark J. Crane
--		James O. Rose

--set default variables
	fax_retry_sleep = 30;
	fax_retry_limit = 4;
	fax_busy_limit = 5;
	api = freeswitch.API();

--include config.lua
	scripts_dir = string.sub(debug.getinfo(1).source,2,string.len(debug.getinfo(1).source)-(string.len(argv[0])+1));
	dofile(scripts_dir.."/resources/functions/config.lua");
	dofile(config());

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--define the explode function
	function explode ( seperator, str ) 
		local pos, arr = 0, {}
		for st, sp in function() return string.find( str, seperator, pos, true ) end do -- for each divider found
			table.insert( arr, string.sub( str, pos, st-1 ) ) -- attach chars left of current divider
			pos = sp + 1 -- jump past current divider
		end
		table.insert( arr, string.sub( str, pos ) ) -- attach chars right of last divider
		return arr
	end

--array count
	function count(t)
		c = 0;
		for k,v in pairs(t) do
  			c = c+1;
		end
		return c;
	end

-- show all channel variables
	--dat = env:serialize()
	--freeswitch.consoleLog("INFO","info:\n" .. dat .. "\n")

-- example channel variables relating to fax
	--variable_fax_success: 0
	--variable_fax_result_code: 49
	--variable_fax_result_text: The%20call%20dropped%20prematurely
	--variable_fax_ecm_used: off
	--variable_fax_local_station_id: SpanDSP%20Fax%20Ident
	--variable_fax_document_transferred_pages: 0
	--variable_fax_document_total_pages: 0
	--variable_fax_image_resolution: 0x0
	--variable_fax_image_size: 0
	--variable_fax_bad_rows: 0
	--variable_fax_transfer_rate: 14400

-- set channel variables to lua variables
	uuid = env:getHeader("uuid");
	fax_success = env:getHeader("fax_success");
	fax_result_text = env:getHeader("fax_result_text");
	fax_local_station_id = env:getHeader("fax_local_station_id");
	fax_ecm_used = env:getHeader("fax_ecm_used");
	fax_retry_attempts = tonumber(env:getHeader("fax_retry_attempts"));
	fax_retry_limit = tonumber(env:getHeader("fax_retry_limit"));
	--fax_retry_sleep = tonumber(env:getHeader("fax_retry_sleep"));
	fax_uri = env:getHeader("fax_uri");
	fax_file = env:getHeader("fax_file");
	fax_extension_number = env:getHeader("fax_extension_number");
	origination_caller_id_name = env:getHeader("origination_caller_id_name");
	origination_caller_id_number = env:getHeader("origination_caller_id_number");
	fax_bad_rows = env:getHeader("fax_bad_rows");
	fax_transfer_rate = env:getHeader("fax_transfer_rate");

	bridge_hangup_cause = env:getHeader("bridge_hangup_cause");
	fax_result_code = env:getHeader("fax_result_code");
	fax_busy_attempts = tonumber(env:getHeader("fax_busy_attempts"));

	hangup_cause_q850 = tonumber(env:getHeader("hangup_cause_q850"));

--set default values
	if (not origination_caller_id_name) then
		origination_caller_id_name = '000000000000000';
	end
	if (not origination_caller_id_number) then
		origination_caller_id_number = '000000000000000';
	end
	if (not fax_busy_attempts) then
		fax_busy_attempts = 0;
	end
	--we got a busy signal....  hack we should really check sip_term_cause
	if (not fax_success) then
		fax_success = "0";
		fax_result_code = 2;
	end
	if (hangup_cause_q850 == "17") then
		fax_success = "0";
		fax_result_code = 2;
	end
	if (not fax_result_text) then
		fax_result_text = "FS_NOT_SET";
	end

--get the values from the fax file
	array = explode("/", fax_file);
	domain_name = array[count(array)-3];
	extension_name = array[count(array)-2];
	file_name = array[count(array)];

--get the domain_uuid using the domain name required for multi-tenant
	if (domain_name ~= nil) then
		sql = "SELECT domain_uuid FROM v_domains ";
		sql = sql .. "WHERE domain_name = '" .. domain_name .. "' ";
		if (debug["sql"]) then
			freeswitch.consoleLog("notice", "[voicemail] SQL: " .. sql .. "\n");
		end
		status = dbh:query(sql, function(rows)
			domain_uuid = rows["domain_uuid"];
		end);
	end

--add to fax logs
	sql = "insert into v_fax_logs ";
	sql = sql .. "(";
	sql = sql .. "fax_log_uuid, ";
	sql = sql .. "domain_uuid, ";
	sql = sql .. "fax_success, ";
	sql = sql .. "fax_result_code, ";
	sql = sql .. "fax_result_text, ";
	sql = sql .. "fax_file, ";
	if (fax_ecm_used ~= nil) then
		sql = sql .. "fax_ecm_used, ";
	end
	if (fax_local_station_id ~= nil) then
		sql = sql .. "fax_local_station_id, ";
	end
	sql = sql .. "fax_document_transferred_pages, ";
	sql = sql .. "fax_document_total_pages, ";
	if (fax_image_resolution ~= nil) then
		sql = sql .. "fax_image_resolution, ";
	end
	if (fax_image_size ~= nil) then
		sql = sql .. "fax_image_size, ";
	end
	if (fax_bad_rows ~= nil) then
		sql = sql .. "fax_bad_rows, ";
	end
	if (fax_transfer_rate ~= nil) then
		sql = sql .. "fax_transfer_rate, ";
	end
	if (fax_retry_attempts ~= nil) then
		sql = sql .. "fax_retry_attempts, ";
	end
	if (fax_retry_limit ~= nil) then
		sql = sql .. "fax_retry_limit, ";
	end
	if (fax_retry_sleep ~= nil) then
		sql = sql .. "fax_retry_sleep, ";
	end
	sql = sql .. "fax_uri, ";
	sql = sql .. "fax_date, ";
	sql = sql .. "fax_epoch ";
	sql = sql .. ") ";
	sql = sql .. "values ";
	sql = sql .. "(";
	sql = sql .. "'"..uuid.."', ";
	sql = sql .. "'"..domain_uuid.."', ";
	sql = sql .. "'"..fax_success.."', ";
	sql = sql .. "'"..fax_result_code .."', ";
	sql = sql .. "'"..fax_result_text.."', ";
	sql = sql .. "'"..fax_file.."', ";
	if (fax_ecm_used ~= nil) then
		sql = sql .. "'"..fax_ecm_used.."', ";
	end
	if (fax_local_station_id ~= nil) then
		sql = sql .. "'"..fax_local_station_id.."', ";
	end
	if (fax_document_transferred_pages == nil) then
		sql = sql .. "'0', ";
	else
		sql = sql .. "'"..fax_document_transferred_pages.."', ";
	end
	if (fax_document_total_pages == nil) then
		sql = sql .. "'0', ";
	else
		sql = sql .. "'"..fax_document_total_pages.."', ";
	end
	if (fax_image_resolution ~= nil) then
		sql = sql .. "'"..fax_image_resolution.."', ";
	end
	if (fax_image_size ~= nil) then
		sql = sql .. "'"..fax_image_size.."', ";
	end
	if (fax_bad_rows ~= nil) then
		sql = sql .. "'"..fax_bad_rows.."', ";
	end
	if (fax_transfer_rate ~= nil) then
		sql = sql .. "'"..fax_transfer_rate.."', ";
	end
	if (fax_retry_attempts ~= nil) then
		sql = sql .. "'"..fax_retry_attempts.."', ";
	end
	if (fax_retry_limit ~= nil) then
		sql = sql .. "'"..fax_retry_limit.."', ";
	end
	if (fax_retry_sleep ~= nil) then
		sql = sql .. "'"..fax_retry_sleep.."', ";
	end
	sql = sql .. "'"..fax_uri.."', ";
	if (database["type"] == "sqlite") then
		sql = sql .. "'"..os.date("%Y-%m-%d %X").."', ";
	else
		sql = sql .. "now(), ";
	end
	sql = sql .. "'"..os.time().."' ";
	sql = sql .. ")";
	--if (debug["sql"]) then
		freeswitch.consoleLog("notice", "[fax_retry] "..sql.."\n");
	--end
	dbh:query(sql);

--for email
	email_address = env:getHeader("mailto_address");
	--email_address = api:execute("system", "/bin/echo -n "..email_address.." | /bin/sed -e s/\,/\\\\,/g");
	--if (not email_address) then
	--	email_address = '';
	--end
	email_address = email_address:gsub(",", "\\,");
	from_address = env:getHeader("mailfrom_address");
	if (from_address == null) then
		from_address = email_address;
	end
	--needs to be fixed on operating systems that do not have sed or echo utilities.
	number_dialed = api:execute("system", "/bin/echo -n "..fax_uri.." | sed -e s,.*/,,g");
	--do not use apostrophies in message, they are not excaped and the mail will fail.
	email_message_fail = "We are sorry the fax failed to go through.  It has been attached. Please check the number "..number_dialed..", and if it was correct you might consider emailing it instead."
	email_message_success = "We are happy to report the fax was sent successfully.  It has been attached for your records."

-- send the selected variables to the console
	if (fax_success == null) then
		freeswitch.consoleLog("INFO","fax_success: '" .. fax_success .. "'\n");
	end
	freeswitch.consoleLog("INFO","fax_result_text: '" .. fax_result_text .. "'\n");
	freeswitch.consoleLog("INFO","fax_file: '" .. fax_file .. "'\n");
	freeswitch.consoleLog("INFO","fax_file: \"" .. fax_file .. "\"\n");
	freeswitch.consoleLog("INFO","uuid: '" .. uuid .. "'\n");
	--freeswitch.consoleLog("INFO","fax_ecm_used: '" .. fax_ecm_used .. "'\n");
	freeswitch.consoleLog("INFO","fax_retry_attempts: " .. fax_retry_attempts.. "\n");
	freeswitch.consoleLog("INFO","fax_retry_limit: " .. fax_retry_limit.. "\n");
	freeswitch.consoleLog("INFO","fax_retry_sleep: " .. fax_retry_sleep.. "\n");
	freeswitch.consoleLog("INFO","fax_uri: '" .. fax_uri.. "'\n");
	freeswitch.consoleLog("INFO","origination_caller_id_name: " .. origination_caller_id_name .. "\n");
	freeswitch.consoleLog("INFO","origination_caller_id_number: " .. origination_caller_id_number .. "\n");
	freeswitch.consoleLog("INFO","fax_result_code: ".. fax_result_code .."\n");
	freeswitch.consoleLog("INFO","mailfrom_address: ".. from_address .."\n");
	freeswitch.consoleLog("INFO","mailto_address: ".. email_address .."\n");
	freeswitch.consoleLog("INFO","hangup_cause_q850: '" .. hangup_cause_q850 .. "'\n");

-- if the fax failed then try again
	if (fax_success == "0") then
	--DEBUG
	--email_cmd = "/bin/echo '"..email_message_fail.."' | /usr/bin/mail -s 'Fax to: "..number_dialed.." FAILED' -r "..from_address.." -a '"..fax_file.."' "..email_address;

--to keep the originate command shorter these are things we always send. One place to adjust for all.
	originate_same = "mailto_address='"..email_address.."',mailfrom_address='"..from_address.."',origination_caller_id_name='"..origination_caller_id_name.. "',origination_caller_id_number="..origination_caller_id_number..",fax_uri="..fax_uri..",fax_retry_limit="..fax_retry_limit..",fax_retry_sleep="..fax_retry_sleep..",fax_verbose=true,fax_file='"..fax_file.."'";


		if (fax_retry_attempts < fax_retry_limit) then 

			--timed out waitng for comm or on first message, or busy code
			if (fax_result_code == "2"  or fax_result_code == "3" or hangup_cause_q850 == 17) then
				--do nothing. don't want to increment
				freeswitch.consoleLog("INFO","Last Fax was probably Busy, don't increment retry_attempts. \n"); 
				fax_busy_attempts = fax_busy_attempts + 1;
				if (fax_busy_attempts > fax_busy_limit) then
					fax_retry_attempts = 17;
				else
					freeswitch.msleep(fax_retry_sleep * 1000);
				end
			--unallocated number
			elseif (hangup_cause_q850 == 1 ) then
				fax_retry_attempts = 10;
				email_message_fail = "We are sorry the fax failed to go through.  The number you specified is not a working number.  The fax has been attached. Please check the number "..number_dialed..", and if it was correct you might consider emailing it instead."

			elseif (fax_retry_attempts < 5 ) then
				freeswitch.consoleLog("INFO","Last Fax Failed, try a different way. Wait first.\n");
				freeswitch.msleep(fax_retry_sleep * 500);
			else
				freeswitch.consoleLog("INFO","All attempts to send fax to "..number_dialed.."FAILED\n");
			end

			if (fax_retry_attempts == 1) then
			--send t38 on ECM on
				freeswitch.consoleLog("INFO","FAX TRYING ["..fax_retry_attempts.."] of [4] to: "..number_dialed.." with: t38 ON ECM ON, Fast\n");
				if (hangup_cause_q850 ~= 17) then
					fax_retry_attempts = fax_retry_attempts + 1;
				end
				cmd = "originate {fax_retry_attempts="..fax_retry_attempts..","..originate_same..",fax_use_ecm=true,fax_enable_t38=true,fax_enable_t38_request=true,fax_disable_v17=false,fax_busy_attempts='"..fax_busy_attempts.."',api_hangup_hook='lua fax_retry.lua'}"..fax_uri.." &txfax('"..fax_file.."')";

			elseif (fax_retry_attempts == 2) then
			--send t38 off, ECM on
				freeswitch.consoleLog("INFO","FAX TRYING ["..fax_retry_attempts.."] of [4] to: "..number_dialed.." with: t38 OFF ECM ON, Fast\n");
				if (hangup_cause_q850 ~= 17) then
					fax_retry_attempts = fax_retry_attempts + 1;
				end
				cmd = "originate {fax_retry_attempts="..fax_retry_attempts..","..originate_same..",fax_use_ecm=true,fax_enable_t38=false,fax_enable_t38_request=false,fax_disable_v17=false,fax_busy_attempts='"..fax_busy_attempts.."',api_hangup_hook='lua fax_retry.lua'}"..fax_uri.." &txfax('"..fax_file.."')";

			elseif (fax_retry_attempts == 3) then
			--send t38 on v17 [slow] on ECM off
				freeswitch.consoleLog("INFO","FAX TRYING ["..fax_retry_attempts.."] of [4] to: "..number_dialed.." with: t38 ON ECM OFF, SLOW\n");
				if (hangup_cause_q850 ~= 17) then
					fax_retry_attempts = fax_retry_attempts + 1;
				end

				cmd = "originate {fax_retry_attempts="..fax_retry_attempts..","..originate_same..",fax_use_ecm=false,fax_enable_t38=true,fax_enable_t38_request=true,fax_disable_v17=true,fax_busy_attempts='"..fax_busy_attempts.."',api_hangup_hook='lua fax_retry.lua'}"..fax_uri.." &txfax('"..fax_file.."')";

			elseif (fax_retry_attempts == 4) then
			--send t38 off v17 [slow] on ECM off
				freeswitch.consoleLog("INFO","FAX TRYING ["..fax_retry_attempts.."] of [4] to: "..number_dialed.." with: t38 OFF ECM OFF, SLOW\n");
				if (hangup_cause_q850 ~= 17) then
					fax_retry_attempts = fax_retry_attempts + 1;
				end

				cmd = "originate {fax_retry_attempts="..fax_retry_attempts..","..originate_same..",fax_use_ecm=false,fax_enable_t38=false,fax_enable_t38_request=false,fax_disable_v17=true,fax_busy_attempts='"..fax_busy_attempts.."',api_hangup_hook='lua fax_retry.lua'}"..fax_uri.." &txfax('"..fax_file.."')";

			--bad number
			elseif (fax_retry_attempts == 10) then
				freeswitch.consoleLog("INFO","FAX_RETRY FAILED: BAD NUMBER\n");
				freeswitch.consoleLog("INFO", "FAX_RETRY_STATS FAILURE BAD NUMBER: GATEWAY[".. fax_uri .."]");
				email_message_fail = email_message_fail.."We tried sending, but the number entered was not a working phone number "
				email_address = email_address:gsub("\\,", ",");
				freeswitch.email("",
									"",
									"To: "..email_address.."\nFrom: "..from_address.."\nSubject: Fax to: "..number_dialed.." was INVALID",
									email_message_fail ,
									fax_file
								);

			--busy number
			elseif (fax_retry_attempts == 17) then
				freeswitch.consoleLog("INFO","FAX_RETRY FAILED: TRIED ["..fax_busy_attempts.."] of [4]: BUSY NUMBER\n");
				freeswitch.consoleLog("INFO", "FAX_RETRY_STATS FAILURE BUSY: GATEWAY[".. fax_uri .."], BUSY NUMBER");
				email_message_fail = email_message_fail.."  We tried sending, but the call was busy "..fax_busy_attempts.." of those times."
				email_address = email_address:gsub("\\,", ",");
				freeswitch.email("",
									"",
									"To: "..email_address.."\nFrom: "..from_address.."\nSubject: Fax to: "..number_dialed.." was BUSY",
									email_message_fail ,
									fax_file
								);

			else
				--the fax failed completely. send a message
				freeswitch.consoleLog("INFO","FAX_RETRY FAILED: TRIED ["..fax_retry_attempts.."] of [4]: GIVING UP\n");
				freeswitch.consoleLog("INFO", "FAX_RETRY_STATS FAILURE: GATEWAY[".. fax_uri .."], tried 5 combinations without success");

				email_message_fail = email_message_fail.."  We tried sending 5 times ways.  You may also want to know that the call was busy "..fax_busy_attempts.." of those times."
				email_address = email_address:gsub("\\,", ",");

				freeswitch.email("",
									"",
									"To: "..email_address.."\nFrom: "..from_address.."\nSubject: Fax to: "..number_dialed.." FAILED",
									email_message_fail ,
									fax_file
								);

				fax_retry_attempts = fax_retry_attempts + 1;

			end
			api = freeswitch.API();
			if ( not cmd ) then
				freeswitch.consoleLog("INFO","Last Fallthrough (5th) of FAX_RETRY.lua: \n");
			else
				freeswitch.consoleLog("INFO","retry cmd: " .. cmd .. "\n");
				reply = api:executeString(cmd);
			end
		end

	else
		--Success
		if (fax_retry_attempts == 0) then
			fax_trial = "fax_use_ecm=false,fax_enable_t38=true,fax_enable_t38_request=true,fax_disable_v17=default";
		elseif (fax_retry_attempts == 1) then
			fax_trial = "fax_use_ecm=true,fax_enable_t38=true,fax_enable_t38_request=true,fax_disable_v17=false";
		elseif (fax_retry_attempts == 2) then
			fax_trial = "fax_use_ecm=true,fax_enable_t38=false,fax_enable_t38_request=false,fax_disable_v17=false";
		elseif (fax_retry_attempts == 3) then
			fax_trial = "fax_use_ecm=true,fax_enable_t38=true,fax_enable_t38_request=true,fax_disable_v17=true";
		elseif (fax_retry_attempts == 4) then
			fax_trial = "fax_use_ecm=false,fax_enable_t38=false,fax_enable_t38_request=false,fax_disable_v17=false";
		else
			fax_trial = "fax_retry had an issue and tried more than 5 times"
		end
		freeswitch.consoleLog("INFO", "FAX_RETRY_STATS SUCCESS: GATEWAY[".. fax_uri .."] VARS[" .. fax_trial .. "]");
		email_address = email_address:gsub("\\,", ",");

		freeswitch.email("",
							"",
							"To: "..email_address.."\nFrom: "..from_address.."\nSubject: Fax to: "..number_dialed.." SENT",
							email_message_success ,
							fax_file
						);
	end