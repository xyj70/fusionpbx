#!/bin/bash
rm -rf /usr/src/fusionpbx-core
rm -rf /usr/src/debs-fusionpbx-core

svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx /usr/src/fusionpbx-core

cd /usr/src/fusionpbx-core

for i in adminer call_clock call_broadcast call_center call_center_active call_flows calls_active click_to_call \
         confrenece_center conferences conferences_active contacts content devices dialplanfratures edit exec fax \
         fifo fifo_list follow_me hot_desking ivr_menu meeting park provisioning recordings ring_groups schemas services \
         sql_query time_conditions traffic_graph xmpp
do rm -rf app/"${i}"
done        
  
mkdir /usr/src/fusionpbx-themes
for i in accessible clasic default enhanced nature ;do rm-rf app/"${i}" ;done        

dpkg-buildpackage -rfakeroot -i

cd /usr/src

mkdir -p debs-fusionpbx-core

mv *.changes debs-fusionpbx-core
mv *.deb debs-fusionpbx-core
mv *.dsc debs-fusionpbx-core
mv *.gz debs-fusionpbx-core
