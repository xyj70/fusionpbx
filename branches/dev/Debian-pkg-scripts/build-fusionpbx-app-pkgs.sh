#!/bin/bash
PKGVER=3.5-1 # this is the version number you update
TIME=$(date +"%a, %d %b %Y %X")
STABLE=/usr/home/repo/deb/debian
DEVEL=/usr/home/repo/deb-dev/debian
REPO=$DEVEL
WRKDIR=/usr/src/fusionpbx-pkgs-build

rm -rf /usr/src/fusionpbx-pkgs-build

build_stable_pkgs="n"

if [[ $build_stable_pkgs == "y" ]]; then
svn_src=http://fusionpbx.googlecode.com/svn/trunk
else
svn_src=http://fusionpbx.googlecode.com/svn/branches/dev
fi

#get pkg system scripts
svn export http://fusionpbx.googlecode.com/svn/branches/dev/Debian-pkg-scripts "$WRKDIR"

#SET Version nmbr in debian/changelog
cat > "$WRKDIR"/fusionpbx-core/debian/changelog << DELIM
fusionpbx-core ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-core

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM

#SET Version nmbr in debian/changelog
cat > "$WRKDIR"/fusionpbx-conf/debian/changelog << DELIM
fusionpbx-conf ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-conf

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM

#SET Version nmbr in debian/changelog
cat > "$WRKDIR"/fusionpbx-scripts/debian/changelog << DELIM
fusionpbx-conf ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-scripts

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM


#SET Version nmbr in debian/changelog
cat > "$WRKDIR"/fusionpbx-sounds/debian/changelog << DELIM
fusionpbx-conf ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-sounds

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM


for i in accessible classic default enhanced nature
do cat > "$WRKDIR"/fusionpbx-themes/fusionpbx-theme-"${i}"/debian/changelog << DELIM
fusionpbx-theme-${i} ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-theme-${i//_/-}

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM
done

for i in adminer call_block call_broadcast call_center call_center_active call_flows calls \
calls_active click_to_call conference_centers conferences conferences_active \
contacts content destinations devices dialplan dialplan_inbound \
dialplan_outbound edit exec extensions fax fifo fifo_list follow_me gateways hot_desking \
ivr_menu login log_viewer meetings modules music_on_hold park provision recordings \
registrations ring_groups schemas services settings sipml5 sip_profiles sip_status sql_query \
system time_conditions traffic_graph vars voicemail_greetings voicemails xml_cdr xmpp
do cat > "$WRKDIR"/fusionpbx-apps/fusionpbx-app-${i//_/-}/debian/changelog << DELIM
fusionpbx-app-${i//_/-} ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-app-${i//_/-}

 -- Richard Neese <r.neese@gmail.com>  $TIME -0500

DELIM
done

#get src for core
svn export --force "$svn_src"/fusionpbx "$WRKDIR"/fusionpbx-core

#conf dir src
svn export --force "$svn_src"/fusionpbx/resources/templates/conf "$WRKDIR"/fusionpbx-conf/conf

#scripts dir src
svn export --force "$svn_src"/fusionpbx/resources/install/scripts "$WRKDIR"/fusionpbx-scripts/scripts

#sounds dir src
svn export --force "$svn_src"/fusionpbx/resources/install/sounds "$WRKDIR"/fusionpbx-sounds/sounds

#get src for theme
for i in accessible classic default enhanced nature
do svn export "$svn_src"/fusionpbx/themes/"${i}" "$WRKDIR"/fusionpbx-themes/fusionpbx-theme-"${i}"/"${i}"
done

#get src for apps
for i in adminer call_block call_broadcast call_center call_center_active call_flows calls \
calls_active click_to_call conference_centers conferences conferences_active \
contacts content destinations devices dialplan dialplan_inbound \
dialplan_outbound edit exec extensions fax fifo fifo_list follow_me gateways hot_desking \
ivr_menu login log_viewer meetings modules music_on_hold park provision recordings \
registrations ring_groups schemas services settings sip_profiles sip_status sql_query \
system time_conditions traffic_graph vars voicemail_greetings voicemails xml_cdr xmpp
do svn export "$svn_src"/fusionpbx/app/"${i}" "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i//_/-}"/"${i}"
done

#phone provisioing templates
for i in aastra cisco grandstream linksys panasonic polycom snom yealink
do svn export "$svn_src"/fusionpbx/resources/templates/provision/"${i}" "$WRKDIR"/fusionpbx-templates/fusionpbx-provisioning-template-"${i}"/"${i}"
done

#Extra Appss
for i in sipml5
do svn export "$svn_src"/apps/"${i}" "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i//_/-}"/"${i}"
done

#Build pkgs
#build core pkg
cd "$WRKDIR"/fusionpbx-core
rm -rf app/* themes/* resources/templates/provision resources/templates/conf resources/install/scripts resources/install/sounds
dpkg-buildpackage -rfakeroot -i

#patch config files
#remove unused extensions from configs dir
for i in "$WRKDIR"/fusionpbx-conf/conf/directory/default/*.xml ;do rm "$i" ; done
for i in "$WRKDIR"/fusionpbx-conf/conf/directory/default/*.noload ;do rm "$i" ; done

#fix sounds dir
/bin/sed "$WRKDIR"/fusionpbx-conf/conf/autoload_configs/local_stream.conf.xml -i -e s,'<directory name="default" path="$${sounds_dir}/music/8000">','<directory name="default" path="$${sounds_dir}/music/default/8000">',g

#Adding changes to freeswitch profiles
#Enableing device login auth failures ing the sip profiles.
sed "$WRKDIR"/fusionpbx-configs/conf/sip_profiles/internal.xml -i -e s,'<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>',g

sed "$WRKDIR"/fusionpbx-configs/conf/sip_profiles/internal.xml -i -e s,'<!-- *<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/> *-->','<param name="log-auth-failures" value="true"/>', \
				-e s,'<!--<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/>-->','<param name="log-auth-failures" value="true"/>',g

#Build conf pkg
cd "$WRKDIR"/fusionpbx-configs
dpkg-buildpackage -rfakeroot -i

#Build conf pkg
cd "$WRKDIR"/fusionpbx-scripts
dpkg-buildpackage -rfakeroot -i

#Build conf pkg
cd "$WRKDIR"/fusionpbx-sounds
dpkg-buildpackage -rfakeroot -i

#build theme pkgs
for i in accessible classic default enhanced nature
do cd "$WRKDIR"/fusionpbx-themes/fusionpbx-theme-"${i}"
dpkg-buildpackage -rfakeroot -i
done

#build app pkgs
for i in adminer call-block call-broadcast call-center call-center-active call-flows calls \
calls-active click-to-call conference-centers conferences conferences-active \
contacts content destinations devices dialplan dialplan-inbound \
dialplan-outbound edit exec extensions fax fifo fifo-list follow-me gateways hot-desking \
ivr-menu login log-viewer meetings modules music-on-hold park provision recordings \
registrations ring-groups schemas services settings sipml5 sip-profiles sip-status sql-query \
system time-conditions traffic-graph vars voicemail-greetings voicemails xml-cdr xmpp
do cd "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i}" 
dpkg-buildpackage -rfakeroot -i
done

for i in aastra cisco grandstream linksys panasonic polycom snom yealink
do cd "$WRKDIR"/fusionpbx-templates/fusionpbx-provisioning-template-"${i}"
dpkg-buildpackage -rfakeroot -i
done

cd "$WRKDIR"
mkdir -p "$WRKDIR"/debs-fusionpbx-wheezy

for i in "$WRKDIR" "$WRKDIR"/fusionpbx-apps "$WRKDIR"/fusionpbx-themes "$WRKDIR"/fusionpbx-templates
do
mv "${i}"/*.deb "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.changes "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.gz "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.dsc "$WRKDIR"/debs-fusionpbx-wheezy
done

cp -rp "$WRKDIR"/debs-fusionpbx-wheezy/* "$REPO"/incoming

cd "$REPO" && ./import-new-pkgs.sh
