--	Part of FusionPBX
--	Copyright (C) 2013 Mark J Crane <markjcrane@fusionpbx.com>
--	All rights reserved.
--
--	Redistribution and use in source and binary forms, with or without
--	modification, are permitted provided that the following conditions are met:
--
--	1. Redistributions of source code must retain the above copyright notice,
--	  this list of conditions and the following disclaimer.
--
--	2. Redistributions in binary form must reproduce the above copyright
--	  notice, this list of conditions and the following disclaimer in the
--	  documentation and/or other materials provided with the distribution.
--
--	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
--	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
--	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
--	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
--	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
--	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
--	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
--	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
--	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
--	POSSIBILITY OF SUCH DAMAGE.

--set defaults
	expire = {}
	expire["directory"] = "3600";
	expire["dialplan"] = "300";
	expire["sofia.conf"] = "3600";

--set the debug options
	debug["params"] = true;
	debug["sql"] = false;
	debug["xml_request"] = true;
	debug["xml_string"] = false;
	debug["cache"] = false;

--general functions
	dofile(scripts_dir.."/resources/functions/trim.lua");
	dofile(scripts_dir.."/resources/functions/file_exists.lua");
	dofile(scripts_dir.."/resources/functions/explode.lua");

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--exits the script if we didn't connect properly
	assert(dbh:connected());

--if the params class and methods do not exist then add them to prevent errors
	if (not params) then
		params = {}
		function params:getHeader(name)
			self.name = name
		end
		function params:serialize(name)
			self.name = name
		end
	end

--show the params in the console
	if (debug["params"]) then
		freeswitch.consoleLog("notice", "[xml_handler] Params:\n" .. params:serialize() .. "\n");
	end

--show the xml request in the console
	if (debug["xml_request"]) then
		freeswitch.consoleLog("notice", "[xml_handler] Section: " .. XML_REQUEST["section"] .. "\n");
		freeswitch.consoleLog("notice", "[xml_handler] Tag Name: " .. XML_REQUEST["tag_name"] .. "\n");
		freeswitch.consoleLog("notice", "[xml_handler] Key Name: " .. XML_REQUEST["key_name"] .. "\n");
		freeswitch.consoleLog("notice", "[xml_handler] Key Value: " .. XML_REQUEST["key_value"] .. "\n");
	end

--get the params and set them as variables
	domain_name = params:getHeader("sip_from_host");
	if (domain_uuid == nil) then
		domain_uuid = params:getHeader("domain_uuid");
	end
	domain_name = params:getHeader("domain");
	if (domain_name == nil) then
		domain_name = params:getHeader("domain_name");
	end
	if (domain_name == nil) then
		domain_name = params:getHeader("variable_domain_name");
	end
	purpose   = params:getHeader("purpose");
	profile   = params:getHeader("profile");
	key    = params:getHeader("key");
	user   = params:getHeader("user");
	user_context = params:getHeader("variable_user_context");
	call_context = params:getHeader("Caller-Context");
	destination_number = params:getHeader("Caller-Destination-Number");
	caller_id_number = params:getHeader("Caller-Caller-ID-Number");
	hunt_context = params:getHeader("Hunt-Context");
	if (hunt_context ~= nil) then
		call_context = hunt_context;
	end

--prepare the api object
	api = freeswitch.API();

--get the domain_uuid
	if (domain_uuid == nil) then
		--get the domain_uuid
			if (domain_name ~= nil) then
				sql = "SELECT domain_uuid FROM v_domains ";
				sql = sql .. "WHERE domain_name = '" .. domain_name .."' ";
				if (debug["sql"]) then
					freeswitch.consoleLog("notice", "[xml_handler] SQL: " .. sql .. "\n");
				end
				status = dbh:query(sql, function(rows)
					domain_uuid = rows["domain_uuid"];
				end);
			end
	end

--process the sections
	if (XML_REQUEST["section"] == "configuration") then
		configuration = scripts_dir.."/app/xml_handler/resources/scripts/configuration/"..XML_REQUEST["key_value"]..".lua";
		if (debug["xml_request"]) then
			freeswitch.consoleLog("notice", "[xml_handler] " .. configuration .. "\n");
		end
		if (file_exists(configuration)) then
			dofile(configuration);
		end
	end
	if (XML_REQUEST["section"] == "directory") then
		dofile(scripts_dir.."/app/xml_handler/resources/scripts/directory/directory.lua");
	end
	if (XML_REQUEST["section"] == "dialplan") then
		dofile(scripts_dir.."/app/xml_handler/resources/scripts/dialplan/dialplan.lua");
	end

--close the database connection
	dbh:release();