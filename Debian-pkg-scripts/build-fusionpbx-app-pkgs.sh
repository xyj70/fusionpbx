#!/bin/bash
wrkdir=/usr/src/fusion-pkgs
build_stable="n"

rm -rf "$wrkdir"
mkdir "$wrkdir"

#get pkg system scripts
svn export http://fusionpbx.googlecode.com/svn/branches/dev/Debian-pkg-scripts "$wrkdir"

#pkg core

#get src for core
svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx "$wrkdir"/fusionpbx-core

#get src for themes         
for h in accessable classic default enhanced nature
do svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/"${i}" /usr/src/fusionpbx-themes/fusionpbx-themes-"${i}"/"${i}";
done

#get src for aps 
for i in adminer call_block call_broadcast call_center call_center_active call_flows calls \
calls_active click_to_call conference_centers conferences conferences_active \
contacts content destinations devices dialplan dialplan_features dialplan_inbound \
dialplan_outbound edit exec extensions fax fifo fifo_list follow_me gateways hot_desking \
ivr_menu login log_viewer meetings modules music_on_hold park provision recordings \
registrations ring_groups schemas services settings sip_profiles sip_status sql_query \
system time_conditions traffic_graph vars voicemail_greetings voicemails xml_cdr xmpp
do
j="${i//_/-}"
do svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/app/"${i}" "$wrkdir"/fusionpbx-apps/"${j}"/"${i}" ;done
done

#Build pkgs

#build core pkg
cd /usr/src/fusionpbx-core
rm -rf app/* themes/*       
dpkg-buildpackage -rfakeroot -i

#build theme pkgs
for h in accessable classic default enhanced nature
do cd "$wrkdir"/fusionpbx-themes-"${i}";
dpkg-buildpackage -r fakeroot -i
done    

#build app pkgs
for k in adminer call-block call-broadcast call-center call-center-active call-flows calls \
calls-active click-to-call conference-centers conferences conferences-active \
contacts content destinations devices dialplan dialplan-features dialplan-inbound \
dialplan-outbound edit exec extensions fax fifo fifo-list follow-me gateways hot-desking \
ivr-menu login log-viewer meetings modules music-on-hold park provision recordings \
registrations ring-groups schemas services settings sip-profiles sip-status sql-query \  
system time-conditions traffic-graph vars voicemail-greetings voicemails xml-cdr xmpp

do cd "$wrkdir"/fusionpbx-app-"${k}" ; dpkg-buildpackage -r fakeroot -i
done    


      


