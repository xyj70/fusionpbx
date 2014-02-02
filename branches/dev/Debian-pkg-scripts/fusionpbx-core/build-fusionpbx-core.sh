#!/bin/bash
rm -rf /usr/src/fusionpbx-core
rm -rf /usr/src/debs-fusionpbx-core

svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx /usr/src/fusionpbx-core

cd /usr/src/fusionpbx-core

rm -rf app/* themes/*       

dpkg-buildpackage -rfakeroot -i

cd /usr/src

mkdir -p debs-fusionpbx-core

mv *.changes debs-fusionpbx-core
mv *.deb debs-fusionpbx-core
mv *.dsc debs-fusionpbx-core
mv *.gz debs-fusionpbx-core
